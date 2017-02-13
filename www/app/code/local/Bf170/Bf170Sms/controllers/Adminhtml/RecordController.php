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
 
class Bf170_Bf170Sms_Adminhtml_RecordController extends Mage_Adminhtml_Controller_Action {
	
	protected function _isAllowed(){
		// 缺省Magento权限判断
		$magentoAccessAllowed = parent::_isAllowed();
		if($magentoAccessAllowed){
			return true;
		}
		
		// 根据Module/Controller/Action名称的特别定义
		$module = strtolower($this->getRequest()->getModuleName());
		$module = preg_replace("/_adminhtml$/i", "", $module);
		$controller = strtolower($this->getRequest()->getControllerName());
		$action = strtolower($this->getRequest()->getActionName());
		$controllerActionAcl = "{$module}/{$controller}_{$action}";
		if(Mage::getSingleton('admin/session')->isAllowed($controllerActionAcl)){
			return true;
		}
		
		// 根据管理级别的定义
		switch ($action) {
			case 'index':
			case 'new':
			case 'edit':
				$aclResource = 'hpusernetwork/manage';
				break;
			case 'save':
			case 'adjustment':
			case 'adjustmentsave':
				$aclResource = 'hpusernetwork/manage_adv';
				break;
			case 'delete':
			default:
				$aclResource = 'hpusernetwork/manage_admin';
				break;
		}
		return Mage::getSingleton('admin/session')->isAllowed($aclResource);
	}
	
	public function indexAction(){
		$customerId = $this->getRequest()->getParam('customer_id');
		if(!!$customerId){
			Mage::register('bf170sms_record_index_customer_id', $customerId);
		}
		$this->loadLayout()->_setActiveMenu('bf170sms');
		$this->_addContent($this->getLayout()->createBlock('bf170sms/adminhtml_record_index'));
		$this->renderLayout();
	}
	
	public function newAction() {
        $this->_forward('edit', 'record', 'bf170sms_adminhtml');
    }
	
	public function editAction(){
		$recordId = $this->getRequest()->getParam('id');
		$record = Mage::getModel('bf170sms/record')->load($recordId);

		if(!!$record->getId()){
			Mage::register('bf170sms_record', $record);
		}
		
		$this->loadLayout()->_setActiveMenu('bf170sms');
		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
		$this->_addContent($this->getLayout()->createBlock('bf170sms/adminhtml_record_edit'));
		$this->renderLayout();
    }

	public function saveAction(){
		try{
			$postData = $this->getRequest()->getParams();
        	$recordId = $this->getRequest()->getParam('id');
			Mage::getSingleton('adminhtml/session')->setBf170SmsRecordFormData($postData);
			$record = Mage::getModel('bf170sms/record')->load($recordId);
			$record->addData($postData);
			$record->save();
			Mage::getSingleton('adminhtml/session')->setBf170SmsRecordFormData(null);
			Mage::getSingleton('adminhtml/session')->addSuccess( Mage::helper("bf170sms")->__( "短信记录已更新" ) );
			$this->_redirect('*/*/edit', array('id' => $record->getId()));
			return;
		}catch(Exception $ex){
			Mage::getSingleton('adminhtml/session')->addError( Mage::helper("bf170sms")->__( $ex->getMessage() ) );
			$this->_redirectReferer();
			return;
		}
    }
    
	public function deleteAction(){
		try{
			$recordId = $this->getRequest()->getParam('id');
			$record = Mage::getModel('bf170sms/record')->load($recordId);
			$record->delete();
			Mage::getSingleton('adminhtml/session')->addSuccess( Mage::helper("bf170sms")->__( "短信记录已删除" ) );
		}catch(Exception $ex){
			Mage::getSingleton('adminhtml/session')->addError( Mage::helper("bf170sms")->__( $ex->getMessage() ) );
		}
		$this->_redirect('*/*/index');
    }
	
}