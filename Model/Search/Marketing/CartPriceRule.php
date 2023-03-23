<?php
declare(strict_types=1);

namespace MageDad\AdminBot\Model\Search\Marketing;

use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory;

class CartPriceRule extends \Magento\Framework\DataObject
{
    /**
     * Adminhtml data
     *
     * @var \Magento\Backend\Helper\Data
     */
    protected $_adminhtmlData = null;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    protected $filterBuilder;

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
        CollectionFactory $collectionFactory
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
            $collection->addFieldToFilter('rule_id', ['eq' => $this->getQuery()]);
        } else {
            $collection->addFieldToFilter(
                ['name', 'code'],
                [
                    ['like' => '%'.$this->getQuery().'%'],
                    ['like' => '%'.$this->getQuery().'%']
                ]
            );
        }

        #echo $collection->getSelect()->__toString();die();
        foreach ($collection as $cartPrice) {
            $result[] = [
                'id' => 'cartPrice/1/' . $cartPrice->getId(),
                'type' => __('Cart Price Rule'),
                'name' => $cartPrice->getName(),
                'extraInfo' => [],
                'url' => $this->_adminhtmlData->getUrl('sales_rule/promo_quote/edit', ['id' => $cartPrice->getId()]),
            ];
        }

        $this->setResults($result);
        return $this;
    }
}
