<?php
/**
 * @author MageDad Team
 * @copyright Copyright (c) 2023 Magedad (https://www.magedad.com)
 * @package Magento 2 Admin ChatBot
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model\Search\Sales;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory;

class Shipment extends SalesSearch
{
    /**
     * Construct
     *
     * @param CollectionFactory $collectionFactory
     * @param UrlInterface $urlBuilder
     * @param Data $priceHelper
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        UrlInterface $urlBuilder,
        Data $priceHelper
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->urlBuilder = $urlBuilder;
        parent::__construct($priceHelper);
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
        $collection
            ->addAttributeToSelect('entity_id')
            ->addAttributeToSelect('increment_id')
            ->addAttributeToSelect('created_at')
            ->join(
                ['so' => $collection->getTable('sales_order')],
                'so.entity_id = main_table.order_id',
                [
                    'customer_firstname' => 'so.customer_firstname',
                    'customer_lastname' => 'so.customer_lastname',
                    'customer_email' => 'so.customer_email',
                    'order_increment_id' => 'so.increment_id',
                    'order_id' => 'so.entity_id',
                ]
            )
            ->addFieldToFilter(
                ['main_table.increment_id', 'so.customer_email', 'so.customer_firstname', 'so.customer_lastname'
                ],
                [
                    ['eq' => $query],
                    ['like' => '%' . $query . '%'],
                    ['like' => '%' . $query . '%'],
                    ['like' => '%' . $query . '%'],
                ]
            )->setCurPage(
                $this->getStart()
            )->setPageSize(
                $this->getLimit()
            )->setOrder(
                'main_table.entity_id',
                'DESC'
            )->load();

        foreach ($collection as $shipment) {
            $orderUrl = $this->urlBuilder->getUrl('sales/order/view', ['order_id' => $shipment->getOrderId()]);
            $extraInfo = [
                'Order Id' => "<a href='" . $orderUrl . "'>#" . $shipment->getOrderIncrementId() . "</a>",
                'Customer Name' => $this->getCustomerName($shipment->getOrder()),
                'Email' => $shipment->getCustomerEmail(),
                'Customer Group' => $this->getCustomerGroupName($shipment->getOrder()),
                'Created At' => $shipment->getCreatedAt()
            ];

            $result[] = [
                'id' => $shipment->getId(),
                'type' => $collection->getSize() > 0 ? __('Shipments') : __('Shipment'),
                'name' => __('Shipment #%1', $shipment->getIncrementId()),
                'extraInfo' => $extraInfo,
                'url' => $this->urlBuilder->getUrl('sales/shipment/view', ['shipment_id' => $shipment->getId()]),
            ];
        }

        $this->setResults($result);

        return $this;
    }
}
