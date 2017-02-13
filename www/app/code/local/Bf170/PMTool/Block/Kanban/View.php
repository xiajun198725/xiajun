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
 
class Bf170_PMTool_Block_Kanban_View extends Mage_Core_Block_Template {
	
	public function getKanban(){
		return Mage::registry('current_kanban');
	}
	
	public function getKanbanUser(){
		return Mage::registry('current_kanban_user');
	}
	
	public function getProcessList(){
		return $this->getKanban()->prepareProcessList();
	}
	
	public function getDelBuzhou(){
		return Mage::registry('current_kanban_buzhou');
	}
	
}