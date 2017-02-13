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

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('blog_article')};
CREATE TABLE {$this->getTable('blog_article')} (
  `entity_id` int(10) unsigned NOT NULL auto_increment COMMENT 'Entity ID',
  `product_id` int(10) unsigned COMMENT 'Product ID',
  `price` decimal(12, 4) COMMENT 'Price',
  `name` varchar(255) COMMENT 'Name',
  `status` smallint(5) unsigned COMMENT 'Status',
  `created_at` timestamp NULL COMMENT 'Created At',
  `updated_at` timestamp NULL COMMENT 'Updated At',
  PRIMARY KEY  (`entity_id`),
  INDEX `INDEX_BLOG_ARTICLE_STATUS` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='博客文章信息';

    ");

$installer->endSetup();
