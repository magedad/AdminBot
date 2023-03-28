<?php
/**
 * @author MageDad Team
 * @copyright Copyright (c) 2023 Magedad (https://www.magedad.com)
 * @package Magento 2 Admin ChatBot
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model\Entity;

use MageDad\AdminBot\Model\ReplyFormat;
use Magento\Framework\UrlInterface;

/*
 * phpcs:disable Magento2.Translation.ConstantUsage
 */
class Category extends Entity
{
    public const CATEGORY_QUERY = 'Category';
    public const ADD_CATEGORY_QUERY = 'Add category';
    public const EDIT_CATEGORY_QUERY = 'Edit/View Category';
    public const SEARCH_CATEGORY_QUERY = 'Search Categories';

    public const SEARCH_WORDS = [
        self::CATEGORY_QUERY,
        self::ADD_CATEGORY_QUERY,
        self::EDIT_CATEGORY_QUERY,
        self::SEARCH_CATEGORY_QUERY,
        'Categories' // additional serch word
    ];

    /**
     * Constructor
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
        $categoryAllQuery = array_map('strtolower', self::SEARCH_WORDS);
        return in_array(strtolower($query), $categoryAllQuery);
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

    /**
     * MainOption
     *
     * @param string $query
     * @return array
     */
    private function mainOption(string $query)
    {
        if (!$this->authorization->isAllowed('Magento_Catalog::categories')) {
            return [];
        }

        return $this->returnData(
            __('Please select relevant option.'),
            [
                $this->addCategory(),
                $this->returnData(__(self::EDIT_CATEGORY_QUERY)),
                $this->returnData(__(self::SEARCH_CATEGORY_QUERY)),
            ]
        );
    }

    /**
     * Add category
     *
     * @return array
     */
    private function addCategory()
    {
        return $this->returnData(
            __(self::ADD_CATEGORY_QUERY),
            [],
            $this->getCategoryCreateUrl(),
        );
    }

    /**
     * Add category url
     *
     * @return mixed
     */
    private function getCategoryCreateUrl()
    {
        return $this->urlBuilder->getUrl(
            'catalog/category/index',
            ['_secure' => true]
        );
    }

    /**
     * Edit category
     *
     * @param string $query
     * @return array
     */
    private function editCategory(string $query)
    {
        return $this->returnData(
            $this->typeCommand(__('Category {id/sku/name}')),
            [],
            '',
            __('Category'). " "
        );
    }

    /**
     * Search category
     *
     * @param string $query
     * @return array
     */
    private function searchCategory(string $query)
    {
        return $this->returnData(
            $this->typeCommand(__('Category {name/description/sku/categoryId}')),
            [],
            '',
            __('Categories'). " "
        );
    }
}
