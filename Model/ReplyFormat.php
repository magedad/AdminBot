<?php

namespace MageDad\AdminBot\Model;

use Magento\Backend\App\Action\Context;

class ReplyFormat
{
    public function returnData($title = '', $options = [], $url = '', $placeholder = '', $extraInfo = '', $type = '', $subtitle = '')
    {
        foreach ($options as $key => $option) {
            if (!isset($option['title'])) {
                unset($options[$key]);
            }
        }

        return [
            'title' => $title,
            'subtitle' => $subtitle,
            'options' => $options ?? [],
            'url' => $url,
            'placeholder' => $placeholder,
            'extraInfo' => $extraInfo,
            'type' => $type,
        ];
    }
}