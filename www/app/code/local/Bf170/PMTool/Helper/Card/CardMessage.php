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
class Bf170_PMTool_Helper_Card_CardMessage extends Mage_Core_Helper_Abstract {
	
	public function getbuzhouId($buzhouId,$kanbianId){
		$kanbans = Mage::getModel('pmtool/kanban')->load($kanbianId);
		$buzhouIds = $kanbans->getData('process_info');
		$buzhouIds = json_decode($buzhouIds,true);
		foreach($buzhouIds as $buzhousId=>$buzhouName){
			if($buzhouId == $buzhousId){
				return true;
			}
		}
		return false;
	}
	
	public function getKanbanUsers($kanbanCustomers){
		$kanbanCustomers = json_decode($kanbanCustomers,true);
		return $kanbanCustomers[2];
	}
	
	public function getcustomersId($kanbanCustomers){
		$customerdatas = array();
		foreach($kanbanCustomers as $customerId){
			$customerdatas[$customerId] = urlencode(Mage::getModel('customer/customer')->load($customerId)->getFirstname());
		}
		$customerJsoninfo = urldecode(json_encode($customerdatas));
		return $customerJsoninfo;
	}
	
	public function getbuzhouIds($buzhouId){
		$data = Mage::getSingleton('core/resource')->getConnection('core_read');
      	$table = Mage::getSingleton('core/resource')->getTableName('pmtool_card');
        $result = $data->select()->from(array('main_table'=>$table))->where('main_table.process_id=?',$buzhouId)->limit(1);
      	$cards = $data->fetchAll($result);
      	return $cards;
	}
	
	//检查卡片名称是否存在
	public function getKapianName($buzhouId,$kanbianId,$kapianName){
		$data = Mage::getSingleton('core/resource')->getConnection('core_read');
      	$table = Mage::getSingleton('core/resource')->getTableName('pmtool_card');
        $result = $data->select()->from(array('main_table'=>$table))->where('main_table.process_id=?',$buzhouId)
        		->where('main_table.kanban_id=?',$kanbianId)->where('main_table.name=?',$kapianName);
      	$cards = $data->fetchAll($result);
      	return $cards;
	}
	
	//拿出单个看板中所有的卡片
	public function getAllkapians($buzhouId,$kanbianId,$kapianName){
		$data = Mage::getSingleton('core/resource')->getConnection('core_read');
      	$table = Mage::getSingleton('core/resource')->getTableName('pmtool_card');
        $result = $data->select()->from(array('main_table'=>$table))->where('main_table.process_id=?',$buzhouId)
        		->where('main_table.kanban_id=?',$kanbianId)->where('main_table.name=?',$kapianName);
      	$cards = $data->fetchAll($result);
      	return $cards;
	}
	
	//对卡片评价信息进行处理
	public function getKapianEvaluate($kapianevaluate,$cardDatas){
		$name = Mage::getSingleton('customer/session')->getCustomer()->getFirstname();
		$times = date('Y-m-d H:i:s',time());
		if($evaluates = $cardDatas->getData('comment_info')){
			$evaluate = json_decode($evaluates,true);
			$evaluate[] = $name.':'.$kapianevaluate.$times;
			$evaluateValues = array();
			foreach($evaluate as $data){
				$evaluateValues[] = urlencode($data);
			}
			$evaluate = urldecode(json_encode($evaluateValues));
			return $evaluate;
		}
		$evaluates = array();
		$evaluates[] = urlencode($name.':'.$kapianevaluate.$times);
		$evaluate = urldecode(json_encode($evaluates));
		return $evaluate;
	}
}