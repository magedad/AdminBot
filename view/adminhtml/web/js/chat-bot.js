define([
    'jquery',
    'jquery/ui',
    'Magento_Ui/js/modal/modal'
], function ($, ui, modal) {
  'use strict';

  $.widget('mage.boties', {
    options: {
      autoSuggestClass: '.auto-suggest',
      sendButtonClass: '.send-message',
      chatHeaderClass: '.chat-header',
      inputMessageId: '.chat-message',
      botImage: '',
      url: '',
    },

    /**
     * @private
     */
    _create: function () {
      this._initiateChatBot();
    },

    _initiateChatBot: function() {
        var $this = this;
        var $element = this.element;
        var $options = this.options;
        $element.find($options.chatHeaderClass).off().on("click", function(e) {
            if (typeof e.target.href == 'undefined') {
                $element.find('.prime').toggleClass('zmdi-comment-outline').toggleClass('zmdi-close').toggleClass('is-active').toggleClass('is-visible');
                $(this).toggleClass('is-float');
                $element.find('.chat').toggleClass('is-visible');
                $element.find('.fab').toggleClass('is-visible');
                $element.parent().toggleClass('is-active');
            } else {
                $this.showShortcuts();
            }
        });
        $element.find('#prime').off().on("click", function() {
           $element.find('.prime').toggleClass('zmdi-comment-outline').toggleClass('zmdi-close').toggleClass('is-active').toggleClass('is-visible');
           $(this).toggleClass('is-float');
           $element.find('.chat').toggleClass('is-visible');
           $element.find('.fab').toggleClass('is-visible');
           $element.parent().toggleClass('is-active');
        });

        $element.find('#search-type').off().on("change", function(e){
            if ($(this).val()) {
                $this.submitMessage($(this).val());
                $(this).val('');
            }
        });
        $element.find('.tags li').off().on("click", function(e){
            if (typeof e.target.href == 'undefined' && !$(this).find('.extra-info').hasClass("expanded")) {
                $(this).find('.extra-info').toggleClass('expanded');
            }
        });
        $($options.autoSuggestClass).off().on("click", function(e){
            var msg = '<span class="chat-msg-item chat-msg-item_user">'+ $(this).text() +'</span>';
            $(".chat-converse").append(msg);
            $element.find(".chat-converse").scrollTop($(".chat-converse")[0].scrollHeight);
            $this.submitMessage($(this).text());
        });

        $($options.sendButtonClass).off().on("click", function(e){
            e.preventDefault();
            var value = $element.find($options.inputMessageId).val();
            if (value == '') {
                return;
            }
            var msg = '<span class="chat-msg-item chat-msg-item_user">'+ value +'</span>';
            $(".chat-converse").append(msg);
            $element.find(".chat-converse").scrollTop($(".chat-converse")[0].scrollHeight);
            $element.find($options.inputMessageId).val('');
            $this.submitMessage(value);
        });
    },

    submitMessage: function(msg) {
        if (msg == '') {
            return;
        }
        var $this = this;
        $.ajax({
            type: 'POST',
            data: {form_key: window.FORM_KEY, message: msg},
            url: this.options.url
        })
        .done(function(result) {
            $this.replyTemplate(result);
        })
        .fail(function(result){
        })
        .always(function(_){
        });
    },

    replyTemplate: function(data) {
        var $element = this.element;
        var replay = '<span class="chat-msg-item chat-msg-item-admin"><div class="chat-avatar"><img src="'+this.options.botImage+'"/></div>';
        if (data.title !="" && data.url != '') {
            replay = replay + '<div class="reply-message"><a href="'+data.url+'">'+ data.title +'</a></div>';
        } else if (data.title !="") {
            replay = replay + '<div>'+ data.title +'</div>';
        }
        replay = replay + '</span>';

        $element.find(".chat-converse").append(replay);
        if (data.options.length) {
            var replay = '<span class="chat-msg-item chat-msg-item-tags"><ul class="tags">';
            $.each(data.options, function (i, option) {
                if (option.title != "" && option.url == '') {
                  replay = replay + '<li class="auto-suggest">';
                  replay = replay + option.title;

                  if (option.extraInfo != '' && option.extraInfo instanceof Object) {
                    replay = replay + '<div class="extra-info">';
                   // replay = replay + '<a href="javascript:void(0)" class="view-more">View more</a>';
                    $.each(option.extraInfo, function (j, value) {
                        replay = replay + '<div>'+key+': '+value+'</div>';
                    });
                    replay = replay + '</div>';
                  }

                  replay = replay + '</li>';
                }

                if (option.title !="" && option.url != '') {
                    replay = replay + '<li>';
                    replay = replay + '<a href="'+option.url+'">'+option.title+'</a>';
                    if (typeof option.subtitle !== undefined && option.subtitle) {
                        replay = replay + '<div class="subtitle">'+option.subtitle+'</div>';
                    }
                    if (option.extraInfo != '' && option.extraInfo instanceof Object) {
                        replay = replay + '<div class="extra-info">';
                        //replay = replay + '<a href="javascript:void(0)" class="view-more">View more</a>';
                        $.each(option.extraInfo, function (key, value) {
                            replay = replay + '<div>'+key+': '+value+'</div>';
                        });
                        replay = replay + '</div>';
                    }

                    replay = replay + '</li>';
                }
            });

            replay = replay + '</ul></span>';
            $element.find(".chat-converse").append(replay);
        }

        if (data.placeholder) {
            $element.find(this.options.inputMessageId).val(data.placeholder);
            $element.find(this.options.inputMessageId).focus();
        }

        $element.find(".chat-converse").scrollTop($(".chat-converse")[0].scrollHeight);
        this._initiateChatBot();
    },

    showShortcuts: function()
    {
      $.ajax({
          type: 'GET',
          url: this.options.shortcutUrl,
          showLoader: true
      })
      .done(function(result) {
          var options = {
              type: 'popup',
              title: $.mage.__('Here is the list of command for quick search in Chatbot'),
              responsive: true,
              innerScroll: false,
              buttons: []
          };
          var popup = modal(options, $('#chatbot-shortcut-modal'));
          var finalResult = "<ul>";
          $.each(result, function (i, value) {
              finalResult+="<li>"+value+"</li>";
          })
          finalResult+="</ul>";
          $('#chatbot-shortcut-modal-content').html(finalResult);
          $('#chatbot-shortcut-modal').modal('openModal');
      })
      .fail(function(result){
      })
      .always(function(_){
      });
    }
  });

  return $.mage.boties;

});
