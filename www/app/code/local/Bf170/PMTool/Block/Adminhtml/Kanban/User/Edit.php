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
 
class Bf170_PMTool_Block_Adminhtml_Kanban_User_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
	
	public function __construct(){
    	parent::__construct();
        $this->_objectId = 'entity_id';
        $this->_blockGroup = 'pmtool';
        $this->_controller = 'adminhtml_kanban_user';
        $this->_mode = 'edit';
        $this->_removeButton('reset');

		if(!Mage::getSingleton('admin/session')->isAllowed('pmtool/manage_adv')){
        	$this->_removeButton('save');
		}
		if(!Mage::getSingleton('admin/session')->isAllowed('pmtool/manage_admin')){
        	$this->_removeButton('delete');
		}
    }
    
	public function getHeaderText() {
        return Mage::helper('pmtool')->__('看板用户详情');
    }
    
	public function getBackUrl(){
        return $this->getUrl('*/*/index', array('_current'=>true));
    }

    public function getSaveUrl(){
        return $this->getUrl('*/*/save', array('_current'=>true));
    }
    
	public function getDeleteUrl(){
		$kanbanUser = Mage::registry('pmtool_kanban_user');
		if(!!$kanbanUser && !!$kanbanUser->getId()){
			return $this->getUrl('*/*/delete', array('_current'=>true, 'id' => $kanbanUser->getId()));
		}else{
			return '';
		}
    }
    
}