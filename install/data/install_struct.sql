SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


DROP TABLE IF EXISTS `lylme_apply`;
CREATE TABLE `lylme_apply` (
  `apply_id` int(4) NOT NULL,
  `apply_name` varchar(20) NOT NULL,
  `apply_url` varchar(255) NOT NULL,
  `apply_group` int(2) NOT NULL,
  `apply_icon` text NOT NULL,
  `apply_desc` varchar(30) NOT NULL,
  `apply_time` datetime NOT NULL,
  `apply_status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='收录申请';

DROP TABLE IF EXISTS `lylme_config`;
CREATE TABLE `lylme_config` (
  `id` int(11) NOT NULL,
  `k` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '键',
  `v` text COLLATE utf8mb4_unicode_ci COMMENT '值',
  `description` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '描述'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='导航配置' ROW_FORMAT=COMPACT;

INSERT INTO `lylme_config` (`id`, `k`, `v`, `description`) VALUES
(1, 'admin_user', 'admin', '管理员用户名'),
(2, 'admin_pwd', '1ef987f238b7a80eaf3689cfc42aad2d', '管理员密码(1ef987f238b7a80eaf3689cfc42aad2d)对应123456'),
(3, 'md5pass', '1', '启用md5加密密码'),
(4, 'apply', '0', '收录申请'),
(5, 'apply_gg', '<b>收录说明：</b><br>1. 禁止提交违规违法站点<br>2. 页面整洁，无多个弹窗广告和恶意跳转<br>3. 非盈利性网站，网站正常访问<br>4. 添加本站友链或网站已ICP备案优先收录<br>', '收录公告'),
(6, 'background', './assets/img/background.jpg', '背景图片'),
(7, 'cdnpublic', NULL, 'CDN地址'),
(8, 'copyright', 'Copyright ©2022 <a href=\"/\">LyLme Spage</a>.  All Rights Reserved.', '版权代码'),
(9, 'description', '六零导航页(LyLme Spage)致力于简洁高效无广告的上网导航和搜索入口，沉淀最具价值链接，全站无商业推广，简约而不简单。', '网站描述'),
(10, 'home-title', '<h2 class=\"title\">上网，从这里开始！</h2>', '首页标题'),
(11, 'icp', '', '备案号'),
(12, 'keywords', '六零导航页,百度搜索,哔哩哔哩搜索,知乎搜索,六零导航,LyLme Spage,六零,LyLme,网站导航,上网导航', '关键字'),
(13, 'logo', './assets/img/logo.png', '网站图标'),
(14, 'template', 'default', '网站模板'),
(15, 'title', '上网导航 - LyLme Spage', '网站名称'),
(16, 'tq', 'true', '天气显示开关'),
(17, 'version', 'v1.8.0', '程序版本'),
(18, 'wap_background', NULL, '手机端背景'),
(19, 'wztj', '', '网站统计代码'),
(20, 'yan', 'true', '随机一言开关'),
(21, 'wxplus', '', '微信推送密钥'),
(22, 'wxplustime', '20:00', '微信推送时间');


ALTER TABLE `lylme_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `k` (`k`);


ALTER TABLE `lylme_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
COMMIT;

DROP TABLE IF EXISTS `lylme_groups`;
CREATE TABLE `lylme_groups` (
  `group_id` int(2) NOT NULL COMMENT '分组ID',
  `group_name` varchar(10) NOT NULL COMMENT '分组名称',
  `group_icon` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '分组图标',
  `group_order` int(4) NOT NULL DEFAULT '5' COMMENT '分组排序',
  `group_status` int(1) NOT NULL DEFAULT '1' COMMENT '分组状态',
  `group_pwd` int(2) NOT NULL DEFAULT '0' COMMENT '加密组ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

DROP TABLE IF EXISTS `lylme_links`;
CREATE TABLE `lylme_links` (
  `id` int(4) NOT NULL COMMENT '网站ID',
  `name` varchar(20) NOT NULL COMMENT '链接标题',
  `group_id` int(2) NOT NULL DEFAULT '1' COMMENT '分组名称',
  `url` varchar(255) NOT NULL COMMENT '链接地址',
  `icon` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '链接图标',
  `link_desc` varchar(255) DEFAULT NULL COMMENT '链接描述',
  `link_order` int(4) DEFAULT '10' COMMENT '链接排序',
  `link_status` int(1) NOT NULL DEFAULT '1' COMMENT '链接状态',
  `link_pwd` int(2) DEFAULT '0' COMMENT '加密组ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

DROP TABLE IF EXISTS `lylme_pwd`;
CREATE TABLE `lylme_pwd` (
  `pwd_id` int(2) NOT NULL COMMENT '加密组ID',
  `pwd_name` varchar(20) NOT NULL COMMENT '加密组名称',
  `pwd_key` varchar(20) NOT NULL COMMENT '加密组密码',
  `pwd_ps` varchar(30) DEFAULT NULL COMMENT '加密组备注'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `lylme_sou`;
CREATE TABLE `lylme_sou` (
  `sou_id` int(11) NOT NULL COMMENT '搜索引擎ID',
  `sou_alias` varchar(20) NOT NULL COMMENT '搜索引擎别名',
  `sou_name` varchar(20) NOT NULL COMMENT '搜索引擎名称',
  `sou_hint` varchar(30) NOT NULL DEFAULT '请输入搜索关键词' COMMENT '搜索引擎提示文字',
  `sou_color` varchar(20) NOT NULL DEFAULT '#696a6d' COMMENT '搜索引擎字体颜色',
  `sou_link` varchar(255) NOT NULL COMMENT '搜索引擎地址',
  `sou_waplink` varchar(255) DEFAULT NULL COMMENT '搜索引擎移动端地址',
  `sou_icon` text NOT NULL COMMENT '搜索引擎图标',
  `sou_st` int(1) NOT NULL DEFAULT '1' COMMENT '搜索引擎开关',
  `sou_order` int(2) NOT NULL COMMENT '搜索引擎排序'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='搜索引擎';

DROP TABLE IF EXISTS `lylme_tags`;
CREATE TABLE `lylme_tags` (
  `tag_id` int(11) NOT NULL,
  `tag_name` varchar(30) NOT NULL,
  `tag_link` varchar(60) NOT NULL,
  `tag_target` int(1) NOT NULL DEFAULT '1',
  `sort` int(11) NOT NULL DEFAULT '10' COMMENT '权重'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `lylme_apply`
  ADD PRIMARY KEY (`apply_id`);

ALTER TABLE `lylme_config`
  ADD PRIMARY KEY (`k`) USING BTREE;

ALTER TABLE `lylme_groups`
  ADD PRIMARY KEY (`group_id`) USING BTREE,
  ADD UNIQUE KEY `group_name` (`group_name`) USING BTREE;

ALTER TABLE `lylme_links`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `组` (`group_id`) USING BTREE;

ALTER TABLE `lylme_pwd`
  ADD PRIMARY KEY (`pwd_id`);

ALTER TABLE `lylme_sou`
  ADD PRIMARY KEY (`sou_id`);

ALTER TABLE `lylme_tags`
  ADD PRIMARY KEY (`tag_id`);


ALTER TABLE `lylme_apply`
  MODIFY `apply_id` int(4) NOT NULL AUTO_INCREMENT;

ALTER TABLE `lylme_groups`
  MODIFY `group_id` int(2) NOT NULL AUTO_INCREMENT COMMENT '分组ID';

ALTER TABLE `lylme_links`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT COMMENT '网站ID';

ALTER TABLE `lylme_pwd`
  MODIFY `pwd_id` int(2) NOT NULL AUTO_INCREMENT COMMENT '加密组ID';

ALTER TABLE `lylme_sou`
  MODIFY `sou_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '搜索引擎ID';

ALTER TABLE `lylme_tags`
  MODIFY `tag_id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `lylme_links`
  ADD CONSTRAINT `组` FOREIGN KEY (`group_id`) REFERENCES `lylme_groups` (`group_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
