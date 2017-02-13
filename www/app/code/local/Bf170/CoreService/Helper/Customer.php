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

class Bf170_CoreService_Helper_Customer extends Mage_Core_Helper_Data {
	
	const MEMBERSHIP_TYPE_MEMBER		= 0;	// 云商
	const MEMBERSHIP_TYPE_DIAMOND		= 50;	// 创享云商
	const MEMBERSHIP_TYPE_DEALER		= 100;	// 经销商
	const MEMBERSHIP_TYPE_AGENT			= 200;	// 代理商
	
	const LOGIN_LINK_LIFETIME			= 3600;
	
	public function getMembershipTypeOptions() {
		return array(
				self::MEMBERSHIP_TYPE_MEMBER		=> '云商',
				self::MEMBERSHIP_TYPE_DIAMOND		=> '创享云商',
				self::MEMBERSHIP_TYPE_DEALER		=> '经销商',
				self::MEMBERSHIP_TYPE_AGENT			=> '代理商',
		);
	}
	
	public function getMembershipTypeLabel($customer){
		$membershipTypeOptions = $this->getMembershipTypeOptions();
		if(!!$customer && !!$customer->getId() && array_key_exists($customer->getData('membership_type'), $membershipTypeOptions)){
			return $membershipTypeOptions[$customer->getData('membership_type')];
		}
		return '有误';
	}
	
}