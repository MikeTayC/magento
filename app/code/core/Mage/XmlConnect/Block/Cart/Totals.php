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
 * Shopping cart totals xml renderer
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Cart_Totals extends Mage_Checkout_Block_Cart_Totals
{
   /**
     * Render cart totals xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        $totalsXmlObj   = new Mage_XmlConnect_Model_Simplexml_Element('<totals></totals>');
        $taxConfig      = Mage::getSingleton('tax/config');
        $displayInclTax = $displayBoth = false;

        foreach ($this->getQuote()->getTotals() as $total) {
            $code  = $total->getCode();
            if ($code == 'giftcardaccount') {
                continue;
            }
            $title = '';
            $value = null;
            $renderer = $this->_getTotalRenderer($code)->setTotal($total);
            switch ($code) {
                case 'subtotal':
                    if ($renderer->displayBoth()) {
                        $title = $this->helper('xmlconnect')->__('Subtotal (Excl. Tax)');
                        $this->_addTotalDataToXmlObj($totalsXmlObj, $code . '_excl_tax', $title, $total->getValueExclTax());

                        $code  = $code . '_incl_tax';
                        $title = $this->helper('xmlconnect')->__('Subtotal (Incl. Tax)');
                        $value = $total->getValueInclTax();
                    }
                    break;
                case 'shipping':
                    if ($renderer->displayBoth()) {
                        $title = $renderer->getExcludeTaxLabel();
                        $this->_addTotalDataToXmlObj($totalsXmlObj, $code . '_excl_tax', $title, $renderer->getShippingExcludeTax());

                        $code  = $code . '_incl_tax';
                        $title = $renderer->getIncludeTaxLabel();
                        $value = $renderer->getShippingIncludeTax();
                    } else if ($renderer->displayIncludeTax()) {
                        $value = $renderer->getShippingIncludeTax();
                    } else {
                        $value = $renderer->getShippingExcludeTax();
                    }
                    break;
                case 'grand_total':
                    $grandTotalExlTax = $renderer->getTotalExclTax();
                    $displayBoth = $renderer->includeTax() && $grandTotalExlTax >= 0;
                    if ($displayBoth) {
                        $title = $this->helper('xmlconnect')->__('Grand Total (Excl. Tax)');
                        $this->_addTotalDataToXmlObj($totalsXmlObj, $code . '_excl_tax', $title, $grandTotalExlTax);

                        $code  = $code . '_incl_tax';
                        $title = $this->helper('xmlconnect')->__('Grand Total (Incl. Tax)');
                    }
                    break;
                default:
                    break;
            }
            if ($title == '') {
                $title = $total->getTitle();
            }
            if (is_null($value)) {
                $value = $total->getValue();
            }
            $this->_addTotalDataToXmlObj($totalsXmlObj, $code, $title, $value);
        }

        return $totalsXmlObj->asNiceXml();
    }

    /**
     * Add total data to totals xml object
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $totalsXmlObj
     * @param string $code
     * @param string $title
     * @param float $value
     */
    protected function _addTotalDataToXmlObj($totalsXmlObj, $code, $title, $value)
    {
        $value = Mage::helper('xmlconnect')->formatPriceForXml($value);
        $totalXmlObj = $totalsXmlObj->addChild($code);
        $totalXmlObj->addChild('title', $totalsXmlObj->xmlentities(strip_tags($title)));
        $formatedValue = $this->getQuote()->getStore()->formatPrice($value, false);
        $totalXmlObj->addChild('value', $value);
        $totalXmlObj->addChild('formated_value', $formatedValue);
    }
}
