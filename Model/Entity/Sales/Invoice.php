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
class Invoice extends Entity
{
    public const INVOICE_QUERY = 'Invoice';
    public const AUTO_REPLY_WORDS = [
        self::INVOICE_QUERY,
        'invoices' // additional serch word
    ];

    /**
     * Constructor
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
        $invoiceAllQuery = array_map('strtolower', self::AUTO_REPLY_WORDS);
        return in_array(strtolower($query), $invoiceAllQuery) || in_array($query, $invoiceAllQuery);
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
        if (!$this->authorization->isAllowed('Magento_Sales::invoice')) {
            return [];
        }

        if (strtolower($query) == strtolower(self::INVOICE_QUERY) || strtolower($query) == 'invoices') {
            return $this->mainOption($query);
        }

        return [];
    }

    /**
     * Search invoice
     *
     * @param string $query
     * @return array
     */
    public function mainOption(string $query)
    {
        return $this->returnData(
            $this->typeCommand(__('Invoice {Incrment Id/Customer email/Customer Name}')),
            [],
            '',
            __('Invoice') . " "
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
            __('Invoice {Incrment Id/Customer email/Customer Name}')
        ];
    }
}
