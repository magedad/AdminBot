<?php
/**
 * @author MageDad Team
 * @copyright Copyright (c) 2023 Magedad (https://www.magedad.com)
 * @package Magento 2 Admin ChatBot
 */

declare(strict_types=1);

namespace MageDad\AdminBot\Model\Search\Marketing;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\DataObject;
use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory;

class CartPriceRule extends DataObject
{
    /**
     * @param UrlInterface $urlBuilder
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        UrlInterface $urlBuilder,
        CollectionFactory $collectionFactory
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->collectionFactory = $collectionFactory;
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

        $collection = $this->collectionFactory->create();

        if (is_numeric(trim($this->getQuery()))) {
            $collection->addFieldToFilter('rule_id', ['eq' => $this->getQuery()]);
        } else {
            $collection->addFieldToFilter(
                ['name', 'code'],
                [
                    ['like' => '%' . $this->getQuery() . '%'],
                    ['like' => '%' . $this->getQuery() . '%']
                ]
            );
        }

        foreach ($collection as $cartPrice) {
            $result[] = [
                'type' => __('Cart Price Rule'),
                'name' => $cartPrice->getName(),
                'extraInfo' => [],
                'url' => $this->urlBuilder->getUrl('sales_rule/promo_quote/edit', ['id' => $cartPrice->getId()])
            ];
        }

        $this->setResults($result);
        return $this;
    }
}
