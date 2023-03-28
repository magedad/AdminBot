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
class CmsPage extends Entity
{
    public const CMSPAGE_QUERY = 'CMS Page';
    public const ADD_CMSPAGE_QUERY = 'Add CMS Page';
    public const SEARCH_CMSPAGE_QUERY = 'Search/Edit/View Pages';

    public const SEARCH_WORDS = [
        self::CMSPAGE_QUERY,
        self::ADD_CMSPAGE_QUERY,
        self::SEARCH_CMSPAGE_QUERY,
    ];

    public const ADDITIONAL_SEARCH_WORDS = [
        'cms pages',
        'pages',
        'page',
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
        $cmspageAllQuery = array_map('strtolower', self::SEARCH_WORDS);
        return in_array(strtolower($query), $cmspageAllQuery)
            || in_array(strtolower($query), self::ADDITIONAL_SEARCH_WORDS);
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
     * Get Search   Word
     *
     * @return string[]
     */
    public function getSearchWord()
    {
        return array_merge(self::SEARCH_WORDS, self::ADDITIONAL_SEARCH_WORDS);
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
        if (!$this->authorization->isAllowed('Magento_Cms::page')) {
            return [];
        }

        if (strtolower($query) == strtolower(self::CMSPAGE_QUERY)
            || in_array(strtolower($query), self::ADDITIONAL_SEARCH_WORDS)
        ) {
            return $this->cmsPage($query);
        }

        if (strtolower($query) == strtolower(self::SEARCH_CMSPAGE_QUERY)) {
            return $this->searchCmsPage($query);
        }

        return [];
    }

    /**
     * Cms Page
     *
     * @param string $query
     * @return array
     */
    public function cmsPage(string $query)
    {
        return $this->returnData(
            __('Please select relevant option.'),
            [
                $this->addCmsPage(),
                $this->returnData(__(self::SEARCH_CMSPAGE_QUERY)),
            ]
        );
    }

    /**
     * Add cms page
     *
     * @return array
     */
    public function addCmsPage()
    {
        return $this->returnData(
            __('Add new cms page'),
            [],
            $this->getCmsPageCreateUrl()
        );
    }

    /**
     * Get cms page create url
     *
     * @return mixed
     */
    protected function getCmsPageCreateUrl()
    {
        return $this->urlBuilder->getUrl(
            '*/cms_page/new',
            ['_secure' => true]
        );
    }

    /**
     * Search cms page
     *
     * @param string $query
     * @return array
     */
    public function searchCmsPage(string $query)
    {
        return $this->returnData(
            $this->typeCommand(__('Cms Page {id/name/url_key/keword}')),
            [],
            '',
            __('Cms Pages') . " "
        );
    }
}
