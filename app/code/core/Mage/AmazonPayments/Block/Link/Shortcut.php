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
 * @package     Mage_AmazonPayments
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Paypal shortcut link
 *
 * @category   Mage
 * @package    Mage_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_AmazonPayments_Block_Link_Shortcut extends Mage_Core_Block_Template
{
    public function getCheckoutUrl()
    {
        return $this->getUrl('amazonpayments/cba/shortcut');
    }

    public function getImageUrl()
    {
        return Mage::getStoreConfig('payment/amazonpayments_cba/button_url');
    }

    public function _toHtml()
    {
        if (Mage::getStoreConfigFlag('payment/amazonpayments_cba/active')
            && Mage::getSingleton('checkout/session')->getQuote()->validateMinimumAmount()) {
            return parent::_toHtml();
        }

        return '';
    }

    public function getCbaOneClickJsUrl()
    {
        if (Mage::getStoreConfigFlag('payment/amazonpayments_cba/sandbox_flag')) {
            $url = 'https://images-na.ssl-images-amazon.com/images/G/01/cba/js/widget/sandbox/widget.js';
        } else {
            $url = 'https://images-na.ssl-images-amazon.com/images/G/01/cba/js/widget/widget.js';
        }
        return $url;
    }

    public function getCbaStylesheetUrl()
    {
        $url = 'https://images-na.ssl-images-amazon.com/images/G/01/cba/styles/one-click.css';
        return $url;
    }

    public function getCbaJquerySetupUrl()
    {
        $url = 'https://images-na.ssl-images-amazon.com/images/G/01/cba/js/jquery.js';
        return $url;
    }

    /**
     * Return true if 1-Click is enabled
     *
     * @return boolean
     */
    public function getIsOneClickEnabled()
    {
        return Mage::getStoreConfigFlag('payment/amazonpayments_cba/use_oneclick');
    }

}
