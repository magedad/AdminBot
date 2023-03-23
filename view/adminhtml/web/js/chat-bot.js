define([
  'jquery',
  'jquery/ui'
], function ($) {
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
      this._initiateLibrary();
    },

    _initiateLibrary: function() {
        var $this = this;
        var $element = this.element;
        var $options = this.options;
        $element.find($options.chatHeaderClass).off().on("click", function() {
            $element.find('.prime').toggleClass('zmdi-comment-outline').toggleClass('zmdi-close').toggleClass('is-active').toggleClass('is-visible');
              $(this).toggleClass('is-float');
              $element.find('.chat').toggleClass('is-visible');
              $element.find('.fab').toggleClass('is-visible');
        });
        $element.find('#prime').off().on("click", function() {
          $element.find('.prime').toggleClass('zmdi-comment-outline').toggleClass('zmdi-close').toggleClass('is-active').toggleClass('is-visible');
          $(this).toggleClass('is-float');
          $element.find('.chat').toggleClass('is-visible');
          $element.find('.fab').toggleClass('is-visible');
        });

        $element.find('#search-type').off().on("change", function(e){
            if ($(this).val()) {
                $this.submitMessage($(this).val());
                $(this).val('');
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
            console.log('data.options');
            console.log(data.options);
            var replay = '<span class="chat-msg-item chat-msg-item-tags"><ul class="tags">';
            $.each(data.options, function (i, option) {
                if (option.title != "" && option.url == '') {
                  replay = replay + '<li class="auto-suggest">'+option.title+'</li>';
                }

                if (option.title !="" && option.url != '') {
                    replay = replay + '<li>';
                    replay = replay + '<a href="'+option.url+'">'+option.title+'</a>';
                    if (typeof option.subtitle !== undefined && option.subtitle) {
                        replay = replay + '<div class="subtitle">'+option.subtitle+'</div>';
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
        this._initiateLibrary();
    }

  });

  return $.mage.boties;

});