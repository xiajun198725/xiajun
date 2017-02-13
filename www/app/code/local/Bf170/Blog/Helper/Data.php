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
class Bf170_Blog_Helper_Data extends Mage_Core_Helper_Data
{
    
    // Admin 后台，需要一个基本的Data Helper
    
    // 其他的一些常用帮助函数也可以放在这里
    public function getBlogIndexUrl()
    {
        return $this->_getUrl('blog/article/index');
    }
}