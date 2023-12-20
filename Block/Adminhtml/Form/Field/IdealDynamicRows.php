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

namespace Buckaroo\Magento2\Block\Adminhtml\Form\Field;

use Buckaroo\Magento2\Block\Adminhtml\Form\Field\ImageRenderer;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

class IdealDynamicRows extends AbstractFieldArray
{
    /**
     * @var ImageRenderer
     */
    private $imageRenderer;

    protected function _prepareToRender()
    {
        $this->addColumn('image', [
            'label'    => __('Image'),
            'renderer' => $this->getImageRenderer()
        ]);
        $this->addColumn('name', ['label' => __('Name')]);
        $this->addColumn('code', ['label' => __('Code')]);
        // Add more columns as needed

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Row');
    }

    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $class = 'draggable-item'; // CSS class for draggable rows

        $row->setData('extra_params', sprintf('class="%s"', $class));
        $row->setData('class', $class);
    }

    /**
     * Get the JavaScript to enable dragging for rows
     *
     * @return string
     */
    public function getAfterElementHtml()
    {
        $js = "
        <script>
            require(['jquery', 'jquery/ui'], function($) {
                $(document).ready(function() {
                    $('.admin__control-dynamic-rows tbody').sortable({
                        items: '.draggable-item',
                        cursor: 'move',
                        axis: 'y',
                        opacity: 0.7,
                        revert: true,
                        tolerance: 'pointer',
                        forcePlaceholderSize: true,
                        placeholder: 'sortable-placeholder',
                        cancel: 'input,textarea,button,select,option,[role=\"button\"]'
                    });
                });
            });
        </script>
    ";

        return $js;
    }

    /**
     * @return ImageRenderer
     * @throws LocalizedException
     */
    private function getImageRenderer()
    {
        if (!$this->imageRenderer) {
            $this->imageRenderer = $this->getLayout()->createBlock(
                ImageRenderer::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->imageRenderer;
    }
}
