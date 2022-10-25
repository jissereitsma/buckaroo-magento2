<?php

namespace Buckaroo\Magento2\Gateway\Http\Client;

use Buckaroo\Magento2\Gateway\Http\Client\AbstractTransaction;
use Buckaroo\Transaction\Response\TransactionResponse;

class TransactionAuthorizeEncrypted extends AbstractTransaction
{
    /**
     * @inheritdoc
     */
    protected function process(string $paymentMethod,  array $data): TransactionResponse
    {
        return $this->adapter->authorizeEncrypted($paymentMethod, $data);
    }
}
