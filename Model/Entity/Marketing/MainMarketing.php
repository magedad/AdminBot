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

class MainMarketing extends Entity
{
    public const MARKETING_QUERY = 'Marketing';
    public const AUTO_REPLY_WORDS = [
        self::MARKETING_QUERY
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
    public function autoReplyQueryCheck(string $query)
    {
        $salesAllQuery = array_map('strtolower', self::AUTO_REPLY_WORDS);
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
        return $this->checkQueryWithKeyword(self::AUTO_REPLY_WORDS, $query);
    }

    /**
     * Clean Query
     *
     * @param string $query
     * @return string
     */
    public function cleanQuery(string $query)
    {
        return $this->cleanUpQuery(self::AUTO_REPLY_WORDS, $query);
    }

    /**
     * Get Reply
     *
     * @param string $query
     * @return array
     */
    public function getReply(string $query)
    {
        if (strtolower($query) == strtolower(self::MARKETING_QUERY) || strtolower($query) == 'sale') {
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
                $this->getCatalogPriceRuleOption(),
                $this->getCartPriceRulesOption(),
                $this->getEmailTemplatesOption(),
                $this->getURLRewritesOption(),
                // $this->returnData(__('Newsletter Templates')),
                // $this->returnData(__('Newsletter Queue')),
                // $this->returnData(__('Search Terms')),
                // $this->returnData(__('Search Synonyms')),
                // $this->returnData(__('Site Map')),
                // $this->returnData(__('Reviews')),
                // $this->returnData(__('Pending Reviews')),
            ]
        );
    }

    /**
     * Get Catalog Price Rule Option
     *
     * @return array|void
     */
    private function getCatalogPriceRuleOption()
    {
        if ($this->authorization->isAllowed('Magento_SalesRule::quote')) {
            return $this->returnData(__('Catalog Price Rule'));
        }
    }

    /**
     * Get Cart Price Rules Option
     *
     * @return array|void
     */
    private function getCartPriceRulesOption()
    {
        if ($this->authorization->isAllowed('Magento_CatalogRule::promo_catalog')) {
            return $this->returnData(__('Cart Price Rules'));
        }
    }

    /**
     * Get Email Templates Option
     *
     * @return array|void
     */
    private function getEmailTemplatesOption()
    {
        if ($this->authorization->isAllowed('Magento_Email::template')) {
            return $this->returnData(__('Email Templates'));
        }
    }

    /**
     * Get URLRewrites Option
     *
     * @return array|void
     */
    private function getURLRewritesOption()
    {
        if ($this->authorization->isAllowed('Magento_UrlRewrite::urlrewrite')) {
            return $this->returnData(__('URL Rewrites'));
        }
    }
}
