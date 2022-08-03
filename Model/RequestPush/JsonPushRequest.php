<?php

namespace Buckaroo\Magento2\Model\RequestPush;

use Buckaroo\Magento2\Api\PushRequestInterface;
use Buckaroo\Magento2\Exception;
use Buckaroo\Magento2\Model\Validator\PushSDK as Validator;

/**
 * @method getDatarequest()
 * @method getAmountCredit()
 * @method getRelatedtransactionRefund()
 * @method getInvoicekey()
 * @method getSchemekey()
 * @method getServiceCreditmanagement3Invoicekey()
 */
class JsonPushRequest extends AbstractPushRequest implements PushRequestInterface
{
    private array $request = [];
    private array $originalRequest;
    /**
     * @var Validator $validator
     */
    private Validator $validator;
    /**
     * @throws Exception
     */
    public function __construct(array $requestData, Validator $validator)
    {
        $this->originalRequest = $requestData;
        if(isset($requestData['Transaction'])) {
            $this->request = $requestData['Transaction'];
        } else {
            throw new Exception(__('Json request could not be processed, please use httppost'));
        }
        $this->validator  = $validator;

    }

    /**
     * @param $store
     * @return bool
     */
    public function validate($store = null): bool
    {
       return $this->validator->validate($this->getData());
    }

    public function get($name){
        if(method_exists($this, $name)){
            return $this->$name();
        }
        elseif(property_exists($this,$name)){
            return $this->$name;
        } else {
            $propertyName = ucfirst($name);
            if(isset($this->request[$propertyName])) {
                return $this->request[$propertyName];
            }
        }
        return null;
    }

    public function getData(): array
    {
        return $this->request;
    }

    public function getOriginalRequest()
    {
        return $this->originalRequest;
    }

    public function getAmount()
    {
        return $this->request["AmountDebit"] ?? null;
    }

    public function getAmountDebit()
    {
        return $this->request["AmountDebit"] ?? null;
    }

    public function getCurrency()
    {
        return $this->request["Currency"] ?? null;
    }

    public function getCustomerName()
    {
        return $this->request["CustomerName"] ?? null;
    }

    public function getDescription()
    {
        return $this->request["Description"] ?? null;
    }

    public function getInvoiceNumber()
    {
        return $this->request["Invoice"] ?? null;
    }

    public function getMutationType()
    {
        return $this->request["MutationType"] ?? null;
    }

    public function getOrderNumber()
    {
        return $this->request["Order"] ?? null;
    }

    public function getPayerHash()
    {
        return $this->request["PayerHash"] ?? null;
    }

    public function getPayment()
    {
        return $this->request["PaymentKey"] ?? null;
    }

    public function getStatusCode()
    {
        return $this->request["Status"]["Code"]["Code"] ?? null;
    }

    public function getStatusCodeDetail()
    {
        return $this->request["Status"]["SubCode"]["Code"]?? null;
    }

    public function getStatusMessage()
    {
        return $this->request["Status"]["SubCode"]["Description"] ?? null;
    }

    public function getTransactionMethod()
    {
        return $this->request["ServiceCode"] ?? null;
    }

    public function getTransactionType()
    {
        return $this->request["TransactionType"] ?? null;
    }

    public function getTransactions()
    {
        return $this->request["Key"] ?? null;
    }

    public function getAdditionalInformation($propertyName)
    {
        if(isset($this->request['AdditionalParameters']['List']) && is_array($this->request['AdditionalParameters']['List']))
        {
            foreach ($this->request['AdditionalParameters']['List'] as $parameter) {
                if(isset($parameter['Name']) && $parameter['Name'] == $propertyName) {
                    return $parameter['Value'] ?? null;
                }
            }
        }

        return null;
    }

    public function isTest()
    {
        return $this->request["IsTest"] ?? null;
    }

    public function setTransactions($transactions)
    {
        $this->request['Key'] = $transactions;
    }

    public function setAmount($amount) {
        $this->request['AmountDebit'] = $amount;
    }

}