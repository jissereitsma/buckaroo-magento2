<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * It is available through the world-wide-web at this URL:
 * https://tldrlegal.com/license/mit-license
 * If you are unable to obtain it through the world-wide-web, please email
 * to support@buckaroo.nl, so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@buckaroo.nl for more information.
 *
 * @copyright Copyright (c) Buckaroo B.V.
 * @license   https://tldrlegal.com/license/mit-license
 */

namespace Buckaroo\Magento2\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\Invoice;
use Buckaroo\Magento2\Model\ConfigProvider\Account;
use Buckaroo\Magento2\Logging\BuckarooLoggerInterface;
use Buckaroo\Magento2\Helper\Data;
use Buckaroo\Magento2\Helper\PaymentGroupTransaction;

class GroupTransactionRegister implements ObserverInterface
{
    /**
     * @var Account
     */
    private $accountConfig;

    /**
     * @var InvoiceSender
     */
    private $invoiceSender;

    /**
     * @var BuckarooLoggerInterface
     */
    private BuckarooLoggerInterface $logger;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var PaymentGroupTransaction
     */
    private PaymentGroupTransaction $groupTransaction;

    /**
     * @param Account $accountConfig
     * @param InvoiceSender $invoiceSender
     * @param PaymentGroupTransaction $groupTransaction
     * @param BuckarooLoggerInterface $logger
     * @param Data $helper
     */
    public function __construct(
        Account $accountConfig,
        InvoiceSender $invoiceSender,
        PaymentGroupTransaction $groupTransaction,
        BuckarooLoggerInterface $logger,
        Data $helper
    ) {
        $this->accountConfig = $accountConfig;
        $this->invoiceSender = $invoiceSender;
        $this->groupTransaction = $groupTransaction;
        $this->logger = $logger;
        $this->helper = $helper;
    }

    /**
     * Set total paid by a group transaction for sales_order_invoice_pay event
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var Invoice $invoice */
        $invoice = $observer->getEvent()->getInvoice();
        $payment = $invoice->getOrder()->getPayment();

        if (strpos($payment->getMethod(), 'buckaroo_magento2') === false) {
            return;
        }

        $order = $invoice->getOrder();

        $items = $this->groupTransaction->getGroupTransactionItems($order->getIncrementId());
        foreach ($items as $item) {
            $this->logger->addDebug(sprintf(
                '[GROUP_TRANSACTION] | [Observer] | [%s:%s] - Set Order Total Paid | orderTotalPaid: %s',
                __METHOD__, __LINE__,
                var_export([$order->getTotalPaid(), $item['amount']], true)
            ));
            $totalPaid = $order->getTotalPaid() + $item['amount'];
            $baseTotalPaid = $order->getBaseTotalPaid() + $item['amount'];
            if (($totalPaid < $order->getGrandTotal())
                || ($this->helper->areEqualAmounts($totalPaid, $order->getGrandTotal()))
            ) {
                $order->setTotalPaid($totalPaid);
            }
            if (($baseTotalPaid < $order->getBaseGrandTotal())
                || ($this->helper->areEqualAmounts($baseTotalPaid, $order->getBaseGrandTotal()))
            ) {
                $order->setBaseTotalPaid($baseTotalPaid);
            }
        }
    }
}
