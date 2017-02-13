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

class Bf170_UserAccess_Model_Rewrite_Admin_User extends Mage_Admin_Model_User {

	public function validate() {
		$errors = array();

		if (!Zend_Validate::is($this->getUsername(), 'NotEmpty')) {
			$errors[] = Mage::helper('adminhtml')->__('User Name is required field.');
		}

		if (!Zend_Validate::is($this->getFirstname(), 'NotEmpty')) {
			$errors[] = Mage::helper('adminhtml')->__('First Name is required field.');
		}

//		if (!Zend_Validate::is($this->getLastname(), 'NotEmpty')) {
//			$errors[] = Mage::helper('adminhtml')->__('Last Name is required field.');
//		}

		if (!Zend_Validate::is($this->getEmail(), 'EmailAddress')) {
			$errors[] = Mage::helper('adminhtml')->__('Please enter a valid email.');
		}

		if ($this->hasNewPassword()) {
			if (Mage::helper('core/string')->strlen($this->getNewPassword()) < self::MIN_PASSWORD_LENGTH) {
				$errors[] = Mage::helper('adminhtml')->__('Password must be at least of %d characters.', self::MIN_PASSWORD_LENGTH);
			}

			if (!preg_match('/[a-z]/iu', $this->getNewPassword())
				|| !preg_match('/[0-9]/u', $this->getNewPassword())
			) {
				$errors[] = Mage::helper('adminhtml')->__('Password must include both numeric and alphabetic characters.');
			}

			if ($this->hasPasswordConfirmation() && $this->getNewPassword() != $this->getPasswordConfirmation()) {
				$errors[] = Mage::helper('adminhtml')->__('Password confirmation must be same as password.');
			}
		}

		if ($this->userExists()) {
			$errors[] = Mage::helper('adminhtml')->__('A user with the same user name or email aleady exists.');
		}

		if (empty($errors)) {
			return true;
		}
		return $errors;
	}

}