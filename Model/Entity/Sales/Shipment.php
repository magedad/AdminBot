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
class Shipment extends Entity
{
    public const SHIPMENT_QUERY = 'Shipment';
    public const AUTO_REPLY_WORDS = [
        self::SHIPMENT_QUERY,
        'shipments' // additional serch word
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
        if (!$this->authorization->isAllowed('Magento_Sales::shipment')) {
            return [];
        }

        if (strtolower($query) == strtolower(self::SHIPMENT_QUERY) || strtolower($query) == 'shipments') {
            return $this->mainOption($query);
        }

        if (strtolower($query) == strtolower(self::SEARCH_SHIPMENT_QUERY)) {
            return $this->searchShipment($query);
        }

        return [];
    }

    /**
     * Search shipment
     *
     * @param string $query
     * @return array
     */
    public function mainOption(string $query)
    {
        return $this->returnData(
            $this->typeCommand(__('Shipment {Incrment Id/Customer email/Customer Name}')),
            [],
            '',
            __('Shipment') . " "
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
            __('Shipment {Incrment Id/Customer email/Customer Name}')
        ];
    }
}
