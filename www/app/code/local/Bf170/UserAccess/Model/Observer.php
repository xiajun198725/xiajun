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
class Bf170_UserAccess_Model_Observer {
	
	public function restrictWebsiteAccess($observer) {
		// 仅对前台限制
		if(Mage::app()->getStore()->isAdmin()){
			return;
		}
		
		$controller = $observer->getEvent()->getControllerAction();

		// 缺省无限制模块
		$allowedGuestModules = array(
				// 静态页面
				'cms'			=> array(),
				// 缓存页面
				'pagecache'		=> array(),
				// 用户页面
				'customer'		=> array(
						'account' => array(
								// 系统缺省页面
								'create',
								'createpost',
								'login',
								'loginpost',
								'logoutsuccess',
								'forgotpassword',
								'forgotpasswordpost',
								'resetpassword',
								'resetpasswordpost',
								'confirm',
								'confirmation',
								// 暴蜂蜜蚂新增页面
								'sendloginsmsajax',
								'sendregistersmsajax',
						)
				),
				// 系统跳转页面
				'coreservice'	=> array(
						'image'		=> array(),
						'service'	=> array()
				),
		);
		
		// 如非以上模块页面，强行跳回登录页
		$redirectUrl = Mage::getUrl("customer/account/login");
		switch(Mage::app()->getWebsite()->getCode()){
			case Bf170_CoreService_Helper_Data::WEBSITE_CODE_WWW:
			default:
				break;
		}
		$isRedirect = $this->_restrictBasicWebsiteAccess($controller, $allowedGuestModules);
		if(!!$isRedirect){
			$controller->getResponse()->setRedirect($redirectUrl);
			$controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
			Mage::getSingleton('customer/session')->setAfterAuthUrl(Mage::helper('core/url')->getCurrentUrl());
		}
		
		return;
	}
	
	protected function _restrictBasicWebsiteAccess($controller, $allowedGuestModules){
		$isRedirect			= false;
		$request			= $controller->getRequest();
		
		$moduleName			= strtolower($request->getModuleName());
		$controllerName		= strtolower($request->getControllerName()); 
		$actionName			= strtolower($request->getActionName());
		
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		if(!$customer || !$customer->getId()){
			// Not allowed guest module => login page
			if(!array_key_exists($moduleName, $allowedGuestModules)){
				$isRedirect = true;
			}elseif(!empty($allowedGuestModules[$moduleName]) 
					&& !array_key_exists($controllerName, $allowedGuestModules[$moduleName])
			){
				$isRedirect = true;
			}elseif(!empty($allowedGuestModules[$moduleName][$controllerName])
					&& !in_array($actionName, $allowedGuestModules[$moduleName][$controllerName])
			){
				$isRedirect = true;
			}
		}else{
			// By default allow all after login
		}
		
		return $isRedirect;
	}

}