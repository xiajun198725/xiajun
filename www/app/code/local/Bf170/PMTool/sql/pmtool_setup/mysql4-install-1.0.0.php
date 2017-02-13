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

-- DROP TABLE IF EXISTS {$this->getTable('pmtool/kanban')};
CREATE TABLE {$this->getTable('pmtool/kanban')} (
	`entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主ID',
	`name` varchar(255) COMMENT '名称',
	`description` text COMMENT '前台描述',
	`process_info` text COMMENT '步骤信息',
	`tag_info` text COMMENT '标签信息',
	`additional_info` text COMMENT '系统参数',
	`type` smallint(5) unsigned COMMENT '类型',
	`status` smallint(5) unsigned COMMENT '状态',
	`created_at` timestamp NOT NULL COMMENT '创建时间',
	`updated_at` timestamp NOT NULL COMMENT '修改时间',
	PRIMARY KEY (`entity_id`),
	INDEX `INDEX_PMTOOL_KANBAN_TYPE` (`type`),
	INDEX `INDEX_PMTOOL_KANBAN_STATUS` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='看板';

-- DROP TABLE IF EXISTS {$this->getTable('pmtool/kanban_user')};
CREATE TABLE {$this->getTable('pmtool/kanban_user')} (
	`entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主ID',
	`customer_id` int(10) unsigned COMMENT '用户ID',
	`kanban_id` int(10) unsigned COMMENT '看板ID',
	`type` smallint(5) unsigned COMMENT '类型',
	`status` smallint(5) unsigned COMMENT '状态',
	`created_at` timestamp NOT NULL COMMENT '创建时间',
	`updated_at` timestamp NOT NULL COMMENT '修改时间',
	PRIMARY KEY (`entity_id`),
	INDEX `INDEX_PMTOOL_KANBAN_USER_CUSTOMER_ID` (`customer_id`),
	INDEX `INDEX_PMTOOL_KANBAN_USER_KANBAN_ID` (`kanban_id`),
	INDEX `INDEX_PMTOOL_KANBAN_USER_TYPE` (`type`),
	INDEX `INDEX_PMTOOL_KANBAN_USER_STATUS` (`status`),
	UNIQUE INDEX `UQ_PMTOOL_KANBAN_USER_CUSTOMER_KANBAN` (`customer_id`, `kanban_id`),
	CONSTRAINT `FK_PMTOOL_KANBAN_USER_CUSTOMER_ID` FOREIGN KEY (`customer_id`) REFERENCES {$this->getTable('customer/entity')} (`entity_id`) ON DELETE SET NULL ON UPDATE CASCADE,
	CONSTRAINT `FK_PMTOOL_KANBAN_USER_KANBAN_ID` FOREIGN KEY (`kanban_id`) REFERENCES {$this->getTable('pmtool/kanban')} (`entity_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='看板用户';

-- DROP TABLE IF EXISTS {$this->getTable('pmtool/card')};
CREATE TABLE {$this->getTable('pmtool/card')} (
	`entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主ID',
	`kanban_id` int(10) unsigned COMMENT '看板ID',
	`process_id` int(10) unsigned COMMENT '步骤ID',
	`sort_order` int(10) unsigned COMMENT '排序',
	`name` varchar(255) COMMENT '名称',
	`priority` smallint(5) unsigned COMMENT '优先级',
	`tag_info` text COMMENT '标签信息',
	`due_at` timestamp NOT NULL COMMENT '到期时间',
	`description` text COMMENT '前台描述',
	`task_info` text COMMENT '任务信息',
	`workload_estimate` int(10) unsigned COMMENT '工作量估算',
	`attachment_info` text COMMENT '附件信息',
	`comment_info` text COMMENT '评论信息',
	`action_info` text COMMENT '操作信息',
	`additional_info` text COMMENT '系统参数',
	`status` smallint(5) unsigned COMMENT '状态',
	`created_at` timestamp NOT NULL COMMENT '创建时间',
	`updated_at` timestamp NOT NULL COMMENT '修改时间',
	PRIMARY KEY (`entity_id`),
	INDEX `INDEX_PMTOOL_CARD_KANBAN_ID` (`kanban_id`),
	INDEX `INDEX_PMTOOL_CARD_PRIORITY` (`priority`),
	INDEX `INDEX_PMTOOL_CARD_STATUS` (`status`),
	CONSTRAINT `FK_PMTOOL_CARD_KANBAN_ID` FOREIGN KEY (`kanban_id`) REFERENCES {$this->getTable('pmtool/kanban')} (`entity_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='卡片';

-- DROP TABLE IF EXISTS {$this->getTable('pmtool/card_user')};
CREATE TABLE {$this->getTable('pmtool/card_user')} (
	`entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主ID',
	`customer_id` int(10) unsigned COMMENT '用户ID',
	`card_id` int(10) unsigned COMMENT '卡片ID',
	`type` smallint(5) unsigned COMMENT '类型',
	`status` smallint(5) unsigned COMMENT '状态',
	`created_at` timestamp NOT NULL COMMENT '创建时间',
	`updated_at` timestamp NOT NULL COMMENT '修改时间',
	PRIMARY KEY (`entity_id`),
	INDEX `INDEX_PMTOOL_CARD_USER_TYPE` (`type`),
	INDEX `INDEX_PMTOOL_CARD_USER_CUSTOMER_ID` (`customer_id`),
	INDEX `INDEX_PMTOOL_CARD_USER_CARD_ID` (`card_id`),
	INDEX `INDEX_PMTOOL_CARD_USER_STATUS` (`status`),
	UNIQUE INDEX `UQ_PMTOOL_CARD_USER_CUSTOMER_CARD` (`customer_id`, `card_id`),
	CONSTRAINT `FK_PMTOOL_CARD_USER_CUSTOMER_ID` FOREIGN KEY (`customer_id`) REFERENCES {$this->getTable('customer/entity')} (`entity_id`) ON DELETE SET NULL ON UPDATE CASCADE,
	CONSTRAINT `FK_PMTOOL_CARD_USER_CARD_ID` FOREIGN KEY (`card_id`) REFERENCES {$this->getTable('pmtool/card')} (`entity_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='卡片用户';

");

$installer->endSetup();