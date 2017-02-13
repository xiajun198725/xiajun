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

class Bf170_CoreService_Helper_Import extends Mage_Core_Helper_Data {
	
	const IMPORT_RECORD_LIMIT		= 200;
	
	public function loadDataFromCsv($inputName){
		if(!isset($_FILES[$inputName]['name']) || !(file_exists($_FILES[$inputName]['tmp_name']))) {
			Mage::throwException('上传的文件无效');
		}
		$fileHandle = fopen($_FILES[$inputName]['tmp_name'], 'r');
		$fileData = array();
		if (($fileHandle = $fileHandle) !== false) {
			while (($rowRawData = fgets($fileHandle, 65536)) !== false) {
				$rowRawData = mb_convert_encoding($rowRawData, "UTF-8", "GB2312,GBK,Big5,Shift_JI,UTF-8");
				// 基本数据清理
				$rowData = explode(",", $rowRawData);
				foreach($rowData as $dataKey => $dataValue){
					$rowData[$dataKey] = trim($dataValue);
				}
				$fileData[] = $rowData;
			}
			fclose($fileHandle);
		}else{
			Mage::throwException('文件内容有误，请上传有效的CSV文件');
		}
		
		// 去掉表头
		if(isset($fileData)){
			unset($fileData[0]);
		}
		if(empty($fileData)){
			Mage::throwException('上传的CSV文件没有有效数据');
		}
		if(count($fileData) > Bf170_CoreService_Helper_Import::IMPORT_RECORD_LIMIT){
			Mage::throwException('为保证效率，后台导入的条目请勿超过' . self::IMPORT_RECORD_LIMIT . '条，大量内容导入请联系系统管理员特别处理');
		}
		
		return $fileData;
	}

}