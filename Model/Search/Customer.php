<?php
/**
 * @author MageDad Team
 * @copyright Copyright (c) 2023 Magedad (https://www.magedad.com)
 * @package Magento 2 Admin ChatBot
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model\Search;

use MageDad\AdminBot\Model\Entity\Customer as CustomerEntity;
use Magento\Backend\Model\UrlInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Helper\View;
use Magento\Customer\Model\ResourceModel\Group\Collection as CustomerGroup;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\DataObject;
use Magento\Store\Model\StoreManagerInterface;

class Customer extends DataObject
{
    /**
     * @param UrlInterface $urlBuilder
     * @param CustomerRepositoryInterface $customerRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param FilterBuilder $filterBuilder
     * @param View $customerViewHelper
     * @param StoreManagerInterface $storeManager
     * @param CustomerGroup $customerGroup
     */
    public function __construct(
        UrlInterface $urlBuilder,
        CustomerRepositoryInterface $customerRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        FilterBuilder $filterBuilder,
        View $customerViewHelper,
        StoreManagerInterface $storeManager,
        CustomerGroup $customerGroup
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->_customerViewHelper = $customerViewHelper;
        $this->customerGroup = $customerGroup;
        $this->storeManager = $storeManager;
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
        $filters = [];
        $query = $this->getQuery();
        $customerNoReply = array_map('strtolower', CustomerEntity::NO_AUTO_REPLY_QUERY);
        if (!in_array($query, $customerNoReply)) {
            $searchFields = ['firstname', 'lastname', 'billing_company', 'email', 'entity_id'];
            foreach ($searchFields as $field) {
                $filters[] = $this->filterBuilder
                    ->setField($field)
                    ->setConditionType('like')
                    ->setValue($this->getQuery() . '%')
                    ->create();
            }
        }

        $sortOrder = $this->sortOrderBuilder->setField('entity_id')->setDirection('DESC')->create();
        $this->searchCriteriaBuilder->addFilters($filters)->setSortOrders([$sortOrder]);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $searchResults = $this->customerRepository->getList($searchCriteria);
        $customerGroup = $this->getCustomerGroups();
        foreach ($searchResults->getItems() as $customer) {
            $extraInfo = [
                "Customer group" => $customerGroup[$customer->getGroupId()],
                "Name" => $customer->getFirstname() . " " . $customer->getLastname(),
                "Account Created in" => $this->getCreatedInStore($customer->getStoreId()),
            ];
            $result[] = [
                'type' => __('Customer'),
                'name' => $this->_customerViewHelper->getCustomerName($customer),
                'subtitle' => $customer->getEmail(),
                'extraInfo' => $extraInfo,
                'url' => $this->urlBuilder->getUrl('customer/index/edit', ['id' => $customer->getId()]),
            ];
        }
        $this->setResults($result);
        return $this;
    }

    /**
     * Get customer group
     *
     * @return array
     */
    public function getCustomerGroups(): array
    {
        $customerGroups = $this->customerGroup->toOptionArray();
        $groups = [];
        foreach ($customerGroups as $key => $group) {
            $groups[$group['value']] = $group['label'];
        }
        return $groups;
    }

    /**
     * Retrieve store
     *
     * @param string $storeId
     * @return mixed
     */
    public function getCreatedInStore(string $storeId)
    {
        return $this->storeManager->getStore(
            $storeId
        )->getName();
    }
}
