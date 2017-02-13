<?php

/*
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User Software Agreement (EULA).
 * It is also available through the world-wide-web at this URL:
 * http://www.bf170.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to eula@bf170.com so we can send you a copy immediately.
 */
class Bf170_Blog_Block_Adminhtml_Article_Index_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('blogArticleGrid');
    }
    
    // 准备数据（或查找结果）
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('blog/article')->getCollection();
        $this->setCollection($collection);
        parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('blog')->__('主 ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'entity_id'
        ));
        
        $this->addColumn('product_id', array(
            'header' => Mage::helper('blog')->__('商品ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'product_id'
        ));
        
        $this->addColumn('price', array(
            'header' => Mage::helper('blog')->__('价格'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'price',
            
            // 价格类数据可以用特别的格式
            'type' => 'price',
            'currency_code' => Mage::app()->getStore()
                ->getBaseCurrency()
                ->getCode()
        ));
        
        $this->addColumn('name', array(
            'header' => Mage::helper('blog')->__('名称'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'name'
        ));
        
        $this->addColumn('status', array(
            'header' => Mage::helper('blog')->__('当前状态'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'status',
            
            // 下拉菜单格式
            'type' => 'options',
            'options' => Mage::helper('blog/article')->getArticleStatusValues()
        ));
        
        $this->addColumn('created_at', array(
            'header' => Mage::helper('blog')->__('建立时间'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'created_at'
        ));
        // 价格类数据可有时区转换逻辑，这里先忽略
        
        $this->addColumn('updated_at', array(
            'header' => Mage::helper('blog')->__('更新时间'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'updated_at'
        ));
        
        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array(
            'id' => $row->getId()
        ));
        
        // return false; // 则表单里的行不可编辑
    }
}