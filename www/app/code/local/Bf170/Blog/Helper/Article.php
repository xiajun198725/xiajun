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
class Bf170_Blog_Helper_Article extends Mage_Core_Helper_Data
{
    
    // 状态名字查询，下拉菜单时用
    public function getArticleStatusValues()
    {
        return array(
            Bf170_Blog_Model_Article::STATUS_AVAILABLE => Mage::helper('blog')->__('可用'),
            Bf170_Blog_Model_Article::STATUS_RESERVED => Mage::helper('blog')->__('预留'),
            Bf170_Blog_Model_Article::STATUS_ERROR => Mage::helper('blog')->__('有误')
        );
    }
}