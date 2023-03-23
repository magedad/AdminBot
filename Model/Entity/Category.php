<?php

declare(strict_types=1);

namespace MageDad\AdminBot\Model\Entity;

class Category extends Entity
{
    public const CATEGORY_QUERY = 'Category';
    public const ADD_CATEGORY_QUERY = 'Add category';
    public const EDIT_CATEGORY_QUERY = 'Edit/View Category';
    public const SEARCH_CATEGORY_QUERY = 'Search Categories';
    public const TAKE_ACTION = 'action';
    public const SEARCH_WORDS = [
        self::CATEGORY_QUERY,
        self::ADD_CATEGORY_QUERY,
        self::EDIT_CATEGORY_QUERY,
        self::SEARCH_CATEGORY_QUERY,
        'Categories' // additional serch word
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
        $categoryAllQuery = array_map('strtolower', self::SEARCH_WORDS);
        return in_array(strtolower($query), $categoryAllQuery);
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
        if (strtolower($query) == strtolower(self::CATEGORY_QUERY) || strtolower($query) == 'categories') {
            return $this->mainOption($query);
        }

        if (strtolower($query) == strtolower(self::ADD_CATEGORY_QUERY)) {
            return $this->addCategory($query);
        }

        if (strtolower($query) == strtolower(self::EDIT_CATEGORY_QUERY)) {
            return $this->editCategory($query);
        }

        if (strtolower($query) == strtolower(self::SEARCH_CATEGORY_QUERY)) {
            return $this->searchCategory($query);
        }

        return [];
    }

    private function mainOption($query)
    {
        if (!$this->authorization->isAllowed('Magento_Catalog::categories')) {
            return [];
        }

        return $this->returnData(
            __('Please select relevant option.'),
            [
                $this->returnData(__(self::ADD_CATEGORY_QUERY)),
                $this->returnData(__(self::EDIT_CATEGORY_QUERY)),
                $this->returnData(__(self::SEARCH_CATEGORY_QUERY)),
            ]
        );
    }

    private function addCategory($query)
    {
        return $this->returnData(
            __('Click here for add new category.'),
            '',
            $this->getCategoryCreateUrl(),
        );
    }

    private function editCategory($query)
    {
       return $this->returnData(
            __('Category {id/sku/name}'),
            [],
            '',
            __('Category')." "
       );
    }

    private function searchCategory($query)
    {
       return $this->returnData(
            __('Category {name/description/sku/categoryId}'),
            [],
            '',
            __('Categories')." "
       );
    }

    private function getCategoryCreateUrl()
    {
        return $this->urlBuilder->getUrl(
            'catalog/category/index',
            ['_secure' => true]
        );
    }

}