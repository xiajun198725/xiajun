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
 
class Bf170_Bf170Sms_Block_Adminhtml_Record_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
	
	protected function _prepareForm() {
		$form = new Varien_Data_Form(array(
			'id'		=> 'edit_form',
			'action'	=> $this->getData('action'),
			'method'	=> 'post'
		));
		
		$fieldset = $form->addFieldset('record_info', array('legend'=>Mage::helper('bf170sms')->__('短信记录')));

		$record = Mage::registry('bf170sms_record');
		
		if(!!$record && !!$record->getId()){
			$fieldset->addField('entity_id', 'label', array(
				'label'		=> Mage::helper('bf170sms')->__('主ID'),
				'name'		=> 'entity_id',
				'readonly'	=> true
			));
		}
		
		$customer = Mage::getModel('customer/customer')->load($record->getData('customer_id'));
		if(!!$customer && !!$customer->getId()){
			$fieldset->addField('customer_name', 'label', array(
				'label'		=> Mage::helper('bf170sms')->__('用户姓名'),
				'name'		=> 'customer_name',
				'readonly'	=> true,
				'note'		=> "{$customer->getName()}"
			));
			$fieldset->addField('customer_email', 'label', array(
				'label'		=> Mage::helper('bf170sms')->__('用户邮箱'),
				'name'		=> 'customer_email',
				'readonly'	=> true,
				'note'		=> "{$customer->getEmail()}"
			));
			$customerLink = Mage::helper('adminhtml')->getUrl('adminhtml/customer/edit', array('id' => $record->getData('customer_id')));
			$fieldset->addField('customer_link', 'label', array(
				'label'		=> Mage::helper('bf170sms')->__('用户详情'),
				'name'		=> 'customer_link',
				'readonly'	=> true,
				'note'		=> "<a href=\"{$customerLink}\">用户ID {$customer->getId()} -- 详情</a>"
			));
		}
		
		$fieldset->addField('client_ip', 'label', array(
				'label'		=> Mage::helper('bf170sms')->__('用户IP'),
				'name'		=> 'client_ip',
				'readonly'	=> true
		));
			
		if(!!Mage::getSingleton('admin/session')->isAllowed('hpusernetwork/manage_adv')){
			$fieldset->addField('type', 'select', array(
				'label'		=> Mage::helper('bf170sms')->__('类别'),
				'name'		=> 'type',
				'values'	=> Mage::helper('bf170sms/record')->getTypeValues(),
				'required'	=> true,
			));
		}else{
			$fieldset->addField('type_label', 'label', array(
				'label'		=> Mage::helper('bf170sms')->__('类别'),
				'name'		=> 'type_label',
				'note'		=> $record->getTypeLabel(),
			));
		}
		
		$fieldset->addField('telephone', 'text', array(
				'label'		=> Mage::helper('bf170sms')->__('电话'),
				'name'		=> 'telephone',
				'required'	=> true
		));
		
		$fieldset->addField('content', 'textarea', array(
				'label'		=> Mage::helper('bf170sms')->__('信息'),
				'name'		=> 'content',
		));
		
		$fieldset->addField('api_info', 'label', array(
				'label'		=> Mage::helper('bf170sms')->__('API详情'),
				'name'		=> 'api_info',
				'readonly'	=> true,
		));
		
		if(!!Mage::getSingleton('admin/session')->isAllowed('hpusernetwork/manage_adv')){
			$fieldset->addField('status', 'select', array(
				'label'		=> Mage::helper('bf170sms')->__('当前状态'),
				'name'		=> 'status',
				'values'	=> Mage::helper('bf170sms/record')->getStatusValues(),
				'required'	=> true,
			));
		}else{
			$fieldset->addField('status_label', 'label', array(
				'label'		=> Mage::helper('bf170sms')->__('当前状态'),
				'name'		=> 'status_label',
				'note'		=> $record->getStatusLabel(),
			));
		}
		
		$formattedCreatedAt = Mage::helper('core')->formatTime($record->getData('created_at'), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true);
        $fieldset->addField('formatted_created_at', 'label', array(
            'label'     => Mage::helper('bf170sms')->__('建立时间'),
            'name'      => 'formatted_created_at',
        	'readonly'	=> true,
        	'note'		=> $formattedCreatedAt
        ));
        
        $formattedUpdateddAt = Mage::helper('core')->formatTime($record->getData('updated_at'), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true);
        $fieldset->addField('formatted_updated_at', 'label', array(
            'label'     => Mage::helper('bf170sms')->__('更新时间'),
            'name'      => 'formatted_updated_at',
        	'readonly'	=> true,
        	'note'		=> $formattedUpdateddAt
        ));
		
	 	//1) Try to load from object data from the registry as the default
		if ( !!$record && !!$record->getId() ) {
			$form->setValues($record->getData());
		}
		//2) Use session data for failed save content, (usually validation failure, just not to lose user input)
		if ( $recordData = Mage::getSingleton('adminhtml/session')->getBf170SmsRecordFormData() ){
			$form->addValues($recordData);
			Mage::getSingleton('adminhtml/session')->setBf170SmsRecordFormData(null);
		}

		$form->setUseContainer(true);
		$this->setForm($form);
		return parent::_prepareForm();
	}
	
}