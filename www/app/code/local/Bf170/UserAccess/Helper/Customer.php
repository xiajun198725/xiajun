<?php
/*
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User Software Agreement (EULA).
 * It is also available through the world-wide-web at this URL:
 * http://www.bf170.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to eula@harapartners.com so we can send you a copy immediately.
 * 
 */

class Bf170_UserAccess_Helper_Customer extends Mage_Core_Helper_Data {

	const DEFAULT_EMAIL_DOMAIN_NAME			= 'bf170.com';

	/*
	 * 额外的用户信息认证
	 */
	public function getAdditionalCustomerRegistrationErrors($customer){
		$errors = array();
		
		// 电话号码需要用手机（短信接收）
		$errors = array_merge($errors, $this->_getCustomerTelephoneErrors($customer)); 
		
		// 身份证格式验证
		//$errors = array_merge($errors, $this->_getCustomerIdCardNumberErrors($customer)); 
		
		return $errors;
	}
	
	public function generateCustomerEmailByTelephone($telephone){
		$telephone = preg_replace("/[^0-9]/", '', $telephone); // basic clean-up
		$email = $telephone . "@" . self::DEFAULT_EMAIL_DOMAIN_NAME;
		return $email;
	}
	
	protected function _getCustomerTelephoneErrors($customer){
		$errors = array();
		// telephone, cell phone only!
		$telephone = $customer->getData('telephone');
		$telephone = preg_replace("/[^0-9]/", '', $telephone); // basic clean-up
		$isTelephoneValid = Mage::helper('coreservice')->validateTelephone($telephone);
		if(!$isTelephoneValid){
			$errors[] = "手机号码无效";
		}elseif($customer->getData('telephone') != $customer->getOrigData('telephone')){
			$existingCustomer = Mage::getModel('customer/customer')->getCollection()
					->addAttributeToFilter('telephone', $telephone)
					->getFirstItem();
			if(!!$existingCustomer && !!$existingCustomer->getId()){
				$errors[] = "此手机号码已被使用，请使用其它的手机号码";
			}
		}
		$customer->setData('telephone', $telephone);
		return $errors;
	}
	
	protected function _getCustomerIdCardNumberErrors($customer){
		$errors = array();
		// id_card_number
		// Either 1) 15 digit numerical or 2) 18 digits with last char = X, all uppercase!
		$idCardNumber = $customer->getData('id_card_number');
		$idCardNumber = strtoupper(preg_replace("/[^A-Za-z0-9]/", '', $idCardNumber)); // basic clean-up
		$isIdCardNumberValid = Mage::helper('coreservice')->validateIdCardNumber($idCardNumber);
		if(!$isIdCardNumberValid){
			$errors[] = "身份证号码无效：如果身份证件号码末位是X，请在“半角”状态下输入并使用大写";
		}elseif($customer->getData('id_card_number') != $customer->getOrigData('id_card_number')){
			$existingCustomer = Mage::getModel('customer/customer')->getCollection()
					->addAttributeToFilter('id_card_number', $idCardNumber)
					->getFirstItem();
			if(!!$existingCustomer && !!$existingCustomer->getId()){
				$errors[] = "此身份证号码已被使用，请使用其它的身份证号码";
			}
		}
		$customer->setData('id_card_number', $idCardNumber);
		return $errors;
	}
	
}