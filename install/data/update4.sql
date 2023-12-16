-- v1.1.9
ALTER TABLE `lylme_links` ADD `link_pwd` INT(2) NULL DEFAULT '0' COMMENT '加密组ID' AFTER `link_status`;
ALTER TABLE `lylme_groups` ADD `group_pwd` INT(2) NOT NULL DEFAULT '0' COMMENT '加密组ID' AFTER `group_status`;
CREATE TABLE `lylme_pwd` (
  `pwd_id` int(2) NOT NULL COMMENT '加密组ID',
  `pwd_name` varchar(20) NOT NULL COMMENT '加密组名称',
  `pwd_key` varchar(20) NOT NULL COMMENT '加密组密码',
  `pwd_ps` varchar(30) DEFAULT NULL COMMENT '加密组备注'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `lylme_pwd`
  ADD PRIMARY KEY (`pwd_id`);
ALTER TABLE `lylme_pwd`
  MODIFY `pwd_id` int(2) NOT NULL AUTO_INCREMENT COMMENT '加密组ID', AUTO_INCREMENT=1;
  INSERT INTO `lylme_tags` (`tag_id`, `tag_name`, `tag_link`, `tag_target`) VALUES (NULL, '查看', '/pwd', '0');