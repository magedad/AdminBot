<?php
/**
 * @author MageDad Team
 * @copyright Copyright (c) 2023 Magedad (https://www.magedad.com)
 * @package Magento 2 Admin ChatBot
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model\Search;

use Magento\Framework\DataObject;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Backend\Model\UrlInterface;
use Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory;

class Invoice extends DataObject
{
    /**
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

        $collection = $this->collectionFactory->create();
        $collection
            ->addAttributeToSelect('entity_id')
            ->addAttributeToSelect('increment_id')
            ->addAttributeToSelect('subtotal')
            ->addAttributeToSelect('grand_total')
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

        foreach ($collection as $invoice) {
            $orderUrl = $this->urlBuilder->getUrl->getUrl('sales/order/view', ['order_id' => $invoice->getOrderId()]);
            $extraInfo = [
                'Order Id' => "<a href='" . $orderUrl . "'>#" . $invoice->getOrderIncrementId() . "</a>",
                'Customer Name' => $invoice->getCustomerFirstname() . ' ' . $invoice->getCustomerLastname(),
                'Email' => $invoice->getCustomerEmail(),
                'Subtotal' => $this->priceHelper->currency($invoice->getSubtotal()),
                'Grand Total' => $this->priceHelper->currency($invoice->getGrandTotal()),
                'Created At' => $invoice->getCreatedAt()
            ];

            $result[] = [
                'id' => $invoice->getId(),
                'type' => $collection->getSize() > 0 ? __('Invoices') : __('Invoice'),
                'name' => __('Invoice #%1', $invoice->getIncrementId()),
                'extraInfo' => $extraInfo,
                'url' => $this->urlBuilder->getUrl->getUrl('sales/invoice/view', ['invoice_id' => $invoice->getId()]),
            ];
        }

        $this->setResults($result);

        return $this;
    }
}
