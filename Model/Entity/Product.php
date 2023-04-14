<?php
/**
 * @author MageDad Team
 * @copyright Copyright (c) 2023 Magedad (https://www.magedad.com)
 * @package Magento 2 Admin ChatBot
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model\Entity;

use MageDad\AdminBot\Model\ReplyFormat;
use Magento\Catalog\Model\Product\TypeFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\UrlInterface;

/*
 * phpcs:disable Magento2.Translation.ConstantUsage
 */
#[\AllowDynamicProperties]
class Product extends Entity
{
    public const PRODUCT_QUERY = 'Product';
    public const ADD_PRODUCT_QUERY = 'Add product';
    public const SEARCH_PRODUCT_QUERY = 'Search Products';

    public const AUTO_REPLY_WORDS = [
        self::PRODUCT_QUERY,
        self::ADD_PRODUCT_QUERY,
        self::SEARCH_PRODUCT_QUERY,
        'products' // additional serch word
    ];

    /**
     * Construct
     *
     * @param TypeFactory $typeFactory
     * @param ProductFactory $productFactory
     * @param UrlInterface $urlBuilder
     * @param ReplyFormat $replyFormat
     */
    public function __construct(
        TypeFactory $typeFactory,
        ProductFactory $productFactory,
        UrlInterface $urlBuilder,
        ReplyFormat $replyFormat
    ) {
        $this->productFactory = $productFactory;
        $this->typeFactory = $typeFactory;
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
    public function autoReplyQueryCheck(string $query)
    {
        $productAllQuery = array_map('strtolower', self::AUTO_REPLY_WORDS);
        return in_array(strtolower($query), $productAllQuery);
    }

    /**
     * Check Is My Query With Keyword
     *
     * @param string $query
     * @return bool
     */
    public function checkIsMyQueryWithKeyword(string $query)
    {
        return $this->checkQueryWithKeyword(self::AUTO_REPLY_WORDS, $query);
    }

    /**
     * Clean Query
     *
     * @param string $query
     * @return string
     */
    public function cleanQuery(string $query)
    {
        return $this->cleanUpQuery(self::AUTO_REPLY_WORDS, $query);
    }

    /**
     * Get reply
     *
     * @param string $query
     * @return array
     */
    public function getReply(string $query)
    {
        if (strtolower($query) == strtolower(self::PRODUCT_QUERY) || strtolower($query) == 'products') {
            return $this->mainOption($query);
        }

        if (strtolower($query) == strtolower(self::ADD_PRODUCT_QUERY)) {
            return $this->addProduct($query);
        }

        if (strtolower($query) == strtolower(self::SEARCH_PRODUCT_QUERY)) {
            return $this->searchProduct($query);
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
        if (!$this->authorization->isAllowed('Magento_Catalog::products')) {
            return [];
        }

        return $this->returnData(
            __('Please select relevant option.'),
            [
                $this->returnData(__(self::ADD_PRODUCT_QUERY)),
                $this->returnData(__(self::SEARCH_PRODUCT_QUERY)),
            ]
        );
    }

    /**
     * Add product
     *
     * @param string $query
     * @return array
     */
    private function addProduct(string $query)
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

    /**
     * Get product create url
     *
     * @param string $type
     * @return mixed
     */
    private function getProductCreateUrl(string $type)
    {
        return $this->urlBuilder->getUrl(
            'catalog/product/new',
            ['set' => $this->productFactory->create()->getDefaultAttributeSetId(), 'type' => $type, '_secure' => true]
        );
    }

    /**
     * Search product
     *
     * @param string $query
     * @return array
     */
    private function searchProduct(string $query)
    {
        return $this->returnData(
            $this->typeCommand(__('Product {name/description/sku/productId}')),
            [],
            '',
            __('Product') . " "
        );
    }

    /**
     * Shortcut List
     *
     * @return array
     */
    public function getShortcutList(): array
    {
        return [
            __('Add product'),
            __('Product {name/description/sku/productId}')
        ];
    }
}
