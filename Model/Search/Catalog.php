<?php

namespace MageDad\AdminBot\Model\Search;

use Magento\Search\Model\QueryFactory;

/**
 * Search model for backend search
 */
class Catalog extends \Magento\Framework\DataObject
{
    /**
     * Catalog search data
     *
     * @var \Magento\Search\Model\QueryFactory
     */
    protected $queryFactory = null;

    /**
     * Magento string lib
     *
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    protected $string;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $_adminhtmlData = null;

    /**
     * @param \Magento\Backend\Helper\Data $adminhtmlData
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param QueryFactory $queryFactory
     */
    public function __construct(
        \Magento\Backend\Helper\Data $adminhtmlData,
        \Magento\Framework\Stdlib\StringUtils $string,
        QueryFactory $queryFactory
    ) {
        $this->_adminhtmlData = $adminhtmlData;
        $this->string = $string;
        $this->queryFactory = $queryFactory;
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

        $collection = $this->queryFactory->get()
            ->getSearchCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addBackendSearchFilter($this->getQuery())
            ->setCurPage($this->getStart())
            ->setPageSize($this->getLimit())
            ->load();

        if ($collection->getSize() == 0) {
            $collection = $this->getProductCollectionById();
        }

        foreach ($collection as $product) {
            $extraInfo = [
                'SKU' => $product->getSku()
            ];
            $result[] = [
                'id' => 'product/1/' . $product->getId(),
                'type' => __('Product'),
                'name' => $product->getName(),
                'extraInfo' => $extraInfo,
                'url' => $this->_adminhtmlData->getUrl('catalog/product/edit', ['id' => $product->getId()]),
            ];
        }

        $this->setResults($result);

        return $this;
    }

    public function getProductCollectionById()
    {
        $collection = $this->queryFactory->get()
            ->getSearchCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('entity_id', ['eq' => $this->getQuery()]);

        return $collection;
    }
}
