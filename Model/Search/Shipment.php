<?php

namespace MageDad\AdminBot\Model\Search;

class Shipment extends \Magento\Framework\DataObject
{
    /**
     * Adminhtml data
     *
     * @var \Magento\Backend\Helper\Data
     */
    protected $adminhtmlData = null;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $collectionFactory
     * @param \Magento\Backend\Helper\Data $adminhtmlData
     */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory $collectionFactory,
        \Magento\Backend\Helper\Data $adminhtmlData
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->adminhtmlData = $adminhtmlData;
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

        $query = $this->getQuery();

        $collection = $this->collectionFactory->create();
        $collection->addAttributeToSelect('entity_id')
        ->addAttributeToSelect('increment_id')
        ->join(
            ['so' => $collection->getTable('sales_order')],
            'so.entity_id = main_table.order_id',
            ['customer_firstname' => 'so.customer_firstname', 'customer_lastname' => 'so.customer_lastname']
        )
        ->addFieldToFilter(
            ['main_table.increment_id', 'so.customer_email', 'so.customer_firstname', 'so.customer_lastname'
            ],
            [
                ['eq' => $query ],
                ['like' =>'%'. $query . '%'],
                ['like' =>'%'. $query . '%'],
                ['like' =>'%'. $query . '%'],
            ]
        )->setCurPage(
            $this->getStart()
        )->setPageSize(
            $this->getLimit()
        )->setOrder(
            'main_table.entity_id','DESC'
        )->load();

        foreach ($collection as $shipment) {
            $result[] = [
                'id' => $shipment->getId(),
                'type' => $collection->getSize() > 0 ? __('Shipments') : __('Shipment'),
                'name' => __('Shipment #%1', $shipment->getIncrementId()),
                'extraInfo' => $shipment->getCustomerFirstname() . ' ' . $shipment->getCustomerLastname(),
                'url' => $this->adminhtmlData->getUrl('sales/shipment/view', ['shipment_id' => $shipment->getId()]),
            ];
        }

        $this->setResults($result);

        return $this;
    }
}
