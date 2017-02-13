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
class Bf170_Blog_Block_Adminhtml_Article_Index extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_blockGroup = 'blog';
        $this->_controller = 'adminhtml_article_index';
        $this->_headerText = Mage::helper('blog')->__('博客文章查询');
        parent::__construct();
    }
}