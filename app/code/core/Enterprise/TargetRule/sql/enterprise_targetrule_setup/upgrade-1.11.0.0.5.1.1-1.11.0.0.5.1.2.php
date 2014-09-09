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
 * @category    Enterprise
 * @package     Enterprise_TargetRule
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/* @var $installer Enterprise_TargetRule_Model_Resource_Setup */
$installer = $this;

$connection = $installer->getConnection();
$tableRelated = $installer->getTable('enterprise_targetrule/index_related_product');
$tableUpsell = $installer->getTable('enterprise_targetrule/index_upsell_product');
$tableCrosssell = $installer->getTable('enterprise_targetrule/index_crosssell_product');

$installer->startSetup();

$connection->addColumn($tableRelated, 'position', 'int NOT NULL default 0');
$connection->addColumn($tableUpsell, 'position', 'int NOT NULL default 0');
$connection->addColumn($tableCrosssell, 'position', 'int NOT NULL default 0');

$installer->endSetup();
