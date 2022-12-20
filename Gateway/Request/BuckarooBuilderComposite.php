<?php

namespace Buckaroo\Magento2\Gateway\Request;

use Buckaroo\Magento2\Service\DataBuilderService;
use Magento\Framework\ObjectManager\TMap;
use Magento\Framework\ObjectManager\TMapFactory;
use Magento\Payment\Gateway\Request\BuilderInterface;

class BuckarooBuilderComposite implements BuilderInterface
{
    /**
     * @var BuilderInterface[] | TMap
     */
    private $builders;

    /**
     * @var DataBuilderService
     */
    private $dataBuilderService;

    /**
     * @param TMapFactory $tmapFactory
     * @param DataBuilderService $dataBuilderService
     * @param array $builders
     */
    public function __construct(
        TMapFactory $tmapFactory,
        DataBuilderService $dataBuilderService,
        array $builders = []
    ) {
        $this->builders = $tmapFactory->create(
            [
                'array' => $builders,
                'type' => BuilderInterface::class
            ]
        );
        $this->dataBuilderService = $dataBuilderService;
    }

    /**
     * Builds ENV request
     *
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        foreach ($this->builders as $key => $builder) {
            // @TODO implement exceptions catching
            $this->dataBuilderService->addData($builder->build($buildSubject));
        }

        return $this->dataBuilderService->getData();
    }
}
