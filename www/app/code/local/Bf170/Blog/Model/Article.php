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
 *
 */
class Bf170_Blog_Model_Article extends Mage_Core_Model_Abstract
{
    
    // 对象的状态代码在此定义，状态的名字在相应的Helper内定义
    const STATUS_AVAILABLE = 0;
 // Default value
    const STATUS_RESERVED = 100;

    const STATUS_ERROR = 900;
    
    // 如果需要相应的事件相应逻辑
    protected $_eventPrefix = 'blog_article';
    
    // ======================== Internal Processing (automatic) ======================== //
    protected function _construct()
    {
        // 指向相应的 Resource (Model)
        $this->_init('blog/article');
    }
    
    // 一般在保存前，赋予更新时间（初次保存，赋予创建时间）
    protected function _beforeSave()
    {
        parent::_beforeSave();
        // For new object which does not specify 'created_at'
        if (! $this->getId() && ! $this->getData('created_at')) {
            $this->setData('created_at', now());
        }
        // Always specify 'updated_at'
        $this->setData('updated_at', now());
        return $this;
    }
}