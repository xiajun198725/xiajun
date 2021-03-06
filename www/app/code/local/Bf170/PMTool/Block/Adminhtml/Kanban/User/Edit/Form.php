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
 
class Bf170_PMTool_Block_Adminhtml_Kanban_User_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
	
	protected function _prepareForm() {
		$kanbanUser = Mage::registry('pmtool_kanban_user');
		$form = new Varien_Data_Form(array(
			'id'		=> 'edit_form',
			'action'	=> $this->getData('action'),
			'method'	=> 'post'
		));
		
		$fieldset = $form->addFieldset('kanban_user_info', array('legend'=>Mage::helper('pmtool')->__('看板用户')));
		
		if(!!$kanbanUser && !!$kanbanUser->getId()){
			$fieldset->addField('entity_id', 'label', array(
				'label'		=> Mage::helper('pmtool')->__('主ID'),
				'name'		=> 'entity_id',
				'readonly'	=> true
			));
		}
		
		$fieldset->addField('customer_id', 'select', array(
				'label'		=> Mage::helper('pmtool')->__('用户ID'),
				'name'		=> 'customer_id',
				'values'	=> Mage::helper('pmtool')->getAllCustomerValues(),
				'required'	=> true,
		));
		
		$fieldset->addField('kanban_id', 'select', array(
				'label'		=> Mage::helper('pmtool')->__('看板ID'),
				'name'		=> 'kanban_id',
				'values'	=> Mage::helper('pmtool/kanban')->getAllKanbanValues(),
				'required'	=> true,
		));
		
		$fieldset->addField('type', 'select', array(
				'label'		=> Mage::helper('pmtool')->__('类型'),
				'name'		=> 'type',
				'values'	=> Mage::helper('pmtool/kanban_user')->getTypeValues(),
				'required'	=> true,
		));
		
		$fieldset->addField('status', 'select', array(
				'label'		=> Mage::helper('pmtool')->__('状态'),
				'name'		=> 'status',
				'values'	=> Mage::helper('pmtool/kanban_user')->getStatusValues(),
				'required'	=> true,
		));

		if(!!$kanbanUser && !!$kanbanUser->getId()){
			$formattedCreatedAt = Mage::helper('core')->formatTime($kanbanUser->getData('created_at'), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true);
			$fieldset->addField('formatted_created_at', 'label', array(
					'label'	 => Mage::helper('pmtool')->__('建立时间'),
					'name'	  => 'formatted_created_at',
					'readonly'	=> true,
					'note'		=> $formattedCreatedAt
			));
			
			$formattedUpdateddAt = Mage::helper('core')->formatTime($kanbanUser->getData('updated_at'), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true);
			$fieldset->addField('formatted_updated_at', 'label', array(
					'label'	 => Mage::helper('pmtool')->__('更新时间'),
					'name'	  => 'formatted_updated_at',
					'readonly'	=> true,
					'note'		=> $formattedUpdateddAt
			));
		}
		
	 	//1) 如果对象存在，使用对象数据作为缺省值
		if (!!$kanbanUser && !!$kanbanUser->getId()) {
			$form->setValues($kanbanUser->getData());
		}
		//2) 如果session中有相应数据，则上次保存失败，使用上次表单中提交数据
		if ($kanbanUserData = Mage::getSingleton('adminhtml/session')->getPMToolKanbanUserFormData()){
			$form->addValues($kanbanUserData);
			Mage::getSingleton('adminhtml/session')->setPMToolKanbanUserFormData(null);
		}

		$form->setUseContainer(true);
		$this->setForm($form);
		return parent::_prepareForm();
	}
	
}