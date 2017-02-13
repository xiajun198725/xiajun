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
 
class Bf170_PMTool_Adminhtml_Card_UserController extends Mage_Adminhtml_Controller_Action {
	
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
				$aclResource = 'pmtool/manage';
				break;
			case 'save':
				$aclResource = 'pmtool/manage_adv';
				break;
			case 'delete':
			default:
				$aclResource = 'pmtool/manage_admin';
				break;
		}
		return Mage::getSingleton('admin/session')->isAllowed($aclResource);
	}
	
	public function indexAction(){
		$this->loadLayout()->_setActiveMenu('pmtool');
		$this->_addContent($this->getLayout()->createBlock('pmtool/adminhtml_card_user_index'));
		$this->renderLayout();
	}
	
	public function newAction() {
        $this->_forward('edit', 'card_user', 'pmtool_adminhtml');
    }
	
	public function editAction(){
		$cardUserId = $this->getRequest()->getParam('id');
		$cardUser = Mage::getModel('pmtool/card_user')->load($cardUserId);

		if(!!$cardUser->getId()){
			Mage::register('pmtool_card_user', $cardUser);
		}
		
		$this->loadLayout()->_setActiveMenu('pmtool');
		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
		$this->_addContent($this->getLayout()->createBlock('pmtool/adminhtml_card_user_edit'));
		$this->renderLayout();
    }

	public function saveAction(){
		try{
			$postData = $this->getRequest()->getParams();
        	$cardUserId = $this->getRequest()->getParam('id');
			Mage::getSingleton('adminhtml/session')->setPMToolCardUserFormData($postData);
			$cardUser = Mage::getModel('pmtool/card_user')->load($cardUserId);
			$cardUser->addData($postData);
			$cardUser->save();
			Mage::getSingleton('adminhtml/session')->setPMToolCardUserFormData(null);
			Mage::getSingleton('adminhtml/session')->addSuccess( Mage::helper("pmtool")->__( "卡片用户已更新" ) );
			$this->_redirect('*/*/edit', array('id' => $cardUser->getId()));
			return;
		}catch(Exception $ex){
			Mage::getSingleton('adminhtml/session')->addError( Mage::helper("pmtool")->__( $ex->getMessage() ) );
			$this->_redirectReferer();
			return;
		}
    }
    
	public function deleteAction(){
		try{
			$cardUserId = $this->getRequest()->getParam('id');
			$cardUser = Mage::getModel('pmtool/card_user')->load($cardUserId);
			$cardUser->delete();
			Mage::getSingleton('adminhtml/session')->addSuccess( Mage::helper("pmtool")->__( "卡片用户已删除" ) );
		}catch(Exception $ex){
			Mage::getSingleton('adminhtml/session')->addError( Mage::helper("pmtool")->__( $ex->getMessage() ) );
		}
		$this->_redirect('*/*/index');
    }
	
}