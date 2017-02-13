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

include_once "Mage/Customer/controllers/AccountController.php"; 
class Bf170_UserAccess_Customer_AccountController extends Mage_Customer_AccountController {
	
	/*
	 * 登录/注册控制逻辑比较复杂，有微信通道/网页通道/APP通道
	 * 所需记录参数也很复杂，比如短信码和推荐码
	 * 为了统一管理参数，所有需要用session传递的参数统一放入
	 * $accessControlObj对象
	 */
	
	public function preDispatch() {
		parent::preDispatch();
		
		// Additional open actions
		$openActions = array(
				'sendloginsmsajax',
				'sendregistersmsajax',
		);
		$pattern = '/^(' . implode('|', $openActions) . ')/i';
		$action = strtolower($this->getRequest()->getActionName());
		if (preg_match($pattern, $action)) {
			$this->setFlag('', 'no-dispatch', false);
			$this->_getSession()->setNoReferer(true);
			
			//Note default account controller logic will also force redirect to login page, clear such flags
			$this->getResponse()->clearHeader('Location'); // Remove Location header for redirect
			$this->getResponse()->setHttpResponseCode(200); // Set HTTP Response Code to OK
		}
	}
	
	// ==================== Login required actions ==================== //
	public function indexAction() {
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->renderLayout();
	}
	
	/*
	 * 项目管理内部使用，不允许直接创建账户
	 */
	public function createAction(){
		$this->_getSession()->addError('请联系系统管理员申请账户');
		$this->_redirect('*/*/');
		return;
	}
	
	public function createPostAction(){
		$this->_getSession()->addError('请联系系统管理员申请账户');
		$this->_redirect('*/*/');
		return;
	}
	
	public function loginPostAction(){
		try{
			// 判断是否有有效的form_key，防止攻击
			if (!$this->_validateFormKey()) {
				Mage::throwException('表单信息验证失败');
			}
			
			// 用户如果已经登录，直接跳转到index页
			if ($this->_getSession()->isLoggedIn()) {
				$this->_redirect('*/*/');
				return;
			}
			
			// 表单必须是post
			if (!$this->getRequest()->isPost()) {
				Mage::throwException('表单信息无效');
			}
			
			$login = $this->getRequest()->getPost('login');
			if (empty($login['telephone']) || empty($login['password'])){
				Mage::throwException('请填入有效的手机/密码');
			}
			$accessControlObj = $this->_getAccessControlObj();
			$customer = Mage::getModel('customer/customer')->loadByTelephone($login['telephone']);
			
			// -----------------------------------------------------------------
			// 1）此后尝试本地短信登录
			$password = $login['password'];
			if($telephone == $this->_getSession()->getData('last_login_sms_telephone')
					&& $password == $this->_getSession()->getData('last_login_sms_code')
			){
				$customer = Mage::getModel('customer/customer')->loadByTelephone($telephone);
				if(!!$customer && !!$customer->getId() && $customer->getData('is_active')){
					// 短信登录
					$this->_getSession()->setCustomerAsLoggedIn($customer);
					
					// 登录成功后，清空session中$accessControlObj的相关数据
					$accessControlObj->setData('last_login_sms_telephone', null);
					$accessControlObj->setData('last_login_sms_code', null);
					$this->_setAccessControlObj($accessControlObj);
					
					// 提醒用户更改密码，仅限下一次更改密码无需使用当前密码做验证
					$this->_getSession()->setData('edit_password_directly', true);
					$this->_getSession()->addNotice('您刚刚使用短信进行登录，请在“账户信息”下，修改您的密码');
					$this->_redirectUrl(Mage::getUrl('customer/account/edit', array('_secure' => true)));
					return;
				}
			}
			
			// -----------------------------------------------------------------
			// 2）最后缺省为本机密码登录，更改密码时必须使用当前密码做验证
			// 使用电话telephone代替用户名username进行登录
			$this->_getSession()->setData('edit_password_directly', false);
			$this->_getSession()->login($login['telephone'], $login['password']);
			
			// 登录成功后相关逻辑，同时指定成功后的重定向页面
			$this->_loginPostRedirect();
			
		}catch(Exception $ex){
			$this->_getSession()->addError($ex->getMessage());
			$this->_redirectReferer();
			return;
		}
		
	}
	
	/*
	 * 更改密码逻辑变化：当前session为短信登录时修改密码时无需提交原有密码
	 */
	public function editPostAction(){
		if (!$this->_validateFormKey()) {
			return $this->_redirect('*/*/edit');
		}

		if ($this->getRequest()->isPost()) {
			/** @var $customer Mage_Customer_Model_Customer */
			$customer = $this->_getSession()->getCustomer();

			/** @var $customerForm Mage_Customer_Model_Form */
			$customerForm = $this->_getModel('customer/form');
			$customerForm->setFormCode('customer_account_edit')
				->setEntity($customer);

			$customerData = $customerForm->extractData($this->getRequest());

			$errors = array();
			$customerErrors = $customerForm->validateData($customerData);
			if ($customerErrors !== true) {
				$errors = array_merge($customerErrors, $errors);
			} else {
				$customerForm->compactData($customerData);
				$errors = array();

				// If password change was requested then add it to common validation scheme
				if ($this->getRequest()->getParam('change_password')) {
					$currPass	= $this->getRequest()->getPost('current_password');
					$newPass	= $this->getRequest()->getPost('password');
					$confPass	= $this->getRequest()->getPost('confirmation');

					$oldPass = $this->_getSession()->getCustomer()->getPasswordHash();
					if ( $this->_getHelper('core/string')->strpos($oldPass, ':')) {
						list($_salt, $salt) = explode(':', $oldPass);
					} else {
						$salt = false;
					}

					if ( $this->_getSession()->getData('edit_password_directly')
							|| $customer->hashPassword($currPass, $salt) == $oldPass
					) {
						if (strlen($newPass)) {
							/**
							 * Set entered password and its confirmation - they
							 * will be validated later to match each other and be of right length
							 */
							$customer->setPassword($newPass);
							$customer->setConfirmation($confPass);
						} else {
							$errors[] = $this->__('New password field cannot be empty.');
						}
					} else {
						$errors[] = $this->__('Invalid current password');
					}
				}

				// Validate account and compose list of errors if any
				$customerErrors = $customer->validate();
				if (is_array($customerErrors)) {
					$errors = array_merge($errors, $customerErrors);
				}
			}

			if (!empty($errors)) {
				$this->_getSession()->setCustomerFormData($this->getRequest()->getPost());
				foreach ($errors as $message) {
					$this->_getSession()->addError($message);
				}
				$this->_redirect('*/*/edit');
				return $this;
			}

			try {
				$customer->setConfirmation(null);
				$customer->save();
				$this->_getSession()->setCustomer($customer)
					->addSuccess($this->__('The account information has been saved.'));

				$this->_redirect('customer/account');
				return;
			} catch (Mage_Core_Exception $e) {
				$this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
					->addError($e->getMessage());
			} catch (Exception $e) {
				$this->_getSession()->setCustomerFormData($this->getRequest()->getPost())
					->addException($e, $this->__('Cannot save the customer.'));
			}
		}

		$this->_redirect('*/*/edit');
	}
	
	// 忘记密码使用手机号码进行修改密码
	public function sendLoginSmsAjaxAction(){
		try{
			$apiErrorType = Bf170_Bf170Api_Helper_Api::API_STATUS_GENERAL_ERROR;
			$login = $this->getRequest()->getParam('login');
			
			if (!$this->_validateFormKey()) {
				Mage::throwException('表单失效，请刷新本页');
			}
			if ($this->_getSession()->isLoggedIn()) {
				Mage::throwException('您已经登录，请刷新本页');
			}
			if(empty($login['telephone'])){
				Mage::throwException('请填入有效的手机号码');
			}
			$telephone = preg_replace("/[^0-9 ]/", '', $login['telephone']);
			if(!is_numeric($telephone) || !Mage::helper('coreservice')->validateTelephone($telephone)){
				Mage::throwException('请填入有效的手机号码');
			}
			$customer = Mage::getModel('customer/customer')->loadByTelephone($telephone);
			if(!$customer || !$customer->getId()){
				$apiErrorType = Bf170_Bf170Api_Helper_Api::API_STATUS_DATA_VALIDATION_ERROR;
				Mage::throwException('无法加载用户信息');
			}
			if(!$customer->getData('is_active')){
				$apiErrorType = Bf170_Bf170Api_Helper_Api::API_STATUS_GENERAL_ERROR;
				Mage::throwException('此用户不可用');
			}
			
			// ========== 主逻辑，发送短信 ========== //
			$loginSmsCode = Mage::helper('coreservice')->generateRandomNumber(Bf170_Bf170Sms_Helper_Data::DEFAULT_SMS_LENGTH);
			Mage::helper('bf170sms/record')->sendSms(
					Bf170_Bf170Sms_Model_Record::TYPE_SMS_LOGIN, 
					$telephone, 
					Mage::helper('bf170sms/record')->generateSmsLoginContent($loginSmsCode)
			);
			
			/*
			 * 登录/注册控制逻辑比较复杂，有微信通道/网页通道/APP通道
			 * 所需记录参数也很复杂，比如短信码和推荐码
			 * 为了统一管理参数，所有需要用session传递的参数统一放入
			 * $accessControlObj对象
			 */
			// 必须同时指定电话+短信，防止用户将该短信使用于不同的电话号码
			$accessControlObj = $this->_getAccessControlObj();
			$accessControlObj->setData('last_login_sms_telephone', $telephone);
			$accessControlObj->setData('last_login_sms_code', $registerSmsCode);
			$this->_setAccessControlObj($accessControlObj);
			
			$responseData = array(
					'status'		=> Bf170_Bf170Api_Helper_Api::API_STATUS_SUCCESS,
					'message'		=> '',
			);
		}catch (Exception $ex){
			$responseData = array(
					'status'		=> $apiErrorType,
					'message'		=> $ex->getMessage(),
			);
		}
		
		// 登录成功/失败后，网页重定向至缺省用户页
		$this->_getSession()->setBeforeAuthUrl(Mage::getUrl('customer/account', array('_secure' => true)));
		Mage::helper('bf170api/api')->sendCorsResponse($responseData);
		exit; // 该action旨在返回有效JSON，在此停止
	}
	
	// 注册时必须验证手机号码
	public function sendRegisterSmsAjaxAction(){
		try{
			$apiErrorType = Bf170_Bf170Api_Helper_Api::API_STATUS_GENERAL_ERROR;
			$telephone = $this->getRequest()->getParam('telephone');
			
			if (!$this->_validateFormKey()) {
				Mage::throwException('表单失效，请刷新本页');
			}
			if(empty($telephone)){
				Mage::throwException('请填入有效的手机号码');
			}
			$telephone = preg_replace("/[^0-9 ]/", '', $telephone);
			if(!is_numeric($telephone) || !Mage::helper('coreservice')->validateTelephone($telephone)){
				Mage::throwException('请填入有效的手机号码');
			}
			$customer = Mage::getModel('customer/customer')->loadByTelephone($telephone);
			if(!!$customer && !!$customer->getId()){
				$apiErrorType = Bf170_Bf170Api_Helper_Api::API_STATUS_DATA_VALIDATION_ERROR;
				Mage::throwException('该手机号码已被使用');
			}
			
			// ========== 主逻辑，发送短信 ========== //
			$registerSmsCode = Mage::helper('coreservice')->generateRandomNumber(Bf170_Bf170Sms_Helper_Data::DEFAULT_SMS_LENGTH);
			Mage::helper('bf170sms/record')->sendSms(
					Bf170_Bf170Sms_Model_Record::TYPE_VALIDATE_TELEPHONE, 
					$telephone, 
					Mage::helper('bf170sms/record')->generateValidateTelephoneContent($registerSmsCode)
			);
			
			/*
			 * 登录/注册控制逻辑比较复杂，有微信通道/网页通道/APP通道
			 * 所需记录参数也很复杂，比如短信码和推荐码
			 * 为了统一管理参数，所有需要用session传递的参数统一放入
			 * $accessControlObj对象
			 */
			// 必须同时指定电话+短信，防止用户将该短信使用于不同的电话号码
			$accessControlObj = $this->_getAccessControlObj();
			$accessControlObj->setData('last_register_sms_telephone', $telephone);
			$accessControlObj->setData('last_register_sms_code', $registerSmsCode);
			$this->_setAccessControlObj($accessControlObj);
			
			$responseData = array(
					'status'		=> Bf170_Bf170Api_Helper_Api::API_STATUS_SUCCESS,
					'message'		=> '',
			);
		}catch (Exception $ex){
			$responseData = array(
					'status'		=> $apiErrorType,
					'message'		=> $ex->getMessage(),
			);
		}
		
		// 注册成功/失败后，网页重定向至缺省用户页
		$this->_getSession()->setBeforeAuthUrl(Mage::getUrl('customer/account', array('_secure' => true)));
		Mage::helper('bf170api/api')->sendCorsResponse($responseData);
		exit; // 该action旨在返回有效JSON，在此停止
	}
	
	/*
	 * 登录/注册控制逻辑比较复杂，有微信通道/网页通道/APP通道
	 * 所需记录参数也很复杂，比如短信码和推荐码
	 * 为了统一管理参数，所有需要用session传递的参数统一放入
	 * $accessControlObj对象
	 */
	protected function _getAccessControlObj(){
		// 写入推荐关系
		$accessControlObj = $this->_getSession()->getData('access_control_obj');
		if(!$accessControlObj){
			$accessControlObj = new Varien_Object();
		}
		return $accessControlObj;
	}
	
	protected function _setAccessControlObj($accessControlObj){
		$this->_getSession()->setData('access_control_obj', $accessControlObj);
		return true;
	}

}