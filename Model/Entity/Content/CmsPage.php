<?php

declare(strict_types=1);

namespace MageDad\AdminBot\Model\Entity\Content;

use MageDad\AdminBot\Model\Entity\Entity;

class CmsPage extends Entity
{
    public const CMSPAGE_QUERY = 'CMS Page';
    public const ADD_CMSPAGE_QUERY = 'Add CMS Page';
    public const SEARCH_CMSPAGE_QUERY = 'Search/Edit/View Pages';
    public const TAKE_ACTION = 'action';
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
        $cmspageAllQuery = array_map('strtolower', self::SEARCH_WORDS);
        return in_array(strtolower($query), $cmspageAllQuery) || in_array(strtolower($query), self::ADDITIONAL_SEARCH_WORDS) ;
    }

    public function getSearchWord()
    {
        return array_merge(self::SEARCH_WORDS, self::ADDITIONAL_SEARCH_WORDS);
    }

    public function checkIsMyQueryWithKeyword($query)
    {
        return $this->checkQueryWithKeyword($this->getSearchWord(), $query);
    }

    public function cleanQuery($query)
    {
        return $this->cleanUpQuery($this->getSearchWord(), $query);
    }

    public function getReply($query)
    {
        if (!$this->authorization->isAllowed('Magento_Cms::page')) {
            return [];
        }

        if (strtolower($query) == strtolower(self::CMSPAGE_QUERY) || in_array(strtolower($query), self::ADDITIONAL_SEARCH_WORDS)) {
            return $this->cmsPage($query);
        }

        if (strtolower($query) == strtolower(self::ADD_CMSPAGE_QUERY)) {
            return $this->addCmsPage($query);
        }

        if (strtolower($query) == strtolower(self::SEARCH_CMSPAGE_QUERY)) {
            return $this->searchCmsPage($query);
        }

        return [];
    }

    public function cmsPage($query)
    {
        return $this->returnData(
            __('Please select relevant option.'),
            [
                $this->returnData(__(self::ADD_CMSPAGE_QUERY)),
                $this->returnData(__(self::SEARCH_CMSPAGE_QUERY)),
            ]
        );
    }

    public function addCmsPage($query)
    {
        return $this->returnData(
            __('Click here to add new page.'),
            [],
            $this->getCmsPageCreateUrl()
        );
    }

    public function searchCmsPage($query)
    {
       return $this->returnData(
            __('Cms Page {id/name/url_key/keword}'),
            [],
            '',
            __('Cms Pages')." "
       );
    }

    protected function getCmsPageCreateUrl()
    {
        return $this->urlBuilder->getUrl(
            '*/cms_page/new',
            ['_secure' => true]
        );
    }
}