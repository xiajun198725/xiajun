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

class Bf170_CoreService_Helper_Data extends Mage_Core_Helper_Data {
	
	// 系统浮点型计算时的精度，缺省保留小数点后4位，运算时使用1E-5进行判断
	const DECIMAL_ACCURACY					= 1E-5;
	
	const WEBSITE_ID_WWW					= 1;
	
	const WEBSITE_CODE_WWW					= 'base';
	
	const ID_CARD_TYPE_CHINA_ID				= 0;
	const ID_CARD_TYPE_HONGKONG_ID			= 100;
	const ID_CARD_TYPE_MACAU_ID				= 200;
	
	const IMAGE_UPLOAD_MAX_SIZE					= 4000000; // 4M
	const IMAGE_UPLOAD_RESIZE_DEFAULT_WIDTH		= 800; // by default no wider than 800px
	
	// ========== 时间格式/时区转换：通常情况/与第三方接口都采用本地时间（北京时间） ========== //
	/*
	 * 此类函数主要用于第三方接口，采用本地时间（北京时间）
	 * 输入时间为UTC timestamp，不支持datetime格式，输出为本地时间
	 */
	public function getLocalDatetime($timestamp = null, $format = null){
		if(!$timestamp){
			$timestamp = time();
		}
		if(!$format){
			$format = 'yyyyMMddHHmmss';
		}
		$dateObj = Mage::app()->getLocale()->date($timestamp);
		return $dateObj->toString($format);
	}
	
	public function getLocalMonthStartDay($timestamp){
		return $this->getLocalDatetime($timestamp, 'yyyyMM01');
	}
	
	public function getLocalMonthEndDay($timestamp){
		// const Zend_Date::MONTH_DAYS        = 'ddd'
		return $this->getLocalDatetime($timestamp, 'yyyyMMddd');
	}
	
	// ========== 时间格式/时区转换：Magento内部数据库timestamp ========== //
	/*
	 * 此类函数主要用于Magento内部数据库timestamp的比较，需要输出相应月/日的起始UTC timestamp
	 * Mageno的PHP环境是UTC时区，相应数据库保存的datetime也是UTC时区
	 * 
	 * ----- 以下例子为月起始timestamp的计算 -----
	 * $date如果为DateTime格式，如2015-12-20 21:15:40，则无论时区，直接切割为2015-12-01 00:00:00
	 * $date如果为Timestamp格式，如1448927999，则系统认为是UTC Timestamp，会转换到本地时间（北京时间）
	 * 即UTC 2015-11-30 23:59:59，转换到本地时间（北京时间）2015-12-01 07:59:59
	 * 所以该月按2015-12月算，而不是2015-11月
	 */
	public function getMonthStartUtcTimestamp($date, $monthOffset = 0, $format = null){
		if(!$format){
			$format = 'yyyy-MM-dd HH:mm:ss';
		}
		$dateObj = Mage::app()->getLocale()->date($date);
		if($monthOffset != 0){
			/*
			 * 注意 extend_month本意是要清除SQL与Excel月份进位的区别的
			 * 当 1月31日加上1个月是，熟悉 SQL的人将以为结果是 2月28日。另一方面，熟悉 Excel 和 OpenOffice的人将认为结果是 3月3日
			 * 但是由于Zend_Date底层的bug，如果当前时间在不同时区时不同月份，会导致修正错误
			 * extend_month = true，则强行跳过修正项
			 */
			$dateObj->setOptions(array("extend_month" => true));
			$dateObj->addMonth($monthOffset);
		}
		$monthCutoff = $dateObj->toString('yyyy-MM-01 00:00:00');
		
		// Magento运行时以及数据库中均使用UTC时间，必须转换
		$monthCutoffDateObj = Mage::app()->getLocale()->utcDate(null, $monthCutoff, true);
		return $monthCutoffDateObj->toString($format);
	}
	
	public function getCurrentMonthStartUtcTimestamp() {
		return $this->getMonthStartUtcTimestamp(time());
	}
	
	public function getPreviousMonthStartUtcTimestamp() {
		return $this->getMonthStartUtcTimestamp(time(), -1);
	}
	
	public function getDayStartUtcTimestamp($date, $dayOffset = 0, $format = null){
		if(!$format){
			$format = 'yyyy-MM-dd HH:mm:ss';
		}
		$dateObj = Mage::app()->getLocale()->date($date);
		if($dayOffset != 0){
			$dateObj->addDay($dayOffset);
		}
		$dayCutoff = $dateObj->toString('yyyy-MM-dd 00:00:00');
		
		// Magento运行时以及数据库中均使用UTC时间，必须转换
		$dayCutoffDateObj = Mage::app()->getLocale()->utcDate(null, $dayCutoff, true);
		return $dayCutoffDateObj->toString($format);
	}
	
	// ========== 常用验证函数 ========== //
	public function validateTelephone($telephone){
		// Important: only allow cell phone!
		$isTelephoneValid = false;
		$telephone = preg_replace("/[^0-9]/", '', $telephone);
		if(strlen($telephone) == 11 && substr($telephone, 0, 1) == "1"){
			$isTelephoneValid = true;
		}
		return $isTelephoneValid;
	}
	
	public function validateBankCardNumber($bankCardNumber){
		$isBankCardNumberValid = false;
		$bankCardNumber = preg_replace("/[^0-9]/", '', $bankCardNumber);
		if(strlen($bankCardNumber) >= 16 && strlen($bankCardNumber) <= 19){
			$isBankCardNumberValid = true;
		}
		return $isBankCardNumberValid;
	}
	
	public function getBankCardNumberPartial($bankCardNumber){
		// 至少显示首位各一位
		$visibleDigits = ceil(strlen($bankCardNumber) / 6.0) + 2;
		$bankCardNumberPartial = substr_replace($bankCardNumber, str_repeat('*', strlen($bankCardNumber) - $visibleDigits), 1, strlen($bankCardNumber) - $visibleDigits);
		return $bankCardNumberPartial;
	}
	
	public function getIdCardTypeInfo(){
		return array(
				self::ID_CARD_TYPE_CHINA_ID		=> array('code'=> 'china', 'label' => '大陆居民身份证'),
				self::ID_CARD_TYPE_HONGKONG_ID	=> array('code'=> 'hongkong', 'label' => '香港居民身份证'),
				self::ID_CARD_TYPE_MACAU_ID		=> array('code'=> 'macau', 'label' => '澳门居民身份证'),
		);
	}
	
	public function getIdCardTypeOptions() {
        return array(
            	self::ID_CARD_TYPE_CHINA_ID		=> Mage::helper('hpusernetwork')->__('大陆居民身份证'),
            	self::ID_CARD_TYPE_HONGKONG_ID	=> Mage::helper('hpusernetwork')->__('香港居民身份证'),
            	self::ID_CARD_TYPE_MACAU_ID		=> Mage::helper('hpusernetwork')->__('澳门居民身份证'),
        );
    }
    
	public function getIdCardNumberPartial($idCardNumber){
		// 至少显示首位各一位
		$visibleDigits = ceil(strlen($idCardNumber) / 6.0) + 2;
		$idCardNumberPartial = substr_replace($idCardNumber, str_repeat('*', strlen($idCardNumber) - $visibleDigits), 1, strlen($idCardNumber) - $visibleDigits);
		return $idCardNumberPartial;
	}
	
	public function validateIdCardNumber($idCardNumber, $idCardType = self::ID_CARD_TYPE_CHINA_ID){
		switch($idCardType){
			case self::ID_CARD_TYPE_HONGKONG_ID:
				$isIdCardNumberValid = $this->validateHongkongIdCardNumber($idCardNumber);
				break;
			case self::ID_CARD_TYPE_MACAU_ID:
				$isIdCardNumberValid = $this->validateMacauIdCardNumber($idCardNumber);
				break;
			case self::ID_CARD_TYPE_CHINA_ID:
			default:
				$isIdCardNumberValid = $this->validateChinaIdCardNumber($idCardNumber);
				break;
		}
		return $isIdCardNumberValid;
	}
	
	public function validateChinaIdCardNumber($idCardNumber){
		// Either 1) 15 digit numerical or 2) 18 digits with last char = X, all uppercase!
		$isIdCardNumberValid = false;
		$idCardNumber = strtoupper(preg_replace("/[^A-Za-z0-9]/", '', $idCardNumber));
		if(strlen($idCardNumber) == 15 && is_numeric($idCardNumber)){
			$isIdCardNumberValid = true;
		}elseif(strlen($idCardNumber) == 18){
			if(is_numeric($idCardNumber)){
				$isIdCardNumberValid = true;
			}elseif(is_numeric(substr($idCardNumber, 0, 17)) && substr($idCardNumber, 17, 1) == "X"){
				$isIdCardNumberValid = true;
			}
		}
		return $isIdCardNumberValid;
	}
	
	public function validateHongkongIdCardNumber($idCardNumber){
		// 8~9位：香港身份证号码有一个或两个英文字母，一个六位数和一个加上括号的检验位，校验位为0~9或A，例如P103265(1)
		$isIdCardNumberValid = false;
		$idCardNumber = strtoupper(preg_replace("/[^A-Za-z0-9]/", '', $idCardNumber)); // 清理非数字/字母字符
		$initLetters = "";
		$mainNumber = "";
		$validationChar = "";
		if(strlen($idCardNumber) == 8){
			$initLetters = substr($idCardNumber, 0, 1);
			$mainNumbers = substr($idCardNumber, 1, 6);
			$validationChar = substr($idCardNumber, 7, 1);
		}elseif(strlen($idCardNumber) == 9){
			$initLetters = substr($idCardNumber, 0, 2);
			$mainNumbers = substr($idCardNumber, 2, 6);
			$validationChar = substr($idCardNumber, 8, 1);
		}
		if(!!preg_match("/^[a-zA-Z]+$/", $initLetters) && is_numeric($mainNumbers) && !!preg_match("/^[aA0-9]$/", $validationChar)){
			$isIdCardNumberValid = true;
		}
		return $isIdCardNumberValid;
	}
	
	public function validateMacauIdCardNumber($idCardNumber){
		// 8位数，不包含出生年月，格式为 xxxxxxx(x)，x全为数字,无英文字母，首位数只有1、5、7字开头的
		$isIdCardNumberValid = false;
		$idCardNumber = strtoupper(preg_replace("/[^0-9]/", '', $idCardNumber)); // 清理非数字字符
		$initLetters = "";
		$mainNumber = "";
		$validationChar = "";
		if(strlen($idCardNumber) == 8){
			$initLetters = substr($idCardNumber, 0, 1);
			$mainNumbers = substr($idCardNumber, 1, 6);
			$validationChar = substr($idCardNumber, 7, 1);
		}
		if(!!preg_match("/^[157]$/", $initLetters) && is_numeric($mainNumbers) && !!preg_match("/^[0-9]$/", $validationChar)){
			$isIdCardNumberValid = true;
		}
		return $isIdCardNumberValid;
	}
	
	public function validateTaiwanIdCardNumber($idCardNumber){
		$isIdCardNumberValid = false;
		return $isIdCardNumberValid;
	}
	
	// ========== 其他帮助函数 ========== //
	public function getRequestIp(){
		// 生产服务器可能放置在负载均衡之后, 先检查HTTP_X_FORWARDED_FOR，然后是HTTP_CLIENT_IP，最后是REMOTE_ADDR
		if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		    $requestIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}elseif(!empty($_SERVER['HTTP_CLIENT_IP'])) {
		    $requestIp = $_SERVER['HTTP_CLIENT_IP'];
		}else{
		    $requestIp = $_SERVER['REMOTE_ADDR'];
		}
		return $requestIp;
	}
	
	public function loadConfigWithoutCache($path, $scope = 'default', $scopeId = 0){
		$readAdapter = Mage::getSingleton('core/resource')->getConnection('core_read');
		$select = $readAdapter->select()
				->from('core_config_data', array('value'))
				->where('path = ?', $path)
				->where('scope = ?', $scope)
			->where('scope_id = ?', $scopeId);
		return $readAdapter->fetchOne($select);
	}
	
	public function saveConfig($path, $value, $scope = 'default', $scopeId = 0){
		Mage::getResourceModel('core/config')->saveConfig(rtrim($path, '/'), $value, $scope, $scopeId);
		return;
	}
	
	// 生成随机验证码（字符串格式，避免使用整型，防止溢出）
	public function generateRandomNumber($digit = 6){
		$randomNumber = "";
		for($count = 0; $count < $digit; $count ++){
			$randomNumber .= rand(0, 9);
		}
		return $randomNumber;
	}
	
	public function convertChineseToUnicode($stingInChinese){
		
	}
	
	public function packUnicodeHexToUnicodeString($unicodeHex){
		$stingInUnicode = "";
		for($unicodeHexIndex = 0; $unicodeHexIndex < strlen($unicodeHex); $unicodeHexIndex += 4){
			$stingInUnicode .= "\u" . substr($unicodeHex, $unicodeHexIndex, 4);
		}
		return $stingInUnicode;
	}
	
	public function convertUnicodeToChinese($stingInUnicode){
		$stingInChinese = "";
		$jsonConvertResult = json_decode("[{$stingInUnicode}]", 1);
		if(isset($jsonConvertResult[0])){
			return $jsonConvertResult[0];
		}
		return "";
	}
	
	public function initAdditionalLayoutMessages($messagesStorage) {
		if (!is_array($messagesStorage)) {
			$messagesStorage = array($messagesStorage);
		}
		foreach ($messagesStorage as $storageName) {
			$storage = Mage::getSingleton($storageName);
			if ($storage) {
				$block = Mage::app()->getLayout()->getMessagesBlock();
				$block->addMessages($storage->getMessages(true));
				$block->setEscapeMessageFlag($storage->getEscapeMessages(true));
				$block->addStorageType($storageName);
			}
			else {
				Mage::throwException(
					 Mage::helper('core')->__('Invalid messages storage "%s" for layout messages initialization', (string) $storageName)
				);
			}
		}
		return;
	}
	
	public function getSupportedImageMimeTypes(){
		return array(
				'jpg' => 'image/jpeg',
				'png' => 'image/png',
				'gif' => 'image/gif',
		);
	}
	
	public function processUploadImage($inputName, $nameSuffix, $dirPrefix){
		try {
			if(empty($_FILES[$inputName]['name'])){
				Mage::throwException('上传文件无效');
			}
			$finfo = new finfo(FILEINFO_MIME_TYPE);
			$mimeType = $finfo->file($_FILES[$inputName]['tmp_name']);
			$ext = array_search($mimeType, $this->getSupportedImageMimeTypes());
			if(!$ext){
				Mage::throwException('上传图片类型不支持');
			}
			$filename = md5(time() . rand(0, 999999) . $nameSuffix) . ".{$ext}";
			
			$noTrailingDS = false;
			$shouldCreateDir = true;
			$relativeFilename = $dirPrefix . $this->getPaddedFilename($filename);
			$relativePath = dirname($relativeFilename);
			$filenameWithFullPath = $this->getSecurePhotoDir($relativePath, $noTrailingDS, $shouldCreateDir) . basename($filename);
			
			$this->_uploadImage($inputName, $filenameWithFullPath);
		} catch (Exception $ex) {
			// Exception bubble up
			throw $ex;
		}
		return $relativeFilename;
	}
	
	public function getPaddedFilename($filename){
		$paddedFilename = basename($filename);
		$ext = pathinfo($paddedFilename, PATHINFO_EXTENSION);
		$paddedFilenameTrunk = basename($paddedFilename, ".".$ext);
		$paddedFilenameTrunk = preg_replace('/[^\da-z]/i', '_', $paddedFilenameTrunk);
		if(strlen($paddedFilenameTrunk) > 2){
			$paddedFilename = DS . substr($paddedFilenameTrunk, 0, 1) . DS . substr($paddedFilenameTrunk, 1, 1) . DS . $paddedFilename;
		}
		return $paddedFilename;
	}
	
	protected function _uploadImage($inputName, $filename){
		try {
			if (empty($_FILES[$inputName]['name'])){
				Mage::throwException('上传文件无效');
			}
			if ($_FILES[$inputName]['size'] > self::IMAGE_UPLOAD_MAX_SIZE) {
				Mage::throwException('上传图片不应超过 ' . intval(self::IMAGE_UPLOAD_MAX_SIZE / 1000000) . 'M');
			}

			// Must check MIME Type directly, do NOT trust the uploaded value
			$finfo = new finfo(FILEINFO_MIME_TYPE);
			$mimeType = $finfo->file($_FILES[$inputName]['tmp_name']);
			if (!in_array($mimeType, $this->getSupportedImageMimeTypes())) {
				Mage::throwException('上传图片类型不支持');
			}
			
			if (!move_uploaded_file($_FILES[$inputName]['tmp_name'], $filename)) {
				Mage::throwException('无法保存上传图片');
			}
		} catch (Exception $ex) {
			// Exception bubble up
			throw $ex;
		}
	}
	
	public function getSecureDir($relativePath = "", $noTrailingDS = false, $shouldCreateDir = false){
		$targetPath = dirname(BP) . DS . 'secure' . DS;
		$relativePath = trim($relativePath, DS);
		if(!!$relativePath){
			$targetPath .= $relativePath . DS;
		}
		if(!!$noTrailingDS){
			$targetPath = rtrim($targetPath, DS);
		}
		if (!file_exists($targetPath)) {
			if($shouldCreateDir){
				mkdir($targetPath, 0775, true);
			}else{
				return false;
			}
		}
		return $targetPath;
	}
	
	public function getSecurePhotoDir($relativePath = "", $noTrailingDS = false, $shouldCreateDir = false){
		return $this->getSecureDir('photo' . DS . $relativePath, $noTrailingDS, $shouldCreateDir);
	}
	
	public function getSecurePhotoUrl($filepath){
		$encryptedKey = base64_encode(Mage::helper('core')->encrypt($filepath));
		return Mage::getUrl('coreservice/image/viewSecurePhoto', array('key' => $encryptedKey));
	}
	
	public function isValuePositive($value){
		return $value > self::DECIMAL_ACCURACY;
	}
	
	public function isValueNegative($value){
		return $value < -1.0 * self::DECIMAL_ACCURACY;
	}
	
	public function isValueZero($value){
		return abs($value) < self::DECIMAL_ACCURACY;
	}
	
}