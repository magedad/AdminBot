<?php

declare(strict_types=1);

namespace MageDad\AdminBot\Model\Entity\Content;

use MageDad\AdminBot\Model\Entity\Entity;

class CmsBlock extends Entity
{
    public const CMSBLOCK_QUERY = 'CMS Block';
    public const ADD_CMSBLOCK_QUERY = 'Add CMS Block';
    public const SEARCH_CMSBLOCK_QUERY = 'Search/Edit/View Blocks';
    public const TAKE_ACTION = 'action';
    public const SEARCH_WORDS = [
        self::CMSBLOCK_QUERY,
        self::ADD_CMSBLOCK_QUERY,
        self::SEARCH_CMSBLOCK_QUERY,
    ];

    public const ADDITIONAL_SEARCH_WORDS = [
        'cms blocks',
        'blocks',
        'block',
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
        $cmsblockAllQuery = array_map('strtolower', self::SEARCH_WORDS);
        return in_array(strtolower($query), $cmsblockAllQuery) || in_array(strtolower($query), self::ADDITIONAL_SEARCH_WORDS) ;
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
        if (!$this->authorization->isAllowed('Magento_Cms::block')) {
            return [];
        }

        if (strtolower($query) == strtolower(self::CMSBLOCK_QUERY) || in_array(strtolower($query), self::ADDITIONAL_SEARCH_WORDS)) {
            return $this->cmsPage($query);
        }

        if (strtolower($query) == strtolower(self::ADD_CMSBLOCK_QUERY)) {
            return $this->addCmsBlock($query);
        }

        if (strtolower($query) == strtolower(self::SEARCH_CMSBLOCK_QUERY)) {
            return $this->searchCmsBlock($query);
        }

        return [];
    }

    public function cmsPage($query)
    {
        return $this->returnData(
            __('Please select relevant option.'),
            [
                $this->returnData(__(self::ADD_CMSBLOCK_QUERY)),
                $this->returnData(__(self::SEARCH_CMSBLOCK_QUERY)),
            ]
        );
    }

    public function addCmsBlock($query)
    {
        return $this->returnData(
            __('Click here to add new block'),
            [],
            $this->getCmsBlockCreateUrl()
        );
    }

    public function searchCmsBlock($query)
    {
       return $this->returnData(
            __('Cms Block {id/name/url_key/keword}'),
            [],
            '',
            __('Cms Block ')." "
       );
    }

    protected function getCmsBlockCreateUrl()
    {
        return $this->urlBuilder->getUrl(
            '*/cms_block/new',
            ['_secure' => true]
        );
    }
}