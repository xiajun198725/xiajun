<?php
// ========== 无用的属性从相应表单中去除 ========== //
require_once '../app/Mage.php';
Mage::app ('admin');

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