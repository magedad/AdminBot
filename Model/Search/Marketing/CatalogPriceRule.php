<?php
/**
 * @author MageDad Team
 * @copyright Copyright (c) 2023 Magedad (https://www.magedad.com)
 * @package Magento 2 Admin ChatBot
 */

declare(strict_types=1);

namespace MageDad\AdminBot\Model\Search\Marketing;

use Magento\Backend\Model\UrlInterface;
use Magento\CatalogRule\Model\ResourceModel\Rule\CollectionFactory;
use Magento\Framework\DataObject;

class CatalogPriceRule extends DataObject
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
            $collection->addFieldToFilter('name', ['like' => '%' . $this->getQuery() . '%']);
        }

        foreach ($collection as $catalogPrice) {
            $result[] = [
                'type' => __('Catalog Price Rule'),
                'name' => $catalogPrice->getName(),
                'extraInfo' => [],
                'url' => $this->urlBuilder->getUrl(
                    'catalog_rule/promo_catalog/edit',
                    ['id' => $catalogPrice->getId()]
                ),
            ];
        }

        $this->setResults($result);
        return $this;
    }
}
