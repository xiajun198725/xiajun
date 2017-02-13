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
class Bf170_PMTool_Model_Kanban extends Bf170_CoreService_Model_Object {
	
	const STATUS_NORMAL				= 0; //正常
	const STATUS_ARCHIVED			= 100; //归档
	
	const TYPE_PROMO			    = 200; //会员专区
	const TYPE_KANGSIDA			    = 300; //康士达
	const TYPE_FINGERTIP			= 400; //指尖风景
	const TYPE_WEIKETANG			= 500; //魏课堂
	const TYPE_PHONE_APP			= 600; //手机APP
	const TYPE_WORK_KANPAN			= 700; //工作看板
	
	protected $_cardCollection		= null;
	
	protected function _construct() {
		// 指向相应的 Resource (Model)
		$this->_init('pmtool/kanban');
	}
	
	// 一般在保存前，赋予更新时间（初次保存，赋予创建时间）
	protected function _beforeSave() {
		parent::_beforeSave();
		//For new object which does not specify 'created_at'
		if(!$this->getId() && !$this->getData('created_at')){
			$this->setData('created_at', now());
		}
		//Always specify 'updated_at'
		$this->setData('updated_at', now());
		return $this;
	}
	
	// ======================== Utilities: General ======================== //
	public function prepareProcessList(){
		if(!$this->getId()){
			return null;
		}
		$processList = array();
		
		// 基本数据准备
		$processInfo = json_decode($this->getData('process_info'), 1);
		ksort($processInfo);
		foreach($processInfo as $processId => $processData){
			$processInfo[$processId]['id'] = $processId;
			$processInfo[$processId]['card_list'] = array();
		}
		
		// 填入卡片信息
		foreach($this->getCardCollection() as $card){
			// 如果出现卡片未分类情况，创建一个新分类
			if(!array_key_exists($card->getData('process_id'), $processInfo)){
				$processInfo[$card->getData('process_id')] = array(
						'id'		=> $card->getData('process_id'),
						'name'		=> "其他-{$card->getData('process_id')}",
						'card_list'	=> array(),
				);
			}
			$processInfo[$card->getData('process_id')]['card_list'][] = $card;
		}
		// 卡片按照sort_order排序
		foreach($processInfo as $processId => $processData){
			usort($processInfo[$processId]['card_list'], array($this, 'compareCardBySortOrder'));
		}
		
		// 封装成一个对象使用起来比数组更方便
		foreach($processInfo as $processId => $processData){
			$process = new Varien_Object($processData);
			$processList[$processId] = $process;
		}
		
		return $processList;
	}
	
	public function getCardCollection(){
		if(!$this->getId()){
			return null;
		}
		if($this->_cardCollection === null){
			$this->_cardCollection = Mage::getModel('pmtool/card')->getCollection();
			$this->_cardCollection->getSelect()
					->where('kanban_id = ?', $this->getId())
					->where('status = ?', Bf170_PMTool_Model_Card::STATUS_NORMAL);
		}
		return $this->_cardCollection;
	}
	
	public function getUserTypeLabel() {
		$userTypeValues = Mage::helper('pmtool/kanban_user')->getTypeValues();
		$userType = $this->getUserType();
		if(isset($userTypeValues[$userType])){
			return $userTypeValues[$userType];
		}
		return "";
	}
	
	public function getStatusLabel() {
		$statusValues = Mage::helper('pmtool/kanban')->getStatusValues();
		$status = $this->getStatus();
		if(isset($statusValues[$status])){
			return $statusValues[$status];
		}
		return "";
	}
	
	public function forcedUpdatedAt($updatedAt){
		$this->setData('updated_at', $updatedAt);
		$this->_isForcedUpdatedAt = true;
	}
	
	public function compareCardBySortOrder($card1, $card2){
		if ($card1->getData('sort_order') == $card2->getData('sort_order')) {
			return 0;
		}
		return ($card1->getData('sort_order') < $card2->getData('sort_order')) ? -1 : 1;
	}

}