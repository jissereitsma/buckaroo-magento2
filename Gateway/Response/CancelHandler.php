<?php

namespace Buckaroo\Magento2\Gateway\Response;

use Buckaroo\Magento2\Helper\Data;
use Buckaroo\Transaction\Response\TransactionResponse;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Framework\Event\ManagerInterface;

class CancelHandler extends AbstractResponseHandler implements HandlerInterface
{
    /**
     * @var bool
     */
    public $closeCancelTransaction = true;

    /**
     * @throws LocalizedException
     */
    public function handle(array $handlingSubject, array $response)
    {
        if (
            !isset($handlingSubject['payment'])
            || !$handlingSubject['payment'] instanceof PaymentDataObjectInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        if (
            !isset($response['object'])
            || !$response['object'] instanceof TransactionResponse
        ) {
            throw new \InvalidArgumentException('Data must be an instance of "TransactionResponse"');
        }

        $this->transactionResponse = $response['object'];

        $payment = $handlingSubject['payment']->getPayment();
        $arrayResponse = $this->transactionResponse->toArray();

        $this->saveTransactionData($this->transactionResponse, $payment, $this->closeCancelTransaction, true);

        $payment->setAdditionalInformation('voided_by_buckaroo', true);

        // SET REGISTRY BUCKAROO REDIRECT
        $this->addToRegistry('buckaroo_response', $arrayResponse);

        $this->afterVoid($payment, $response);
    }

    /**
     * @param OrderPaymentInterface|InfoInterface $payment
     * @param array|\StdCLass $response
     *
     * @return $this
     */
    protected function afterVoid($payment, $response)
    {
        return $this->dispatchAfterEvent('buckaroo_magento2_method_void_after', $payment, $response);
    }
}
