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

class Bf170_PMTool_Helper_Import extends Mage_Core_Helper_Abstract {
	
	//对卡片上传的文件进行处理
	public function loadDataFromCsv($inputName){
		if(!isset($_FILES[$inputName]['name']) || !(file_exists($_FILES[$inputName]['tmp_name']))) {
			Mage::throwException('上传的文件无效');
		}
		$error = $_FILES[$inputName]['error'];
 		$tmp_name = $_FILES[$inputName]['tmp_name'];
		$size = $_FILES[$inputName]['size'];
		$name = $_FILES[$inputName]['name'];
		$type = $_FILES[$inputName]['type'];
		if ($error == UPLOAD_ERR_OK && $size > 0) {
			
			//文件存储路径
			$path = 'media/'.basename($_FILES[$inputName]['tmp_name']);
			$fp = move_uploaded_file($tmp_name,$path);
		}
		
		$fileData = array(
				'name' => $name,
				'type' => $type,
				'size' => $size,
				'path' => $path
		);
		return $fileData;
	}
}