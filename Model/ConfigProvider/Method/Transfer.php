<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright Copyright (c) 2014 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license   http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */

namespace TIG\Buckaroo\Model\ConfigProvider\Method;

/**
 * @method getDueDate()
 * @method getSendEmail()
 */
class Transfer extends AbstractConfigProvider
{
    const XPATH_TRANSFER_ACTIVE                 = 'payment/tig_buckaroo_transfer/active';
    const XPATH_TRANSFER_PAYMENT_FEE            = 'payment/tig_buckaroo_transfer/payment_fee';
    const XPATH_TRANSFER_PAYMENT_FEE_LABEL      = 'payment/tig_buckaroo_transfer/payment_fee_label';
    const XPATH_TRANSFER_SEND_EMAIL             = 'payment/tig_buckaroo_transfer/send_email';
    const XPATH_TRANSFER_ACTIVE_STATUS          = 'payment/tig_buckaroo_transfer/active_status';
    const XPATH_TRANSFER_ORDER_STATUS_SUCCESS   = 'payment/tig_buckaroo_transfer/order_status_success';
    const XPATH_TRANSFER_ORDER_STATUS_FAILED    = 'payment/tig_buckaroo_transfer/order_status_failed';
    const XPATH_TRANSFER_AVAILABLE_IN_BACKEND   = 'payment/tig_buckaroo_transfer/available_in_backend';
    const XPATH_TRANSFER_DUE_DATE               = 'payment/tig_buckaroo_transfer/due_date';

    const XPATH_ALLOWED_CURRENCIES = 'payment/tig_buckaroo_transfer/allowed_currencies';

    /**
     * @return array
     */
    public function getConfig()
    {
        if (!$this->scopeConfig->getValue(self::XPATH_TRANSFER_ACTIVE)) {
            return [];
        }

        $paymentFeeLabel = $this->getBuckarooPaymentFeeLabel(\TIG\Buckaroo\Model\Method\Transfer::PAYMENT_METHOD_CODE);

        return [
            'payment' => [
                'buckaroo' => [
                    'transfer' => [
                        'sendEmail' => (bool) $this->getSendEmail(),
                        'paymentFeeLabel' => $paymentFeeLabel,
                        'allowedCurrencies' => $this->getAllowedCurrencies(),
                    ]
                ]
            ]
        ];
    }

    /**
     * @return float
     */
    public function getPaymentFee()
    {
        $paymentFee = $this->scopeConfig->getValue(
            self::XPATH_TRANSFER_PAYMENT_FEE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $paymentFee ? $paymentFee : false;
    }
}
