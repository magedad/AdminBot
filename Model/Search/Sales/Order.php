<?php
/**
 * @author MageDad Team
 * @copyright Copyright (c) 2023 Magedad (https://www.magedad.com)
 * @package Magento 2 Admin ChatBot
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model\Search\Sales;

use MageDad\AdminBot\Model\Entity\Sales\Order as OrderEntity;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

class Order extends SalesSearch
{
    /**
     * Construct
     *
     * @param CollectionFactory $collectionFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param UrlInterface $urlBuilder
     * @param Data $priceHelper
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        OrderRepositoryInterface $orderRepository,
        UrlInterface $urlBuilder,
        Data $priceHelper
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->orderRepository = $orderRepository;
        $this->urlBuilder = $urlBuilder;
        $this->priceHelper = $priceHelper;
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
        $collection = $this->collectionFactory->create()
            ->addAttributeToSelect('entity_id');
        $orderNoReply = array_map('strtolower', OrderEntity::NO_AUTO_REPLY_QUERY);
        if (!in_array($query, $orderNoReply)) {
            $collection
                ->addAttributeToSearchFilter(
                    [
                        ['attribute' => 'increment_id', 'like' => $query . '%'],
                        ['attribute' => 'customer_firstname ', 'like' => '%' . $query . '%'],
                        ['attribute' => 'customer_lastname ', 'like' => '%' . $query . '%'],
                        ['attribute' => 'customer_email ', 'like' => $query . '%'],
                        ['attribute' => 'billing_firstname', 'like' => $query . '%'],
                        ['attribute' => 'billing_lastname', 'like' => $query . '%'],
                        ['attribute' => 'billing_telephone', 'like' => $query . '%'],
                        ['attribute' => 'billing_postcode', 'like' => $query . '%'],
                        ['attribute' => 'shipping_firstname', 'like' => $query . '%'],
                        ['attribute' => 'shipping_lastname', 'like' => $query . '%'],
                        ['attribute' => 'shipping_telephone', 'like' => $query . '%'],
                        ['attribute' => 'shipping_postcode', 'like' => $query . '%'],
                    ]
                );
        }
        $collection->setCurPage(
            $this->getStart()
        )->setPageSize(
            $this->getLimit()
        )->setOrder(
            'entity_id',
            'DESC'
        )->load();

        foreach ($collection as $order) {
            $order = $this->orderRepository->get($order->getEntityId());
            $store = $order->getStore();
            $payment = $order->getPayment();
            $method = $payment->getMethodInstance();
            $methodTitle = $method->getTitle();
            $extraInfo = [
                'Customer Name' => $this->getCustomerName($order),
                'Email' => $order->getCustomerEmail(),
                'Customer Group' => $this->getCustomerGroupName($order),
                'Subtotal' => $this->priceHelper->currency($order->getSubtotal()),
                'Grand Total' => $this->priceHelper->currency($order->getGrandTotal()),
                'Order Status' => $order->getStatusLabel(),
                'Payment Method' => $methodTitle,
                'Invoice' => $this->getInvoicesData($order),
                'Shipment' => $this->getShipmentData($order),
                'Creditmemo' => $this->getCreditmemoData($order),
                'Order Date' => $order->getCreatedAt(),
                'Purchased From' =>
                    $store->getWebsite()->getName().", ".$store->getGroup()->getName().", ".$store->getName()
            ];

            $result[] = [
                'type' => __('Order'),
                'name' => __('Order #%1', $order->getIncrementId()),
                'extraInfo' => $extraInfo,
                'url' => $this->urlBuilder->getUrl('sales/order/view', ['order_id' => $order->getId()]),
            ];
        }

        $this->setResults($result);

        return $this;
    }
}
