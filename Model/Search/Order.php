<?php
/**
 * @author MageDad Team
 * @copyright Copyright (c) 2023 Magedad (https://www.magedad.com)
 * @package Magento 2 Admin ChatBot
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model\Search;

use Magento\Backend\Model\UrlInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

class Order extends DataObject
{
    /**
     * @param CollectionFactory $collectionFactory
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

        //TODO: add full name logic
        $collection = $this->collectionFactory->create()
            ->addAttributeToSelect('entity_id')
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
            )->setCurPage(
                $this->getStart()
            )->setPageSize(
                $this->getLimit()
            )->setOrder(
                'entity_id',
                'DESC'
            )->load();

        foreach ($collection as $order) {
            $order = $this->orderRepository->get($order->getEntityId());
            $extraInfo = [
                'Customer Name' => $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname(),
                'Email' => $order->getCustomerEmail(),
                'Subtotal' => $this->priceHelper->currency($order->getSubtotal()),
                'Grand Total' => $this->priceHelper->currency($order->getGrandTotal()),
                'Created At' => $order->getCreatedAt(),
                'Status' => $order->getStatus()
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
