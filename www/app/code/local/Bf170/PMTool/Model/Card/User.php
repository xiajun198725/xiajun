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
class Bf170_PMTool_Model_Card_User extends Bf170_CoreService_Model_Object {
	
	const TYPE_MEMBER			= 0; // 成员
	const TYPE_OWNER			= 100; // 责任人
	
	const STATUS_NORMAL			= 0; // 可用
	const STATUS_DISABLED		= 100; // 禁用
	
	protected function _construct() {
		// 指向相应的 Resource (Model)
		$this->_init('pmtool/card_user');
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
	public function getTypeLabel() {
		$typeValues = Mage::helper('pmtool/card_user')->getTypeValues();
		$type = $this->getType();
		if(isset($typeValues[$type])){
			return $typeValues[$type];
		}
		return "";
	}
	
	public function getStatusLabel() {
		$statusValues = Mage::helper('pmtool/card_user')->getStatusValues();
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