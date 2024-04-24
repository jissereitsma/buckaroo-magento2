<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * It is available through the world-wide-web at this URL:
 * https://tldrlegal.com/license/mit-license
 * If you are unable to obtain it through the world-wide-web, please email
 * to support@buckaroo.nl, so we can send you a copy immediately.
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
declare(strict_types=1);

namespace Buckaroo\Magento2\Service\Applepay;

use Buckaroo\Magento2\Logging\BuckarooLoggerInterface;
use Buckaroo\Magento2\Model\ConfigProvider\Method\Applepay;
use Buckaroo\Magento2\Model\Service\ApplePayFormatData;
use Buckaroo\Magento2\Model\Service\QuoteService;

class Add
{
    /**
     * @var BuckarooLoggerInterface
     */
    private BuckarooLoggerInterface $logger;

    /**
     * @var QuoteService
     */
    private QuoteService $quoteService;

    /**
     * @var ApplePayFormatData
     */
    private ApplePayFormatData $applePayFormatData;

    /**
     * @param BuckarooLoggerInterface $logger
     * @param QuoteService $quoteService
     * @param ApplePayFormatData $applePayFormatData
     */
    public function __construct(
        BuckarooLoggerInterface $logger,
        QuoteService $quoteService,
        ApplePayFormatData $applePayFormatData
    ) {
        $this->logger = $logger;
        $this->quoteService = $quoteService;
        $this->applePayFormatData = $applePayFormatData;
    }

    /**
     * Add Product to Cart on Apple Pay
     *
     * @param array $request
     * @return array|false
     */
    public function process(array $request)
    {
        try {
            // Get Cart
            $cartHash = $request['id'] ?? null;
            $this->quoteService->getEmptyQuote($cartHash);

            // Add product to cart
            $product = $this->applePayFormatData->getProductObject($request['product']);
            $this->quoteService->addProductToCart($product);

            $shippingMethodsResult = [];
            if($this->quoteService->getQuote()->getIsVirtual()) {
                // Get Shipping Address From Request
                $shippingAddressRequest = $this->applePayFormatData->getShippingAddressObject($request['wallet']);

                // Add Shipping Address on Quote
                $this->quoteService->addAddressToQuote($shippingAddressRequest);

                // Add Shipping Address on Quote
                $this->quoteService->addAddressToQuote($shippingAddressRequest);

                // Get Shipping Methods
                $shippingMethods = $this->quoteService->getAvailableShippingMethods();

                foreach ($shippingMethods as $shippingMethod) {
                    $shippingMethodsResult[] = [
                        'carrier_title' => $shippingMethod['carrier_title'],
                        'price_incl_tax' => round($shippingMethod['amount'], 2),
                        'method_code' => $shippingMethod['carrier_code'] . '_' .  $shippingMethod['method_code'],
                        'method_title' => $shippingMethod['method_title'],
                    ];
                }
                $this->quoteService->setShippingMethod($shippingMethodsResult[0]['method_code']);
            }

            //Set Payment Method
            $this->quoteService->setPaymentMethod(Applepay::CODE);

            // Calculate Quote Totals
            $this->quoteService->calculateQuoteTotals();

            // Get Totals
            $totals = $this->quoteService->gatherTotals();

            return [
                'shipping_methods' => $shippingMethodsResult,
                'totals'           => $totals
            ];
        } catch (\Exception $exception) {
            $this->logger->addError(sprintf(
                '[ApplePay] | [Controller] | [%s:%s] - Add Product to Cart on Apple Pay | [ERROR]: %s',
                __METHOD__,
                __LINE__,
                $exception->getMessage()
            ));
            return false;
        }
    }
}
