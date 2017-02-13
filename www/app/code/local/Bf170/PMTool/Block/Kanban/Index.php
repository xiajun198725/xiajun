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
 
class Bf170_PMTool_Block_Kanban_Index extends Mage_Core_Block_Template {
	
	protected $_kanbanCollection		= null;

	public function getKanbanCollection(){
		if($this->_kanbanCollection === null){
			$customerId = Mage::getSingleton('customer/session')->getCustomerId();
			$this->_kanbanCollection = Mage::getModel('pmtool/kanban')->getCollection();
			$this->_kanbanCollection->loadAllByCustomerId($customerId);
		}
		return $this->_kanbanCollection;
	}
	
}