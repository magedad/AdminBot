<?php
/**
 * @author MageDad Team
 * @copyright Copyright (c) 2023 Magedad (https://www.magedad.com)
 * @package Magento 2 Admin ChatBot
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model\Entity\Marketing;

use MageDad\AdminBot\Model\Entity\Entity;
use MageDad\AdminBot\Model\ReplyFormat;
use Magento\Framework\UrlInterface;

/*
 * phpcs:disable Magento2.Translation.ConstantUsage
 */
class CartPriceRule extends Entity
{
    public const CARTPRICERULE_QUERY = 'Cart Price Rule';
    public const ADD_CARTPRICERULE_QUERY = 'Add Cart Price Rule';
    public const SEARCH_CARTPRICERULE_QUERY = 'Search Cart Price Rule';
    public const AUTO_REPLY_WORDS = [
        self::CARTPRICERULE_QUERY,
        self::ADD_CARTPRICERULE_QUERY,
        self::SEARCH_CARTPRICERULE_QUERY,
        'Cart Price Rules', // additional serch word
        'Cart Rules', // additional serch word
        'Cart Rule' // additional serch word
    ];

    /**
     * Construct
     *
     * @param UrlInterface $urlBuilder
     * @param ReplyFormat $replyFormat
     */
    public function __construct(
        UrlInterface     $urlBuilder,
        ReplyFormat $replyFormat
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->replyFormat = $replyFormat;
        parent::__construct();
    }

    /**
     * Check Is My Query
     *
     * @param string $query
     * @return bool
     */
    public function autoReplyQueryCheck(string $query)
    {
        $cartPriceRuleAllQuery = array_map('strtolower', self::AUTO_REPLY_WORDS);
        return in_array(strtolower($query), $cartPriceRuleAllQuery) || in_array($query, $cartPriceRuleAllQuery);
    }

    /**
     * Check Is My Query With Keyword
     *
     * @param string $query
     * @return bool
     */
    public function checkIsMyQueryWithKeyword(string $query)
    {
        return $this->checkQueryWithKeyword(self::AUTO_REPLY_WORDS, $query);
    }

    /**
     * Clean Query
     *
     * @param string $query
     * @return string
     */
    public function cleanQuery(string $query)
    {
        return $this->cleanUpQuery(self::AUTO_REPLY_WORDS, $query);
    }

    /**
     * Get reply
     *
     * @param string $query
     * @return array
     */
    public function getReply(string $query)
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

    /**
     * Cart Price Rule
     *
     * @param string $query
     * @return array
     */
    public function cartPriceRule(string $query)
    {
        return $this->returnData(
            __('Please select relevant option.'),
            [
                $this->addCartPriceRule(),
                $this->returnData(__(self::SEARCH_CARTPRICERULE_QUERY)),
            ]
        );
    }

    /**
     * Add Cart Price Rule
     *
     * @return array
     */
    public function addCartPriceRule()
    {
        $cartPriceRuleUrl = $this->urlBuilder->getUrl(
            'sales_rule/promo_quote/new',
            ['_secure' => true]
        );
        return $this->returnData(
            __(self::ADD_CARTPRICERULE_QUERY),
            [],
            $cartPriceRuleUrl
        );
    }

    /**
     * Search Cart Price Rule
     *
     * @param string $query
     * @return array
     */
    public function searchCartPriceRule(string $query)
    {
        return $this->returnData(
            $this->typeCommand(__('Cart price rule {Coupon code/Rule Name/ID}')),
            [],
            '',
            __('Cart price rule') . " "
        );
    }

    /**
     * Shortcut List
     *
     * @return array
     */
    public function getShortcutList(): array
    {
        return [
            __('Cart price rule {Coupon code/Rule Name/ID}')
        ];
    }
}
