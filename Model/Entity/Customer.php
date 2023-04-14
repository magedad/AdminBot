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
    public const NEW_CUSTOMER_QUERY = 'New customer list';
    public const ADD_CUSTOMER_QUERY = 'Add customer';
    public const SEARCH_CUSTOMER_QUERY = 'Search customers';

    public const AUTO_REPLY_WORDS = [
        self::CUSTOMER_QUERY,
        self::ADD_CUSTOMER_QUERY,
        self::SEARCH_CUSTOMER_QUERY,
        'customers' // additional serch word
    ];

    public const NO_AUTO_REPLY_QUERY = [
        self::NEW_CUSTOMER_QUERY,
        'New customer',
        'New customers',
        'Customer list',
        'Customers list'
    ];

    /**
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
        $customerAllQuery = array_map('strtolower', self::AUTO_REPLY_WORDS);
        return in_array(strtolower($query), $customerAllQuery) || in_array($query, $customerAllQuery);
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
        $customerNoReply = array_map('strtolower', self::NO_AUTO_REPLY_QUERY);

        if (in_array($query, $customerNoReply)) {
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
                $this->returnData(__(self::NEW_CUSTOMER_QUERY)),
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

    /**
     * Shortcut List
     *
     * @return array
     */
    public function getShortcutList(): array
    {
        return [
            __('Customer {name/email/customerId}'),
            __('Customer list'),
        ];
    }
}
