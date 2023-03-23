<?php

declare(strict_types=1);

namespace MageDad\AdminBot\Model\Entity\Sales;

use MageDad\AdminBot\Model\Entity\Entity;

class MainSales extends Entity
{
    public const SALES_QUERY = 'Sales';
    public const SEARCH_WORDS = [
        self::SALES_QUERY,
        'Sale'
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
        $salesAllQuery = array_map('strtolower', self::SEARCH_WORDS);
        return in_array(strtolower($query), $salesAllQuery);
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
        if (strtolower($query) == strtolower(self::SALES_QUERY) || strtolower($query) == 'sale') {
            return $this->mainOptions($query);
        }

        return [];
    }

    private function mainOptions($query)
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

    private function getOrdersOption()
    {
        if ($this->authorization->isAllowed('Magento_Sales::sales_order')) {
            return $this->returnData(__('Orders'));
        }
    }

    private function getInvoicesOption()
    {
        if ($this->authorization->isAllowed('Magento_Sales::invoice')) {
            return $this->returnData(__('Invoices'));
        }
    }

    private function getShipmentsOption()
    {
        if ($this->authorization->isAllowed('Magento_Sales::shipment')) {
            return $this->returnData(__('Shipments'));
        }
    }

    private function getCreditmemosOption()
    {
        if ($this->authorization->isAllowed('Magento_Sales::creditmemo')) {
            return $this->returnData(__('Creditmemos'));
        }
    }
}