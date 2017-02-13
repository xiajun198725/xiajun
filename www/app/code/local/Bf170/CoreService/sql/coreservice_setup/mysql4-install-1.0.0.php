<?php
/*
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User Software Agreement (EULA).
 * It is also available through the world-wide-web at this URL:
 * http://www.bf170.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to eula@bf170.com so we can send you a copy immediately.
 * 
 */

$installer = $this;
$installer->startSetup();

$coreWrite = Mage::getSingleton('core/resource')->getConnection('core_write');
$customerEavSetup = new Mage_Customer_Model_Entity_Setup();

// ========== 用户：firstname 填入姓名，lastname不再使用，无用的属性从相应表单中去除 ========== //
$customerEavSetup->updateAttribute('customer', 'lastname', 'is_required', 0);
$customerEavSetup->updateAttribute('customer', 'lastname', 'validate_rules', NULL);
$customerEavSetup->updateAttribute('customer_address', 'lastname', 'is_required', 0);
$customerEavSetup->updateAttribute('customer_address', 'lastname', 'validate_rules', NULL);
Mage::getSingleton('eav/config')
		->getAttribute('customer', 'prefix')
		->setData('used_in_forms', array())
		->save();
Mage::getSingleton('eav/config')
		->getAttribute('customer', 'middlename')
		->setData('used_in_forms', array())
		->save();
Mage::getSingleton('eav/config')
		->getAttribute('customer', 'lastname')
		->setData('used_in_forms', array())
		->save();
Mage::getSingleton('eav/config')
		->getAttribute('customer', 'suffix')
		->setData('used_in_forms', array())
		->save();
Mage::getSingleton('eav/config')
		->getAttribute('customer', 'taxvat')
		->setData('used_in_forms', array())
		->save();

// ========== 用户：电话 ========== //
$installer->run("
ALTER TABLE {$this->getTable('customer/entity')}
	ADD COLUMN `telephone` varchar(255) COMMENT '电话' AFTER `email`,
	ADD INDEX `INDEX_CUSTOMER_TELEPHONE` (`telephone`);
");
$telephoneData = array(
        'type'			=> 'static',
        'label'			=> '电话',
        'input'			=> 'text',
        'visible'		=> true,
        'required'		=> true,
        'user_defined'	=> false,
		'sort_order'	=> 85,
		'position'      => 85,
);
$customerEavSetup->addAttribute(
		'customer', 
		'telephone', 
		$telephoneData
);
Mage::getSingleton('eav/config')
		->getAttribute('customer', 'telephone')
		->setData('used_in_forms', array('adminhtml_customer', 'checkout_register', 'customer_account_create', 'customer_account_edit'))
		->save();

// ========== 用户地址：省份可能是下拉菜单或字符串（两者选一，但是每项单独不必填），邮编可选，无用的属性从相应表单中去除 ========== //
$customerEavSetup->updateAttribute('customer_address', 'postcode', 'is_required', 0);
Mage::getSingleton('eav/config')
		->getAttribute('customer_address', 'prefix')
		->setData('used_in_forms', array())
		->save();
Mage::getSingleton('eav/config')
		->getAttribute('customer_address', 'middlename')
		->setData('used_in_forms', array())
		->save();
Mage::getSingleton('eav/config')
		->getAttribute('customer_address', 'lastname')
		->setData('used_in_forms', array())
		->save();
Mage::getSingleton('eav/config')
		->getAttribute('customer_address', 'suffix')
		->setData('used_in_forms', array())
		->save();
Mage::getSingleton('eav/config')
		->getAttribute('customer_address', 'vat_id')
		->setData('used_in_forms', array())
		->save();

// ========== 用户地址：添加省份列表 ========== //
$installer->run("		
	INSERT INTO `{$this->getTable('directory/country_region')}` (`country_id`,`code`,`default_name`) VALUES 
		('CN','BJ','北京市'),
		('CN','TJ','天津市'),
		('CN','HE','河北省'),
		('CN','SX','山西省'),
		('CN','NM','内蒙古自治区'),
		('CN','LN','辽宁省'),
		('CN','JL','吉林省'),
		('CN','HL','黑龙江省'),
		('CN','SH','上海市'),
		('CN','JS','江苏省'),
		('CN','ZJ','浙江省'),
		('CN','AH','安徽省'),
		('CN','FJ','福建省'),
		('CN','JX','江西省'),
		('CN','SD','山东省'),
		('CN','HA','河南省'),
		('CN','HB','湖北省'),
		('CN','HN','湖南省'),
		('CN','GD','广东省'),
		('CN','GX','广西壮族自治区'),
		('CN','HI','海南省'),
		('CN','CQ','重庆市'),
		('CN','SC','四川省'),
		('CN','GZ','贵州省'),
		('CN','YN','云南省'),
		('CN','XZ','西藏自治区'),
		('CN','SN','陕西省'),
		('CN','GS','甘肃省'),
		('CN','QH','青海省'),
		('CN','NX','宁夏回族自治区'),
		('CN','XJ','新疆维吾尔自治区'),
		('CN','HK','香港特别行政区'),
		('CN','MO','澳门特别行政区'),
		('CN','TW','台湾省');
");
$stateConfigPath = 'general/region/state_required';
Mage::helper('coreservice')->saveConfig($stateConfigPath, 'CN');

//Force reset DDL Cache after installation
$this->getConnection()->allowDdlCache();
$this->getConnection()->resetDdlCache();
$this->getConnection()->disallowDdlCache();