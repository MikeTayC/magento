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
 * Bundle product options xml renderer
 *
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_XmlConnect_Block_Catalog_Product_Options_Bundle extends Mage_XmlConnect_Block_Catalog_Product_Options
{
    /**
     * Generate bundle product options xml
     *
     * @param Mage_Catalog_Model_Product $product
     * @param bool $isObject
     * @return string | Mage_XmlConnect_Model_Simplexml_Element
     */
    public function getProductOptionsXml(Mage_Catalog_Model_Product $product, $isObject = false)
    {

        $xmlModel = $this->getProductCustomOptionsXmlObject($product);
        $optionsXmlObj = $xmlModel->options;

        if (!$product->isSaleable()) {
            return $isObject ? $xmlModel : $xmlModel->asNiceXml();
        }

        /**
         * Bundle options
         */
        $product->getTypeInstance(true)->setStoreFilter($product->getStoreId(), $product);
        $optionCollection = $product->getTypeInstance(true)->getOptionsCollection($product);
        $selectionCollection = $product->getTypeInstance(true)->getSelectionsCollection(
            $product->getTypeInstance(true)->getOptionsIds($product),
            $product
        );
        $bundleOptions = $optionCollection->appendSelections($selectionCollection, false, false);
        if (!sizeof($bundleOptions)) {
            return $isObject ? $xmlModel : $xmlModel->asNiceXml();
        }

        foreach ($bundleOptions as $_option) {
            $selections = $_option->getSelections();
            if (empty($selections)) {
                continue;
            }

            $optionNode = $optionsXmlObj->addChild('option');

            $type = parent::OPTION_TYPE_SELECT;
            if ($_option->isMultiSelection()) {
                $type = parent::OPTION_TYPE_CHECKBOX;
            }
            $code = 'bundle_option[' . $_option->getId() . ']';
            if ($type == parent::OPTION_TYPE_CHECKBOX) {
                $code .= '[]';
            }
            $optionNode->addAttribute('code', $code);
            $optionNode->addAttribute('type', $type);
            $optionNode->addAttribute('label', $optionsXmlObj->xmlentities(strip_tags($_option->getTitle())));
            if ($_option->getRequired()) {
                $optionNode->addAttribute('is_required', 1);
            }

//            $_default = $_option->getDefaultSelection();

            foreach ($selections as $_selection) {
                if (!$_selection->isSaleable()) {
                    continue;
                }
                $_qty = !($_selection->getSelectionQty() * 1) ? '1' : $_selection->getSelectionQty() * 1;

                $valueNode = $optionNode->addChild('value');
                $valueNode->addAttribute('code', $_selection->getSelectionId());
                $valueNode->addAttribute('label', $optionsXmlObj->xmlentities(strip_tags($_selection->getName())));
                if (!$_option->isMultiSelection()) {
                    if ($_selection->getSelectionCanChangeQty()) {
                        $valueNode->addAttribute('is_qty_editable', 1);
                    }
                }
                $valueNode->addAttribute('qty', $_qty);

                $price = $product->getPriceModel()->getSelectionPreFinalPrice($product, $_selection);
                $price = Mage::helper('xmlconnect')->formatPriceForXml($price);
                if ($price > 0.00) {
                    $valueNode->addAttribute('price', $price);
                    $valueNode->addAttribute('formated_price', $this->_formatPriceString($price, $product));
                }

//              $_selection->getIsDefault();
            }
        }

        return $isObject ? $xmlModel : $xmlModel->asNiceXml();
    }
}
