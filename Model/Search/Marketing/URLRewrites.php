<?php

declare(strict_types=1);

namespace MageDad\AdminBot\Model\Search\Marketing;

use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;


class URLRewrites extends \Magento\Framework\DataObject
{
    /**
     * Adminhtml data
     *
     * @var \Magento\Backend\Helper\Data
     */
    protected $_adminhtmlData = null;

    /**
     * @var \Magento\Customer\Helper\View
     */
    protected $_customerViewHelper;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Backend\Helper\Data $adminhtmlData
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     */
    public function __construct(
        \Magento\Backend\Helper\Data $adminhtmlData,
        UrlRewriteCollectionFactory $collectionFactory
    ) {
        $this->_adminhtmlData = $adminhtmlData;
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
                    ['like' => '%'.$this->getQuery().'%'],
                    ['like' => '%'.$this->getQuery().'%']
                ]
            );
        }

        #echo $collection->getSelect()->__toString();die();
        foreach ($collection as $urlRewrite) {
            $result[] = [
                'id' => 'urlRewrite/1/' . $urlRewrite->getId(),
                'type' => __('Url Rewrite'),
                'name' => $urlRewrite->getRequestPath() . " => " . $urlRewrite->getTargetPath(),
                'extraInfo' => [],
                'url' => $this->_adminhtmlData->getUrl('adminhtml/url_rewrite/edit', ['id' => $urlRewrite->getUrlRewriteId()]),
            ];
        }

        $this->setResults($result);
        return $this;
    }
}
