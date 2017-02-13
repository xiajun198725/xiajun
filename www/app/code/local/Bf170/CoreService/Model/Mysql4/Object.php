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

abstract class Bf170_CoreService_Model_Mysql4_Object extends Mage_Core_Model_Mysql4_Abstract {
	
	public function loadByAttributeFilter($attributeFilter, $isLast = false){
   		$read = $this->_getReadAdapter();
		$select = $read->select()
				->from($this->getMainTable());
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
		if(!!$isLast){
			$select->order('entity_id DESC');
		}
		$result = $read->fetchRow($select);
		if(!$result){
			return array();
		}
		return $result;
    }
    
}