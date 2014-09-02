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
 * Amazon Payments Checkout by Amazon Success Block
 *
 * @category    Mage
 * @package     Mage_AmazonPayments
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_AmazonPayments_Block_Cba_Success extends Mage_Core_Block_Abstract
{
    /**
     * Success page
     *
     * @return string
     */
    protected function _toHtml()
    {
        echo "<p><strong>{$this->__('Thank you for your purchase!') }</strong></p>\n";
        echo $this->__('You will receive an order confirmation email with details of your order and a link to track its progress.<br/>');


        return '';
    }

}
