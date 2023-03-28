<?php
/**
 * @author MageDad Team
 * @copyright Copyright (c) 2023 Magedad (https://www.magedad.com)
 * @package Magento 2 Admin ChatBot
 */

declare(strict_types=1);

namespace MageDad\AdminBot\Model\Search\Marketing;

use Magento\Backend\Model\UrlInterface;
use Magento\Email\Model\ResourceModel\Template\CollectionFactory as EmailTemplateCollectionFactory;
use Magento\Framework\DataObject;

class EmailTemplates extends DataObject
{
    /**
     * @param UrlInterface $urlBuilder
     * @param EmailTemplateCollectionFactory $collectionFactory
     */
    public function __construct(
        UrlInterface $urlBuilder,
        EmailTemplateCollectionFactory $collectionFactory
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
            $collection->addFieldToFilter('template_id', ['eq' => $this->getQuery()]);
        } else {
            $collection->addFieldToFilter('template_code', ['like' => '%' . $this->getQuery() . '%']);
        }

        foreach ($collection as $emailTemplates) {
            $result[] = [
                'type' => __('Email Templates'),
                'name' => $emailTemplates->getTemplateCode(),
                'extraInfo' => [],
                'url' => $this->urlBuilder->getUrl(
                    'adminhtml/email_template/edit',
                    ['id' => $emailTemplates->getTemplateId()]
                ),
            ];
        }

        $this->setResults($result);
        return $this;
    }
}
