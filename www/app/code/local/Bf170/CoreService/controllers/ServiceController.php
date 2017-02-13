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

class Bf170_CoreService_ServiceController extends Mage_Core_Controller_Front_Action{
	
	public function customerLogoutAction(){
		$key = $this->getRequest()->getParam('key');
		// Force logout previous customer
		Mage::getSingleton('customer/session')->logout();
		$this->_redirect("*/*/customerLogin", array('key' => $key));
		return;
	}
	
	public function customerLoginAction(){
		$key = $this->getRequest()->getParam('key');
		$keyInfo = json_decode(Mage::helper('core')->decrypt(base64_decode(urldecode($key))), 1);
		
		if(empty($keyInfo['cid'])){
			Mage::getSingleton('customer/session')->addError('用户信息有误，无法自动登录');
			$this->_redirect("/");
			return;
		}
		
		if(empty($keyInfo['ts']) || $keyInfo['ts'] + Bf170_CoreService_Helper_Customer::LOGIN_LINK_LIFETIME < time()){
			Mage::getSingleton('customer/session')->addError('该链接已失效，无法自动登录');
			$this->_redirect("/");
			return;
		}
		
		$customer = Mage::getModel('customer/customer')->load($keyInfo['cid']);
		if(!$customer || !$customer->getId()){
			Mage::getSingleton('customer/session')->addError('用户信息有误，无法自动登录');
			$this->_redirect("/");
			return;
		}
		
		if(isset($keyInfo['rcid'])){
			$regioncompany = Mage::getModel('hpuserregion/regioncompany')->load($keyInfo['rcid']);
			if(!$regioncompany || !$regioncompany->getId()){
				Mage::getSingleton('customer/session')->addError('公司信息有误，无法自动登录');
				$this->_redirect("/");
				return;
			}
			Mage::getSingleton('customer/session')->setData('regioncompany_id', $regioncompany->getId());
		}
		
		// Force customer should have logged out with the previous action
		Mage::getSingleton('customer/session')->setCustomerAsLoggedIn($customer);
		switch(Mage::app()->getWebsite()->getCode()){
    		case Bf170_CoreService_Helper_Data::WEBSITE_CODE_WWW:
    		default:
    			$this->_redirect("/");
    			break;
    	}
		
		return;
	}
	
}
