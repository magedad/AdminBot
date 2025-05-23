<?php
/**
 * @author MageDad Team
 * @copyright Copyright (c) 2023 Magedad (https://www.magedad.com)
 * @package Magento 2 Admin ChatBot
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model\Search;

use Magento\Backend\Model\UrlInterface;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\DataObject;

class CmsBlocks extends DataObject
{
    /**
     * Initialize dependencies.
     *
     * @param UrlInterface $urlBuilder
     * @param BlockRepositoryInterface $blockRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterBuilder $filterBuilder
     */
    public function __construct(
        UrlInterface $urlBuilder,
        BlockRepositoryInterface $blockRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterBuilder $filterBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->blockRepository = $blockRepository;
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
        $searchFields = ['title', 'identifier'];
        if (is_numeric($this->getQuery())) {
            $searchFields = ['block_id'];
        }

        $filters = [];
        foreach ($searchFields as $field) {

            $value = '%' . $this->getQuery() . '%';
            if ($field == 'block_id') {
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
        $searchResults = $this->blockRepository->getList($searchCriteria);

        foreach ($searchResults->getItems() as $block) {
            $extraInfo = [
                'Enable' => $block->getIsActive() ? 'Yes' : 'No',
                'URL Key' => $block->getIdentifier()
            ];
            $result[] = [
                'type' => __('Page'),
                'name' => $block->getTitle(),
                'extraInfo' => $extraInfo,
                'url' => $this->urlBuilder->getUrl('cms/block/edit', ['block_id' => $block->getId()]),
            ];
        }
        $this->setResults($result);
        return $this;
    }
}
