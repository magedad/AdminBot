<?php
/**
 * @author MageDad Team
 * @copyright Copyright (c) 2023 Magedad (https://www.magedad.com)
 * @package Magento 2 Admin ChatBot
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model;

use Magento\Backend\App\Action\Context;

class ReplyFormat
{
    /**
     * Return data
     *
     * @param string $title
     * @param array $options
     * @param string $url
     * @param string $placeholder
     * @param string $extraInfo
     * @param string $type
     * @param string $subtitle
     * @return array
     */
    public function returnData(
        $title = '',
        $options = [],
        $url = '',
        $placeholder = '',
        $extraInfo = '',
        $type = '',
        $subtitle = ''
    ) {
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
