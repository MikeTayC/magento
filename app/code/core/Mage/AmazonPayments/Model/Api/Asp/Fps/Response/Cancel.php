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
 * AmazonPayments FPS response Model, cancel 
 *
 * @category   Mage
 * @package    Mage_AmazonPayments
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_AmazonPayments_Model_Api_Asp_Fps_Response_Cancel extends Mage_AmazonPayments_Model_Api_Asp_Fps_Response_Abstract
{
    /**
     * rewrited for Mage_AmazonPayments_Model_Api_Asp_Fps_Response_Abstract 
     */
    protected function parse($responseBody)
    {
        if ($responseBody->getName() != 'CancelResponse') {
            return false;
        }
        
        $transactionId = (string)$responseBody->CancelResult->TransactionId;
        $transactionStatus = (string)$responseBody->CancelResult->TransactionStatus;
        
        if($transactionId == '' || $transactionStatus == '') {
           return false;   
        }
        
        $this->setData('TransactionId', $transactionId);
        $this->setData('Status', $transactionStatus);
        
        return parent::parse($responseBody);
    }

    /**
     * Return response TransactionId
     *
     * @return string
     */
    public function getTransactionId()
    {
        return $this->getData('TransactionId');     
    }
}
