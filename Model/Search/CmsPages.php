<?php
/**
 * @author MageDad Team
 * @copyright Copyright (c) 2023 Magedad (https://www.magedad.com)
 * @package Magento 2 Admin ChatBot
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model\Search;

use Magento\Backend\Model\UrlInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\DataObject;

class CmsPages extends DataObject
{
    /**
     * @param UrlInterface $urlBuilder
     * @param PageRepositoryInterface $pageRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     */
    public function __construct(
        UrlInterface $urlBuilder,
        PageRepositoryInterface $pageRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->pageRepository = $pageRepository;
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
        $searchFields = ['title', 'content_heading', 'page_id', 'identifier'];

        if (is_numeric($this->getQuery())) {
            $searchFields = ['page_id'];
        }

        $filters = [];
        foreach ($searchFields as $field) {

            $value = '%' . $this->getQuery() . '%';
            if ($field == 'page_id') {
                $value = $this->getQuery();
            }

            $filters[] = $this->filterBuilder
                ->setField($field)
                ->setConditionType('like')
                ->setValue($value)
                ->create();
        }
        $this->searchCriteriaBuilder->addFilters($filters);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchResults = $this->pageRepository->getList($searchCriteria);

        foreach ($searchResults->getItems() as $page) {
            $extraInfo = [
                'Enable' => $page->getIsActive() ? 'Yes' : 'No',
                'URL Key' => $page->getIdentifier()
            ];
            $result[] = [
                'type' => __('Page'),
                'name' => $page->getTitle(),
                'extraInfo' => $extraInfo,
                'url' => $this->urlBuilder->getUrl('cms/page/edit', ['page_id' => $page->getId()]),
            ];
        }
        $this->setResults($result);
        return $this;
    }
}
