INSERT INTO `lylme_config` VALUES ('template', 'default','网站模板');
INSERT INTO `lylme_config` VALUES ('cdnpublic', NULL,'CDN地址');
INSERT INTO `lylme_config` VALUES ('apply', 0,'收录申请');
CREATE TABLE `lylme_apply` (
  `apply_id` int(4) NOT NULL,
  `apply_name` varchar(20) NOT NULL,
  `apply_url` varchar(255) NOT NULL,
  `apply_group` int(2) NOT NULL,
  `apply_icon` text NOT NULL,
  `apply_mail` varchar(30) NOT NULL,
  `apply_time` datetime NOT NULL,
  `apply_status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='收录申请';
ALTER TABLE `lylme_apply`  ADD PRIMARY KEY (`apply_id`);
ALTER TABLE `lylme_apply`  MODIFY `apply_id` int(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;
COMMIT;
UPDATE `lylme_config` SET `v` = 'v1.1.3' WHERE `lylme_config`.`k` = 'version';