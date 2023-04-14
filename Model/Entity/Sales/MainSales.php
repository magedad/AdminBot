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

class MainSales extends Entity
{
    public const SALES_QUERY = 'Sales';
    public const AUTO_REPLY_WORDS = [
        self::SALES_QUERY,
        'Sale'
    ];

    /**
     * Constructor
     *
     * @param ReplyFormat $replyFormat
     */
    public function __construct(
        protected ReplyFormat $replyFormat
    ) {
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
        $salesAllQuery = array_map('strtolower', self::AUTO_REPLY_WORDS);
        return in_array(strtolower($query), $salesAllQuery);
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
        if (!$this->authorization->isAllowed('Magento_Sales::sales')) {
            return [];
        }

        if (strtolower($query) == strtolower(self::SALES_QUERY) || strtolower($query) == 'sale') {
            return $this->mainOptions($query);
        }

        return [];
    }

    /**
     * Main option
     *
     * @param string $query
     * @return array
     */
    private function mainOptions(string $query)
    {
        return $this->returnData(
            __('Please select relevant option.'),
            [
                $this->getOrdersOption(),
                $this->getInvoicesOption(),
                $this->getShipmentsOption(),
                $this->getCreditmemosOption()
            ]
        );
    }

    /**
     * Get order option
     *
     * @return array|void
     */
    private function getOrdersOption()
    {
        if ($this->authorization->isAllowed('Magento_Sales::sales_order')) {
            return $this->returnData(__('Orders'));
        }
    }

    /**
     * Get invoice option
     *
     * @return array|void
     */
    private function getInvoicesOption()
    {
        if ($this->authorization->isAllowed('Magento_Sales::invoice')) {
            return $this->returnData(__('Invoices'));
        }
    }

    /**
     * Get shipment option
     *
     * @return array|void
     */
    private function getShipmentsOption()
    {
        if ($this->authorization->isAllowed('Magento_Sales::shipment')) {
            return $this->returnData(__('Shipments'));
        }
    }

    /**
     * Get creditmemo option
     *
     * @return array|void
     */
    private function getCreditmemosOption()
    {
        if ($this->authorization->isAllowed('Magento_Sales::creditmemo')) {
            return $this->returnData(__('Creditmemos'));
        }
    }
}
