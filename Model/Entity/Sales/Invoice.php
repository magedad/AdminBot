<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model\Entity\Sales;

use MageDad\AdminBot\Model\Entity\Entity;

class Invoice extends Entity
{
    public const INVOICE_QUERY = 'Invoice';
    public const SEARCH_INVOICE_QUERY = 'Search invoice';
    public const TAKE_ACTION = 'action';
    public const SEARCH_WORDS = [
        self::INVOICE_QUERY,
        self::SEARCH_INVOICE_QUERY,
        'invoices' // additional serch word
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
        $invoiceAllQuery = array_map('strtolower', self::SEARCH_WORDS);
        return in_array(strtolower($query), $invoiceAllQuery) || in_array($query, $invoiceAllQuery);
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
        if (!$this->authorization->isAllowed('Magento_Sales::invoice')) {
            return [];
        }

        if (strtolower($query) == strtolower(self::INVOICE_QUERY) || strtolower($query) == 'invoices') {
            return $this->invoice($query);
        }

        if (strtolower($query) == strtolower(self::SEARCH_INVOICE_QUERY)) {
            return $this->searchInvoice($query);
        }

        return [];
    }

    public function invoice($query)
    {
        return $this->returnData(
            __('Please select relevant option.'),
            [
                $this->returnData(__(self::SEARCH_INVOICE_QUERY)),
            ]
        );
    }

    public function searchInvoice($query)
    {
       return $this->returnData(
            __('Invoice {Keyword}'),
            [],
            '',
            __('Invoice')." "
       );
    }
}