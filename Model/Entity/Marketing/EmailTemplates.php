<?php
/**
 * @author MageDad Team
 * @copyright Copyright (c) 2023 Magedad (https://www.magedad.com)
 * @package Magento 2 Admin ChatBot
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model\Entity\Marketing;

use MageDad\AdminBot\Model\Entity\Entity;
use MageDad\AdminBot\Model\ReplyFormat;
use Magento\Framework\UrlInterface;

/*
 * phpcs:disable Magento2.Translation.ConstantUsage
 */
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

    /**
     * Construct
     *
     * @param UrlInterface $urlBuilder
     * @param ReplyFormat $replyFormat
     */
    public function __construct(
        UrlInterface $urlBuilder,
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
     * Get reply
     *
     * @param string $query
     * @return array
     */
    public function getReply(string $query)
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
                $this->addEmailTemplate(),
                $this->returnData(__(self::SEARCH_EMAILTEMPLATES_QUERY)),
            ]
        );
    }

    /**
     * Add Email Template
     *
     * @return array
     */
    private function addEmailTemplate()
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

    /**
     * Search Email Template
     *
     * @param string $query
     * @return array
     */
    private function searchEmailTemplate(string $query)
    {
        return $this->returnData(
            $this->typeCommand(__('Email Templates {Name/ID}')),
            [],
            '',
            __('Email Templates') . " "
        );
    }
}
