<?php
/**
 * @author MageDad Team
 * @copyright Copyright (c) 2023 Magedad (https://www.magedad.com)
 * @package Magento 2 Admin ChatBot
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model\Search;

use Magento\Backend\Model\UrlInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\DataObject;

class CustomerGroup extends DataObject
{
    /**
     * Initialize dependencies.
     *
     * @param UrlInterface $urlBuilder
     * @param GroupRepositoryInterface $groupRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     */
    public function __construct(
        UrlInterface $urlBuilder,
        GroupRepositoryInterface $groupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->groupRepository = $groupRepository;
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
        $searchFields = ['customer_group_code'];
        if (is_numeric($this->getQuery())) {
            $searchFields = ['customer_group_id'];
        }

        $filters = [];
        foreach ($searchFields as $field) {

            $value = '%' . $this->getQuery() . '%';
            if ($field == 'customer_group_id') {
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
        $searchResults = $this->groupRepository->getList($searchCriteria);

        foreach ($searchResults->getItems() as $group) {
            $result[] = [
                'type' => __('Page'),
                'name' => $group->getCode(),
                'extraInfo' => [],
                'url' => $this->urlBuilder->getUrl('customer/group/edit', ['id' => $group->getId()]),
            ];
        }
        $this->setResults($result);
        return $this;
    }
}
