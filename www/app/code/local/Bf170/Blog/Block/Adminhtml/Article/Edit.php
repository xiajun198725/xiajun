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
class Bf170_Blog_Block_Adminhtml_Article_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'entity_id';
        $this->_blockGroup = 'blog';
        $this->_controller = 'adminhtml_article';
        $this->_mode = 'edit';
    }

    public function getHeaderText()
    {
        return Mage::helper('blog')->__('信息详情');
    }

    public function getBackUrl()
    {
        return $this->getUrl('*/*/index', array(
            '_current' => true
        ));
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array(
            '_current' => true
        ));
    }

    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array(
            '_current' => true
        ));
    }
}