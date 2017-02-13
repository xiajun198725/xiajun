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
class Bf170_PMTool_Helper_Data extends Mage_Core_Helper_Abstract {

	public function getKanbanViewUrl($kanbanId){
		return $this->_getUrl('pmtool/kanban/view', array(
				'info'	=> urlencode(base64_encode(Mage::helper('core')->encrypt($kanbanId))),
		));
	}
	
	public function getKanbanIndexUrl(){
		return $this->_getUrl('pmtool/kanban/index');
	}
	
	public function getAllCustomerValues(){
		$customerInfo = array();
		$customerCollection = Mage::getModel('customer/customer')->getCollection();
		$customerCollection->addAttributeToSelect('firstname');
		foreach($customerCollection as $customer){
			$customerInfo[$customer->getId()] = $customer->getData('firstname');
		}
		return $customerInfo;
	}
	
	public function getCustomerValues($kanban){
		if(!$kanban && !$kanban->getId()){
			Mage::throwException('看板用户不存在');
		}
		$data = Mage::getSingleton('core/resource')->getConnection('core_read');
		$table = Mage::getSingleton('core/resource')->getTableName('pmtool_kanban_user');
		$result = $data->select()->from(array('main_table'=>$table))->where('main_table.type=?',Bf170_PMTool_Model_Kanban_User::TYPE_OWNER)->where('main_table.kanban_id=?',$kanban->getId());
		$kanbanCustomer =$data->fetchAll($result);
		if(!$kanbanCustomer && !$kanbanCustomer->getId()){
			Mage::throwException('看板管理员不存在');
		}
		$customerName = Mage::getModel('customer/customer')->load($kanbanCustomer[0]['customer_id'],'customer_id');
		return  $customerName->getFirstname();
	}
	
	public function getStatueValues($kanban){
		if(!$kanban && !$kanban->getId()){
			Mage::throwException('看板用户不存在');
		}
		$status = $kanban->getData('tag_info');
		$status = json_decode($status,true);
		return $status[1];
	}
	
	public function getCoustomerData($kanbanId){
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		if(!$customer || !$customer->getId()){
			Mage::throwException('无法加载用户信息');
		}
		$data = Mage::getSingleton('core/resource')->getConnection('core_read');
		$table = Mage::getSingleton('core/resource')->getTableName('pmtool_kanban_user');
		$result = $data->select()->from(array('main_table'=>$table))->where('main_table.customer_id=?',$customer->getId())->where('main_table.kanban_id=?',$kanbanId);
		$kanbanCustomer =$data->fetchAll($result);
		if($kanbanCustomer && $kanbanCustomer[0]['type']){
			return 	$kanbanCustomer[0]['type'];
		}
	}
	
	public function getjsonData($kanbanLabel,$kanbanCustomers){
		$jsondatas = array();
		$jsondata = array();
		$jsoncustomer = array();
		switch($kanbanLabel){
			case Bf170_PMTool_Model_Card::PRIORITY_LOW:
				foreach( Mage::helper('pmtool/kanban')->getLabelValues() as $labelId=>$label){
					if($kanbanLabel == $labelId){
						$jsondata[$labelId] = urlencode($label);
						$jsondatas[1] = $jsondata;
					}
				}
			break;
			case Bf170_PMTool_Model_Card::PRIORITY_NORMAL:
				foreach( Mage::helper('pmtool/kanban')->getLabelValues() as $labelId=>$label){
					if($kanbanLabel == $labelId){
						$jsondata[$labelId] = urlencode($label);
						$jsondatas[1] = $jsondata;
					}
				}
			break;
			case Bf170_PMTool_Model_Card::PRIORITY_HIGH:
				foreach( Mage::helper('pmtool/kanban')->getLabelValues() as $labelId=>$label){
					if($kanbanLabel == $labelId){
						$jsondata[$labelId] = urlencode($label);
						$jsondatas[1] = $jsondata;
					}
				}
			break;
			case Bf170_PMTool_Model_Card::PRIORITY_URGENT:
				foreach( Mage::helper('pmtool/kanban')->getLabelValues() as $labelId=>$label){
					if($kanbanLabel == $labelId){
						$jsondata[$labelId] = urlencode($label);
						$jsondatas[1] = $jsondata;
					}
				}
			break;
		}
		foreach($kanbanCustomers as $kanbanCustomer){
			$customerData = Mage::getModel('customer/customer')->load($kanbanCustomer);
			$jsoncustomer[$customerData->getId()] = urlencode($customerData->getFirstname());
		}
		if(!is_array($jsoncustomer) || empty($jsoncustomer)){
			Mage::throwException("看板没有参与的人员");
		}
		$jsondatas[2] = $jsoncustomer;
		return $jsondatas;
	}
	
	//获取卡片的数据
	public function getkapianData($kanbanBuZhouArray){
		$kanbanBuZhouArrs = array();
		foreach($kanbanBuZhouArray as $id=>$kanbanBuZhous){
			$kanbanBuZhouArrs[$id] = $kanbanBuZhous;
			foreach($kanbanBuZhouArrs[$id] as $name=>$kanbanBuZhou){
				$kanbanBuZhouArrs[$id][$name] = urlencode($kanbanBuZhou);	
			}
		}
		return $kanbanBuZhouArrs;
	}
	
	public function getJurisdiction($kanbanId){
		$customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
		$data = Mage::getSingleton('core/resource')->getConnection('core_read');
		$table = Mage::getSingleton('core/resource')->getTableName('pmtool_kanban_user');
		$result = $data->select()->from(array('main_table'=>$table))->where('main_table.customer_id=?',$customerId)->where('main_table.kanban_id=?',$kanbanId);
		$kanbanCustomer =$data->fetchAll($result);
		if($kanbanCustomer && $kanbanCustomer[0]['type'] == Bf170_PMTool_Model_Kanban_User::TYPE_OWNER && $kanbanCustomer[0]['status'] == Bf170_PMTool_Model_Kanban_User::STATUS_NORMAL){
			return true;
		}else{
			return false;
		}
	}
	
	//获取看板步骤的数据
	public function getkbuzhouData($kanbanBuZhouArray,$buzhouId){
		foreach($kanbanBuZhouArray as $id=>$kanbanBuZhous){
			if($buzhouId == $id){
				unset($kanbanBuZhouArray[$id]);
			}
		}
		$kanbanBuZhouArrs = array();
		foreach($kanbanBuZhouArray as $id=>$kanbanBuZhous){
			$kanbanBuZhouArrs[$id] = $kanbanBuZhous;
			foreach($kanbanBuZhouArrs[$id] as $name=>$kanbanBuZhou){
				$kanbanBuZhouArrs[$id][$name] = urlencode($kanbanBuZhou);	
			}
		}
		return $kanbanBuZhouArrs;
	}
	
	//获取看板成员的数据
	public function getUserValues($user){
		$user = json_decode($user,true);
		return $user[2];
	}
	
	//获取卡片限制时间的处理
	public function getTimesValues($times){
		$kapianTimes = array();
		$times = strtotime($times);
		$kapianTimes['y'] = date("Y",$times);
		$kapianTimes['m'] = date("m",$times);
		$kapianTimes['d'] = date("d",$times);
		return $kapianTimes;
	}
}