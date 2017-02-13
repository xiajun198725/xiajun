<?php 

/* NOTICE OF LICENSE
 *
 * This source file is subject to the End User Software Agreement (EULA).
 * It is also available through the world-wide-web at this URL:
 * http://www.bf170.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to eula@bf170.com so we can send you a copy immediately.
 */ 
 
class Bf170_CoreService_Block_Adminhtml_Widget_Grid_Column_Renderer_Customer_Link 
		extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract 
{
	
	public function render(Varien_Object $row) {
		$html = "";
		$customerId = $row->getData($this->getColumn()->getIndex());
		if(!!$customerId){
			$customerUrl = Mage::helper('adminhtml')->getUrl('adminhtml/customer/edit', array('id' => $customerId));
 			$html = "<a href=\"{$customerUrl}\">{$customerId}</a>";
		}
		return $html;
	}
	
}