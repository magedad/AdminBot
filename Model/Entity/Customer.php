<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model\Entity;

class Customer extends Entity
{
    public const CUSTOMER_QUERY = 'Customer';
    public const ADD_CUSTOMER_QUERY = 'Add customer';
    public const EDIT_CUSTOMER_QUERY = 'Edit/View customer';
    public const SEARCH_CUSTOMER_QUERY = 'Search customers';
    public const TAKE_ACTION = 'action';
    public const SEARCH_WORDS = [
        self::CUSTOMER_QUERY,
        self::ADD_CUSTOMER_QUERY,
        self::EDIT_CUSTOMER_QUERY,
        self::SEARCH_CUSTOMER_QUERY,
        'customers' // additional serch word
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
        if (strtolower($query) == strtolower(self::CUSTOMER_QUERY) || strtolower($query) == 'customers') {
            return $this->mainOption($query);
        }

        if (strtolower($query) == strtolower(self::EDIT_CUSTOMER_QUERY)) {
            return $this->editCustomer($query);
        }

        if (strtolower($query) == strtolower(self::SEARCH_CUSTOMER_QUERY)) {
            return $this->searchCustomer($query);
        }

        return [];
    }

    private function mainOption($query)
    {
        if (!$this->authorization->isAllowed('Magento_Customer::customer')) {
            return [];
        }

        return $this->returnData(
            __('Please select relevant option.'),
            [
                $this->addCustomer($query),
                $this->returnData(__(self::EDIT_CUSTOMER_QUERY)),
                $this->returnData(__(self::SEARCH_CUSTOMER_QUERY)),
            ]
        );
    }

    private function addCustomer($query)
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

    private function editCustomer($query)
    {
        if (!$this->authorization->isAllowed('Magento_Customer::manage')) {
            return [];
        }

        return $this->returnData(
            __('Customer {name/email/customerId}'),
            [],
            '',
            __('Customer')." "
        );
    }

    private function searchCustomer($query)
    {
        if (!$this->authorization->isAllowed('Magento_Customer::manage')) {
            return [];
        }

        return $this->returnData(
            __('Customer {name/email/customerId}'),
            [],
            '',
            __('Customer')." "
        );
    }

    protected function getCustomerCreateUrl($type)
    {
        return $this->urlBuilder->getUrl(
            'catalog/product/new',
            ['set' => $this->productFactory->create()->getDefaultAttributeSetId(), 'type' => $type, '_secure' => true]
        );
    }
}