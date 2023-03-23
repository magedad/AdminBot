<?php

declare(strict_types=1);

namespace MageDad\AdminBot\Model\Search\Marketing;

use Magento\Email\Model\ResourceModel\Template\CollectionFactory as EmailTemplateCollectionFactory;


class EmailTemplates extends \Magento\Framework\DataObject
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
        EmailTemplateCollectionFactory $collectionFactory
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
            $collection->addFieldToFilter('template_id', ['eq' => $this->getQuery()]);
        } else {
            $collection->addFieldToFilter('template_code', ['like' => '%' . $this->getQuery() . '%']);
        }

        #echo $collection->getSelect()->__toString();die();
        foreach ($collection as $emailTemplates) {
            $result[] = [
                'id' => 'emailTemplates/1/' . $emailTemplates->getId(),
                'type' => __('Email Templates'),
                'name' => $emailTemplates->getTemplateCode(),
                'extraInfo' => [],
                'url' => $this->_adminhtmlData->getUrl('adminhtml/email_template/edit', ['id' => $emailTemplates->getTemplateId()]),
            ];
        }

        $this->setResults($result);
        return $this;
    }
}
