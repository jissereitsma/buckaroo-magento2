<?php

namespace Buckaroo\Magento2\Gateway\Helper;

use Magento\Payment\Model\MethodInterface;
use Magento\Quote\Model\Quote;

class SubjectReader extends \Magento\Payment\Gateway\Helper\SubjectReader
{
    /**
     * Reads payment method instance from subject
     *
     * @param array $subject
     * @return MethodInterface
     */
    public static function readPaymentMethodInstance(array $subject): MethodInterface
    {
        if (!isset($subject['paymentMethodInstance'])
            || !$subject['paymentMethodInstance'] instanceof MethodInterface
        ) {
            throw new \InvalidArgumentException('Payment data object should be provided');
        }

        return $subject['paymentMethodInstance'];
    }

    /**
     * Reads quote from subject
     *
     * @param array $subject
     * @return Quote
     */
    public static function readQuote(array $subject): Quote
    {
        if (!isset($subject['quote'])
            || !$subject['quote'] instanceof Quote
        ) {
            throw new \InvalidArgumentException('Quote data object should be provided.');
        }

        return $subject['quote'];
    }
}
