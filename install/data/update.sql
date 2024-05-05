-- 表 `lylme_sou`
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
INSERT INTO `lylme_sou` (`sou_id`, `sou_alias`, `sou_name`, `sou_hint`, `sou_color`, `sou_link`, `sou_waplink`, `sou_icon`, `sou_st`, `sou_order`) VALUES
(1, 'baidu', '百度一下', '百度一下，你就知道', '#0c498c', 'https://www.baidu.com/s?word=', 'https://m.baidu.com/s?word=', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-icon_baidulogo\"></use></svg>', 1, 1),
(2, 'sogou', '搜狗搜索', '上网从搜狗开始', '#696a6d', 'https://www.sogou.com/web?query=', NULL, '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-sougou\"></use></svg>', 1, 2),
(3, 'bing', 'Bing必应', '微软必应搜索', '#696a6d', 'https://cn.bing.com/search?q=', NULL, '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-bing\"></use></svg>', 1, 3),
(4, 'zhihu', '知乎搜索', '有问题，上知乎', '#0084fe', 'https://www.zhihu.com/search?q=', NULL, '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-zhihu\"></use></svg>', 1, 4),
(5, 'bilibili', '哔哩哔哩', '(゜-゜)つロ 干杯~', '#00aeec', 'https://search.bilibili.com/all?keyword=', NULL, '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-bili\"></use></svg>', 1, 5),
(6, 'weibo', '微博搜索', '随时随地发现新鲜事', '#ff5722', 'https://s.weibo.com/weibo/', NULL, '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-weibo\"></use></svg>', 1, 6),
(7, 'google', '谷歌搜索', '值得信任的搜索引擎', '#3B83FA', 'https://www.google.com.hk/search?hl=zh-CN&q=', NULL, '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-google00\"></use></svg>', 1, 7),
(8, 'fanyi', '在线翻译', '输入翻译内容（自动检测语言）', '#0084fe', 'https://translate.google.cn/?hl=zh-CN&sl=auto&tl=zh-CN&text=', NULL, '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-fanyi\"></use></svg>', 1, 8);
ALTER TABLE `lylme_sou`
  ADD PRIMARY KEY (`sou_id`);
ALTER TABLE `lylme_sou`
  MODIFY `sou_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '搜索引擎ID', AUTO_INCREMENT=9;
COMMIT;


-- 表`lylme_tags`
CREATE TABLE `lylme_tags` (
  `tag_id` int(11) NOT NULL,
  `tag_name` varchar(30) NOT NULL,
  `tag_link` varchar(60) NOT NULL,
  `tag_target` int(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO `lylme_tags` (`tag_id`, `tag_name`, `tag_link`, `tag_target`) VALUES
(1, '关于本站', '/about', 10);
ALTER TABLE `lylme_tags`
  ADD PRIMARY KEY (`tag_id`);
ALTER TABLE `lylme_tags`
  MODIFY `tag_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;
UPDATE `lylme_config` SET `v` = 'v1.1.2' WHERE `lylme_config`.`k` = 'version';