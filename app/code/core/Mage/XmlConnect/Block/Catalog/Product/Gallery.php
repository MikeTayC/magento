<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Product images gallery block
 *
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_XmlConnect_Block_Catalog_Product_Gallery extends Mage_XmlConnect_Block_Catalog
{

    /**
     * Generate images gallery xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        $productId = $this->getRequest()->getParam('id', null);
        $product = Mage::getModel('catalog/product')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($productId);
        $collection = $product->getMediaGalleryImages();

        $imagesNode = new Mage_XmlConnect_Model_Simplexml_Element('<images></images>');
        $helper = $this->helper('catalog/image');

        foreach ($collection as $item) {
            $imageNode = $imagesNode->addChild('image');

            /**
             * Big image
             */
            $bigImage = $helper->init($product, 'image', $item->getFile())
                ->resize(Mage::helper('xmlconnect/image')->getImageSizeForContent('product_gallery_big'));

            $fileNode = $imageNode->addChild('file');
            $fileNode->addAttribute('type', 'big');
            $fileNode->addAttribute('url', $bigImage);

            $file = Mage::helper('xmlconnect')->urlToPath($bigImage);

            $fileNode->addAttribute('id', ($id = $item->getId()) ? (int) $id : 0);
            $fileNode->addAttribute('modification_time', filemtime($file));

            /**
             * Small image
             */
            $smallImage = $helper->init($product, 'thumbnail', $item->getFile())
                ->resize(Mage::helper('xmlconnect/image')->getImageSizeForContent('product_gallery_small'));

            $fileNode = $imageNode->addChild('file');
            $fileNode->addAttribute('type', 'small');
            $fileNode->addAttribute('url', $smallImage);

            $file = Mage::helper('xmlconnect')->urlToPath($smallImage);
            $fileNode->addAttribute('modification_time', filemtime($file));
        }
        return $imagesNode->asNiceXml();
    }
}
