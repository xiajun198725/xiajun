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

class Bf170_UserAccess_Model_Rewrite_Customer_Resource_Customer extends Mage_Customer_Model_Resource_Customer {
	
	public function loadByTelephone(Mage_Customer_Model_Customer $customer, $telephone, $testOnly = false){
		$adapter = $this->_getReadAdapter();
		$bind	= array('customer_telephone' => $telephone);
		$select  = $adapter->select()
			->from($this->getEntityTable(), array($this->getEntityIdField()))
			->where('telephone = :customer_telephone');

		if ($customer->getSharingConfig()->isWebsiteScope()) {
			if (!$customer->hasData('website_id')) {
				Mage::throwException(
					Mage::helper('customer')->__('Customer website ID must be specified when using the website scope')
				);
			}
			$bind['website_id'] = (int)$customer->getWebsiteId();
			$select->where('website_id = :website_id');
		}

		$customerId = $adapter->fetchOne($select, $bind);
		if ($customerId) {
			$this->load($customer, $customerId);
		} else {
			$customer->setData(array());
		}

		return $this;
	}
	
	public function loadByIdCardNumber(Mage_Customer_Model_Customer $customer, $idCardNumber, $idCardType, $testOnly = false){
		$adapter = $this->_getReadAdapter();
		$bind	= array(
				'customer_id_card_number'	=> $idCardNumber,
				'customer_id_card_type'		=> $idCardType,
		);
		$select  = $adapter->select()
			->from($this->getEntityTable(), array($this->getEntityIdField()))
			->where('id_card_number = :customer_id_card_number')
			->where('id_card_type = :customer_id_card_type');

		if ($customer->getSharingConfig()->isWebsiteScope()) {
			if (!$customer->hasData('website_id')) {
				Mage::throwException(
					Mage::helper('customer')->__('Customer website ID must be specified when using the website scope')
				);
			}
			$bind['website_id'] = (int)$customer->getWebsiteId();
			$select->where('website_id = :website_id');
		}

		$customerId = $adapter->fetchOne($select, $bind);
		if ($customerId) {
			$this->load($customer, $customerId);
		} else {
			$customer->setData(array());
		}

		return $this;
	}

}