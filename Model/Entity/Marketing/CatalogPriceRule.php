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
class CatalogPriceRule extends Entity
{
    public const CATALOGPRICERULE_QUERY = 'Catalog Price Rule';
    public const ADD_CATALOGPRICERULE = 'Add Catalog Price Rule';
    public const SEARCH_CATALOGPRICERULE_QUERY = 'Search Catalog Price Rule';
    public const AUTO_REPLY_WORDS = [
        self::CATALOGPRICERULE_QUERY,
        self::ADD_CATALOGPRICERULE,
        self::SEARCH_CATALOGPRICERULE_QUERY,
        'Catalog Price Rules', // additional serch word
        'Catalog Rules', // additional serch word
        'Catalog Rule' // additional serch word
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
        $CatalogPriceRuleAllQuery = array_map('strtolower', self::AUTO_REPLY_WORDS);
        return in_array(strtolower($query), $CatalogPriceRuleAllQuery) || in_array($query, $CatalogPriceRuleAllQuery);
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
     * Get reply
     *
     * @param string $query
     * @return array
     */
    public function getReply(string $query)
    {
        if (!$this->authorization->isAllowed('Magento_CatalogRule::promo_catalog')) {
            return [];
        }

        if (strtolower($query) == strtolower(self::CATALOGPRICERULE_QUERY)
            || strtolower($query) == 'catalog price rules'
        ) {
            return $this->catalogPriceRule($query);
        }

        if (strtolower($query) == strtolower(self::SEARCH_CATALOGPRICERULE_QUERY)) {
            return $this->searchCatalogPriceRule($query);
        }

        return [];
    }

    /**
     * Catalog Price Rule
     *
     * @param string $query
     * @return array
     */
    public function catalogPriceRule(string $query)
    {
        return $this->returnData(
            __('Please select relevant option.'),
            [
                $this->addCatalogPriceRule(),
                $this->returnData(__(self::SEARCH_CATALOGPRICERULE_QUERY)),
            ]
        );
    }

    /**
     * Add Email Template
     *
     * @return array
     */
    private function addCatalogPriceRule()
    {
        $catalogPriceRuleUrl = $this->urlBuilder->getUrl(
            'catalog_rule/promo_catalog/new',
            ['_secure' => true]
        );
        return $this->returnData(
            __(self::ADD_CATALOGPRICERULE),
            [],
            $catalogPriceRuleUrl
        );
    }

    /**
     * Search Catalog Price Rule
     *
     * @param string $query
     * @return array
     */
    private function searchCatalogPriceRule(string $query)
    {
        return $this->returnData(
            $this->typeCommand(__('Catalog price rule {Rule Name/ID}')),
            [],
            '',
            __('Catalog price rule') . " "
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
            __('Catalog price rule {Rule Name/ID}')
        ];
    }
}
