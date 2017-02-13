<?php 

/* NOTICE OF LICENSE
 *
 * This source file is subject to the End User Software Agreement (EULA).
 * It is also available through the world-wide-web at this URL:
 * http://www.harapartners.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to eula@harapartners.com so we can send you a copy immediately.
 */ 
 
class Bf170_PMTool_Block_Kanban_CustomerData_CustomerMessage extends Mage_Core_Block_Template {
	
	protected $_kanbanCustoerDataCollection		= null;

	public function getkanbanCustoerDataCollection(){
		if($this->_kanbanCustoerDataCollection === null){
			$_customerId = array();
			$customers = Mage::getModel('customer/customer')->getCollection();
			foreach($customers as $custoemr){
				$_customerId[$custoemr->getId()] = $custoemr->getId();
			}
			$this->_kanbanCustoerDataCollection = $_customerId;
		}
		return $this->_kanbanCustoerDataCollection;
	}
	
}