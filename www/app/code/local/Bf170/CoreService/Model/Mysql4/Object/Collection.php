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

abstract class Bf170_CoreService_Model_Mysql4_Object_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
	
	public function addAttributeFilters($attributeFilter){
		$select = $this->getSelect();
		foreach($attributeFilter as $attrCode => $attrValue){
			if(is_array($attrValue)){
				$attrValue = array_unique($attrValue);
				$select->where("{$attrCode} IN(?)", $attrValue);
			}else{
				$attrCode = trim($attrCode);
				$attrValue = trim($attrValue);
				$select->where("{$attrCode} = ?", $attrValue);
			}
		}
		return $this;
    }
	
	public function addCustomerAttributeFilters($customerAttrList, $customerIdAttrName = "customer_id"){
    	$staticAttrList = array();
    	foreach($customerAttrList as $attrCode){
    		$customerAttr = $this->_getCustomerAttrbiteuByCode($attrCode);
    		if(!$customerAttr || !$customerAttr->getId()){
    			Mage::throwException('Invalid attribute filter for region collection.');
    		}
    		if($customerAttr->getBackendType() == 'static'){
    			$staticAttrList[$attrCode] = $customerAttr;
    		}else{
    			$tableName = "customer_entity_{$customerAttr->getBackendType()}";
    			$tableAlias = "ce_{$customerAttr->getBackendType()}_{$attrCode}";
    			$this->getSelect()->joinLeft(
						array($tableAlias => $tableName),
						"({$tableAlias}.entity_id = main_table.{$customerIdAttrName}) AND ({$tableAlias}.attribute_id = {$customerAttr->getId()})",
						array("customer_{$attrCode}" => "{$tableAlias}.value")
				);
    		}
    	}
    	
    	// Append static attributes
    	if(!empty($staticAttrList)){
	    	$staticTableName = Mage::getSingleton('core/resource')->getTableName('customer/entity');
	    	$staticTableAlias = "ce";
	    	$staticColumnArray = array();
	    	foreach($staticAttrList as $attrCode => $staticAttr){
	    		$staticColumnArray["customer_{$attrCode}"] = "{$staticTableAlias}.{$attrCode}";
	    	}
	    	$this->getSelect()->joinLeft(
					array($staticTableAlias => $staticTableName),
					"({$staticTableAlias}.entity_id = main_table.{$customerIdAttrName})",
					$staticColumnArray
			);
    	}
		return $this;
    }
    
    protected function _getCustomerAttrbiteuByCode($attrCode){
    	if(!isset($this->_customerAttributeIdMapping[$attrCode])){
    		$customerAttr = Mage::getModel('eav/entity_attribute')->loadByCode('customer', $attrCode);
    		$this->_customerAttributeIdMapping[$attrCode] = $customerAttr;
    	}
    	return $this->_customerAttributeIdMapping[$attrCode];
    }
    
	public function joinAdminUserTable($adminUserAttrList = array()){
		$adminUserTableName = Mage::getSingleton('core/resource')->getTableName('admin/user');
	    $adminUserTableAlias = "au";
	    if(empty($adminUserAttrList)){
	    	$adminUserAttrList = array('firstname');
	    }
	    foreach($adminUserAttrList as $attrCode){
	    	$adminUserColumnArray["admin_user_{$attrCode}"] = "{$adminUserTableAlias}.{$attrCode}";
	    }
	    $this->getSelect()->joinLeft(
				array($adminUserTableAlias => $adminUserTableName),
				"({$adminUserTableAlias}.user_id = main_table.admin_user_id)",
				$adminUserColumnArray
		);
		return $this;
    }
    
}