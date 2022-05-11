-- v1.1.6
ALTER TABLE `lylme_links` ADD `link_status` INT(1) NOT NULL DEFAULT '1' COMMENT '链接状态' AFTER `link_order`;
ALTER TABLE `lylme_groups` ADD `group_status` INT(1) NOT NULL DEFAULT '1' COMMENT '分组状态' AFTER `group_order`;
