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
 * AmazonPayments ASP payment Model
 *
 * @category   Mage
 * @package    Mage_AmazonPayments
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_AmazonPayments_Model_Payment_Asp extends Mage_Payment_Model_Method_Abstract
{
    /**
     * rewrited for Mage_Payment_Model_Method_Abstract
     */
    protected $_isGateway               = false;
    protected $_canAuthorize            = false;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = false;
    protected $_isInitializeNeeded      = true;

    /**
     * rewrited for Mage_Payment_Model_Method_Abstract
     */
    protected $_formBlockType = 'amazonpayments/asp_form';

    /**
     * rewrited for Mage_Payment_Model_Method_Abstract
     */
    protected $_code  = 'amazonpayments_asp';

    /**
     * current order
     */
    protected $_order;

    /**
     * Get value from the module config
     *
     * @param string $path
     * @return string
     */
    public function getConfig($path)
    {
        return Mage::getStoreConfig('payment/' . $this->_code . '/' . $path);
    }


    /**
     * Get singleton with AmazonPayments ASP API Model
     *
     * @return Mage_AmazonPayments_Model_Api_Asp
     */
    public function getApi()
    {
        return Mage::getSingleton('amazonpayments/api_asp');
    }

    /**
     * Get singleton with AmazonPayments ASP Notification Model
     *
     * @return Mage_AmazonPayments_Model_Payment_Asp_Notification
     */
    public function getNotification()
    {
        return Mage::getSingleton('amazonpayments/payment_asp_notification');
    }

    /**
     * Set model of current order
     *
     * @param Mage_Sales_Model_Order $order
     * @return Mage_AmazonPayments_Model_Payment_Asp
     */
    public function setOrder($order)
    {
        $this->_order = $order;
        return $this;
    }

    /**
     * Get model of current order
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (!$this->_order) {
            $paymentInfo = $this->getInfoInstance();
            $this->_order = Mage::getModel('sales/order')->loadByIncrementId(
                $paymentInfo->getOrder()->getRealOrderId()
            );
        }
        return $this->_order;
    }

    /**
     * @deprecated after 1.4.1.0
     *
     * @param string $request
     * @param string $response
     * @return Mage_AmazonPayments_Model_Payment_Asp
     */
    protected function _log($request, $response = '')
    {
        $this->_debug(array('request' => $request, 'response' => $response));
        return $this;
    }

    /**
     * Send mail
     *
     * @param string $template
     * @param array $variables
     * @return Mage_AmazonPayments_Model_Payment_Asp
     */
    protected function _mail($template, array $variables = array())
    {
        $mailTemplate = Mage::getModel('core/email_template');
        $mailTemplate->setDesignConfig(array('area' => 'frontend'))
                    ->sendTransactional(
                        $this->getConfig($template),
                        $this->getConfig('email_sender_identity'),
                        $this->getConfig('report_email'),
                        null,
                        $variables
                    );
        return $this;
    }

    /**
     * rewrited for Mage_Payment_Model_Method_Abstract
     */
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('amazonpayments/asp/pay');
    }

    /**
     * Return Amazon Simple Pay payment url
     *
     * @return string
     */
    public function getPayRedirectUrl()
    {
        return $this->getApi()->getPayUrl();
    }

    /**
     * Return choice method description
     *
     * @return string
     */
    public function getChoiceMethodDescription()
    {
        return $this->getConfig('choice_method_description');
    }

    /**
     * Return redirect message
     *
     * @return string
     */
    public function getRedirectMessage()
    {
        return $this->getConfig('redirect_message');
    }

    /**
     * Return pay params for current order
     *
     * @return array
     */
    public function getPayRedirectParams()
    {
        $orderId = $this->getOrder()->getRealOrderId();
        $amount = Mage::app()->getStore()->roundPrice($this->getOrder()->getBaseGrandTotal());
        $currencyCode = $this->getOrder()->getBaseCurrencyCode();

        $urlModel = Mage::getModel('core/url')
            ->setUseSession(false);

        return $this->getApi()->getPayParams(
            $orderId,
            $amount,
            $currencyCode,
            $urlModel->getUrl('amazonpayments/asp/returnCancel'),
            $urlModel->getUrl('amazonpayments/asp/returnSuccess'),
            $urlModel->getUrl('amazonpayments/asp/notification')
            );
    }

    /**
     * When a customer redirect to Amazon Simple Pay site
     *
     * @return Mage_AmazonPayments_Model_Payment_Asp
     */
    public function processEventRedirect()
    {
        $this->getOrder()->addStatusToHistory(
           $this->getOrder()->getStatus(),
           Mage::helper('amazonpayments')->__('Customer was redirected to Amazon Simple Pay site')
        )->save();
        return $this;
    }

    /**
     * When a customer successfully returned from Amazon Simple Pay site
     *
     * @return Mage_AmazonPayments_Model_Payment_Asp
     */
    public function processEventReturnSuccess()
    {
        $this->getOrder()->sendNewOrderEmail();
        $this->getOrder()->addStatusToHistory(
           $this->getOrder()->getStatus(),
           Mage::helper('amazonpayments')->__('The customer has successfully returned from Amazon Simple Pay site')
        )->save();
        return $this;
    }

    /**
     * Customer canceled payment and successfully returned from Amazon Simple Pay site
     *
     * @return Mage_AmazonPayments_Model_Payment_Asp
     */
    public function processEventReturnCancel()
    {
        $this->getOrder()->addStatusToHistory(
           $this->getOrder()->getStatus(),
           Mage::helper('amazonpayments')->__('The customer has canceled payment and successfully returned from Amazon Simple Pay site')
        )->save();
        return $this;
    }

    /**
     * rewrited for Mage_Payment_Model_Method_Abstract
     */
    public function initialize($paymentAction, $stateObject)
    {
        $state = Mage_Sales_Model_Order::STATE_NEW;
        $stateObject->setState($state);
        $stateObject->setStatus(Mage::getSingleton('sales/order_config')->getStateDefaultStatus($state));
        $stateObject->setIsNotified(false);
        return $this;
    }

    /**
     * process Amazon Simple Pay notification request
     *
     * @param   array $requestParams
     * @return bool
     */
    public function processNotification($requestParams)
    {
        $this->_debug(array('request' => $requestParams));
        try {
           $this->getNotification()->setPayment($this)->process($requestParams);
        }
        catch(Exception $e) {

            $this->_debug(array('request' => $requestParams, 'error' => $e->getMessage()));
            if ($this->getConfig('report_error_to_email')) {
                $variables = array();
                $variables['request'] = print_r($requestParams, 1);
                $variables['error'] = $e->getMessage();
                $this->_mail('email_template_notification_error', $variables);
            }

            return false;
        }

        return true;
    }

    /**
     * rewrited for Mage_Payment_Model_Method_Abstract
     */
    public function capture(Varien_Object $payment, $amount)
    {
        if (is_null($payment->getCcTransId())) {
            Mage::throwException(
                Mage::helper('amazonpayments')->__('The order was not captured online. Authorization confirmation is required.')
            );
        }
        return $this;
    }

    /**
     * rewrited for Mage_Payment_Model_Method_Abstract
     */
    public function processInvoice($invoice, $payment)
    {
        if (!is_null($payment->getCcTransId()) &&
            is_null($payment->getLastTransId()) &&
            is_null($invoice->getTransactionId())) {

            $amount = Mage::app()->getStore()->roundPrice($invoice->getBaseGrandTotal());
            $currencyCode = $payment->getOrder()->getBaseCurrencyCode();
            $transactionId = $payment->getCcTransId();
            $response = $this->getApi()
                ->setStoreId($payment->getOrder()->getStoreId())
                ->capture($transactionId, $amount, $currencyCode);

            if ($response->getStatus() == Mage_AmazonPayments_Model_Api_Asp_Fps_Response_Abstract::STATUS_ERROR) {
                Mage::throwException(
                    Mage::helper('amazonpayments')->__('The order was not captured. Amazon Simple Pay service error: [%s] %s', $response->getCode(), $response->getMessage())
                );
            }

            if ($response->getStatus() == Mage_AmazonPayments_Model_Api_Asp_Fps_Response_Abstract::STATUS_SUCCESS ||
                $response->getStatus() == Mage_AmazonPayments_Model_Api_Asp_Fps_Response_Abstract::STATUS_PENDING) {

                $payment->setForcedState(Mage_Sales_Model_Order_Invoice::STATE_OPEN);
                $payment->setLastTransId($response->getTransactionId());

                $invoice->setTransactionId($response->getTransactionId());
                $invoice->addComment(Mage::helper('amazonpayments')->__('The invoice was created (online capture). Waiting for capture confirmation from Amazon Simple Pay service.'));

                $payment->getOrder()->addStatusToHistory(
                  $payment->getOrder()->getStatus(),
                  Mage::helper('amazonpayments')->__('The payment was captured online with Amazon Simple Pay service. The invoice was created. Waiting for capture confirmation from the payment service.')
                );

            }
        }
        return $this;
    }

    /**
     * rewrited for Mage_Payment_Model_Method_Abstract
     */
    public function processCreditmemo($creditmemo, $payment)
    {

        $transactionId = $creditmemo->getInvoice()->getTransactionId();

        if (!is_null($transactionId) &&
            is_null($creditmemo->getTransactionId())) {

            $amount = Mage::app()->getStore()->roundPrice($creditmemo->getBaseGrandTotal());
            $currencyCode = $payment->getOrder()->getBaseCurrencyCode();
            $referenseID = $creditmemo->getInvoice()->getIncrementId();
            $response = $this->getApi()
                ->setStoreId($payment->getOrder()->getStoreId())
                ->refund($transactionId, $amount, $currencyCode, $referenseID);

            if ($response->getStatus() == Mage_AmazonPayments_Model_Api_Asp_Fps_Response_Abstract::STATUS_ERROR) {
                Mage::throwException(
                    Mage::helper('amazonpayments')->__('The invoice was not refunded. Amazon Simple Pay service error: [%s] %s', $response->getCode(), $response->getMessage())
                );
            }

            if ($response->getStatus() == Mage_AmazonPayments_Model_Api_Asp_Fps_Response_Abstract::STATUS_SUCCESS ||
                $response->getStatus() == Mage_AmazonPayments_Model_Api_Asp_Fps_Response_Abstract::STATUS_PENDING) {

                $creditmemo->setTransactionId($response->getTransactionId());
                $creditmemo->addComment(Mage::helper('amazonpayments')->__('The payment was refunded online. Waiting for refund confirmation from Amazon Simple Pay service.'));
                $creditmemo->setState(Mage_Sales_Model_Order_Creditmemo::STATE_OPEN);

                $payment->getOrder()->addStatusToHistory(
                  $payment->getOrder()->getStatus(),
                  Mage::helper('amazonpayments')->__('The payment was refunded online with Amazon Simple Pay service. The credit memo was created. Waiting for refund confirmation from Amazon Simple Pay service.')
                );
            }
        }
        return $this;
    }

    /**
     * rewrited for Mage_Payment_Model_Method_Abstract
     */
    public function cancel(Varien_Object $payment)
    {
        if (!is_null($payment->getCcTransId()) &&
            is_null($payment->getLastTransId())) {

            $transactionId = $payment->getCcTransId();
            $response = $this->getApi()
                ->setStoreId($payment->getOrder()->getStoreId())
                ->cancel($transactionId);

            if ($response->getStatus() == Mage_AmazonPayments_Model_Api_Asp_Fps_Response_Abstract::STATUS_ERROR) {
                Mage::throwException(
                    Mage::helper('amazonpayments')->__('The order was not canceled. Amazon Simple Pay service error: [%s] %s', $response->getCode(), $response->getMessage())
                );
            }

            if ($response->getStatus() == Mage_AmazonPayments_Model_Api_Asp_Fps_Response_Abstract::STATUS_CANCELLED) {
                $payment->getOrder()->setState(
                    Mage_Sales_Model_Order::STATE_CANCELED,
                    true,
                    Mage::helper('amazonpayments')->__('Payment authorization canceled with Amazon Simple Pay service.'),
                    $notified = false
                );
            }
        }
        return $this;
    }

    /**
     * Define if debugging is enabled
     *
     * @return bool
     */
    public function getDebugFlag()
    {
        return $this->getConfigData('debug_log');
    }
}
