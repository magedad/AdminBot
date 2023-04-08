<?php
/**
 * @author MageDad Team
 * @copyright Copyright (c) 2023 Magedad (https://www.magedad.com)
 * @package Magento 2 Admin ChatBot
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model\Entity\Content;

use MageDad\AdminBot\Model\Entity\Entity;
use MageDad\AdminBot\Model\ReplyFormat;
use Magento\Framework\UrlInterface;

/*
 * phpcs:disable Magento2.Translation.ConstantUsage
 */
class CmsBlock extends Entity
{
    public const CMSBLOCK_QUERY = 'CMS Block';
    public const ADD_CMSBLOCK_QUERY = 'Add CMS Block';
    public const SEARCH_CMSBLOCK_QUERY = 'Search/Edit/View Blocks';

    public const AUTO_REPLY_WORDS = [
        self::CMSBLOCK_QUERY,
        self::ADD_CMSBLOCK_QUERY,
        self::SEARCH_CMSBLOCK_QUERY,
    ];

    public const ADDITIONAL_AUTO_REPLY_WORDS = [
        'cms blocks',
        'blocks',
        'block',
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
    public function autoReplyQueryCheck(string $query)
    {
        $cmsblockAllQuery = array_map('strtolower', self::AUTO_REPLY_WORDS);
        return in_array(strtolower($query), $cmsblockAllQuery)
            || in_array(strtolower($query), self::ADDITIONAL_AUTO_REPLY_WORDS);
    }

    /**
     * Check Is My Query With Keyword
     *
     * @param string $query
     * @return bool
     */
    public function checkIsMyQueryWithKeyword(string $query)
    {
        return $this->checkQueryWithKeyword($this->getSearchWord(), $query);
    }

    /**
     * Get Search Word
     *
     * @return string[]
     */
    public function getSearchWord()
    {
        return array_merge(self::AUTO_REPLY_WORDS, self::ADDITIONAL_AUTO_REPLY_WORDS);
    }

    /**
     * Clean Query
     *
     * @param string $query
     * @return string
     */
    public function cleanQuery(string $query)
    {
        return $this->cleanUpQuery($this->getSearchWord(), $query);
    }

    /**
     * Get reply
     *
     * @param string $query
     * @return array
     */
    public function getReply(string $query)
    {
        if (!$this->authorization->isAllowed('Magento_Cms::block')) {
            return [];
        }

        if (strtolower($query) == strtolower(self::CMSBLOCK_QUERY)
            || in_array(strtolower($query), self::ADDITIONAL_AUTO_REPLY_WORDS)
        ) {
            return $this->mainOption($query);
        }

        if (strtolower($query) == strtolower(self::SEARCH_CMSBLOCK_QUERY)) {
            return $this->searchCmsBlock($query);
        }

        return [];
    }

    /**
     * Main Option
     *
     * @param string $query
     * @return array
     */
    public function mainOption(string $query): array
    {
        return $this->returnData(
            __('Please select relevant option.'),
            [
                $this->addCmsBlock(),
                $this->returnData(__(self::SEARCH_CMSBLOCK_QUERY)),
            ]
        );
    }

    /**
     * Add Cms Block
     *
     * @return array
     */
    public function addCmsBlock()
    {
        return $this->returnData(
            __('Add new cms block'),
            [],
            $this->getCmsBlockCreateUrl()
        );
    }

    /**
     * Get Cms Block Create Url
     *
     * @return mixed
     */
    protected function getCmsBlockCreateUrl()
    {
        return $this->urlBuilder->getUrl(
            '*/cms_block/new',
            ['_secure' => true]
        );
    }

    /**
     * Search Cms Block
     *
     * @param string $query
     * @return array
     */
    public function searchCmsBlock(string $query)
    {
        return $this->returnData(
            $this->typeCommand(__('Cms block {id/name/url_key/keword}')),
            [],
            '',
            __('Cms block') . " "
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
            __('Cms block {id/name/url_key/keword}')
        ];
    }
}
