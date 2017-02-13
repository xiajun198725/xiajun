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
class Bf170_PMTool_Helper_Card_UserMessage extends Mage_Core_Helper_Abstract {
	
	//卡片参与成员的修改(为了简单化,就是对成员的重新选择)
	public function cardCustomer($cardcustomer,$cardid){
		$customerdatas = array();
		if($cardcustomer){
			foreach($cardcustomer as $customerId){
				$customerdatas[$customerId] = urlencode(Mage::getModel('customer/customer')->load($customerId)->getFirstname());
			}
			$customerJsoninfo = urldecode(json_encode($customerdatas));
			return $customerJsoninfo;
		}else{
		return false;	
		}
	}
}