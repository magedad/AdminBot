<?php
/**
 * @author MageDad Team
 * @copyright Copyright (c) 2023 Magedad (https://www.magedad.com)
 * @package Magento 2 Admin ChatBot
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model\Entity;

class MainCatalog extends Entity
{
    public const CATALOG_QUERY = 'Catalog';
    public const AUTO_REPLY_WORDS = [
        self::CATALOG_QUERY
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
    public function autoReplyQueryCheck(string $query)
    {
        $salesAllQuery = array_map('strtolower', self::AUTO_REPLY_WORDS);
        return in_array(strtolower($query), $salesAllQuery);
    }

    /**
     * Check is my query with keywords
     *
     * @param string $query
     * @return bool
     */
    public function checkIsMyQueryWithKeyword(string $query)
    {
        return $this->checkQueryWithKeyword(self::AUTO_REPLY_WORDS, $query);
    }

    /**
     * Clean query
     *
     * @param string $query
     * @return mixed|string
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
        if (!$this->authorization->isAllowed('Magento_Catalog::catalog')) {
            return [];
        }

        if (strtolower($query) == strtolower(self::CATALOG_QUERY) || strtolower($query) == 'sale') {
            return $this->mainOption($query);
        }

        return [];
    }

    /**
     * Main option
     *
     * @param string $query
     * @return array
     */
    private function mainOption(string $query)
    {
        return $this->returnData(
            __('Please select relevant option.'),
            [
                $this->getProductOption(),
                $this->getCategoryOption()
            ]
        );
    }

    /**
     * Get product option
     *
     * @return array|void
     */
    private function getProductOption()
    {
        if ($this->authorization->isAllowed('Magento_Catalog::products')) {
            return $this->returnData(__('Products'));
        }
    }

    /**
     * Get category option
     *
     * @return array|void
     */
    private function getCategoryOption()
    {
        if ($this->authorization->isAllowed('Magento_Catalog::categories')) {
            return $this->returnData(__('Categories'));
        }
    }

    /**
     * Shortcut List
     *
     * @return array
     */
    public function getShortcutList(): array
    {
        return [
        ];
    }
}
