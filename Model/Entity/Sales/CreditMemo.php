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
use Magento\Catalog\Model\Product\TypeFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\UrlInterface;

/*
 * phpcs:disable Magento2.Translation.ConstantUsage
 */
class CreditMemo extends Entity
{
    public const SHIPMENT_QUERY = 'Creditmemo';
    public const AUTO_REPLY_WORDS = [
        self::SHIPMENT_QUERY,
        'creditmemos' // additional serch word
    ];

    /**
     * Construct
     *
     * @param TypeFactory $typeFactory
     * @param ProductFactory $productFactory
     * @param UrlInterface $urlBuilder
     * @param ReplyFormat $replyFormat
     */
    public function __construct(
        protected TypeFactory $typeFactory,
        protected ProductFactory $productFactory,
        protected UrlInterface $urlBuilder,
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
        $productAllQuery = array_map('strtolower', self::AUTO_REPLY_WORDS);
        return in_array(strtolower($query), $productAllQuery) || in_array($query, $productAllQuery);
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
        if (!$this->authorization->isAllowed('Magento_Sales::creditmemo')) {
            return [];
        }

        if (strtolower($query) == strtolower(self::SHIPMENT_QUERY) || strtolower($query) == 'creditmemos') {
            return $this->mainOption($query);
        }

        return [];
    }

    /**
     * Search creditmemo
     *
     * @param string $query
     * @return array
     */
    public function mainOption(string $query): array
    {
        return $this->returnData(
            $this->typeCommand(__('Creditmemo {Incrment Id/Customer email/Customer Name}')),
            [],
            '',
            __('Creditmemo') . " "
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
            __('Creditmemo {Incrment Id/Customer email/Customer Name}')
        ];
    }
}
