<?php

namespace Buckaroo\Magento2\Gateway\Request;

use Buckaroo\Magento2\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

class ApplepayDataBuilder implements BuilderInterface
{

    /**
     * @inheritDoc
     */
    public function build(array $buildSubject): array
    {
        $paymentDO = SubjectReader::readPayment($buildSubject);

        return [
            'paymentData' => base64_encode(
                (string)$paymentDO->getPayment()->getAdditionalInformation('applepayTransaction')
            ),
            'customerCardName' => $this->getCustomerCardName(),
        ];
    }

    /**
     * Get customer card name from applepay transaction
     *
     * @return string|null
     */
    protected function getCustomerCardName()
    {
        $billingContact = json_decode(
            (string)$this->getPayment()->getAdditionalInformation('billingContact')
        );
        if (
            $billingContact &&
            !empty($billingContact->givenName) &&
            !empty($billingContact->familyName)
        ) {
            return $billingContact->givenName . ' ' . $billingContact->familyName;
        }
    }
}
