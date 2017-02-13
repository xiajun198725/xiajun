<?php 

/* NOTICE OF LICENSE
 *
 * This source file is subject to the End User Software Agreement (EULA).
 * It is also available through the world-wide-web at this URL:
 * http://www.bf170.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to eula@bf170.com so we can send you a copy immediately.
 */ 
 
class Bf170_PMTool_Block_Adminhtml_Kanban_User_Index extends Mage_Adminhtml_Block_Widget_Grid_Container {
	
	public function __construct(){
		$this->_blockGroup = 'pmtool';
		$this->_controller = 'adminhtml_kanban_user_index';
		$this->_headerText = Mage::helper('pmtool')->__('看板用户列表');
    	parent::__construct();
		if(!Mage::getSingleton('admin/session')->isAllowed('pmtool/manage_adv')){
        	$this->_removeButton('add');
		}
	}
	
}