<?php

namespace Buckaroo\Magento2\Gateway\Validator;

use Buckaroo\Magento2\Model\ConfigProvider\Factory as ConfigProviderFactory;
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Buckaroo\Magento2\Model\ConfigProvider\Method\AbstractConfigProvider;
use Magento\Framework\App\State;
use Magento\Framework\App\Area;

class AvailableBasedOnCurrencyValidator extends AbstractValidator
{
    /**
     * Available Based on Currency
     *
     * @param array $validationSubject
     * @return ResultInterface
     * @throws \Buckaroo\Magento2\Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validate(array $validationSubject)
    {
        $isValid = false;

        if (!isset($validationSubject['paymentMethodInstance']) || !isset($validationSubject['quote'])) {
            return $this->createResult(
                false,
                [__('Payment method instance does not exist')]
            );
        }

        $allowedCurrenciesRaw = $validationSubject['paymentMethodInstance']->getConfigData('allowed_currencies');
        $allowedCurrencies = explode(',', (string)$allowedCurrenciesRaw);

        $currentCurrency = $validationSubject['quote']->getCurrency()->getQuoteCurrencyCode();

        if ($allowedCurrenciesRaw === null || in_array($currentCurrency, $allowedCurrencies)) {
            $isValid = true;
        }

        return $this->createResult($isValid);
    }
}
