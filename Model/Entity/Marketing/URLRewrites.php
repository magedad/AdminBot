<?php
/**
 * @author MageDad Team
 * @copyright Copyright (c) 2023 Magedad (https://www.magedad.com)
 * @package Magento 2 Admin ChatBot
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model\Entity\Marketing;

use MageDad\AdminBot\Model\Entity\Entity;

/*
 * phpcs:disable Magento2.Translation.ConstantUsage
 */
class URLRewrites extends Entity
{
    public const URLREWRITES_QUERY = 'URL Rewrite';
    public const ADD_URLREWRITES_QUERY = 'Add URL Rewrites';
    public const SEARCH_URLREWRITES_QUERY = 'Search URL Rewrites';
    public const SEARCH_WORDS = [
        self::URLREWRITES_QUERY,
        self::SEARCH_URLREWRITES_QUERY,
        'URL Rewrites', // additional serch word
    ];

    /**
     * Constructor
     *
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \MageDad\AdminBot\Model\ReplyFormat $replyFormat
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \MageDad\AdminBot\Model\ReplyFormat $replyFormat
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
        $emailTemplateAllQuery = array_map('strtolower', self::SEARCH_WORDS);
        return in_array(strtolower($query), $emailTemplateAllQuery) || in_array($query, $emailTemplateAllQuery);
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
     * Get Reply
     *
     * @param string $query
     * @return array
     */
    public function getReply(string $query)
    {
        if (!$this->authorization->isAllowed('Magento_UrlRewrite::urlrewrite')) {
            return [];
        }

        if (strtolower($query) == strtolower(self::URLREWRITES_QUERY) || strtolower($query) == 'url rewrites') {
            return $this->emailTemplate($query);
        }

        if (strtolower($query) == strtolower(self::SEARCH_URLREWRITES_QUERY)) {
            return $this->searchEmailTemplate($query);
        }

        return [];
    }

    /**
     * Email Template
     *
     * @param string $query
     * @return array
     */
    public function emailTemplate(string $query)
    {
        return $this->returnData(
            __('Please select relevant option.'),
            [
                $this->addCustomer($query),
                $this->returnData(__(self::SEARCH_URLREWRITES_QUERY)),
            ]
        );
    }

    /**
     * Add Customer
     *
     * @param string $query
     * @return array
     */
    public function addCustomer(string $query)
    {
        $customerUrl = $this->urlBuilder->getUrl(
            'adminhtml/url_rewrite/edit',
            ['_secure' => true]
        );
        return $this->returnData(
            __(self::ADD_URLREWRITES_QUERY),
            [],
            $customerUrl
        );
    }

    /**
     * Search Email Template
     *
     * @param string $query
     * @return array
     */
    public function searchEmailTemplate(string $query)
    {
        return $this->returnData(
            $this->typeCommand(__('URL Rewrites {Request Path/Target Path}')),
            [],
            '',
            __('URL Rewrites') . " "
        );
    }
}
