<?php

namespace Buckaroo\Magento2\Block\Adminhtml\Form\Field;

use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\View\Asset\Repository as AssetRepository;

class ImageRenderer extends \Magento\Backend\Block\Template
{
    protected $_template = 'Buckaroo_Magento2::form/field/image.phtml';

    /**
     * The asset repository to generate the correct url to our assets.
     *
     * @var AssetRepository
     */
    protected AssetRepository $assetRepo;

    public function __construct(
        AssetRepository $assetRepo,
        \Magento\Backend\Block\Template\Context $context,
        array $data = [],
        ?JsonHelper $jsonHelper = null,
        ?DirectoryHelper $directoryHelper = null
    ) {
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);
        $this->assetRepo = $assetRepo;
    }

    public function render(\Magento\Framework\DataObject $row)
    {
        $this->assign('row', $row);
        return $this->toHtml();
    }

    public function getIdealIssuerImage($imageName)
    {
        return $this->getImageUrl("ideal/{$imageName}", "svg");
    }

    /**
     * Generate the url to the desired asset.
     *
     * @param string $imgName
     * @param string $extension
     *
     * @return string
     */
    public function getImageUrl($imgName, string $extension = 'png')
    {
        return $this->assetRepo->getUrl("Buckaroo_Magento2::images/{$imgName}.{$extension}");
    }
}