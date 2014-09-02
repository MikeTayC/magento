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
 * AmazonPayments FPS request Model, settle
 *
 * @category   Mage
 * @package    Mage_AmazonPayments
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_AmazonPayments_Model_Api_Asp_Fps_Request_Settle extends Mage_AmazonPayments_Model_Api_Asp_Fps_Request_Abstract
{
    /**
     * rewrited for Mage_AmazonPayments_Model_Api_Asp_Fps_Request_Abstract 
     */
    public function isValid() 
    {
        if (!$this->getData('ReserveTransactionId')) {
            return false;
        }
        return parent::isValid();
    }    

    /**
     * Set request transactionId 
     *
     * @param string $transactionId
     * @return Mage_AmazonPayments_Model_Api_Asp_Fps_Request_Settle
     */
    public function setTransactionId($transactionId)
    {
        return $this->setData('ReserveTransactionId', $transactionId);
    }

    /**
     * Set request amount
     *
     * @param Mage_AmazonPayments_Model_Api_Asp_Amount $amount
     * @return Mage_AmazonPayments_Model_Api_Asp_Fps_Request_Settle
     */
    public function setAmount($amount)
    {
        return $this->setData('TransactionAmount.Value', $amount->getValue())
                    ->setData('TransactionAmount.CurrencyCode', $amount->getCurrencyCode());
    }
}
