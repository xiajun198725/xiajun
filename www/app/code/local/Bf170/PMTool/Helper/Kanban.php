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

class Bf170_PMTool_Helper_Kanban extends Mage_Core_Helper_Abstract {
	
	public function getAllKanbanValues(){
		$kanbanInfo = array();
		$kanbanCollection = Mage::getModel('pmtool/kanban')->getCollection();
		foreach($kanbanCollection as $kanban){
			$kanbanInfo[$kanban->getId()] = $kanban->getData('name');
		}
		return $kanbanInfo;
	}
	
	//对看板的用户进行添加
	public function tianjiaKanbanUser($kanbanCustomer,$kanbanId,$customer){
		$customerId = explode(',',$kanbanCustomer);
		array_pop($customerId);
		
		//添加高级管理员对看板的权限
		if(Mage::getStoreConfig('pmtool/general/is_enabled')){
			foreach($customerId as $id){
				if($id != $customer->getId()){
					if($id != Mage::getStoreConfig('pmtool/general/kanban_senior_manager_id')){
						$kanbancustomer = Mage::getModel('pmtool/kanban_user');
						$kanbancustomer->setData('customer_id',$id);
						$kanbancustomer->setData('kanban_id',$kanbanId);
						$kanbancustomer->setData('type',Bf170_PMTool_Model_Kanban_User::STATUS_NORMAL);
						$kanbancustomer->setData('status',Bf170_PMTool_Model_Kanban_User::TYPE_MEMBER);
						$kanbancustomer->save();
					}
				}
			}
			$kanbancustomer = Mage::getModel('pmtool/kanban_user');
			$kanbancustomer->setData('customer_id',Mage::getStoreConfig('pmtool/general/kanban_senior_manager_id'));
			$kanbancustomer->setData('kanban_id',$kanbanId);
			$kanbancustomer->setData('type',Bf170_PMTool_Model_Kanban_User::TYPE_ROOT);
			$kanbancustomer->setData('status',Bf170_PMTool_Model_Kanban_User::TYPE_MEMBER);
			$kanbancustomer->save();
		}else{
			foreach($customerId as $id){
				if($id != $customer->getId()){
					$kanbancustomer = Mage::getModel('pmtool/kanban_user');
					$kanbancustomer->setData('customer_id',$id);
					$kanbancustomer->setData('kanban_id',$kanbanId);
					$kanbancustomer->setData('type',Bf170_PMTool_Model_Kanban_User::TYPE_MEMBER);
					$kanbancustomer->setData('status',Bf170_PMTool_Model_Kanban_User::STATUS_NORMAL);
					$kanbancustomer->save();
				}
			}
		}		
		return true;
	}
	
	public function getStatusValues(){
		return array(
				Bf170_PMTool_Model_Kanban::STATUS_NORMAL		=> Mage::helper('pmtool')->__('正常'),
				Bf170_PMTool_Model_Kanban::STATUS_ARCHIVED		=> Mage::helper('pmtool')->__('归档'),
		);
	}
	
	public function getTypeValues(){
		return array(
				Bf170_PMTool_Model_Kanban::TYPE_PROMO		    => Mage::helper('pmtool')->__('会员专区'),
				Bf170_PMTool_Model_Kanban::TYPE_KANGSIDA		=> Mage::helper('pmtool')->__('康士达'),
				Bf170_PMTool_Model_Kanban::TYPE_FINGERTIP		=> Mage::helper('pmtool')->__('指尖风景'),
				Bf170_PMTool_Model_Kanban::TYPE_WEIKETANG		=> Mage::helper('pmtool')->__('魏课堂'),
				Bf170_PMTool_Model_Kanban::TYPE_PHONE_APP		=> Mage::helper('pmtool')->__('手机APP'),
				Bf170_PMTool_Model_Kanban::TYPE_WORK_KANPAN		=> Mage::helper('pmtool')->__('工作看板'),
		);
	}
	
	public function getLabelValues(){
		return array(
				Bf170_PMTool_Model_Card::PRIORITY_LOW		   => Mage::helper('pmtool')->__('紧急度低'),
				Bf170_PMTool_Model_Card::PRIORITY_NORMAL	   => Mage::helper('pmtool')->__('紧急度中'),
				Bf170_PMTool_Model_Card::PRIORITY_HIGH		   => Mage::helper('pmtool')->__('紧急度高'),
				Bf170_PMTool_Model_Card::PRIORITY_URGENT	   => Mage::helper('pmtool')->__('紧急度急'),
		);
	}
	
	public function getDefaultProcessInfo(){
		$processInfo = array(
				'1'		=> '计划',
				'2'		=> '准备',
				'3'		=> '执行',
				'4'		=> '验收',
				'5'		=> '完成',
		);
		return $processInfo;
	}
	
}