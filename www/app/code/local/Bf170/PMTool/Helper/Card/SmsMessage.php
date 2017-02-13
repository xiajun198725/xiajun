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
class Bf170_PMTool_Helper_Card_SmsMessage extends Mage_Core_Helper_Abstract {
	
	//卡片修改之后对相应的成员发送修改内容的信息
	public function sendMessages($cardDatas,$message,$kapianevaluate){
		if(!is_object($cardDatas)){
			Mage::throwException("卡片信息有误");
		}
		if(!is_array($message)){
			Mage::throwExcepion("卡片修改信息有误");
		}
		$messages = array();
		if($message['tag_info'] !== $cardDatas->getData('tag_info')){
			$message['tag_info'] = json_decode($message['tag_info'],true);
			$cardures = json_decode($cardDatas->getData('tag_info'),true);
			if(count($message['tag_info']) < count($cardures)){
				$unmber = "加入";
			}else{
				$unmber = "退出";
			}
			foreach($cardures as $k=>$customerName){
				$customer = Mage::getModel('customer/customer')->load($k);
				$messages['telephone'] .= $customer->getData('telephone').",";
			}
			$messages['tag_info_add'] = $cardures;
		}else{
			$cardures = json_decode($cardDatas->getData('tag_info'),true);
			foreach($cardures as $k=>$customerName){
				$customer = Mage::getModel('customer/customer')->load($k);
				$messages['telephone'] .= $customer->getData('telephone').",";
			}
		}
		if($message['description'] !== $cardDatas->getData('description')){
			$messages['description_add'] = $cardDatas->getData('description');
		}
		if($message['due_at'] !== $cardDatas->getData('due_at')){
			$messages['due_at_add'] = $cardDatas->getData('due_at');
		}
		if($message['comment_info'] !== $cardDatas->getData('comment_info')){
			$messages['comment_info_add'] = $kapianevaluate;
		}
		if($message['priority'] !== $cardDatas->getData('priority')){
			$messages['priority_add'] = $cardDatas->getData('priority');
		}
		$messages['name'] = $message['name'];
		$sendMessage = $this->messagearrangement($messages,$unmber);
		$type = Bf170_Bf170Sms_Model_Record::TYPE_SMS_KAPIAN_XIUGAI;
		Mage::helper('bf170sms/record')->sendSms($type,$sendMessage['telephone'],$sendMessage['content']);
		return true;
	}
	
	//添加卡片的信息进行验证
	public function sendAddCardMessage($kapians){
		if(!is_object($kapians)){
			Mage::throwException("卡片添加失败");
		}
		$cardAddMessage = array();
		$cardAddMessage['kanban_id'] = $kapians->getData('kanban_id');
		$cardAddMessage['process_id'] = $kapians->getData('process_id');
		$cardAddMessage['name'] = $kapians->getData('name');
		$cardAddMessage['description'] = $kapians->getData('description');
		$cardAddMessage['due_at'] = $kapians->getData('due_at');
		$cardAddMessage['priority'] = $kapians->getData('priority');
		$cardAddMessage['status'] = $kapians->getData('status');
		$cardAddMessage['tag_info'] = $kapians->getData('tag_info');
		if(!is_array($cardAddMessage)){
			Mage::throwExcepion("卡片添加信息有误");
		}
		if($cardAddMessage['kanban_id']){
			$kanban = Mage::getModel('pmtool/kanban')->load($cardAddMessage['kanban_id']);
			if($kanban && $kanban->getId()){
				$cardAddMessage['kanban_id'] = $kanban->getData('name');
			}
			if($cardAddMessage['process_id']){
				$buzhous = json_decode($kanban->getData('process_info'),1);
				foreach($buzhous as $k=>$name){
					if($cardAddMessage['process_id'] == $k){
						$cardAddMessage['process_id'] = $name['name'];
					}
				}
			}
			if($cardAddMessage['tag_info']){
				$cardures = json_decode($cardAddMessage['tag_info'],1);
				foreach($cardures as $k=>$customerName){
					$customer = Mage::getModel('customer/customer')->load($k);
					$cardAddMessage['telephone'] .= $customer->getData('telephone').",";
					$cardAddMessage['tag_info_name'] .= $customerName.',';
				}
			}
		}
		$sendMessage = $this->cardAddMessage($cardAddMessage);
		$type = Bf170_Bf170Sms_Model_Record::TYPE_SMS_KAPIAN_ADD;
		Mage::helper('bf170sms/record')->sendSms($type,$sendMessage['telephone'],$sendMessage['content']);
		return true;
	}
	
	//对卡片移动之后的位置告诉相应成员
	public function sendDragCardMessage($tuodong,$weituodong,$user,$kanbianId){
		if(is_array($tuodong) && is_array($weituodong)){
			$cardcall = array();
			foreach($tuodong as $a=>$tuodongs){
				foreach($weituodong as $b=>$weituodongs){
					if($a == $b && $tuodongs != $weituodongs){
						$cardcall[$a] = $tuodongs;
					} 
				}
			}
			if(!$cardcall){
				Mage::throwException("请在拖动卡片的位置之后进行保存!");
			}
			$card = array();
			foreach($cardcall as $k=>$cardcalls){
				$card[$k]['drag_'.$k] = $cardcalls;
				$card[$k]['wei_'.$k] = $weituodong[$k];
				foreach($user as $i=>$users){
					if($k == $i){
						$card[$k]['name_'.$k] = json_decode($users,1);
					}
				}
			}
			$kanban = Mage::getModel('pmtool/kanban')->load($kanbianId);
			$card['kanban_name'] = $kanban->getData('name');
			$senddragmessage = $this->bragcardmessage($card,$kanbianId);
			$type = Bf170_Bf170Sms_Model_Record::TYPE_SMS_KAPIAN_DRAG;
			Mage::helper('bf170sms/record')->sendSms($type,$senddragmessage['telephone'],$senddragmessage['content']);
			return true;
		}
	}
	//对卡片修改需要发送的信息进行整理
	private function  messagearrangement($messages,$unmber){
		$customername = Mage::getSingleton('customer/session')->getCustomer()->getFirstname();
		$datas = array();
		$i = 1;
		$datas['content'] = $customername."修改了卡片《".$messages['name']."》的内容如下:";
		if($messages['description_add']){
			$datas['content'] .= $i++.".修改详情:".$messages['description_add'].".";
		}
		if($messages['comment_info_add']){
			$datas['content'] .= $i++.".添加评论:".$messages['comment_info_add'].".";
		}
		if($messages['due_at_add']){
			$datas['content'] .= $i++.".到期时间修改为:".$messages['due_at_add'].'结束.';
		}
		if($messages['priority_add']){
			$status = Mage::helper('pmtool/card')->getPriorityValues();
			$datas['content'] .= $i++.".状态修改为:".$status[$messages['priority_add']].".";
		}
		if($messages['tag_info_add']){
			foreach($messages['tag_info_add'] as $name){
				$names .= $name.".";
			}
			$datas['content'] .= $i++."有成员".$unmber.",现有成员".$names.".";
		}

		$datas['telephone'] = explode(',',rtrim($messages['telephone'],','));
		return $datas;
	}
	
	//对添加卡片需要发送的信息进行整理
	private function cardAddMessage($cardAddMessage){
		$customername = Mage::getSingleton('customer/session')->getCustomer()->getFirstname();
		$datas = array();
		$i = 1;
		$datas['content'] = $customername."添加了《".$cardAddMessage['kanban_id']."》看板中卡片《".$cardAddMessage['name']."》的内容如下:";
		if($cardAddMessage['description']){
			$datas['content'] .= $i++.".详情:".$cardAddMessage['description'].".";
		}
		if($cardAddMessage['due_at']){
			$datas['content'] .= $i++.".到期时间为:".$cardAddMessage['due_at'].'结束.';
		}
		if($cardAddMessage['priority']){
			$status = Mage::helper('pmtool/card')->getPriorityValues();
			$datas['content'] .= $i++.".状态修改为:".$status[$cardAddMessage['priority']].".";
		}
		if($cardAddMessage['tag_info']){
			$datas['content'] .= $i++.".参与成员有:".$cardAddMessage['tag_info_name'].'.';
		}
		if($cardAddMessage['telephone']){
			$datas['telephone'] = explode(',',rtrim($cardAddMessage['telephone'],','));
		}
		return $datas;
	}
	
	//对卡片移动需要发送的信息进行整理
	private function bragcardmessage($cards,$kanbianId){
		$customername = Mage::getSingleton('customer/session')->getCustomer()->getFirstname();
		$datas = array();
		$telephones = array();
		$i = 1;
		$datas['content'] = $customername."移动看板《".$cards['kanban_name']."》中的卡片如下:";
		foreach($cards as $j=>$card){
			if(is_numeric($j)){
				$c = $j;
				$cardobj = Mage::getModel('pmtool/kanban')->load($kanbianId);
				$buzhous = json_decode($cardobj->getData('process_info'),1);
				$carname = Mage::getModel('pmtool/card')->load($j);
				$cardname = $carname->getData('name');
				$datas['content'] .= $i++.".卡片《".$cardname."》从步骤[".$buzhous[$card['wei_'.$j]]['name']."]移动到步骤[".$buzhous[$card['drag_'.$j]]['name']."]中.";
				foreach($card as $e=>$names){
					if($e == "name_$c"){
						foreach($names as $d=>$cardmessage){
							if(!empty($telephones)){
								$customer = Mage::getModel('customer/customer')->load($d);
								if(!in_array($customer->getData('telephone'),$telephones)){
									$telephones[] = $customer->getData('telephone');
								}
							}else{
								$customer = Mage::getModel('customer/customer')->load($d);
								$telephones[] = $customer->getData('telephone');
							}
						}	
					}
				}
			}
		}
		$datas['telephone'] = $telephones;
		return $datas;
	}
}