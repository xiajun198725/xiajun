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

class Bf170_PMTool_Helper_Card extends Mage_Core_Helper_Abstract {
	
	public function getPriorityValues(){
		return array(
				Bf170_PMTool_Model_Card::PRIORITY_LOW		=> Mage::helper('pmtool')->__('低'),
				Bf170_PMTool_Model_Card::PRIORITY_NORMAL	=> Mage::helper('pmtool')->__('正常'),
				Bf170_PMTool_Model_Card::PRIORITY_HIGH		=> Mage::helper('pmtool')->__('高'),
				Bf170_PMTool_Model_Card::PRIORITY_URGENT	=> Mage::helper('pmtool')->__('紧急'),
		);
	}
	
	public function getStatusValues(){
		return array(
				Bf170_PMTool_Model_Card::STATUS_NORMAL		=> Mage::helper('pmtool')->__('正常'),
				Bf170_PMTool_Model_Card::STATUS_ARCHIVED	=> Mage::helper('pmtool')->__('归档'),
		);
	}
	
	public function getTypeValue($kapiantype){
		foreach($this->getPriorityValues() as $typeId=>$type){
			if($typeId == $kapiantype){
				return $type;
			}
		}
	}
	
	//对卡片拖动之后数据的处理
	public function getKapianTuodongValues($buzhouIds,$kapianIds){
		$kapiandatas = array();
		foreach($buzhouIds as $buid=>$buzhouId){
			$buzhou = ltrim($buzhouId,'process-id-');
			foreach($kapianIds as $kaid=>$kapianId){
				if($buid == $kaid){
					foreach($kapianId as $kapianidvalue){
						$kapiandatas[$buzhou][] = ltrim($kapianidvalue,'card-id-');
					}
				}
			}
		}
		return $kapiandatas;
	}
	
	//对表里的评价信息做一下处理
	public function getEvaluate($messageData){
		$evaluates = json_decode($messageData,true);
		return $evaluates;
	}
}