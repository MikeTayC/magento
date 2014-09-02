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
 * @package     Mage_Core
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Core file uploader model
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_File_Uploader extends Varien_File_Uploader
{
    /**
     * Save file to storage
     *
     * @param  array $result
     * @return Mage_Core_Model_File_Uploader
     */
    protected function _afterSave($result)
    {
        if (!isset($result['path']) || empty($result['path'])
            || !isset($result['file']) || empty($result['file'])
        ) {
            return $this;
        }

        $helper = Mage::helper('core/file_storage');

        if ($helper->isInternalStorage()) {
            return $this;
        }

        $dbHelper = Mage::helper('core/file_storage_database');
        $dbHelper->saveFile($result['path'] . $result['file']);

        return $this;
    }
}
