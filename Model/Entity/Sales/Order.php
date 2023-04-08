<?php
/**
 * @author MageDad Team
 * @copyright Copyright (c) 2023 Magedad (https://www.magedad.com)
 * @package Magento 2 Admin ChatBot
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model\Entity\Sales;

use MageDad\AdminBot\Model\Entity\Entity;
use MageDad\AdminBot\Model\ReplyFormat;
use Magento\Framework\UrlInterface;

/*
 * phpcs:disable Magento2.Translation.ConstantUsage
 */
class Order extends Entity
{
    public const ORDER_QUERY = 'Order';
    public const NEW_ORDER_QUERY = 'New Order list';
    public const ADD_ORDER_QUERY = 'Add Order';
    public const SEARCH_ORDER_QUERY = 'Search Orders';

    public const AUTO_REPLY_WORDS = [
        self::ORDER_QUERY,
        self::ADD_ORDER_QUERY,
        self::SEARCH_ORDER_QUERY
    ];

    public const NO_AUTO_REPLY_QUERY = [
        self::NEW_ORDER_QUERY,
        'New Orders',
        'New Order',
        'Order list',
        'Last Orders',
        'orders'
    ];

    /**
     * Constructor
     *
     * @param UrlInterface $urlBuilder
     * @param ReplyFormat $replyFormat
     */
    public function __construct(
        UrlInterface $urlBuilder,
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
        $searchWords = self::AUTO_REPLY_WORDS;
        $orderAllQuery = array_map('strtolower', $searchWords);
        return in_array(strtolower($query), $orderAllQuery) || in_array($query, $orderAllQuery);
    }

    /**
     * Check Is My Query With Keyword
     *
     * @param string $query
     * @return bool
     */
    public function checkIsMyQueryWithKeyword(string $query)
    {
        $searchWords = array_merge(self::AUTO_REPLY_WORDS, self::NO_AUTO_REPLY_QUERY);
        return $this->checkQueryWithKeyword($searchWords, $query);
    }

    /**
     * Clean Query
     *
     * @param string $query
     * @return string
     */
    public function cleanQuery(string $query)
    {
        $orderNoReply = array_map('strtolower', self::NO_AUTO_REPLY_QUERY);

        if (in_array($query, $orderNoReply)) {
            return $query;
        }
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
        if (!$this->authorization->isAllowed('Magento_Sales::sales_order')) {
            return [];
        }

        if (strtolower($query) == strtolower(self::ORDER_QUERY) || strtolower($query) == 'orders') {
            return $this->mainOption($query);
        }

        if (strtolower($query) == strtolower(self::ADD_ORDER_QUERY)) {
            return $this->addOrder();
        }

        if (strtolower($query) == strtolower(self::SEARCH_ORDER_QUERY)) {
            return $this->searchOrder($query);
        }

        return [];
    }

    /**
     * Main Option
     *
     * @param string $query
     * @return array
     */
    public function mainOption(string $query)
    {
        return $this->returnData(
            __('Please select relevant option.'),
            [
                $this->addOrder(),
                $this->returnData(__(self::NEW_ORDER_QUERY)),
                $this->returnData(__(self::SEARCH_ORDER_QUERY)),
            ]
        );
    }

    /**
     * Add Order
     *
     * @return array
     */
    public function addOrder()
    {
        return $this->returnData(
            __(self::ADD_ORDER_QUERY),
            [],
            $this->getOrderCreateUrl()
        );
    }

    /**
     * Get Order Create Url
     *
     * @return mixed
     */
    protected function getOrderCreateUrl()
    {
        return $this->urlBuilder->getUrl(
            'sales/order_create/start',
            ['_secure' => true]
        );
    }

    /**
     * Search order
     *
     * @param string $query
     * @return array
     */
    public function searchOrder(string $query)
    {
        return $this->returnData(
            $this->typeCommand(__('Orders {Incrment Id/Order Id/Customer email/Customer Name}')),
            [],
            '',
            __('Orders') . " "
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
            __('Orders {Incrment Id/Order Id/Customer email/Customer Name}'),
            __('New order')
        ];
    }
}
