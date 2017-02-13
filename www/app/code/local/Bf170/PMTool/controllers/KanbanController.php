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

class Bf170_PMTool_KanbanController extends Mage_Core_Controller_Front_Action{
	
	public function indexAction() {
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->renderLayout();
	}
	
	public function viewAction() {
		try{
			$kanbanInfo = $this->getRequest()->getParam('info');
			$kanbanId = Mage::helper('core')->decrypt(base64_decode(urldecode($kanbanInfo)));
			$kanban = Mage::getModel('pmtool/kanban')->load($kanbanId);
			if(!$kanban || !$kanban->getId()){
				Mage::throwException('无法加载看板信息');
			}
			if($kanban->getStatus() == Bf170_PMTool_Model_Kanban::STATUS_ARCHIVED){
				Mage::throwException('看板已归档');
			}
			$customer = Mage::getSingleton('customer/session')->getCustomer();
			if(!$customer || !$customer->getId()){
				Mage::throwException('无法加载用户信息');
			}
			$kanbanUser = Mage::getModel('pmtool/kanban_user')->loadByAttributeFilter(array(
					'kanban_id'		=> $kanban->getId(),
					'customer_id'	=> $customer->getId(),
			));
			if(!$kanbanUser 
					|| !$kanbanUser->getId() 
					|| $kanbanUser->getData('status') == Bf170_PMTool_Model_Kanban_User::STATUS_DISABLED
			){
				Mage::throwException('尚未获得看板权限');
			}
			$banduan = Mage::helper('pmtool')->getJurisdiction($kanban->getId());
			Mage::register('current_kanban', $kanban);
			Mage::register('current_kanban_buzhou', $banduan);
			Mage::register('current_kanban_user', $kanbanUser);
			$this->loadLayout();
			$this->_initLayoutMessages('customer/session');
			$this->renderLayout();
		}catch(Exception $ex){
			Mage::getSingleton('customer/session')->addError($ex->getMessage());
			$this->_redirect('*/*/index');
		}
	}
	
	//添加看板
	public function addkanbanAction(){
		try{
			$kanbanName = $this->getRequest()->getParam('name');
			$kanbanDescription = $this->getRequest()->getParam('description');
			$kanbanStatus = $this->getRequest()->getParam('kanbanstatu');
			$kanbanType = $this->getRequest()->getParam('kanbantypes');
			$kanbanLabel = $this->getRequest()->getParam('kanbanlabel');
			$kanbanCustomer = $this->getRequest()->getParam('kanbanNames');
			$kanbanCustomers = explode(',',substr($kanbanCustomer,0,-1));
			$label_customer = Mage::helper('pmtool')->getjsonData($kanbanLabel,$kanbanCustomers);
			$jsonInfo = urldecode(json_encode($label_customer));
			$customer = Mage::getSingleton('customer/session')->getCustomer();
			if(!$customer && !$customer->getId()){
				Mage::throwException("无法加载用户信息");
			}
			if($kanbanStatus == Bf170_PMTool_Model_Kanban::STATUS_ARCHIVED){
				Mage::throwException('看板已归档');
			}
			if(!is_numeric($kanbanType)){
				Mage::throwException('您提交的看板类型不符合');
			}
			if(!is_numeric($kanbanStatus)){
				Mage::throwException('您提交的看板状态不符合');
			}
			$kanban = Mage::getModel('pmtool/kanban')->load($kanbanName,'name');
			if(!!$kanban && !!$kanban->getId()){
				Mage::throwException("您所添加的看板已经存在");			
			}
			$kanban = Mage::getModel('pmtool/kanban');
			$kanban->setData('name',$kanbanName);
			$kanban->setData('tag_info',$jsonInfo);
			$kanban->setData('description',$kanbanDescription);
			$kanban->setData('status',$kanbanStatus);
			$kanban->setData('type',$kanbanType);
			$kanban->save();
			$kanban = Mage::getModel('pmtool/kanban')->load($kanbanName,'name');
			if(!$kanban && !$kanban->getId()){
				Mage::throwException("添加看板失败");
			}
			
			//添加看板用户(成员与高级管理员)
			Mage::helper('pmtool/kanban')->tianjiaKanbanUser($kanbanCustomer,$kanban->getId(),$customer);
			
			//使添加看板的用户自动成为这个看板的管理人员
			$kanbancustomer = Mage::getModel('pmtool/kanban_user');
			$kanbancustomer->setData('customer_id',$customer->getId());
			$kanbancustomer->setData('kanban_id',$kanban->getId());
			$kanbancustomer->setData('type',Bf170_PMTool_Model_Kanban_User::TYPE_OWNER);
			$kanbancustomer->setData('status',Bf170_PMTool_Model_Kanban_User::STATUS_NORMAL);
			$kanbancustomer->save();
			$kanbancustomer = Mage::getModel('pmtool/kanban_user')->load($kanban->getId(),'kanban_id');
			if(!$kanbancustomer && !$kanbancustomer->getId()){
				Mage::throwException('看板用户添加失败');
			}
			$jsonInfo = array(
				'status' => "添加成功",	
			);
			echo json_encode($jsonInfo);
			exit;
		}catch(Exception $ex){
			Mage::getSingleton('customer/session')->addError($ex->getMessage());
			$this->_redirect('*/*/index');
		}
	}
	
	//对看板步骤的添加
	public function addbuzhouAction(){
		try{
			$kapianName = $this->getRequest()->getParam('name');
			$kanbanId = $this->getRequest()->getParam('kanbanid');
			$customer = Mage::getSingleton('customer/session')->getCustomer();
			if(!$customer && !$customer->getId()){
				Mage::throwException("无法加载用户信息");
			}
			$kanbanData = Mage::getModel('pmtool/kanban')->load($kanbanId);
			if(!$kanbanData && $kanbanData->getId()){
				Mage::throwException('你添加卡片的看板不存在');
			}
			$kanbanStatus = $kanbanData->getData('status');
			if($kanbanStatus == Bf170_PMTool_Model_Kanban::STATUS_ARCHIVED){
				Mage::throwException('看板已归档,无法添加卡片');
			}
			$tianjiaKapian = array();
			$tianjiaKapian['name'] = $kapianName;
			$kanbanAdditionalInfo = $kanbanData->getData('process_info');
			if(!!$kanbanAdditionalInfo){
				//有卡片步骤存在的情况下的处理方法
				$kanbanBuZhouArray = json_decode($kanbanAdditionalInfo,true);
				$kanbanBuZhouIds = '';
				foreach($kanbanBuZhouArray as $kanbanBuZhouId=>$kapianname){
					$kanbanBuZhouIds = $kanbanBuZhouId;
				}
				$kanbanBuZhouIds++;
				$kanbanBuZhouArray[$kanbanBuZhouIds] = $tianjiaKapian;
				$kanbanBuZhouArrays = Mage::helper('pmtool')->getkapianData($kanbanBuZhouArray);
				$jsonInfo = urldecode(json_encode($kanbanBuZhouArrays));
				$kanbanData->setData('process_info',$jsonInfo);
				$kanbanData->save();
				$jsonInfo = array(
						'status' => "添加成功",	
					);
			echo json_encode($jsonInfo);
			exit;
			}else{
				//没有卡片步骤存在的情况下的处理方法
				$kanbanBuZhouIds = 1;
				$kanbanAdditionalInfo = array();
				$tianjiaKapian['name'] = urlencode($kapianName);
				$kanbanAdditionalInfo[$kanbanBuZhouIds] = $tianjiaKapian;
				$jsonInfo = urldecode(json_encode($kanbanAdditionalInfo));
				$kanbanData->setData('process_info',$jsonInfo);
				$kanbanData->save();
				$jsonInfo = array(
						'status' => "添加成功",	
					);
			echo json_encode($jsonInfo);
			exit;	
			}
			
		}catch (Exception $ex){
			Mage::getSingleton('customer/session')->addError($ex->getMessage());
			$this->_redirect('*/*/index');
		}
	}
	
	//对看板步骤的删除
	public function delbuzhouAction(){
		try{
			$kanbanId = $this->getRequest()->getParam('kanban_id');
			$buzhouId = $this->getRequest()->getParam('buzhou_Id');
			$kanban = Mage::getModel('pmtool/kanban')->load($kanbanId);
			if($kanban->getStatus() == Bf170_PMTool_Model_Kanban::STATUS_ARCHIVED){
				Mage::throwException('看板已归档');
			}
			$customer = Mage::getSingleton('customer/session')->getCustomer();
			if(!$customer || !$customer->getId()){
				Mage::throwException('无法加载用户信息');
			}
			if(!$kanban && !$kanban->getId()){
				Mage::throwException('您所删除的卡片步骤不存在!');
			}
			//查看这个步骤里面是否有卡片
			$kapians = Mage::helper('pmtool/card_cardMessage')->getbuzhouIds($buzhouId);
				if(in_array($buzhouId,$kapians[0])){
					$jsonInfo = array(
							'status' => "存在卡片,无法删除步骤",	
						);
				echo json_encode($jsonInfo);
				exit;
			}
			$kanbanAdditionalInfo = $kanban->getData('process_info');
			if($kanbanAdditionalInfo){
				$kanbanBuZhouArray = json_decode($kanbanAdditionalInfo,true);
				$kanbanBuZhouArrays = Mage::helper('pmtool')->getkbuzhouData($kanbanBuZhouArray,$buzhouId);
				$jsonInfo = urldecode(json_encode($kanbanBuZhouArrays));
				$kanban->setData('process_info',$jsonInfo);
				$kanban->save();
				$jsonInfo = array(
						'status' => "删除卡片步骤成功",	
					);
			echo json_encode($jsonInfo);
			exit;
			}else{
				Mage::throwException('无法删除卡片步骤,请刷新页面');
			}
		}catch (Exception $ex){
			Mage::getSingleton('customer/session')->addError($ex->getMessage());
			$this->_redirect('*/*/index');
		}
		
	}
	
	//卡片上传文件
	public function updatafileAction(){
		try{
			$cardId = $this->getRequest()->getParam('card_id');
			if(!$cardId){
				Mage::throwException("无法进行文件上传");
			}
			$cardId = substr($cardId,'20');
			if(!is_numeric($cardId)){
				Mage::throwException('无法对上传文件进行保存!');
			}
			$fileData = serialize(Mage::helper('pmtool/import')->loadDataFromCsv('fileToUpload'));
			
			//对上传的文件进行保存
			$carddata = Mage::getModel('pmtool/card')->load($cardId);
			$carddata->setData('attachment_info',$fileData);
			$carddata->save();
			exit;
		}catch (Exception $ex){
			Mage::getSingleton('customer/session')->addError($ex->getMessage());
			$this->_redirect('*/*/index');
		}
	}
	
	//对卡片修改的操作
	public function updatacardcommentAction(){
		try{
			$cardId = $this->getRequest()->getParam('cardid');
			$cardcustomer = $this->getRequest()->getParam('name');
			$carddescribe = $this->getRequest()->getParam('description');
			$cardcomment = $this->getRequest()->getParam('comment');
			$kapianevaluate = $this->getRequest()->getParam('evaluate');
			$endtime = $this->getRequest()->getParam('endtime');
			$type = $this->getRequest()->getParam('type');
			$customer = Mage::getSingleton('customer/session')->getCustomer();
			if(!$customer || !$customer->getId()){
				Mage::throwException('无法加载用户信息');
			}
			if(!$cardId){
				Mage::throwException('无法保存数据!');
			}
			$cardid = substr($cardId,'20');
			if(!is_numeric($cardid)){
				Mage::throwException('无法保存数据!');
			}
			
			//对存储信息的处理
			$cardUserData = Mage::helper('pmtool/card_userMessage')->cardCustomer($cardcustomer,$cardid);
			$cardDatas = Mage::getModel('pmtool/card')->load($cardid);
			$message = array();
			$message['tag_info'] = $cardDatas->getData('tag_info');
			$message['description'] = $cardDatas->getData('description');
			$message['due_at'] = $cardDatas->getData('due_at');
			$message['comment_info'] = $cardDatas->getData('comment_info');
			$message['priority'] = $cardDatas->getData('priority');
			$message['name'] = $cardDatas->getData('name');
			
			//标签和参与卡片的用户
			if($cardUserData){
				$cardDatas->setData('tag_info',$cardUserData);
			}
			$cardDatas->setData('description',$carddescribe);

			//评论信息是进行添加,所以需要进一步处理.......
			if($kapianevaluate){
				$kapianevaluates = Mage::helper("pmtool/card_cardMessage")->getKapianEvaluate($kapianevaluate,$cardDatas);
				$cardDatas->setData('comment_info',$kapianevaluates);
			}
			$cardDatas->setData('due_at',$endtime);
			$cardDatas->setData('priority',$type);
			$cardDatas->save();
			
			//对卡片信息修改过后需要给给相关人员进行信息发送
			Mage::helper('pmtool/card_smsMessage')->sendMessages($cardDatas,$message,$kapianevaluate);
		}catch(Exception $ex){
			Mage::getSingleton('customer/session')->addError($ex->getMessage());
			$this->_redirect('*/*/view');
		}
		$jsonInfo = array(
					'status' => "卡片修改成功",	
				);
		echo json_encode($jsonInfo);
		exit;
	}
	
	//对添加卡片的操作
	public function addkapianAction(){
		try{
			$kanbianId = $this->getRequest()->getParam('kanbianId');
			$buzhouId = $this->getRequest()->getParam('buzhouId');
			$kapianName = $this->getRequest()->getParam('kapianName');
			$kapianMiaoshu = $this->getRequest()->getParam('kapianMiaoshu');
			$lateTime = $this->getRequest()->getParam('lateTime');
			$kapianType = $this->getRequest()->getParam('kapianType');
			$kapianStatus = $this->getRequest()->getParam('kapianStatus');
			$customerId = $this->getRequest()->getParam('customerId');
			$kanbanCustomers = explode(',',substr($customerId,0,-1));
			$users = Mage::helper('pmtool/card_cardMessage')->getcustomersId($kanbanCustomers);
			$customer = Mage::getSingleton('customer/session')->getCustomer();
			if(!$customer || !$customer->getId()){
				Mage::throwException('无法加载用户信息');
			}
			$kanbans = Mage::getModel('pmtool/kanban')->load($kanbianId);
			if(!$kanbans && $kanbans->getId()){
				Mage::throwException('您所添加的看板不存在,无法添加卡片!');
			}
			$buzhouIds = Mage::helper('pmtool/card_cardMessage')->getbuzhouId($buzhouId,$kanbianId);
			if(!$buzhouIds){
				Mage::throwException("您所添加的卡片的步骤不存在!");
			}
			$kapiannames = Mage::helper('pmtool/card_cardMessage')->getKapianName($buzhouId,$kanbianId,$kapianName);
			if(!empty($kapiannames)){
				Mage::throwException("您所添加的卡片名称已存在!");
			}
			$kapians = Mage::getModel('pmtool/card');
			$kapians->setData('kanban_id',$kanbianId);
			$kapians->setData('process_id',$buzhouId);
			$kapians->setData('name',$kapianName);
			$kapians->setData('description',$kapianMiaoshu);
			$kapians->setData('due_at',$lateTime);
			$kapians->setData('priority',$kapianType);
			$kapians->setData('status',$kapianStatus);
			$kapians->setData('tag_info',$users);
			$kapians->save();
			
			//对添加的卡片中参与的成员进行短信通知
			Mage::helper('pmtool/card_smsMessage')->sendAddCardMessage($kapians);
			
			//拿出这个步骤里面是否有卡片(局部刷新)
//			$jsonInfos = Mage::helper('pmtool/card_cardMessage')->getAllkapians($buzhouId,$kanbianId,$kapianName);
//			if(empty($jsonInfos)){
//				$jsonInfos = array(
//						'status' => "添加卡片失败",	
//					);
//			}
//			$data_kapians = array();
//			$data_kapian = array();
//			foreach($jsonInfos as $ids=>$jsonInfos){
//				foreach($jsonInfos as $id=>$jsonarray){
//					$data_kapian[$id] = urlencode($jsonarray);
//				}
//			}
//			$jsonInfos = urldecode(json_encode($data_kapian));
		}catch (Exception $ex){
			Mage::getSingleton('customer/session')->addError($ex->getMessage());
			$this->_redirect('*/*/view');
		}
		$jsonInfos = array(
						'status' => "添加卡片成功",	
					);
		echo json_encode($jsonInfos);
		exit;		
	}
	
	//卡片拖动之后进行保存
	public function addressChengeAction(){
		try{
			$buzhouId = $this->getRequest()->getParam('buzhouaddress');
			$kabianId = $this->getRequest()->getParam('cardaddress');
			$kanbianId = $this->getRequest()->getParam('kanbanid');
			$customer = Mage::getSingleton('customer/session')->getCustomer();
			if(!$customer || !$customer->getId()){
				Mage::throwException('无法加载用户信息');
			}
			/********************这里可以添加一个对用户的是否有权限对卡片的拖动的保存*********************/
			//对卡片拖动之后数据进行保存处理
			$kapianTuodongs = Mage::helper('pmtool/card')->getKapianTuodongValues($buzhouId,$kabianId);
			
			//对移动的卡片的数据存入表中
			$kapianinfos = Mage::getModel('pmtool/card')->getCollection();
			$kapianinfos->getSelect()->where("kanban_id=?",$kanbianId);
			$tuodong = array();
			$weituodong = array();
			$user = array();
			foreach($kapianinfos as $kapianinfo){
				
				//卡片未拖动之前的位置
				$weituodong[$kapianinfo->getId()] = $kapianinfo->getData('process_id');
				foreach($kapianTuodongs as $id=>$kapianTuodong){
					if($kapianinfo->getId() == $kapianTuodong[0]){
						$user[$kapianinfo->getId()] = $kapianinfo->getData('tag_info');
						$kapianinfo->setData('process_id',$id);
						$kapianinfo->save();
					}
				}
			}
			foreach($kapianTuodongs as $ids=>$kapid){
				foreach($kapid as $process_id=>$process){
					
					//拖动之后卡片的位置
					$tuodong[$process] = $ids;
				}
			}
			//卡片拖动之后向成员发送短信
			Mage::helper('pmtool/card_smsMessage')->sendDragCardMessage($tuodong,$weituodong,$user,$kanbianId);
			$jsonInfo = array(
						'status' => "卡片移动成功",
					);
			echo json_encode($jsonInfo);
			exit;
		}catch (Exception $ex){
			Mage::getSingleton('customer/session')->addError($ex->getMessage());
			$this->_redirect('*/*/view');
		}
	}
}