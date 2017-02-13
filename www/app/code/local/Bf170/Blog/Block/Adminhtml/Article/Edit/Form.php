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
class Bf170_Blog_Block_Adminhtml_Article_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getData('action'),
            'method' => 'post'
        ));
        
        $fieldset = $form->addFieldset('region_info', array(
            'legend' => Mage::helper('blog')->__('信息详情')
        ));
        
        $region = Mage::registry('blog_article');
        if (! ! $region && ! ! $region->getId()) {
            $fieldset->addField('entity_id', 'label', array(
                'label' => Mage::helper('blog')->__('主ID'),
                'name' => 'entity_id',
                'readonly' => true
            ));
        }
        
        $fieldset->addField('product_id', 'text', array(
            'label' => Mage::helper('blog')->__('商品ID'),
            'name' => 'product_id',
            'required' => true
        ));
        
        $fieldset->addField('price', 'text', array(
            'label' => Mage::helper('blog')->__('价格'),
            'name' => 'price',
            'required' => true,
            'class' => 'validate-number'
        ));
        
        $fieldset->addField('name', 'text', array(
            'label' => Mage::helper('blog')->__('名称'),
            'name' => 'name',
            'required' => true
        ));
        
        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('blog')->__('当前状态'),
            'name' => 'status',
            'values' => Mage::helper('blog/article')->getArticleStatusValues(),
            'required' => true,
            'note' => '通过note添加说明<br/>支持HTML<br/>最长255字符'
        ));
        
        $fieldset->addField('created_at', 'label', array(
            'label' => Mage::helper('blog')->__('建立时间'),
            'name' => 'created_at'
        ));
        
        $fieldset->addField('updated_at', 'label', array(
            'label' => Mage::helper('blog')->__('更新时间'),
            'name' => 'updated_at'
        ));
        
        // Use session date for failed save content, (usually validation failure, just not to lose user input)
        // Then try to load from object data from the registry
        if ($regionData = Mage::getSingleton('adminhtml/session')->getBlogArticleFormData()) {
            $form->setValues($regionData);
            Mage::getSingleton('adminhtml/session')->setBlogArticleFormData(null);
        } elseif (! ! $region && ! ! $region->getId()) {
            $form->setValues($region->getData());
        }
        
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}