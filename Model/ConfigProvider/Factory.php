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

namespace Buckaroo\Magento2\Model\ConfigProvider;

class Factory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var array
     */
    protected $configProviders;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array                                     $configProviders
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $configProviders = []
    ) {
        $this->objectManager = $objectManager;
        $this->configProviders = $configProviders;
    }

    /**
     * Retrieve proper transaction builder for the specified transaction type.
     *
     * @param string $providerType
     *
     * @return \Magento\Checkout\Model\ConfigProviderInterface
     * @throws \LogicException|\Buckaroo\Magento2\Exception
     */
    public function get($providerType)
    {
        if (empty($this->configProviders)) {
            throw new \LogicException('ConfigProvider adapter is not set.');
        }

        $providerType = str_replace('buckaroo_magento2_', '', $providerType);

        foreach ($this->configProviders as $configProviderMetaData) {
            $configProviderType = $configProviderMetaData['type'];
            if ($configProviderType == $providerType) {
                $configProviderClass = $configProviderMetaData['model'];
                break;
            }
        }

        if (!isset($configProviderClass) || empty($configProviderClass)) {
            throw new \Buckaroo\Magento2\Exception(
                new \Magento\Framework\Phrase(
                    'Unknown ConfigProvider type requested: %1.',
                    [$providerType]
                )
            );
        }

        $configProvider = $this->objectManager->get($configProviderClass);
        return $configProvider;
    }

    /**
     * @param $providerType
     *
     * @return bool
     *
     * @throws \LogicException
     */
    public function has($providerType)
    {
        if (empty($this->configProviders)) {
            throw new \LogicException('ConfigProvider adapter is not set.');
        }

        foreach ($this->configProviders as $configProviderMetaData) {
            $configProviderType = $configProviderMetaData['type'];
            if ($configProviderType == $providerType) {
                return true;
            }
        }

        return false;
    }
}
