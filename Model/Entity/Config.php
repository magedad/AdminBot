<?php
/**
 * @author MageDad Team
 * @copyright Copyright (c) 2023 Magedad (https://www.magedad.com)
 * @package Magento 2 Admin ChatBot
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model\Entity;

use MageDad\AdminBot\Model\ReplyFormat;
use Magento\Catalog\Model\Product\TypeFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\UrlInterface;

/*
 * phpcs:disable Magento2.Translation.ConstantUsage
 */
class Config extends Entity
{
    public const CONFIG_QUERY = 'Config';
    public const SEARCH_CONFIG_QUERY = 'Search config';

    public const SEARCH_WORDS = [
        self::CONFIG_QUERY,
        self::SEARCH_CONFIG_QUERY,
        'configs' // additional serch word
    ];

    /**
     * Construct
     *
     * @param TypeFactory $typeFactory
     * @param ProductFactory $productFactory
     * @param UrlInterface $urlBuilder
     * @param ReplyFormat $replyFormat
     */
    public function __construct(
        TypeFactory $typeFactory,
        ProductFactory $productFactory,
        UrlInterface $urlBuilder,
        ReplyFormat $replyFormat
    ) {
        $this->productFactory = $productFactory;
        $this->typeFactory = $typeFactory;
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
        $productAllQuery = array_map('strtolower', self::SEARCH_WORDS);
        return in_array(strtolower($query), $productAllQuery) || in_array($query, $productAllQuery);
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
        if (strtolower($query) == strtolower(self::CONFIG_QUERY) || strtolower($query) == 'configs') {
            return $this->mainOption($query);
        }

        if (strtolower($query) == strtolower(self::SEARCH_CONFIG_QUERY)) {
            return $this->searchConfig($query);
        }

        return [];
    }

    /**
     * Main option
     *
     * @param string $query
     * @return array
     */
    public function mainOption(string $query): array
    {
        return $this->returnData(
            __('Please select relevant option.'),
            [
                $this->returnData(__(self::SEARCH_CONFIG_QUERY)),
            ]
        );
    }

    /**
     * Search config
     *
     * @param string $query
     * @return array
     */
    public function searchConfig(string $query): array
    {
        return $this->returnData(
            $this->typeCommand(__('Config {Keyword}')),
            [],
            '',
            __('Config') . " "
        );
    }
}
