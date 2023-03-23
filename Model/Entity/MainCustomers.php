<?php

declare(strict_types=1);

namespace MageDad\AdminBot\Model\Entity;

class MainCustomers extends Entity
{
    public const CUSTOMERS_QUERY = 'Customers';
    public const SEARCH_WORDS = [
        self::CUSTOMERS_QUERY
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
        if (strtolower($query) == strtolower(self::CUSTOMERS_QUERY)) {
            return $this->mainOption($query);
        }

        return [];
    }

    private function mainOption($query)
    {
        return $this->returnData(
            __('Please select relevant option.'),
            [
                $this->getCustomerOption(),
                $this->getCustomerGroupOption()
            ]
        );
    }

    private function getCustomerOption()
    {
        if ($this->authorization->isAllowed('Magento_Customer::customer')) {
            return $this->returnData(__('Customer'));
        }
    }

    private function getCustomerGroupOption()
    {
        if ($this->authorization->isAllowed('Magento_Customer::group')) {
            return $this->returnData(__('Customer group'));
        }
    }
}