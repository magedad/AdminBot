<?php
/**
 * @author MageDad Team
 * @copyright Copyright (c) 2023 Magedad (https://www.magedad.com)
 * @package Magento 2 Admin ChatBot
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model\Entity;

use MageDad\AdminBot\Model\ReplyFormat;
use Magento\Catalog\Model\Product\TypeFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\UrlInterface;

/*
 * phpcs:disable Magento2.Translation.ConstantUsage
 */
class Customer extends Entity
{
    public const CUSTOMER_QUERY = 'Customer';
    public const ADD_CUSTOMER_QUERY = 'Add customer';
    public const SEARCH_CUSTOMER_QUERY = 'Search customers';

    public const SEARCH_WORDS = [
        self::CUSTOMER_QUERY,
        self::ADD_CUSTOMER_QUERY,
        self::SEARCH_CUSTOMER_QUERY,
        'customers' // additional serch word
    ];

    /**
     * @param TypeFactory $typeFactory
     * @param ProductFactory $productFactory
     * @param UrlInterface $urlBuilder
     * @param ReplyFormat $replyFormat
     */
    public function __construct(
        TypeFactory $typeFactory,
        ProductFactory $productFactory,
        UrlInterface $urlBuilder,
        ReplyFormat $replyFormat
    ) {
        $this->productFactory = $productFactory;
        $this->typeFactory = $typeFactory;
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
    public function checkIsMyQuery(string $query)
    {
        $productAllQuery = array_map('strtolower', self::SEARCH_WORDS);
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
        return $this->checkQueryWithKeyword(self::SEARCH_WORDS, $query);
    }

    /**
     * Clean Query
     *
     * @param string $query
     * @return string
     */
    public function cleanQuery(string $query)
    {
        return $this->cleanUpQuery(self::SEARCH_WORDS, $query);
    }

    /**
     * Get reply
     *
     * @param string $query
     * @return array
     */
    public function getReply(string $query)
    {

        if (!$this->authorization->isAllowed('Magento_Customer::customer')) {
            return [];
        }

        if (strtolower($query) == strtolower(self::CUSTOMER_QUERY) || strtolower($query) == 'customers') {
            return $this->mainOption($query);
        }

        if (strtolower($query) == strtolower(self::SEARCH_CUSTOMER_QUERY)) {
            return $this->searchCustomer($query);
        }

        return [];
    }

    /**
     * MainOption
     *
     * @param string $query
     * @return array
     */
    private function mainOption(string $query)
    {
        return $this->returnData(
            __('Please select relevant option.'),
            [
                $this->addCustomer($query),
                $this->returnData(__(self::SEARCH_CUSTOMER_QUERY)),
            ]
        );
    }

    /**
     * Add customer
     *
     * @param string $query
     * @return array
     */
    private function addCustomer(string $query)
    {
        if (!$this->authorization->isAllowed('Magento_Customer::manage')) {
            return [];
        }

        $customerUrl = $this->urlBuilder->getUrl(
            'customer/index/new',
            ['_secure' => true]
        );
        return $this->returnData(
            __(self::ADD_CUSTOMER_QUERY),
            [],
            $customerUrl
        );
    }

    /**
     * Search customer
     *
     * @param string $query
     * @return array
     */
    private function searchCustomer(string $query)
    {
        if (!$this->authorization->isAllowed('Magento_Customer::manage')) {
            return [];
        }

        return $this->returnData(
            $this->typeCommand(__('Customer {name/email/customerId}')),
            [],
            '',
            __('Customer') . " "
        );
    }

    /**
     * Get customer create url
     *
     * @param string $type
     * @return string
     */
    protected function getCustomerCreateUrl(string $type): string
    {
        return $this->urlBuilder->getUrl(
            'catalog/product/new',
            ['set' => $this->productFactory->create()->getDefaultAttributeSetId(), 'type' => $type, '_secure' => true]
        );
    }
}
