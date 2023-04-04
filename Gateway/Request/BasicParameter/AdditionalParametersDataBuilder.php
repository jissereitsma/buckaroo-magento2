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

namespace Buckaroo\Magento2\Gateway\Request\BasicParameter;

use Magento\Payment\Gateway\Request\BuilderInterface;

class AdditionalParametersDataBuilder implements BuilderInterface
{
    /**
     * @var string
     */
    private string $action;

    /**
     * @var array
     */
    private array $additionalParameters;

    /**
     * Constructor
     *
     * @param string $action
     * @param array $additionalParameters
     */
    public function __construct(
        string $action,
        array $additionalParameters = []
    ) {
        $this->action = $action;
        $this->additionalParameters = $additionalParameters;
    }

    /**
     * Set service action
     *
     * @param array $buildSubject
     * @return array[]
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function build(array $buildSubject): array
    {
        return [
            'additionalParameters' => $this->getAdditionalParameters()
        ];
    }

    /**
     * Get additional parameters
     *
     * @return array
     */
    private function getAdditionalParameters(): array
    {
        $parameterLine = [];
        if (!empty($this->getAction())) {
            $parameterLine['service_action_from_magento'] = strtolower($this->getAction());
        }

        $parameterLine['initiated_by_magento'] = 1;

        if ($additionalParameters = $this->getAllAdditionalParameters()) {
            foreach ($additionalParameters as $key => $value) {
                $parameterLine[$key] = $value;
            }
        }

        return $parameterLine;
    }

    /**
     * Get service action
     *
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * Set service action
     *
     * @param string $action
     * @return $this
     */
    public function setAction(string $action): AdditionalParametersDataBuilder
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get all additional parameters
     *
     * @return array
     */
    public function getAllAdditionalParameters(): array
    {
        return $this->additionalParameters;
    }

    /**
     * Set additional parameter with key
     *
     * @param string $key
     * @param string $value
     * @return $this
     */
    public function setAdditionalParameter(string $key, string $value): AdditionalParametersDataBuilder
    {
        $this->additionalParameters[$key] = $value;

        return $this;
    }

    /**
     * Get additional parameter by key
     *
     * @param string $key
     * @return string|null
     */
    public function getAdditionalParameter(string $key): ?string
    {
        return $this->additionalParameters[$key] ?? null;
    }
}
