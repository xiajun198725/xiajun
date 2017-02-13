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
 
class Bf170_PMTool_Block_Adminhtml_Kanban_Index_Grid extends Mage_Adminhtml_Block_Widget_Grid {
	
	public function __construct(){
		parent::__construct();
		$this->setId('pmtoolKanbanGrid');
		$this->setDefaultSort('entity_id');
		$this->setDefaultDir('DESC');
	}
	
	protected function _prepareCollection(){
		$collection = Mage::getModel('pmtool/kanban')->getCollection();
		$this->setCollection($collection);
		parent::_prepareCollection();
	}
	
	protected function _prepareColumns(){  

		$this->addColumn('entity_id', array(
			'header'	=> Mage::helper('pmtool')->__('主ID'),
			'align'		=> 'right',
			'width'		=> '50px',
			'index'		=> 'entity_id',
			'filter'	=> 'coreservice/adminhtml_widget_grid_column_filter_value',
		));
		
		$this->addColumn('name', array(
			'header'	=> Mage::helper('pmtool')->__('名称'),
			'align'		=> 'right',
			'width'		=> '50px',
			'index'		=> 'name',
		));
		
		$this->addColumn('description', array(
			'header'	=> Mage::helper('pmtool')->__('详情'),
			'align'		=> 'right',
			'width'		=> '250px',
			'index'		=> 'description',
		));
		
		$this->addColumn('tag_info', array(
			'header'	=> Mage::helper('pmtool')->__('标签'),
			'align'		=> 'right',
			'width'		=> '250px',
			'index'		=> 'tag_info',
		));

		$this->addColumn('status', array(
			'header'	=> Mage::helper('pmtool')->__('状态'),
			'align'		=> 'right',
			'width'		=> '50px',
			'index'		=> 'status',
			'type'		=> 'options',
			'options'	=> Mage::helper('pmtool/kanban')->getStatusValues()
		));
		
		$this->addColumn('type', array(
			'header'	=> Mage::helper('pmtool')->__('类型'),
			'align'		=> 'right',
			'width'		=> '50px',
			'index'		=> 'type',
			'type'		=> 'options',
			'options'	=> Mage::helper('pmtool/kanban')->getTypeValues()
		));
		
		$this->addColumn('created_at', array(
			'header'	=> Mage::helper('pmtool')->__('建立时间'),
			'align'		=> 'right',
			'width'		=> '50px',
			'index'		=> 'created_at',
			'type'		=> 'datetime',
		));
		
		$this->addColumn('updated_at', array(
			'header'	=> Mage::helper('pmtool')->__('更新时间'),
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