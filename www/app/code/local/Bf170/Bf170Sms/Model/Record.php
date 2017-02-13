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
class Bf170_Bf170Sms_Model_Record extends Bf170_CoreService_Model_Object {
	
	const TYPE_VALIDATE_TELEPHONE		= 0;     // 验证手机号码有效/注册验证
	const TYPE_VALIDATE_ACCESS			= 100;   // 赋予高级权限，一般在支付/酬蚂钱包类操作时使用
	const TYPE_SMS_LOGIN				= 200;   // 短信登录
	const TYPE_SMS_KAPIAN_XIUGAI        = 300;   //卡片修改
	const TYPE_SMS_KAPIAN_ADD           = 400;   //卡片添加
	const TYPE_SMS_KAPIAN_DRAG          = 600;   //卡片移动
	
	const STATUS_PENDING				= 0; // 待发送
	const STATUS_SUCCESS				= 100; // 发送成功
	const STATUS_ERROR 			    	= 500; // 发送失败
	
	protected function _construct() {
    	// 指向相应的 Resource (Model)
        $this->_init('bf170sms/record');
    }
    
    // 一般在保存前，赋予更新时间（初次保存，赋予创建时间）
	protected function _beforeSave() {
    	parent::_beforeSave();
    	//For new object which does not specify 'created_at'
    	if(!$this->getId() && !$this->getData('created_at')){
    		$this->setData('created_at', now());
    	}
    	//Always specify 'updated_at'
    	$this->setData('updated_at', now());
    	return $this;
    }
    
    /*
     * 【注意】短信发送错误会以Exception的形式向上传递
     */
    public function send($shouldSave = false){
    	try{
    		// 调用普通短信接口
    		$responseInfo = Mage::helper('bf170sms/api')->sendSmsPt($this->getData('telephone'), $this->getData('content'));
    		if(!empty($responseInfo['raw_response'])){
    			$this->setData('api_info', $responseInfo['raw_response']);
    		}
    		$this->setData('content', ''); // 为保证安全，清除已发送短信
    		$this->setData('status', self::STATUS_SUCCESS);
	    	if(!!$shouldSave){
	    		$this->save();
	    	}
    	}catch(Exception $ex){
    		$this->setData('status', self::STATUS_ERROR);
    		$this->setData('api_info', $ex->getMessage());
	    	if(!!$shouldSave){
	    		$this->save();
	    	}
	    	Mage::throwException("短信发送失败：{$ex->getMessage()}");
    	}
    	return $this;
    }
    
    // ======================== Utilities: General ======================== //
    public function getTypeLabel() {
    	$typeValues = Mage::helper('bf170sms/record')->getTypeValues();
    	$type = $this->getType();
    	if(isset($typeValues[$type])){
    		return $typeValues[$type];
    	}
    	return "";
    }
    
	public function getStatusLabel() {
    	$statusValues = Mage::helper('bf170sms/record')->getStatusValues();
    	$status = $this->getStatus();
    	if(isset($statusValues[$status])){
    		return $statusValues[$status];
    	}
    	return "";
    }
    
    public function forcedUpdatedAt($updatedAt){
    	$this->setData('updated_at', $updatedAt);
    	$this->_isForcedUpdatedAt = true;
    }

}