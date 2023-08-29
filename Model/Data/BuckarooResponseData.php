<?php

namespace Buckaroo\Magento2\Model\Data;

use Buckaroo\Magento2\Api\Data\BuckarooResponseDataInterface;
use Buckaroo\Transaction\Response\TransactionResponse;
use Magento\Framework\Api\AbstractSimpleObject;

class BuckarooResponseData extends AbstractSimpleObject implements BuckarooResponseDataInterface
{
    /**
     * @var TransactionResponse
     */
    private TransactionResponse $transactionResponse;

    public function getResponse(): TransactionResponse
    {
        return $this->transactionResponse;
    }

    /**
     * @param TransactionResponse $transactionResponse
     * @return $this
     */
    public function setResponse(TransactionResponse $transactionResponse): BuckarooResponseData
    {
        $this->transactionResponse = $transactionResponse;
        return $this;
    }
}
