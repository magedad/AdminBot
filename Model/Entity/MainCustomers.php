<?php
/**
 * @author MageDad Team
 * @copyright Copyright (c) 2023 Magedad (https://www.magedad.com)
 * @package Magento 2 Admin ChatBot
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model\Entity;

use MageDad\AdminBot\Model\ReplyFormat;
use Magento\Framework\UrlInterface;

class MainCustomers extends Entity
{
    public const CUSTOMERS_QUERY = 'Customers';
    public const SEARCH_WORDS = [
        self::CUSTOMERS_QUERY
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
    public function checkIsMyQuery(string $query)
    {
        $salesAllQuery = array_map('strtolower', self::SEARCH_WORDS);
        return in_array(strtolower($query), $salesAllQuery);
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
        if (strtolower($query) == strtolower(self::CUSTOMERS_QUERY)) {
            return $this->mainOption($query);
        }

        return [];
    }

    /**
     * Main Option
     *
     * @param string $query
     * @return array
     */
    private function mainOption(string $query)
    {
        return $this->returnData(
            __('Please select relevant option.'),
            [
                $this->getCustomerOption(),
                $this->getCustomerGroupOption()
            ]
        );
    }

    /**
     * Get Customer Option
     *
     * @return array|void
     */
    private function getCustomerOption()
    {
        if ($this->authorization->isAllowed('Magento_Customer::customer')) {
            return $this->returnData(__('Customer'));
        }
    }

    /**
     * Get Customer Group Option
     *
     * @return array|void
     */
    private function getCustomerGroupOption()
    {
        if ($this->authorization->isAllowed('Magento_Customer::group')) {
            return $this->returnData(__('Customer group'));
        }
    }
}
