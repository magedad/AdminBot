<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model\Entity;

class Config extends Entity
{
    public const CONFIG_QUERY = 'Config';
    public const SEARCH_CONFIG_QUERY = 'Search config';
    public const TAKE_ACTION = 'action';
    public const SEARCH_WORDS = [
        self::CONFIG_QUERY,
        self::SEARCH_CONFIG_QUERY,
        'configs' // additional serch word
    ];

    public function __construct(
        \Magento\Catalog\Model\Product\TypeFactory $typeFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        \MageDad\AdminBot\Model\ReplyFormat $replyFormat
    ) {
        $this->productFactory = $productFactory;
        $this->typeFactory = $typeFactory;
        $this->urlBuilder = $urlBuilder;
        $this->replyFormat = $replyFormat;
    }

    public function checkIsMyQuery($query)
    {
        $productAllQuery = array_map('strtolower', self::SEARCH_WORDS);
        return in_array(strtolower($query), $productAllQuery) || in_array($query, $productAllQuery);
    }

    public function checkIsMyQueryWithKeyword($query)
    {
        return $this->checkQueryWithKeyword(self::SEARCH_WORDS, $query);
    }

    public function cleanQuery($query)
    {
        return $this->cleanUpQuery(self::SEARCH_WORDS, $query);
    }

    public function getReply($query)
    {
        if (strtolower($query) == strtolower(self::CONFIG_QUERY) || strtolower($query) == 'configs') {
            return $this->config($query);
        }

        if (strtolower($query) == strtolower(self::SEARCH_CONFIG_QUERY)) {
            return $this->searchConfig($query);
        }

        return [];
    }

    public function config($query)
    {
        return $this->returnData(
            __('Please select relevant option.'),
            [
                $this->returnData(__(self::SEARCH_CONFIG_QUERY)),
            ]
        );
    }

    public function searchConfig($query)
    {
       return $this->returnData(
            __('Config {Keyword}'),
            [],
            '',
            __('Config')." "
       );
    }
}