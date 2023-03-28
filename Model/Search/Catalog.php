<?php
/**
 * @author MageDad Team
 * @copyright Copyright (c) 2023 Magedad (https://www.magedad.com)
 * @package Magento 2 Admin ChatBot
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model\Search;

use Magento\Backend\Model\UrlInterface;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\DataObject;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\Stdlib\StringUtils;
use Magento\InventorySalesAdminUi\Model\GetSalableQuantityDataBySku;
use Magento\Search\Model\QueryFactory;

class Catalog extends DataObject
{
    /**
     * @param UrlInterface $urlBuilder
     * @param StringUtils $string
     * @param ProductRepository $productRepository
     * @param Data $priceHelper
     * @param GetSalableQuantityDataBySku $getSalableQuantityDataBySku
     * @param QueryFactory $queryFactory
     */
    public function __construct(
        UrlInterface $urlBuilder,
        StringUtils $string,
        ProductRepository $productRepository,
        Data $priceHelper,
        GetSalableQuantityDataBySku $getSalableQuantityDataBySku,
        QueryFactory $queryFactory
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->string = $string;
        $this->productRepository = $productRepository;
        $this->queryFactory = $queryFactory;
        $this->priceHelper = $priceHelper;
        $this->getSalableQuantityDataBySku = $getSalableQuantityDataBySku;
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
            $product = $this->productRepository->getById($product->getId());
            $salable = $this->getSalableQuantityDataBySku->execute($product->getSku());

            $salableQty = '';
            foreach ($salable as $key => $item) {
                $salableQty .= $item['stock_name'] . " = " . $item['qty'] . "<br>";
            }
            $stockItem = $product->getExtensionAttributes()->getStockItem();
            $extraInfo = [
                'SKU' => $product->getSku(),
                'Price' => $this->priceHelper->currency($product->getPrice()),
                'Quantity' => $stockItem->getQty(),
                'Salable Quantity' => $salableQty,
                'Is in stock' => $stockItem->getIsInStock(),
                'Manage stock' => $stockItem->getManageStock(),
                'URL Key' => $product->getUrlKey(),
                'Product Type' => $product->getTypeId(),
            ];

            if ($product->getSpecialPrice()) {
                $extraInfo['Special Price'] = $this->priceHelper->currency($product->getSpecialPrice());
            }

            $result[] = [
                'type' => __('Product'),
                'name' => $product->getName(),
                'extraInfo' => $extraInfo,
                'url' => $this->urlBuilder->getUrl(
                    'catalog/product/edit',
                    ['id' => $product->getId()]
                )
            ];
        }

        $this->setResults($result);

        return $this;
    }

    /**
     * Get product collection by id
     *
     * @return mixed
     */
    public function getProductCollectionById()
    {
        $collection = $this->queryFactory->get()
            ->getSearchCollection()
            ->addAttributeToSelect('name')
            ->addAttributeToFilter('entity_id', ['eq' => $this->getQuery()]);

        return $collection;
    }
}
