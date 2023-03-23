<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model\Entity\Marketing;

use MageDad\AdminBot\Model\Entity\Entity;

class CartPriceRule extends Entity
{
    public const CARTPRICERULE_QUERY = 'Cart Price Rule';
    public const SEARCH_CARTPRICERULE_QUERY = 'Search Cart Price Rule';
    public const SEARCH_WORDS = [
        self::CARTPRICERULE_QUERY,
        self::SEARCH_CARTPRICERULE_QUERY,
        'Cart Price Rules', // additional serch word
        'Cart Rules', // additional serch word
        'Cart Rule' // additional serch word
    ];

    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \MageDad\AdminBot\Model\ReplyFormat $replyFormat
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->replyFormat = $replyFormat;
        parent::__construct();
    }

    public function checkIsMyQuery($query)
    {
        $cartPriceRuleAllQuery = array_map('strtolower', self::SEARCH_WORDS);
        return in_array(strtolower($query), $cartPriceRuleAllQuery) || in_array($query, $cartPriceRuleAllQuery);
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
        if (!$this->authorization->isAllowed('Magento_SalesRule::quote')) {
            return [];
        }

        if (strtolower($query) == strtolower(self::CARTPRICERULE_QUERY) || strtolower($query) == 'cart price rules') {
            return $this->cartPriceRule($query);
        }

        if (strtolower($query) == strtolower(self::SEARCH_CARTPRICERULE_QUERY)) {
            return $this->searchCartPriceRule($query);
        }

        return [];
    }

    public function cartPriceRule($query)
    {
        return $this->returnData(
            __('Please select relevant option.'),
            [
                $this->returnData(__(self::SEARCH_CARTPRICERULE_QUERY)),
            ]
        );
    }

    public function searchCartPriceRule($query)
    {
       return $this->returnData(
            __('Cart Price Rule { Coupon code / Rule Name / ID }'),
            [],
            '',
            __('Cart Price Rule')." "
       );
    }
}