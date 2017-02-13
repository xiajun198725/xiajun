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

class Bf170_CoreService_ImageController extends Mage_Core_Controller_Front_Action {
	
	public function viewSecurePhotoAction() {
		$key = $this->getRequest()->getParam('key');
		if(!$key){
			exit;
		}
		$relativeFilepath = Mage::helper('core')->decrypt(base64_decode($key));
		$imageFile = Mage::helper('coreservice')->getSecurePhotoDir($relativeFilepath, true);
		if (!file_exists($imageFile)) {
			exit;
		}
		header('Content-Type: ' . mime_content_type($imageFile));
		header('Content-Length: ' . filesize($imageFile));
		readfile($imageFile);
		exit;
	}

}