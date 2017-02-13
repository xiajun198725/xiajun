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
class Bf170_PMTool_Helper_Card_User extends Mage_Core_Helper_Abstract {
	
	public function getTypeValues(){
		return array(
				Bf170_PMTool_Model_Card_User::TYPE_MEMBER		=> Mage::helper('pmtool')->__('成员'),
				Bf170_PMTool_Model_Card_User::TYPE_OWNER		=> Mage::helper('pmtool')->__('管理员'),
		);
	}
	
	public function getStatusValues(){
		return array(
				Bf170_PMTool_Model_Card_User::STATUS_NORMAL		=> Mage::helper('pmtool')->__('正常'),
				Bf170_PMTool_Model_Card_User::STATUS_DISABLED	=> Mage::helper('pmtool')->__('禁用'),
		);
	}
	
}