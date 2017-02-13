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
 
class Bf170_PMTool_Block_Adminhtml_Kanban_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
	
	protected function _prepareForm() {
		$kanban = Mage::registry('pmtool_kanban');
		$form = new Varien_Data_Form(array(
			'id'		=> 'edit_form',
			'action'	=> $this->getData('action'),
			'method'	=> 'post'
		));
		
		$fieldset = $form->addFieldset('kanban_info', array('legend'=>Mage::helper('pmtool')->__('看板详情')));
		
		if(!!$kanban && !!$kanban->getId()){
			$fieldset->addField('entity_id', 'label', array(
				'label'		=> Mage::helper('pmtool')->__('主ID'),
				'name'		=> 'entity_id',
				'readonly'	=> true,
			));
		}
		
		$fieldset->addField('name', 'text', array(
				'label'		=> Mage::helper('pmtool')->__('名称'),
				'name'		=> 'name',
				'required'	=> true,
		));
		
		$fieldset->addField('description', 'textarea', array(
				'label'		=> Mage::helper('pmtool')->__('详情'),
				'name'		=> 'description',
		));
		
		$fieldset->addField('process_info', 'textarea', array(
				'label'		=> Mage::helper('pmtool')->__('步骤信息'),
				'name'		=> 'process_info',
				'note'		=> '请填入有效JSON信息',
		));
		
		$fieldset->addField('tag_info', 'textarea', array(
				'label'		=> Mage::helper('pmtool')->__('标签信息'),
				'name'		=> 'tag_info',
				'note'		=> '请填入有效JSON信息',
		));
		
		$fieldset->addField('status', 'select', array(
				'label'		=> Mage::helper('pmtool')->__('状态'),
				'name'		=> 'status',
				'values'	=> Mage::helper('pmtool/kanban')->getStatusValues(),
				'required'	=> true,
		));
		
		$fieldset->addField('type', 'select', array(
				'label'		=> Mage::helper('pmtool')->__('类型'),
				'name'		=> 'type',
				'values'	=> Mage::helper('pmtool/kanban')->getTypeValues(),
				'required'	=> true,
		));

		if(!!$kanban && !!$kanban->getId()){
			$formattedCreatedAt = Mage::helper('core')->formatTime($kanban->getData('created_at'), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true);
			$fieldset->addField('formatted_created_at', 'label', array(
					'label'	 => Mage::helper('pmtool')->__('建立时间'),
					'name'	  => 'formatted_created_at',
					'readonly'	=> true,
					'note'		=> $formattedCreatedAt
			));
			
			$formattedUpdateddAt = Mage::helper('core')->formatTime($kanban->getData('updated_at'), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true);
			$fieldset->addField('formatted_updated_at', 'label', array(
					'label'	 => Mage::helper('pmtool')->__('更新时间'),
					'name'	  => 'formatted_updated_at',
					'readonly'	=> true,
					'note'		=> $formattedUpdateddAt
			));
		}
		
	 	//1) 如果对象存在，使用对象数据作为缺省值
		if (!!$kanban && !!$kanban->getId()) {
			$form->setValues($kanban->getData());
		}
		//2) 如果session中有相应数据，则上次保存失败，使用上次表单中提交数据
		if ($kanbanData = Mage::getSingleton('adminhtml/session')->getPMToolKanbanFormData()){
			$form->addValues($kanbanData);
			Mage::getSingleton('adminhtml/session')->setPMToolKanbanFormData(null);
		}

		$form->setUseContainer(true);
		$this->setForm($form);
		return parent::_prepareForm();
	}
	
}