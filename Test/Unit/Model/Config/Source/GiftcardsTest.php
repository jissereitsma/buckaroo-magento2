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
 * @copyright Copyright (c) 2016 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license   http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\Buckaroo\Test\Unit\Model\Config\Source;

use TIG\Buckaroo\Model\Config\Source\Giftcards;
use TIG\Buckaroo\Test\BaseTest;

class GiftcardsTest extends BaseTest
{
    protected $instanceClass = Giftcards::class;

    /**
     * @return array
     */
    public function toOptionArrayProvider()
    {
        return [
            'no giftcards' => [
                [],
                [
                    [
                        'value' => '',
                        'label' => __('You have not yet added any giftcards')
                    ]
                ]
            ],
            'single giftcard' => [
                [
                    [
                        'servicecode' => 'code1',
                        'label' => 'giftcard 1'
                    ]
                ],
                [
                    [
                        'value' => 'code1',
                        'label' => 'giftcard 1'
                    ]
                ]
            ],
            'multiple giftcard' => [
                [
                    [
                        'servicecode' => 'code2',
                        'label' => 'giftcard 2'
                    ],
                    [
                        'servicecode' => 'code3',
                        'label' => 'giftcard 3'
                    ],
                    [
                        'servicecode' => 'code4',
                        'label' => 'giftcard 4'
                    ]
                ],
                [
                    [
                        'value' => 'code2',
                        'label' => 'giftcard 2'
                    ],
                    [
                        'value' => 'code3',
                        'label' => 'giftcard 3'
                    ],
                    [
                        'value' => 'code4',
                        'label' => 'giftcard 4'
                    ]
                ]
            ]
        ];
    }

    /**
     * @param $giftcardData
     * @param $expected
     *
     * @dataProvider toOptionArrayProvider
     */
    public function testToOptionArray($giftcardData, $expected)
    {
        $sortOrderBuilderMock = $this->getFakeMock(\Magento\Framework\Api\SortOrderBuilder::class)
            ->setMethods(['setField', 'setAscendingDirection', 'create'])
            ->getMock();
        $sortOrderBuilderMock->expects($this->once())->method('setField')->with('label')->willReturnSelf();
        $sortOrderBuilderMock->expects($this->once())->method('setAscendingDirection')->willReturnSelf();
        $sortOrderBuilderMock->expects($this->once())->method('create')->willReturnSelf();

        $searchCriteriaMock = $this->getFakeMock(\Magento\Framework\Api\SearchCriteria::class)->getMock();

        $searchCriteriaBuilderMock = $this->getFakeMock(\Magento\Framework\Api\SearchCriteriaBuilder::class)
            ->setMethods(['create'])
            ->getMock();
        $searchCriteriaBuilderMock->expects($this->once())->method('create')->willReturn($searchCriteriaMock);

        $modelsResult = [];

        foreach ($giftcardData as $giftcard) {
            $modelMock = $this->getFakeMock(\TIG\Buckaroo\Api\Data\GiftcardInterface::class)->getMock();
            $modelMock->expects($this->once())->method('getServicecode')->willReturn($giftcard['servicecode']);
            $modelMock->expects($this->once())->method('getLabel')->willReturn($giftcard['label']);
            $modelsResult[] = $modelMock;
        }

        $searchResult = $this->getObject(\Magento\Framework\Api\SearchResults::class);
        $searchResult->setItems($modelsResult);
        $searchResult->setTotalCount(count($modelsResult));

        $giftcardRepositoryMock = $this->getFakeMock(\TIG\Buckaroo\Api\GiftcardRepositoryInterface::class)->getMock();
        $giftcardRepositoryMock->expects($this->once())
            ->method('getList')
            ->with($searchCriteriaMock)
            ->willReturn($searchResult);

        $instance = $this->getInstance([
            'sortOrderBuilder' => $sortOrderBuilderMock,
            'searchCriteriaBuilder' => $searchCriteriaBuilderMock,
            'giftcardRepository' => $giftcardRepositoryMock
        ]);
        $result = $instance->toOptionArray();

        $this->assertInternalType('array', $result);
        $this->assertEquals($expected, $result);
    }
}
