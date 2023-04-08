<?php
/**
 * @author MageDad Team
 * @copyright Copyright (c) 2023 Magedad (https://www.magedad.com)
 * @package Magento 2 Admin ChatBot
 */
declare(strict_types=1);

namespace MageDad\AdminBot\Model\Search\Sales;

use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Backend\Model\UrlInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\Invoice\CollectionFactory;

class SalesSearch extends DataObject
{
    /**
     * Construct
     *
     * @param Data $priceHelper
     * @param GroupRepositoryInterface $groupRepository
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        Data $priceHelper,
        GroupRepositoryInterface $groupRepository = null,
        UrlInterface $urlBuilder = null
    ) {
        $this->priceHelper = $priceHelper;
        $this->groupRepository =
            $groupRepository ?: ObjectManager::getInstance()->get(GroupRepositoryInterface::class);
        $this->urlBuilder =
            $urlBuilder ?: ObjectManager::getInstance()->get(UrlInterface::class);
    }

    /**
     * Get Invoices Data
     *
     * @param Order $order
     * @return string
     */
    protected function getInvoicesData(Order $order)
    {
        $invoices = [];
        foreach ($order->getInvoiceCollection() as $invoice) {
            $invoices[] =
                '<a href="'
                    .$this->urlBuilder->getUrl('sales/invoice/view', ['invoice_id' => $invoice->getId()]).'">'
                    .$invoice->getIncrementId()
                .'</a>';
        }

        return count($invoices) > 0 ? implode(',', $invoices) : 'N/A';
    }

    /**
     * Get Shipment Data
     *
     * @param Order $order
     * @return string
     */
    protected function getShipmentData(Order $order)
    {
        $shipments = [];
        foreach ($order->getShipmentsCollection() as $shipment) {
            $shipments[] =
                '<a href="'
                    .$this->urlBuilder->getUrl('sales/shipment/view', ['shipment_id' => $shipment->getId()]).'">'
                    .$shipment->getIncrementId()
                .'</a>';
        }

        return count($shipments) > 0 ? implode(',', $shipments) : 'N/A';
    }

    /**
     * Get Creditmemo Data
     *
     * @param Order $order
     * @return string
     */
    protected function getCreditmemoData(Order $order)
    {
        $creditmemos = [];
        foreach ($order->getCreditmemosCollection() as $creditmemo) {
            $creditmemos[] =
                '<a href="'
                    .$this->urlBuilder->getUrl('sales/creditmemo/view', ['creditmemo_id' => $creditmemo->getId()]).'">'
                    .$creditmemo->getIncrementId()
                .'</a>';
        }

        return count($creditmemos) > 0 ? implode(',', $creditmemos) : 'N/A';
    }

    /**
     * Return name of the customer group.
     *
     * @param Order $order
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getCustomerGroupName(Order $order)
    {
        if ($order) {
            $customerGroupId = $order->getCustomerGroupId();
            try {
                if ($customerGroupId !== null) {
                    return $this->groupRepository->getById($customerGroupId)->getCode();
                }
            } catch (NoSuchEntityException $e) {
                return '';
            }
        }

        return '';
    }

    /**
     * Get URL to edit the customer.
     *
     * @param Order $order
     * @return string
     */
    protected function getCustomerName(Order $order)
    {
        $customerName = $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname();
        if ($order->getCustomerIsGuest() || !$order->getCustomerId()) {
            return $customerName;
        }

        return '<a href="'.$this->urlBuilder->getUrl('customer/index/edit', ['id' => $order->getCustomerId()]).'">'
                .$customerName
            .'</a>' ;
    }
}
