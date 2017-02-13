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

$installer = $this;
$installer->startSetup();

// 用户注册时，customer_id为空，不设为外键
$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('bf170sms/record')};
CREATE TABLE {$this->getTable('bf170sms/record')} (
	`entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主ID',
    `customer_id` int(10) unsigned COMMENT '用户ID',
    `client_ip` varchar(255) COMMENT '客户端IP',
    `type` smallint(5) unsigned COMMENT '短信类型',
    `telephone` varchar(255) COMMENT '手机号码',
    `content` text  COMMENT '短信内容',
    `scheduled_at` timestamp COMMENT '（预定）发送时间',
    `api_info` text  COMMENT '短信接口详情',
    `status` smallint(5) unsigned COMMENT '短信发送的状态',
    `created_at` timestamp NOT NULL COMMENT '创建时间',
    `updated_at` timestamp NOT NULL COMMENT '修改时间',
    PRIMARY KEY (`entity_id`),
    INDEX `INDEX_BF170SMS_RECORD_CUSTOMER_ID` (`customer_id`),
    INDEX `INDEX_BF170SMS_RECORD_CLIENT_IP` (`client_ip`),
    INDEX `INDEX_BF170SMS_RECORD_TYPE` (`type`),
    INDEX `INDEX_BF170SMS_RECORD_TELEPHONE` (`telephone`),
	INDEX `INDEX_BF170SMS_RECORD_STATUS` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='短信记录';

");

$installer->endSetup();