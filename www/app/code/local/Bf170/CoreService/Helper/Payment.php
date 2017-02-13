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

class Bf170_CoreService_Helper_Payment extends Mage_Core_Helper_Data {

	const METHOD_POS				= 'pos';
	const METHOD_BANK_TRANSFER		= 'bank_transfer';
	const METHOD_SHOPPINGCREDIT		= 'shoppingcredit';
	const METHOD_CHOUMA				= 'chouma';
	const METHOD_WEIXINPAY			= 'weixinpay';
	const METHOD_ALIPAY				= 'alipay';
	const METHOD_OTHER				= 'other';

	public function getMethods() {
		return array(
				self::METHOD_POS 				=> 'POS刷卡',
				self::METHOD_BANK_TRANSFER 		=> '银行转账',
				self::METHOD_SHOPPINGCREDIT		=> '购物币',
				self::METHOD_CHOUMA				=> '酬蚂',
				self::METHOD_WEIXINPAY			=> '微信支付',
				self::METHOD_ALIPAY 			=> '支付宝',
				self::METHOD_OTHER		 		=> '其他方式',
		);
	}
	
	public function getAvailableMethods(){
		$paymentMethods = array(
				self::METHOD_WEIXINPAY => array(
						'image_url'		=> Mage::getDesign()->getSkinUrl('images/bf170checkout/weixinpay.jpg'),
						'label'			=> '微信支付',
				),
		);
		return $paymentMethods;
	}

}