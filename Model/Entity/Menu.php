<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model\Entity;

class Menu extends Entity
{
    public const MENU_QUERY = 'Menu';
    public const SEARCH_MENU_QUERY = 'Search menu';
    public const TAKE_ACTION = 'action';
    public const SEARCH_WORDS = [
        self::MENU_QUERY,
        self::SEARCH_MENU_QUERY,
        'menus' // additional serch word
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
        if (strtolower($query) == strtolower(self::MENU_QUERY) || strtolower($query) == 'menus') {
            return $this->menu($query);
        }

        if (strtolower($query) == strtolower(self::SEARCH_MENU_QUERY)) {
            return $this->searchMenu($query);
        }

        return [];
    }

    public function menu($query)
    {
        return $this->returnData(
            __('Please select relevant option.'),
            [
                $this->returnData(__(self::SEARCH_MENU_QUERY)),
            ]
        );
    }

    public function searchMenu($query)
    {
       return $this->returnData(
            __('Menu {Keyword}'),
            [],
            '',
            __('Menu')." "
       );
    }
}