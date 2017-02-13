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

class Bf170_Bf170Sms_Helper_Api extends Mage_Core_Helper_Abstract {
	
	const TYPE_PUTONG						= 'pt'; // 普通短信
	const TYPE_GEXING						= 'gx'; // 个性短信
	
	const REQUEST_DEFAULT_TIMEOUT			= 5; // 缺省超时5秒
	
	const RESPONSE_STATUS_SUCCESS			= 0; // 成功
	const RESPONSE_STATUS_CENSORED			= 1; // 敏感词
	const RESPONSE_STATUS_BALANCE_ERROR		= 2; // 余额不足
	const RESPONSE_STATUS_ERROR				= -1; // 失败/系统异常
	
	const ERROR_LOG_FILE			 		= 'bf170sms_api_error.log';
	
	//获取短信发送接口信息
	public function sendSmsPt($telephone,$content){
		try{
			if(!Mage::getStoreConfig('bf170sms/general/is_enabled')){
				Mage::throwException('短信接口未启用');
			}

			$requestData = array();
			$requestData['mobile']		= $telephone;
			$requestData['name']		= Mage::getStoreConfig('bf170sms/general/api_username');
			$requestData['pwd']			= Mage::helper('core')->decrypt(Mage::getStoreConfig('bf170sms/general/api_password'));//解密
			$requestData['content']		= $content;
			$requestData['type']		= self::TYPE_PUTONG;
			$requestData['sign']		= Mage::getStoreConfig('bf170sms/general/content_signature');
			
			// 发送请求
			$timeout = Mage::getStoreConfig('bf170sms/general/api_timeout');//缺省超时时间
			if(!$timeout){
				$timeout = self::REQUEST_DEFAULT_TIMEOUT;
			}
			$apiUrl = Mage::getStoreConfig('bf170sms/general/api_service_url');
			$curlHandler = curl_init();
		    curl_setopt($curlHandler, CURLOPT_POST, 1);
		    curl_setopt($curlHandler, CURLOPT_URL, $apiUrl);
		    curl_setopt($curlHandler, CURLOPT_POSTFIELDS, $requestData);
		    curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt($curlHandler, CURLOPT_TIMEOUT, $timeout);
		    $rawResponse = curl_exec($curlHandler);
		    $curlError = curl_error($curlHandler);
		    curl_close($curlHandler);
		    // CURL请求错误
		    if(!empty($curlError)){
		    	Mage::throwException($curlError);
		    }
		    
		    // 处理响应
		    $responseInfo = $this->_parseResponse($rawResponse,$requestData);
		}catch (Exception $ex){
			Mage::log($ex->getMessage(), null, self::ERROR_LOG_FILE, true);
			Mage::throwException("短信接口服务错误");
		}
		
		return $responseInfo;
	}
	
	// 解析响应
	protected function _parseResponse($rawResponse,$requestData){
		$responseDatas = explode(",", $rawResponse);
		if(empty($responseDatas) || !is_numeric($responseDatas[0])){
			Mage::throwException("响应返回信息无效");
		}
		$responseInfo = array();
		$responseInfo['status'] = $responseData[0];
		$responseInfo['raw_response'] = $rawResponse;
		switch($responseInfo['status']){
			case self::RESPONSE_STATUS_SUCCESS:
				break;
			case self::RESPONSE_STATUS_CENSORED:
				Mage::throwException("短信内容包含敏感词");
				break;
			case self::RESPONSE_STATUS_BALANCE_ERROR:
				Mage::throwException("短信限额已满");
				break;
			default:
			case self::RESPONSE_STATUS_ERROR:
				Mage::throwException("系统错误");
				break;
		}
		return $responseInfo;
	}
	
}