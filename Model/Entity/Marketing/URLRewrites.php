<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model\Entity\Marketing;

use MageDad\AdminBot\Model\Entity\Entity;

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
        $emailTemplateAllQuery = array_map('strtolower', self::SEARCH_WORDS);
        return in_array(strtolower($query), $emailTemplateAllQuery) || in_array($query, $emailTemplateAllQuery);
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

    public function emailTemplate($query)
    {
        return $this->returnData(
            __('Please select relevant option.'),
            [
                $this->addCustomer($query),
                $this->returnData(__(self::SEARCH_URLREWRITES_QUERY)),
            ]
        );
    }

    public function addCustomer($query)
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

    public function searchEmailTemplate($query)
    {
       return $this->returnData(
            __('URL Rewrites {Request Path/Target Path/ Product,Category,Page  Id}'),
            [],
            '',
            __('URL Rewrites')." "
       );
    }
}