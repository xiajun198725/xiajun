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
class Bf170_PMTool_Model_Card extends Bf170_CoreService_Model_Object {
	
	const PRIORITY_LOW				= 0; // 低
	const PRIORITY_NORMAL			= 100; // 正常
	const PRIORITY_HIGH				= 200; // 高
	const PRIORITY_URGENT			= 1000; // 紧急
	
	const STATUS_NORMAL				= 0; // 正常
	const STATUS_ARCHIVED			= 100; // 归档
	
	protected function _construct() {
		// 指向相应的 Resource (Model)
		$this->_init('pmtool/card');
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
	public function getJsonInfo($kanbanId){
		$kanbancustomers = Mage::getModel('pmtool/kanban')->load($kanbanId);
		$kanbanUser = $kanbancustomers->getData('tag_info');
		$usermessage = json_decode($kanbanUser,true);
		$usermessage = $usermessage[2];
		$taginfo = Mage::helper('pmtool/card')->getPriorityValues();
		$ures_info = json_decode($this->getData('tag_info'),true);	
		$infoArray = array(
			'card_id'		=> $this->getId(),
			'process_id'	=> $this->getData('process_id'),
			'name'		    => $this->getData('name'),
			'description'   => $this->getData('description'),
			'kapian_taginfo'=> $taginfo,
		    'customer_name' => $usermessage,
			'tag_info'      => $ures_info,
			'due_at'        => $this->getData('due_at'),
			'priority'        => $this->getData('priority'),
			'comment_info'  => Mage::helper('pmtool/card')->getEvaluate($this->getData('comment_info')),
			'tag_kapian'    => Mage::helper('pmtool/card')->getTypeValue($this->getData('priority')),
		);
		return json_encode($infoArray);
	}
	
	public function getStatusLabel() {
		$statusValues = Mage::helper('pmtool/card')->getStatusValues();
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

}