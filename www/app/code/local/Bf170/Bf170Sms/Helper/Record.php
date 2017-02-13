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

class Bf170_Bf170Sms_Helper_Record extends Mage_Core_Helper_Abstract {
	
	public function sendSms($type, $telephones, $content){
		if(!array_key_exists($type, $this->getTypeValues())){
			Mage::throwException('无效的短信类型');
		}
		if(is_array($telephones)){
			foreach($telephones as $k=>$telephone){
			$telephone = trim($telephone);
				if(!$telephone || !Mage::helper('coreservice')->validateTelephone($telephone)){
					Mage::throwException('无效的手机号码');
				}
			}
			$telephone = implode(',',$telephones);
		}else{
			$telephone = trim($telephones);
			if(!$telephone || !Mage::helper('coreservice')->validateTelephone($telephone)){
				Mage::throwException('无效的手机号码');
			}
		}
		$content = trim($content);
		if(!$content){
			Mage::throwException('无效的短信内容');
		}
		$record = Mage::getModel('bf170sms/record');
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		if(!!$customer && !!$customer->getId()){
			$record->setData('customer_id', $customer->getId());
		}
		$record->setData('client_ip', Mage::helper('coreservice')->getRequestIp());
		$record->setData('type', $type);
		$record->setData('telephone', $telephone);
		$record->setData('content', $content);
		$record->setData('scheduled_at', now()); //即时发送
		$record->setData('status', Bf170_Bf170Sms_Model_Record::STATUS_PENDING);
		$record->send(true);
	}
	
	public function generateSmsLoginContent($smsCode){
		return "您的临时登录密码为{$smsCode}（仅可使用一次），请注意保密";
	}
	
	public function generateValidateTelephoneContent($smsCode){
		return "您的手机号验证码为{$smsCode}，请注意保密";
	}
	
	public function getTypeValues(){
		return array(
				Bf170_Bf170Sms_Model_Record::TYPE_VALIDATE_TELEPHONE	=> Mage::helper('bf170sms')->__('验证手机'),
				Bf170_Bf170Sms_Model_Record::TYPE_VALIDATE_ACCESS		=> Mage::helper('bf170sms')->__('赋予权限'),
				Bf170_Bf170Sms_Model_Record::TYPE_SMS_LOGIN				=> Mage::helper('bf170sms')->__('短信登录'),
				Bf170_Bf170Sms_Model_Record::TYPE_SMS_KAPIAN_XIUGAI		=> Mage::helper('bf170sms')->__('卡片修改'),
				Bf170_Bf170Sms_Model_Record::TYPE_SMS_KAPIAN_ADD		=> Mage::helper('bf170sms')->__('卡片添加'),
				Bf170_Bf170Sms_Model_Record::TYPE_SMS_KAPIAN_DRAG		=> Mage::helper('bf170sms')->__('卡片移动'),
		);
	}
	
	public function getStatusValues(){
		return array(
				Bf170_Bf170Sms_Model_Record::STATUS_PENDING		=> Mage::helper('bf170sms')->__('待发送'),
				Bf170_Bf170Sms_Model_Record::STATUS_SUCCESS		=> Mage::helper('bf170sms')->__('已发送'),
				Bf170_Bf170Sms_Model_Record::STATUS_ERROR		=> Mage::helper('bf170sms')->__('失败'),
		);
	}
	
}