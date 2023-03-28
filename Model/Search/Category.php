<?php
/**
 * @author MageDad Team
 * @copyright Copyright (c) 2023 Magedad (https://www.magedad.com)
 * @package Magento 2 Admin ChatBot
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model\Search;

use Magento\Backend\Model\UrlInterface;
use Magento\Catalog\Api\CategoryListInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\DataObject;

class Category extends DataObject
{
    /**
     * @param UrlInterface $urlBuilder
     * @param CategoryListInterface $categoryRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     */
    public function __construct(
        UrlInterface $urlBuilder,
        CategoryListInterface $categoryRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->categoryRepository = $categoryRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * Load search results
     *
     * @return $this
     */
    public function load()
    {
        $result = [];
        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($result);
            return $this;
        }

        $this->searchCriteriaBuilder->setCurrentPage($this->getStart());
        $this->searchCriteriaBuilder->setPageSize($this->getLimit());
        $searchFields = ['name'];
        $filters = [];
        foreach ($searchFields as $field) {
            $filters[] = $this->filterBuilder
                ->setField($field)
                ->setConditionType('like')
                ->setValue($this->getQuery() . '%')
                ->create();
        }

        $searchFields = ['entity_id', 'url_key'];
        foreach ($searchFields as $field) {
            $filters[] = $this->filterBuilder
                ->setField($field)
                ->setConditionType('eq')
                ->setValue($this->getQuery())
                ->create();
        }

        $this->searchCriteriaBuilder->addFilters($filters);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchResults = $this->categoryRepository->getList($searchCriteria);

        foreach ($searchResults->getItems() as $category) {
            $result[] = [
                'type' => __('Category'),
                'name' => 'Category - ' . $category->getName(),
                'extraInfo' => $category->getName(),
                'url' => $this->urlBuilder->getUrl('catalog/category/edit', ['id' => $category->getId()]),
            ];
        }
        $this->setResults($result);
        return $this;
    }
}
