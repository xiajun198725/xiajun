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
 
class Bf170_Bf170Sms_Block_Adminhtml_Record_Index_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	
	public function __construct(){
		parent::__construct();
		$this->setId('bf170smsRecordGrid');
		$this->setDefaultSort('entity_id');
		$this->setDefaultDir('DESC');
	}
	
	protected function _prepareCollection(){
		$collection = Mage::getModel('bf170sms/record')->getCollection();
		$customerId = Mage::registry('bf170sms_record_index_customer_id');
		if(!!$customerId){
			$collection->addFieldToFilter('customer_id', $customerId);
		}
				
		$this->setCollection($collection);
		parent::_prepareCollection();
	}
	
	protected function _prepareColumns(){  

		$this->addColumn('entity_id', array(
			'header'	=> Mage::helper('bf170sms')->__('主ID'),
			'align'		=> 'right',
			'width'		=> '50px',
			'index'		=> 'entity_id',
			'filter'	=> 'coreservice/adminhtml_widget_grid_column_filter_value',
		));
		
		$this->addColumn('customer_id', array(
			'header'	=> Mage::helper('bf170sms')->__('用户ID'),
			'align'		=> 'right',
			'width'		=> '50px',
			'index'		=> 'customer_id',
			'filter'	=> 'coreservice/adminhtml_widget_grid_column_filter_value',
			'renderer'	=> 'Bf170_CoreService_Block_Adminhtml_Widget_Grid_Column_Renderer_Customer_Link'
		));
		
		$this->addColumn('client_ip', array(
			'header'	=> Mage::helper('bf170sms')->__('客户端IP'),
			'align'		=> 'right',
			'width'		=> '50px',
			'index'		=> 'client_ip',
		));
		
		$this->addColumn('telephone', array(
			'header'	=> Mage::helper('bf170sms')->__('电话'),
			'align'		=> 'right',
			'width'		=> '50px',
			'index'		=> 'telephone',
		));
		
		$this->addColumn('type', array(
			'header'	=> Mage::helper('bf170sms')->__('类别'),
			'align'		=> 'right',
			'width'		=> '50px',
			'index'		=> 'type',
			'type'		=> 'options',
			'options'	=> Mage::helper('bf170sms/record')->getTypeValues()
		));

		$this->addColumn('status', array(
			'header'	=> Mage::helper('bf170sms')->__('当前状态'),
			'align'		=> 'right',
			'width'		=> '50px',
			'index'		=> 'status',
			'type'		=> 'options',
			'options'	=> Mage::helper('bf170sms/record')->getStatusValues()
		));
		
		$this->addColumn('created_at', array(
			'header'	=> Mage::helper('bf170sms')->__('建立时间'),
			'align'		=> 'right',
			'width'		=> '50px',
			'index'		=> 'created_at',
			'type'		=> 'datetime',
		));
		
		$this->addColumn('updated_at', array(
			'header'	=> Mage::helper('bf170sms')->__('更新时间'),
			'align'		=> 'right',
			'width'		=> '50px',
			'index'		=> 'updated_at',
			'type'		=> 'datetime',
		));

		return parent::_prepareColumns();
	}
	
	public function getRowUrl($row){
		return $this->getUrl('*/*/edit', array('id'=>$row->getId()));
	}
	
}