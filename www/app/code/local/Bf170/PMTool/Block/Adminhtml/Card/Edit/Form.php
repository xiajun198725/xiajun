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
 
class Bf170_PMTool_Block_Adminhtml_Card_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {
	
	protected function _prepareForm() {
		$card = Mage::registry('pmtool_card');
		$form = new Varien_Data_Form(array(
			'id'		=> 'edit_form',
			'action'	=> $this->getData('action'),
			'method'	=> 'post'
		));
		
		$fieldset = $form->addFieldset('card_info', array('legend'=>Mage::helper('pmtool')->__('卡片详情')));
		
		if(!!$card && !!$card->getId()){
			$fieldset->addField('entity_id', 'label', array(
				'label'		=> Mage::helper('pmtool')->__('主ID'),
				'name'		=> 'entity_id',
				'readonly'	=> true
			));
		}
		
		$fieldset->addField('kanban_id', 'select', array(
				'label'		=> Mage::helper('pmtool')->__('看板ID'),
				'name'		=> 'kanban_id',
				'values'	=> Mage::helper('pmtool/kanban')->getAllKanbanValues(),
				'required'	=> true,
		));
		
		$fieldset->addField('process_id', 'text', array(
				'label'		=> Mage::helper('pmtool')->__('步骤ID'),
				'name'		=> 'process_id',
				'required'	=> true,
		));
		
		$fieldset->addField('sort_order', 'text', array(
				'label'		=> Mage::helper('pmtool')->__('排序'),
				'name'		=> 'sort_order',
		));
		
		$fieldset->addField('name', 'text', array(
				'label'		=> Mage::helper('pmtool')->__('名称'),
				'name'		=> 'name',
				'required'	=> true,
		));
		
		$fieldset->addField('priority', 'select', array(
				'label'		=> Mage::helper('pmtool')->__('优先级'),
				'name'		=> 'priority',
				'values'	=> Mage::helper('pmtool/card')->getPriorityValues(),
				'required'	=> true,
		));
		
		$fieldset->addField('tag_info', 'textarea', array(
				'label'		=> Mage::helper('pmtool')->__('标签信息'),
				'name'		=> 'tag_info',
				'note'		=> '请填入有效JSON信息',
		));
		
		$fieldset->addField('due_at', 'text', array(
				'label'		=> Mage::helper('pmtool')->__('到期时间'),
				'name'		=> 'due_at',
				'note'		=> 'yyyy-mm-dd HH:ii:ss',
		));
		
		$fieldset->addField('description', 'textarea', array(
				'label'		=> Mage::helper('pmtool')->__('详情'),
				'name'		=> 'description',
		));
		
		$fieldset->addField('task_info', 'textarea', array(
				'label'		=> Mage::helper('pmtool')->__('任务信息'),
				'name'		=> 'task_info',
				'note'		=> '请填入有效JSON信息',
		));
		
		$fieldset->addField('workload_estimate', 'text', array(
				'label'		=> Mage::helper('pmtool')->__('工作量估算'),
				'name'		=> 'workload_estimate',
				'note'		=> '非负整数',
		));
		
		$fieldset->addField('attachment_info', 'textarea', array(
				'label'		=> Mage::helper('pmtool')->__('附件信息'),
				'name'		=> 'attachment_info',
				'note'		=> '请填入有效JSON信息',
		));
		
		$fieldset->addField('comment_info', 'textarea', array(
				'label'		=> Mage::helper('pmtool')->__('评论信息'),
				'name'		=> 'comment_info',
				'note'		=> '请填入有效JSON信息',
		));
		
		$fieldset->addField('action_info', 'textarea', array(
				'label'		=> Mage::helper('pmtool')->__('操作信息'),
				'name'		=> 'action_info',
				'note'		=> '请填入有效JSON信息',
		));
		
		$fieldset->addField('status', 'select', array(
				'label'		=> Mage::helper('pmtool')->__('状态'),
				'name'		=> 'status',
				'values'	=> Mage::helper('pmtool/card')->getStatusValues(),
				'required'	=> true,
		));

		if(!!$card && !!$card->getId()){
			$formattedCreatedAt = Mage::helper('core')->formatTime($card->getData('created_at'), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true);
			$fieldset->addField('formatted_created_at', 'label', array(
					'label'	 => Mage::helper('pmtool')->__('建立时间'),
					'name'	  => 'formatted_created_at',
					'readonly'	=> true,
					'note'		=> $formattedCreatedAt
			));
			
			$formattedUpdateddAt = Mage::helper('core')->formatTime($card->getData('updated_at'), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true);
			$fieldset->addField('formatted_updated_at', 'label', array(
					'label'	 => Mage::helper('pmtool')->__('更新时间'),
					'name'	  => 'formatted_updated_at',
					'readonly'	=> true,
					'note'		=> $formattedUpdateddAt
			));
		}
		
	 	//1) 如果对象存在，使用对象数据作为缺省值
		if (!!$card && !!$card->getId()) {
			$form->setValues($card->getData());
		}
		//2) 如果session中有相应数据，则上次保存失败，使用上次表单中提交数据
		if ($cardData = Mage::getSingleton('adminhtml/session')->getPMToolCardFormData()){
			$form->addValues($cardData);
			Mage::getSingleton('adminhtml/session')->setPMToolCardFormData(null);
		}

		$form->setUseContainer(true);
		$this->setForm($form);
		return parent::_prepareForm();
	}
	
}