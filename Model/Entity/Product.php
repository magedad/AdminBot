<?php

declare(strict_types=1);

namespace MageDad\AdminBot\Model\Entity;

class Product extends Entity
{
    public const PRODUCT_QUERY = 'Product';
    public const ADD_PRODUCT_QUERY = 'Add product';
    public const EDIT_PRODUCT_QUERY = 'Edit/View Product';
    public const SEARCH_PRODUCT_QUERY = 'Search Products';
    public const TAKE_ACTION = 'action';
    public const SEARCH_WORDS = [
        self::PRODUCT_QUERY,
        self::ADD_PRODUCT_QUERY,
        self::EDIT_PRODUCT_QUERY,
        self::SEARCH_PRODUCT_QUERY,
        'products' // additional serch word
    ];

    public function __construct(
        \Magento\Catalog\Model\Product\TypeFactory $typeFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        \MageDad\AdminBot\Model\ReplyFormat $replyFormat
    ) {
        $this->productFactory = $productFactory;
        $this->typeFactory = $typeFactory;
        $this->urlBuilder = $urlBuilder;
        $this->replyFormat = $replyFormat;
        parent::__construct();
    }

    public function checkIsMyQuery($query)
    {
        $productAllQuery = array_map('strtolower', self::SEARCH_WORDS);
        return in_array(strtolower($query), $productAllQuery);
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
        if (strtolower($query) == strtolower(self::PRODUCT_QUERY) || strtolower($query) == 'products') {
            return $this->mainOption($query);
        }

        if (strtolower($query) == strtolower(self::ADD_PRODUCT_QUERY)) {
            return $this->addProduct($query);
        }

        if (strtolower($query) == strtolower(self::EDIT_PRODUCT_QUERY)) {
            return $this->editProduct($query);
        }

        if (strtolower($query) == strtolower(self::SEARCH_PRODUCT_QUERY)) {
            return $this->searchProduct($query);
        }

        return [];
    }

    private function mainOption($query)
    {
        if (!$this->authorization->isAllowed('Magento_Catalog::products')) {
            return [];
        }

        return $this->returnData(
            __('Please select relevant option.'),
            [
                $this->returnData(__(self::ADD_PRODUCT_QUERY)),
                $this->returnData(__(self::EDIT_PRODUCT_QUERY)),
                $this->returnData(__(self::SEARCH_PRODUCT_QUERY)),
            ]
        );
    }

    private function addProduct($query)
    {
        $types = $this->typeFactory->create()->getTypes();
        uasort(
            $types,
            function ($elementOne, $elementTwo) {
                return ($elementOne['sort_order'] < $elementTwo['sort_order']) ? -1 : 1;
            }
        );
        $productType = [];
        foreach ($types as $typeId => $type) {
            $productType[] = $this->returnData(
                __($type['label']),
                [],
                $this->getProductCreateUrl($typeId)
            );
        }

        return $this->returnData(
            __('Select product type for add new product.'),
            $productType
        );
    }

    private function editProduct($query)
    {
       return $this->returnData(
            __('Product {id/sku/name}'),
            [],
            '',
            __('Product')." "
       );
    }

    private function searchProduct($query)
    {
       return $this->returnData(
            __('Product {name/description/sku/productId}'),
            [],
            '',
            __('Products')." "
       );
    }

    private function getProductCreateUrl($type)
    {
        return $this->urlBuilder->getUrl(
            'catalog/product/new',
            ['set' => $this->productFactory->create()->getDefaultAttributeSetId(), 'type' => $type, '_secure' => true]
        );
    }
}