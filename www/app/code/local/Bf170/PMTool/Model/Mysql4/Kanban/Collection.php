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

class Bf170_PMTool_Model_Mysql4_Kanban_Collection extends Bf170_CoreService_Model_Mysql4_Object_Collection {
	
	public function _construct() {
		parent::_construct();
		$this->_init('pmtool/kanban');
	}
	
	public function loadAllByCustomerId($customerId){
		$userTableName = Mage::getSingleton('core/resource')->getTableName('pmtool/kanban_user');
		$userTableAlias = 'ku';
		$columnArray = array(
				'user_customer_id'	=> $userTableAlias . '.customer_id',
				'user_type'			=> $userTableAlias . '.type',
				'user_status'		=> $userTableAlias . '.status',
		);
		$this->getSelect()
				->joinLeft(
						array($userTableAlias => $userTableName),
						"{$userTableAlias}.kanban_id = main_table.entity_id",
						$columnArray
				)
				->where("{$userTableAlias}.customer_id = ?", $customerId)
				->where("{$userTableAlias}.status = ?", Bf170_PMTool_Model_Kanban::STATUS_NORMAL);
		return $this;
	}
	
}