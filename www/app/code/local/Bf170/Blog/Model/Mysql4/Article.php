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
class Bf170_Blog_Model_Mysql4_Article extends Mage_Core_Model_Mysql4_Abstract {
	
	// 指向相应的 Table以及Primary ID
    protected function _construct() {
        $this->_init('blog/article', 'entity_id');
    }
    
}