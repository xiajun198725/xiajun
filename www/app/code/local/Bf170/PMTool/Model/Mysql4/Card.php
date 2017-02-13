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
class Bf170_PMTool_Model_Mysql4_Card extends Bf170_CoreService_Model_Mysql4_Object {
	
	public function _construct() {    
		$this->_init('pmtool/card', 'entity_id');
	}
	
}