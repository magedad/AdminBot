<?php
/**
 * @author MageDad Team
 * @copyright Copyright (c) 2023 Magedad (https://www.magedad.com)
 * @package Magento 2 Admin ChatBot
 */

declare(strict_types=1);

namespace MageDad\AdminBot\Model\Search\Marketing;

use Magento\Backend\Model\UrlInterface;
use Magento\Customer\Helper\View;
use Magento\Framework\DataObject;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;

class URLRewrites extends DataObject
{
    /**
     * @param UrlInterface $urlBuilder
     * @param UrlRewriteCollectionFactory $collectionFactory
     */
    public function __construct(
        UrlInterface $urlBuilder,
        UrlRewriteCollectionFactory  $collectionFactory
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
            $collection->addFieldToFilter('entity_id', ['eq' => $this->getQuery()]);
        } else {
            $collection->addFieldToFilter(
                ['request_path', 'target_path'],
                [
                    ['like' => '%' . $this->getQuery() . '%'],
                    ['like' => '%' . $this->getQuery() . '%']
                ]
            );
        }

        foreach ($collection as $urlRewrite) {
            $result[] = [
                'type' => __('Url Rewrite'),
                'name' => $urlRewrite->getRequestPath() . " => " . $urlRewrite->getTargetPath(),
                'extraInfo' => [],
                'url' => $this->urlBuilder->getUrl(
                    'adminhtml/url_rewrite/edit',
                    ['id' => $urlRewrite->getUrlRewriteId()]
                ),
            ];
        }

        $this->setResults($result);
        return $this;
    }
}
