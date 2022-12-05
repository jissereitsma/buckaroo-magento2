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

class Creditcard extends AbstractConfigProvider
{
    public const CODE = 'buckaroo_magento2_creditcard';
    /**
     * Creditcard service codes.
     */
    private const CREDITCARD_SERVICE_CODE_MASTERCARD    = 'mastercard';
    private const CREDITCARD_SERVICE_CODE_VISA          = 'visa';
    private const CREDITCARD_SERVICE_CODE_AMEX          = 'amex';
    private const CREDITCARD_SERVICE_CODE_MAESTRO       = 'maestro';
    private const CREDITCARD_SERVICE_CODE_VPAY          = 'vpay';
    private const CREDITCARD_SERVICE_CODE_VISAELECTRON  = 'visaelectron';
    private const CREDITCARD_SERVICE_CODE_CARTEBLEUE    = 'cartebleuevisa';
    private const CREDITCARD_SERVICE_CODE_CARTEBANCAIRE = 'cartebancaire';
    private const CREDITCARD_SERVICE_CODE_DANKORT       = 'dankort';
    private const CREDITCARD_SERVICE_CODE_NEXI          = 'nexi';
    private const CREDITCARD_SERVICE_CODE_POSTEPAY      = 'postepay';

    public const XPATH_CREDITCARD_ALLOWED_CREDITCARDS  = 'payment/buckaroo_magento2_creditcard/allowed_creditcards';

    public const XPATH_CREDITCARD_MASTERCARD_UNSECURE_HOLD
        = 'payment/buckaroo_magento2_creditcard/mastercard_unsecure_hold';
    public const XPATH_CREDITCARD_VISA_UNSECURE_HOLD
        = 'payment/buckaroo_magento2_creditcard/visa_unsecure_hold';
    public const XPATH_CREDITCARD_MAESTRO_UNSECURE_HOLD
        = 'payment/buckaroo_magento2_creditcard/maestro_unsecure_hold';

    public const XPATH_CREDITCARD_SORT                 = 'payment/buckaroo_magento2_creditcard/sorted_creditcards';
    public const XPATH_SELECTION_TYPE                  = 'buckaroo_magento2/account/selection_type';
    public const XPATH_PAYMENT_FLOW                    = 'payment/buckaroo_magento2_creditcard/payment_action';
    public const DEFAULT_SORT_VALUE                    = '99';

    /** @var array[] */
    protected $issuers = [
        [
            'name' => 'American Express',
            'code' => self::CREDITCARD_SERVICE_CODE_AMEX,
            'sort' => 0
        ],
        [
            'name' => 'Carte Bancaire',
            'code' => self::CREDITCARD_SERVICE_CODE_CARTEBANCAIRE,
            'sort' => 0
        ],
        [
            'name' => 'Carte Bleue',
            'code' => self::CREDITCARD_SERVICE_CODE_CARTEBLEUE,
            'sort' => 0
        ],
        [
            'name' => 'Dankort',
            'code' => self::CREDITCARD_SERVICE_CODE_DANKORT,
            'sort' => 0
        ],
        [
            'name' => 'Maestro',
            'code' => self::CREDITCARD_SERVICE_CODE_MAESTRO,
            'sort' => 0
        ],
        [
            'name' => 'MasterCard',
            'code' => self::CREDITCARD_SERVICE_CODE_MASTERCARD,
            'sort' => 0
        ],
        [
            'name' => 'Nexi',
            'code' => self::CREDITCARD_SERVICE_CODE_NEXI,
            'sort' => 0
        ],
        [
            'name' => 'PostePay',
            'code' => self::CREDITCARD_SERVICE_CODE_POSTEPAY,
            'sort' => 0
        ],
        [
            'name' => 'VISA',
            'code' => self::CREDITCARD_SERVICE_CODE_VISA,
            'sort' => 0
        ],
        [
            'name' => 'VISA Electron',
            'code' => self::CREDITCARD_SERVICE_CODE_VISAELECTRON,
            'sort' => 0
        ],
        [
            'name' => 'VPay',
            'code' => self::CREDITCARD_SERVICE_CODE_VPAY,
            'sort' => 0
        ],
    ];

    /**
     * Add the active flag to the creditcard list. This is used in the checkout process.
     *
     * @return array
     */
    public function formatIssuers(): array
    {
        $sort = $this->getSort();

        if (!empty($sort)) {
            $sorted = explode(',', trim($sort));
            $sortedPosition = 1;
            foreach ($sorted as $cardName) {
                $sortedArray[$cardName] = $sortedPosition++;
            }
        }

        $issuers = parent::formatIssuers();
        foreach ($issuers as $item) {
            $item['sort'] = $sortedArray[$item['name']] ?? self::DEFAULT_SORT_VALUE;
            $allCreditcard[$item['code']] = $item;
        }

        $allowed = explode(',', (string)$this->getAllowedCreditcards());

        $cards = [];
        foreach ($allowed as $value) {
            if (isset($allCreditcard[$value])) {
                $cards[] = $allCreditcard[$value];
            }
        }

        usort($cards, function ($cardA, $cardB) {
            return $cardA['sort'] - $cardB['sort'];
        });

        return $cards;
    }

    /**
     * @inheritDoc
     */
    public function getConfig()
    {
        $issuers = $this->formatIssuers();
        $paymentFeeLabel = $this->getBuckarooPaymentFeeLabel(self::CODE);

        $selectionType = $this->scopeConfig->getValue(
            static::XPATH_SELECTION_TYPE,
            ScopeInterface::SCOPE_STORE
        );

        $paymentFlow = $this->scopeConfig->getValue(
            static::XPATH_PAYMENT_FLOW,
            ScopeInterface::SCOPE_STORE
        );

        return [
            'payment' => [
                'buckaroo' => [
                    'creditcard' => [
                        'cards' => $issuers,
                        'paymentFeeLabel' => $paymentFeeLabel,
                        'allowedCurrencies' => $this->getAllowedCurrencies(),
                        'selectionType' => $selectionType,
                        'paymentFlow' => $paymentFlow,
                    ],
                ],
            ],
        ];
    }

    /**
     * @param string $cardType
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function getCardName($cardType)
    {
        $config = $this->getConfig();

        foreach ($config['payment']['buckaroo']['creditcard']['cards'] as $card) {
            if ($card['code'] == $cardType) {
                return $card['name'];
            }
        }

        throw new \InvalidArgumentException("No card found for card type: {$cardType}");
    }

    /**
     * @param string $cardType
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function getCardCode($cardType)
    {
        $config = $this->getConfig();
        foreach ($config['payment']['buckaroo']['creditcard']['cards'] as $card) {
            if ($card['name'] == $cardType) {
                return $card['code'];
            }
        }

        throw new \InvalidArgumentException("No card found for card type: {$cardType}");
    }

    /**
     * @inheritDoc
     */
    public function getAllowedCreditcards($store = null)
    {
        return $this->scopeConfig->getValue(
            static::XPATH_CREDITCARD_ALLOWED_CREDITCARDS,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @inheritDoc
     */
    public function getMastercardUnsecureHold($store = null)
    {
        return $this->scopeConfig->getValue(
            static::XPATH_CREDITCARD_MASTERCARD_UNSECURE_HOLD,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @inheritDoc
     */
    public function getVisaUnsecureHold($store = null)
    {
        return $this->scopeConfig->getValue(
            static::XPATH_CREDITCARD_VISA_UNSECURE_HOLD,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @inheritDoc
     */
    public function getMaestroUnsecureHold($store = null)
    {
        return $this->scopeConfig->getValue(
            static::XPATH_CREDITCARD_MAESTRO_UNSECURE_HOLD,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @inheritDoc
     */
    public function getSort($store = null)
    {
        return $this->scopeConfig->getValue(
            static::XPATH_CREDITCARD_SORT,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
