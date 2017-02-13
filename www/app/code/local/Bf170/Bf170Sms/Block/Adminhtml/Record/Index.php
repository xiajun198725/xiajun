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
 
class Bf170_Bf170Sms_Block_Adminhtml_Record_Index extends Mage_Adminhtml_Block_Widget_Grid_Container {
	
	public function __construct(){
		$this->_blockGroup = 'bf170sms';
		$this->_controller = 'adminhtml_record_index';
		
		$customerId = Mage::registry('bf170sms_record_index_customer_id');
		if(!!$customerId){
			$this->_headerText = Mage::helper('bf170sms')->__("短信记录（用户ID:{$customerId}）");
		}else{
			$this->_headerText = Mage::helper('bf170sms')->__('短信记录');
		}
    	
    	parent::__construct();
    	$this->_removeButton('add');
	}
	
}