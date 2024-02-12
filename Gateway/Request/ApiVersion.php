<?php

namespace Buckaroo\Magento2\Gateway\Request;

use Buckaroo\Magento2\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Buckaroo\Magento2\Model\Config\Source\ApiVersion as ApiVersionOptions;

class ApiVersion implements BuilderInterface
{
    /**
     * @param array $buildSubject
     * @return string[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function build(array $buildSubject)
    {
        $paymentDO = SubjectReader::readPayment($buildSubject);
        $payment = $paymentDO->getPayment();

        $apiVersion = $payment->getMethodInstance()->getConfigData('api_version');

        return ['version' => (int)$apiVersion ?? ApiVersionOptions::API_VERSION_1];
    }
}