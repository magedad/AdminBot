<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model\Entity\Sales;

use MageDad\AdminBot\Model\Entity\Entity;

class Order extends Entity
{
    public const ORDER_QUERY = 'Order';
    public const ADD_ORDER_QUERY = 'Add Order';
    public const VIEW_ORDER_QUERY = 'View Order';
    public const SEARCH_ORDER_QUERY = 'Search Orders';
    public const TAKE_ACTION = 'action';
    public const SEARCH_WORDS = [
        self::ORDER_QUERY,
        self::ADD_ORDER_QUERY,
        self::VIEW_ORDER_QUERY,
        self::SEARCH_ORDER_QUERY,
        'orders' // additional serch word
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
        $orderAllQuery = array_map('strtolower', self::SEARCH_WORDS);
        return in_array(strtolower($query), $orderAllQuery) || in_array($query, $orderAllQuery);
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
        if (!$this->authorization->isAllowed('Magento_Sales::sales_order')) {
            return [];
        }

        if (strtolower($query) == strtolower(self::ORDER_QUERY) || strtolower($query) == 'orders') {
            return $this->mainOption($query);
        }

        if (strtolower($query) == strtolower(self::ADD_ORDER_QUERY)) {
            return $this->addOrder();
        }

        if (strtolower($query) == strtolower(self::VIEW_ORDER_QUERY)) {
            return $this->editOrder($query);
        }

        if (strtolower($query) == strtolower(self::SEARCH_ORDER_QUERY)) {
            return $this->searchOrder($query);
        }

        return [];
    }

    public function mainOption($query)
    {
        return $this->returnData(
            __('Please select relevant option.'),
            [
                $this->returnData(__(self::ADD_ORDER_QUERY)),
                $this->returnData(__(self::VIEW_ORDER_QUERY)),
                $this->returnData(__(self::SEARCH_ORDER_QUERY)),
            ]
        );
    }

    public function addOrder()
    {
        return $this->returnData(
            __('Click here for add new order.'),
            [],
            $this->getOrderCreateUrl()
        );
    }

    public function editOrder($query)
    {
       return $this->returnData(
            __('Order {id/sku/name}'),
            [],
            '',
            __('Order')." "
       );
    }

    public function searchOrder($query)
    {
       return $this->returnData(
            __('Orders {Incrment Id/Order Id/Customer email/Customer Name}'),
            [],
            '',
            __('Orders')." "
       );
    }

    protected function getOrderCreateUrl()
    {
        return $this->urlBuilder->getUrl(
            'sales/order_create/start',
            ['_secure' => true]
        );
    }
}