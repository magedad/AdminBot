<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model\Entity\Sales;

use MageDad\AdminBot\Model\Entity\Entity;

class Shipment extends Entity
{
    public const SHIPMENT_QUERY = 'Shipment';
    public const SEARCH_SHIPMENT_QUERY = 'Search shipment';
    public const TAKE_ACTION = 'action';
    public const SEARCH_WORDS = [
        self::SHIPMENT_QUERY,
        self::SEARCH_SHIPMENT_QUERY,
        'shipments' // additional serch word
    ];

    public function __construct(
        \Magento\Catalog\Model\Product\TypeFactory $typeFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        \MageDad\AdminBot\Model\ReplyFormat $replyFormat
    ) {
        $this->productFactory = $productFactory;
        $this->typeFactory = $typeFactory;
        $this->urlBuilder = $urlBuilder;
        $this->replyFormat = $replyFormat;
        parent::__construct();
    }

    public function checkIsMyQuery($query)
    {
        $productAllQuery = array_map('strtolower', self::SEARCH_WORDS);
        return in_array(strtolower($query), $productAllQuery) || in_array($query, $productAllQuery);
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
        if (!$this->authorization->isAllowed('Magento_Sales::shipment')) {
            return [];
        }

        if (strtolower($query) == strtolower(self::SHIPMENT_QUERY) || strtolower($query) == 'shipments') {
            return $this->shipment($query);
        }

        if (strtolower($query) == strtolower(self::SEARCH_SHIPMENT_QUERY)) {
            return $this->searchShipment($query);
        }

        return [];
    }

    public function shipment($query)
    {
        return $this->returnData(
            __('Please select relevant option.'),
            [
                $this->returnData(__(self::SEARCH_SHIPMENT_QUERY)),
            ]
        );
    }

    public function searchShipment($query)
    {
       return $this->returnData(
            __('Shipment {Keyword}'),
            [],
            '',
            __('Shipment')." "
       );
    }
}