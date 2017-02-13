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

class Bf170_CoreService_Helper_Rsa extends Mage_Core_Helper_Data {
	
	const CRYPT_RSA_PRIVATE_FORMAT_PKCS1		= 0;
	
	const CRYPT_RSA_ENCRYPTION_OAEP				= 1;
	const CRYPT_RSA_ENCRYPTION_PKCS1			= 2;
	
	public function __construct(){
		set_include_path(get_include_path() . PATH_SEPARATOR . dirname(dirname(__FILE__)). '/lib/rsa/');
	}
	
	public function encrypt($pubKey, $pubKeyType, $source, $encionMode){
		include_once ('Crypt/RSA.php');
		$rsa = new Crypt_RSA();
		$rsa->loadKey($pubKey, $pubKeyType);
		$rsa->setEncryptionMode($encionMode);
		return base64_encode($rsa->encrypt($source));
	}
	
}