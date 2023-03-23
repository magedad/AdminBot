<?php
declare(strict_types=1);

namespace MageDad\AdminBot\Model\Search;

class CmsBlocks extends \Magento\Framework\DataObject
{
    /**
     * Adminhtml data
     *
     * @var \Magento\Backend\Helper\Data
     */
    protected $_adminhtmlData = null;

    /**
     * @var \Magento\Cms\Api\BlockRepositoryInterface
     */
    protected $blockRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Backend\Helper\Data $adminhtmlData
     * @param \Magento\Cms\Api\BlockRepositoryInterface $blockRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     */
    public function __construct(
        \Magento\Backend\Helper\Data $adminhtmlData,
        \Magento\Cms\Api\BlockRepositoryInterface $blockRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder
    ) {
        $this->_adminhtmlData = $adminhtmlData;
        $this->pageRepository = $blockRepository;
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
        $searchResults = $this->pageRepository->getList($searchCriteria);

        foreach ($searchResults->getItems() as $block) {
            $result[] = [
                'id' => 'customer/1/' . $block->getId(),
                'type' => __('Page'),
                'name' => $block->getTitle(),
                'extraInfo' => $block->getTitle(),
                'url' => $this->_adminhtmlData->getUrl('cms/block/edit', ['block_id' => $block->getId()]),
            ];
        }
        $this->setResults($result);
        return $this;
    }
}