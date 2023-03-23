<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model\Entity\Marketing;

use MageDad\AdminBot\Model\Entity\Entity;

class EmailTemplates extends Entity
{
    public const EMAILTEMPLATES_QUERY = 'Email Template';
    public const ADD_EMAILTEMPLATES_QUERY = 'Add Email Templates';
    public const SEARCH_EMAILTEMPLATES_QUERY = 'Search Email Templates';
    public const SEARCH_WORDS = [
        self::EMAILTEMPLATES_QUERY,
        self::SEARCH_EMAILTEMPLATES_QUERY,
        'Email Templates', // additional serch word
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
        if (!$this->authorization->isAllowed('Magento_Email::template')) {
            return [];
        }

        if (strtolower($query) == strtolower(self::EMAILTEMPLATES_QUERY) || strtolower($query) == 'email templates') {
            return $this->emailTemplate($query);
        }

        if (strtolower($query) == strtolower(self::SEARCH_EMAILTEMPLATES_QUERY)) {
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
                $this->returnData(__(self::SEARCH_EMAILTEMPLATES_QUERY)),
            ]
        );
    }

    public function addCustomer($query)
    {
        $customerUrl = $this->urlBuilder->getUrl(
                'adminhtml/email_template/new',
                ['_secure' => true]
            );
        return $this->returnData(
            __(self::ADD_EMAILTEMPLATES_QUERY),
            [],
            $customerUrl
        );
    }

    public function searchEmailTemplate($query)
    {
       return $this->returnData(
            __('Email Templates {Name/ID}'),
            [],
            '',
            __('Email Templates')." "
       );
    }
}