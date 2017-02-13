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

class Mage_Adminhtml_Block_Permissions_User_Grid extends Mage_Adminhtml_Block_Widget_Grid {

	public function __construct() {
		parent::__construct();
		$this->setId('permissionsUserGrid');
		$this->setDefaultSort('username');
		$this->setDefaultDir('asc');
		$this->setUseAjax(true);
	}

	protected function _prepareCollection() {
		$innerCollection = Mage::getResourceModel('admin/user_collection');
		$innerCollection->getSelect()
				->joinLeft(array('ar' => 'admin_role'), 'main_table.user_id = ar.user_id', array('parent_id'))
				->joinLeft(array('ar_name' => 'admin_role'), 'ar.parent_id = ar_name.role_id', array('role_name'))
				->where('ar.role_type = ?', 'U');

		// Wrap around the collection again to support sort and filtering
		$outerCollection = Mage::getResourceModel('admin/user_collection');
		$outerCollection->getSelect()->reset();
		$outerCollection->getSelect()->from(array('main_table' => $innerCollection->getSelect()));
				
		$this->setCollection($outerCollection);
		return parent::_prepareCollection();
	}

	protected function _prepareColumns() {
		$this->addColumn('user_id', array(
			'header'	=> Mage::helper('adminhtml')->__('ID'),
			'width'		=> '25px',
			'align'		=> 'right',
			'sortable'  => true,
			'index'		=> 'user_id'
		));

		$this->addColumn('username', array(
			'header'	=> Mage::helper('adminhtml')->__('User Name'),
			'width'		=> '50px',
			'index'		=> 'username'
		));

		$this->addColumn('firstname', array(
			'header'	=> Mage::helper('adminhtml')->__('First Name'),
			'width'		=> '50px',
			'index'		=> 'firstname'
		));

//		$this->addColumn('lastname', array(
//			'header'	=> Mage::helper('adminhtml')->__('Last Name'),
//			'index'	 => 'lastname'
//		));

		$this->addColumn('email', array(
			'header'	=> Mage::helper('adminhtml')->__('Email'),
			'width'		=> '150px',
			'align'		=> 'left',
			'index'		=> 'email'
		));
		
		$this->addColumn('role_name', array(
			'header'	=> Mage::helper('adminhtml')->__('用户组'),
			'width'		=> '50px',
			'index'		=> 'role_name'
		));

		$this->addColumn('is_active', array(
			'header'	=> Mage::helper('adminhtml')->__('Status'),
			'width'		=> '50px',
			'index'		=> 'is_active',
			'type'		=> 'options',
			'options'	=> array('1' => Mage::helper('adminhtml')->__('Active'), '0' => Mage::helper('adminhtml')->__('Inactive')),
		));

		return parent::_prepareColumns();
	}

	public function getRowUrl($row) {
		return $this->getUrl('*/*/edit', array('user_id' => $row->getId()));
	}

	public function getGridUrl() {
		//$uid = $this->getRequest()->getParam('user_id');
		return $this->getUrl('*/*/roleGrid', array());
	}

}