<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * It is available through the world-wide-web at this URL:
 * https://tldrlegal.com/license/mit-license
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to support@buckaroo.nl so we can send you a copy immediately.
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

namespace Buckaroo\Magento2\Model\ConfigProvider\Method;

use Magento\Store\Model\ScopeInterface;

class Belfius extends AbstractConfigProvider
{
    const CODE = 'buckaroo_magento2_belfius';
    const XPATH_BELFIUS_PAYMENT_FEE           = 'payment/buckaroo_magento2_belfius/payment_fee';
    const XPATH_BELFIUS_PAYMENT_FEE_LABEL     = 'payment/buckaroo_magento2_belfius/payment_fee_label';
    const XPATH_BELFIUS_ACTIVE                = 'payment/buckaroo_magento2_belfius/active';
    const XPATH_BELFIUS_ACTIVE_STATUS         = 'payment/buckaroo_magento2_belfius/active_status';
    const XPATH_BELFIUS_ORDER_STATUS_SUCCESS  = 'payment/buckaroo_magento2_belfius/order_status_success';
    const XPATH_BELFIUS_ORDER_STATUS_FAILED   = 'payment/buckaroo_magento2_belfius/order_status_failed';
    const XPATH_BELFIUS_AVAILABLE_IN_BACKEND  = 'payment/buckaroo_magento2_belfius/available_in_backend';

    const XPATH_ALLOWED_CURRENCIES = 'payment/buckaroo_magento2_belfius/allowed_currencies';

    const XPATH_ALLOW_SPECIFIC                  = 'payment/buckaroo_magento2_belfius/allowspecific';
    const XPATH_SPECIFIC_COUNTRY                = 'payment/buckaroo_magento2_belfius/specificcountry';
    const XPATH_SPECIFIC_CUSTOMER_GROUP         = 'payment/buckaroo_magento2_belfius/specificcustomergroup';

    /**
     * @return array|void
     */
    public function getConfig()
    {
        $paymentFeeLabel = $this->getBuckarooPaymentFeeLabel(
            \Buckaroo\Magento2\Model\Method\Belfius::PAYMENT_METHOD_CODE
        );

        return [
            'payment' => [
                'buckaroo' => [
                    'belfius' => [
                        'paymentFeeLabel' => $paymentFeeLabel,
                        'allowedCurrencies' => $this->getAllowedCurrencies(),
                    ],
                ],
            ],
        ];
    }

    /**
     * @param null|int $storeId
     *
     * @return float
     */
    public function getPaymentFee($storeId = null)
    {
        $paymentFee = $this->scopeConfig->getValue(
            static::XPATH_BELFIUS_PAYMENT_FEE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $paymentFee ? $paymentFee : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentFeeLabel($store = null)
    {
        return $this->scopeConfig->getValue(
            static::XPATH_BELFIUS_PAYMENT_FEE_LABEL,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getActive($store = null)
    {
        return $this->scopeConfig->getValue(
            static::XPATH_BELFIUS_ACTIVE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveStatus($store = null)
    {
        return $this->scopeConfig->getValue(
            static::XPATH_BELFIUS_ACTIVE_STATUS,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderStatusSuccess($store = null)
    {
        return $this->scopeConfig->getValue(
            static::XPATH_BELFIUS_ORDER_STATUS_SUCCESS,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderStatusFailed($store = null)
    {
        return $this->scopeConfig->getValue(
            static::XPATH_BELFIUS_ORDER_STATUS_FAILED,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableInBackend($store = null)
    {
        return $this->scopeConfig->getValue(
            static::XPATH_BELFIUS_AVAILABLE_IN_BACKEND,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
