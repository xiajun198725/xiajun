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

class Mage_Adminhtml_Block_Customer_Grid extends Mage_Adminhtml_Block_Widget_Grid {

	public function __construct() {
		parent::__construct();
		$this->setId('customerGrid');
		$this->setUseAjax(true);
		$this->setDefaultSort('entity_id');
		$this->setSaveParametersInSession(true);
	}

	protected function _prepareCollection() {
		$collection = Mage::getResourceModel('customer/customer_collection')
			->addAttributeToSelect(array('firstname',' email', 'created_at', 'group_id', 'telephone'));
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns() {
		$this->addColumn('entity_id', array(
			'header'	=> Mage::helper('customer')->__('ID'),
			'width'	 => '50px',
			'index'	 => 'entity_id',
			'type'		=> 'number',
		));
		
		$this->addColumn('firstname', array(
			'header'	=> Mage::helper('customer')->__('Name'),
			'width'	 => '50px',
			'index'	 => 'firstname'
		));
		
		$this->addColumn('email', array(
			'header'	=> Mage::helper('customer')->__('Email'),
			'width'	 => '150px',
			'index'	 => 'email'
		));

		$groups = Mage::getResourceModel('customer/group_collection')
			->addFieldToFilter('customer_group_id', array('gt'=> 0))
			->load()
			->toOptionHash();

		$this->addColumn('group', array(
			'header'	=>  Mage::helper('customer')->__('Group'),
			'width'	 =>  '50px',
			'index'	 =>  'group_id',
			'type'	  =>  'options',
			'options'   =>  $groups,
		));

		$this->addColumn('telephone', array(
			'header'	=> Mage::helper('customer')->__('Telephone'),
			'width'	 => '100px',
			'index'	 => 'telephone'
		));
		
		$this->addColumn('customer_since', array(
			'header'	=> Mage::helper('customer')->__('Customer Since'),
			'width'	 => '100px',
			'type'	  => 'datetime',
			'align'	 => 'center',
			'index'	 => 'created_at',
			'gmtoffset' => true
		));

//		if (!Mage::app()->isSingleStoreMode()) {
//			$this->addColumn('website_id', array(
//				'header'	=> Mage::helper('customer')->__('Website'),
//				'align'	 => 'center',
//				'width'	 => '80px',
//				'type'	  => 'options',
//				'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(true),
//				'index'	 => 'website_id',
//			));
//		}

		$this->addColumn('action',
			array(
				'header'	=>  Mage::helper('customer')->__('Action'),
				'width'	 => '100px',
				'type'	  => 'action',
				'getter'	=> 'getId',
				'actions'   => array(
					array(
						'caption'   => Mage::helper('customer')->__('Edit'),
						'url'	   => array('base'=> '*/*/edit'),
						'field'	 => 'id'
					)
				),
				'filter'	=> false,
				'sortable'  => false,
				'index'	 => 'stores',
				'is_system' => true,
		));

		$this->addExportType('*/*/exportCsv', Mage::helper('customer')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('customer')->__('Excel XML'));
		return parent::_prepareColumns();
	}

	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('entity_id');
		$this->getMassactionBlock()->setFormFieldName('customer');

		$this->getMassactionBlock()->addItem('delete', array(
			 'label'	=> Mage::helper('customer')->__('Delete'),
			 'url'	  => $this->getUrl('*/*/massDelete'),
			 'confirm'  => Mage::helper('customer')->__('Are you sure?')
		));

		$this->getMassactionBlock()->addItem('newsletter_subscribe', array(
			 'label'	=> Mage::helper('customer')->__('Subscribe to Newsletter'),
			 'url'	  => $this->getUrl('*/*/massSubscribe')
		));

		$this->getMassactionBlock()->addItem('newsletter_unsubscribe', array(
			 'label'	=> Mage::helper('customer')->__('Unsubscribe from Newsletter'),
			 'url'	  => $this->getUrl('*/*/massUnsubscribe')
		));

		$groups = $this->helper('customer')->getGroups()->toOptionArray();

		array_unshift($groups, array('label'=> '', 'value'=> ''));
		$this->getMassactionBlock()->addItem('assign_group', array(
			 'label'		=> Mage::helper('customer')->__('Assign a Customer Group'),
			 'url'		  => $this->getUrl('*/*/massAssignGroup'),
			 'additional'   => array(
				'visibility'	=> array(
					 'name'	 => 'group',
					 'type'	 => 'select',
					 'class'	=> 'required-entry',
					 'label'	=> Mage::helper('customer')->__('Group'),
					 'values'   => $groups
				 )
			)
		));

		return $this;
	}

	public function getGridUrl() {
		return $this->getUrl('*/*/grid', array('_current'=> true));
	}

	public function getRowUrl($row) {
		return $this->getUrl('*/*/edit', array('id'=>$row->getId()));
	}
	
}
