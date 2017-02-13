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
abstract class Bf170_CoreService_Model_Object extends Mage_Core_Model_Abstract {
    
	// ======================== Public Actions ======================== //
	public function validate() {
    	//Throw Mage exceptions if necessary
    	return $this;
    }
    
	// ======================== Utilities: Loader ======================== //
    public function loadByAttributeFilter($attributeFilter, $isLast = false){
   		$result = $this->getResource()->loadByAttributeFilter($attributeFilter, $isLast);
		$this->addData($result);
		return $this;
    }
    
}