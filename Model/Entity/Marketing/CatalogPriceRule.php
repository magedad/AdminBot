<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model\Entity\Marketing;

use MageDad\AdminBot\Model\Entity\Entity;

class CatalogPriceRule extends Entity
{
    public const CATALOGPRICERULE_QUERY = 'Catalog Price Rule';
    public const SEARCH_CATALOGPRICERULE_QUERY = 'Search Catalog Price Rule';
    public const SEARCH_WORDS = [
        self::CATALOGPRICERULE_QUERY,
        self::SEARCH_CATALOGPRICERULE_QUERY,
        'Catalog Price Rules', // additional serch word
        'Catalog Rules', // additional serch word
        'Catalog Rule' // additional serch word
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
        $CatalogPriceRuleAllQuery = array_map('strtolower', self::SEARCH_WORDS);
        return in_array(strtolower($query), $CatalogPriceRuleAllQuery) || in_array($query, $CatalogPriceRuleAllQuery);
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
        if (!$this->authorization->isAllowed('Magento_CatalogRule::promo_catalog')) {
            return [];
        }

        if (strtolower($query) == strtolower(self::CATALOGPRICERULE_QUERY) || strtolower($query) == 'catalog price rules') {
            return $this->catalogPriceRule($query);
        }

        if (strtolower($query) == strtolower(self::SEARCH_CATALOGPRICERULE_QUERY)) {
            return $this->searchCatalogPriceRule($query);
        }

        return [];
    }

    public function catalogPriceRule($query)
    {
        return $this->returnData(
            __('Please select relevant option.'),
            [
                $this->returnData(__(self::SEARCH_CATALOGPRICERULE_QUERY)),
            ]
        );
    }

    public function searchCatalogPriceRule($query)
    {
       return $this->returnData(
            __('Catalog Price Rule {Rule Name / ID}'),
            [],
            '',
            __('Catalog Price Rule')." "
       );
    }
}