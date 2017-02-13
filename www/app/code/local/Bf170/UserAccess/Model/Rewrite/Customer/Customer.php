<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User Software Agreement (EULA).
 * It is also available through the world-wide-web at this URL:
 * http://www.bf170.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to eula@bf170.com so we can send you a copy immediately.
 */

class Bf170_UserAccess_Model_Rewrite_Customer_Customer extends Mage_Customer_Model_Customer {
	
	/*
	 * 注意用手机号码登录
	 */
	public function authenticate($telephone, $password){
		$this->loadByTelephone($telephone);
		if ($this->getConfirmation() && $this->isConfirmationRequired()) {
			throw Mage::exception('Mage_Core', Mage::helper('customer')->__('This account is not confirmed.'),
				self::EXCEPTION_EMAIL_NOT_CONFIRMED
			);
		}
		if (!$this->validatePassword($password)) {
			throw Mage::exception('Mage_Core', Mage::helper('customer')->__('Invalid login or password.'),
				self::EXCEPTION_INVALID_EMAIL_OR_PASSWORD
			);
		}
		Mage::dispatchEvent('customer_customer_authenticated', array(
		   'model'	=> $this,
		   'password' => $password,
		));

		return true;
	}
	
	public function loadByTelephone($telephone){
		$this->_getResource()->loadByTelephone($this, $telephone);
		return $this;
	}
	
	public function loadByIdCardNumber($idCardNumber, $idCardType = Bf170_CoreService_Helper_Data::ID_CARD_TYPE_CHINA_ID){
		$this->_getResource()->loadByIdCardNumber($this, $idCardNumber, $idCardType);
		return $this;
	}
	
	protected function _beforeSave(){
		$this->_cleanCriticalData();
		
		// 中文姓名仅仅使用firstname一项，不区分firstname和lastname
		if(!!$this->getOrigData('lastname') 
				&& $this->getData('lastname') != $this->getOrigData('lastname')
		){
			Mage::throwException('请勿修改用户姓氏信息');
		}
		
		return parent::_beforeSave();
	}
	
	protected function _beforeDelete() {
		if(!$this->getData('is_forced_delete_allowed')){
			Mage::throwException("请勿直接删除已有会员账户");
		}
		return parent::_beforeDelete();
	}
	
	protected function _cleanCriticalData() {
		// ========== 数据清理，先检查再赋值，减少不必要的数据改动 ========== //
		// 身份证号需大写字母或数字，无空格
		if(!!$this->getData('id_card_number')){
			$cleanIdCardNumber = strtoupper(preg_replace("/[^A-Za-z0-9]/", '', $this->getData('id_card_number')));
			if($this->getData('id_card_number') != $cleanIdCardNumber){
				$this->setData('id_card_number', $cleanIdCardNumber);
			}
		}
		// 姓名，无空格
		if(!!$this->getData('firstname')){
			$cleanFirstname = trim($this->getData('firstname'));
			if($this->getData('firstname') != $cleanFirstname){
				$this->setData('firstname', $cleanFirstname);
			}
		}
		if(!!$this->getData('lastname')){
			$cleanLastname = trim($this->getData('lastname'));
			if($this->getData('lastname') != $cleanLastname){
				$this->setData('lastname', $cleanLastname);
			}
		}
		// 电话号码需数字，无空格
		if(!!$this->getData('telephone')){
			$cleanTelephone = preg_replace("/[^0-9]/", '', $this->getData('telephone'));
			if($this->getData('telephone') != $cleanTelephone){
				$this->setData('telephone', $cleanTelephone);
			}
		}
	}
	
	public function getName() {
		return $this->getFirstname();
	}
	
	public function validate() {
		$errors = array();
		if (!Zend_Validate::is( trim($this->getFirstname()) , 'NotEmpty')) {
			$errors[] = Mage::helper('customer')->__('The first name cannot be empty.');
		}
		
		if (!Zend_Validate::is($this->getEmail(), 'EmailAddress')) {
			$errors[] = Mage::helper('customer')->__('Invalid email address "%s".', $this->getEmail());
		}

		$password = $this->getPassword();
		if (!$this->getId() && !Zend_Validate::is($password , 'NotEmpty')) {
			$errors[] = Mage::helper('customer')->__('The password cannot be empty.');
		}
		if (strlen($password) && !Zend_Validate::is($password, 'StringLength', array(6))) {
			$errors[] = Mage::helper('customer')->__('The minimum password length is %s', 6);
		}
		$confirmation = $this->getConfirmation();
		if ($password != $confirmation) {
			$errors[] = Mage::helper('customer')->__('Please make sure your passwords match.');
		}
		
		$additionalErrors = Mage::helper('useraccess/customer')->getAdditionalCustomerRegistrationErrors($this);
		if(!empty($additionalErrors)){
			$errors = array_merge($errors, $additionalErrors);
		}

		if (empty($errors)) {
			return true;
		}
		return $errors;
	}
	
	/*
	 * 一般情况下不填邮件email，通过手机号生成一个email并填入
	 * 此类邮件不必发送
	 */
	public function sendNewAccountEmail($type = 'registered', $backUrl = '', $storeId = '0'){
		if($this->getData('email') == Mage::helper('useraccess/customer')->generateCustomerEmailByTelephone($this->getData('telephone'))){
			return $this;
		}
		return parent::sendNewAccountEmail($type, $backUrl, $storeId);
	}
	
}