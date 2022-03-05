/*
 Source Server         : 六零导航页
 Source Server Type    : MySQL
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for lylme_config
-- ----------------------------
DROP TABLE IF EXISTS `lylme_config`;
CREATE TABLE `lylme_config`  (
  `k` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '键',
  `v` text COLLATE utf8mb4_unicode_ci COMMENT '值',
  `description` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '描述',
  PRIMARY KEY (`k`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci COMMENT = '导航配置' ROW_FORMAT = Compact;

-- ----------------------------
-- Records of lylme_config
-- ----------------------------
INSERT INTO `lylme_config` VALUES ('admin_user', 'admin', '管理员账号');
INSERT INTO `lylme_config` VALUES ('admin_pwd', '123456', '管理员密码');
INSERT INTO `lylme_config` VALUES ('title', '上网导航 - LyLme Spage', '网站名称');
INSERT INTO `lylme_config` VALUES ('description', '六零导航页(LyLme Spage)致力于简洁高效无广告的上网导航和搜索入口，沉淀最具价值链接，全站无商业推广，简约而不简单。', '网站描述');
INSERT INTO `lylme_config` VALUES ('icp', '', '备案号');
INSERT INTO `lylme_config` VALUES ('home-title', '<h2 class="title">上网，从这里开始！</h2>', '首页标题');
INSERT INTO `lylme_config` VALUES ('copyright', 'Copyright ©2022 <a href=\"/\">LyLme Spage</a>.  All Rights Reserved.', '版权代码');
INSERT INTO `lylme_config` VALUES ('keywords', '六零导航页,百度搜索,哔哩哔哩搜索,知乎搜索,六零导航,LyLme Spage,六零,LyLme,网站导航,上网导航', '关键字');
INSERT INTO `lylme_config` VALUES ('logo', './assets/img/logo.png', '网站图标');
INSERT INTO `lylme_config` VALUES ('background', '', '背景图片');
INSERT INTO `lylme_config` VALUES ('version', 'v1.1.1', '程序版本');
INSERT INTO `lylme_config` VALUES ('yan', 'true', '随机一言开关');
INSERT INTO `lylme_config` VALUES ('tq', 'true', '天气显示开关');
INSERT INTO `lylme_config` VALUES ('wztj', '', '网站统计代码');
-- ----------------------------
-- Table structure for lylme_groups
-- ----------------------------
DROP TABLE IF EXISTS `lylme_groups`;
CREATE TABLE `lylme_groups`  (
  `group_id` int(2) NOT NULL AUTO_INCREMENT COMMENT '分组ID',
  `group_name` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '分组名称',
  `group_icon` text COLLATE utf8mb4_unicode_ci COMMENT '分组图标',
   `group_order` int(4) NOT NULL DEFAULT '5' COMMENT '分组排序',
  PRIMARY KEY (`group_id`) USING BTREE,
  UNIQUE INDEX `group_name`(`group_name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of lylme_groups
-- ----------------------------
INSERT INTO `lylme_groups` (`group_id`, `group_name`, `group_icon`, `group_order`) VALUES
(1, '常用导航', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-remen\"></use></svg>', 1),
(2, '设计视觉', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-sheji\"></use></svg>', 2),
(3, '社交&存储', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-msg\"></use></svg>', 3),
(4, '工具', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-ai-tool\"></use></svg>', 4),
(5, '开发', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-kongzhitai\"></use></svg>', 5),
(6, '游戏娱乐', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-game00\"></use></svg>', 6),
(7, '网站公告', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-gg00\"></use></svg>', 7);
-- ----------------------------
-- Table structure for lylme_links
-- ----------------------------
DROP TABLE IF EXISTS `lylme_links`;
CREATE TABLE `lylme_links`  (
  `id` int(4) NOT NULL AUTO_INCREMENT COMMENT '网站ID',
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '链接标题',
  `group_id` int(2) NOT NULL DEFAULT 1 COMMENT '分组名称',
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '链接地址',
  `icon` text COLLATE utf8mb4_unicode_ci COMMENT '链接图标',
  `PS` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '备注',
  `link_order` int(4) DEFAULT '10' COMMENT '链接排序',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `组`(`group_id`) USING BTREE,
  CONSTRAINT `组` FOREIGN KEY (`group_id`) REFERENCES `lylme_groups` (`group_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 81 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of lylme_links
-- ----------------------------
INSERT INTO `lylme_links` (`id`, `name`, `group_id`, `url`, `icon`, `PS`, `link_order`) VALUES
(1, '百度', 1, 'https://www.baidu.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-icon_baidulogo\"></use></svg>', NULL, 10),
(2, '腾讯视频', 1, 'https://v.qq.com', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-tengxunshipin\"></use></svg>', NULL, 10),
(3, '优酷', 1, 'https://www.youku.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-youku\"></use></svg>', NULL, 10),
(4, '爱奇艺', 1, 'https://www.iqiyi.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-aiqiyi\"></use></svg>', NULL, 10),
(5, '淘宝', 1, 'https://www.taobao.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-taobao\"></use></svg>', NULL, 10),
(6, '哔哩哔哩', 1, 'https://www.bilibili.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-bili\"></use></svg>', NULL, 10),
(7, '微博', 1, 'https://www.weibo.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-weibo\"></use></svg>', NULL, 10),
(8, 'QQ邮箱', 1, 'https://mail.qq.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-qqmail00\"></use></svg>', NULL, 10),
(9, '百度贴吧', 1, 'https://tieba.baidu.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-tieba00\"></use></svg>', NULL, 10),
(10, 'CCTV直播', 1, 'https://tv.cctv.com/live/index.shtml', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-cctv\"></use></svg>', NULL, 10),
(11, '抖音网页版', 1, 'https://www.douyin.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-douyin00\"></use></svg>', NULL, 10),
(12, '快手网页版', 1, 'https://www.kuaishou.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-kuaishou00\"></use></svg>', NULL, 10),
(13, '网易云音乐', 1, 'https://music.163.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-wyyyy00\"></use></svg>', NULL, 10),
(14, 'QQ音乐', 1, 'https://y.qq.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-qqmusic00\"></use></svg>', NULL, 10),
(15, '酷狗音乐', 1, 'https://www.kugou.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-kugou00\"></use></svg>', NULL, 10),
(16, '虎牙直播', 1, 'https://www.huya.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-huya00\"></use></svg>', NULL, 10),
(17, '斗鱼直播', 1, 'https://www.douyu.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-douyu00\"></use></svg>', NULL, 10),
(18, '企鹅电竞', 1, 'https://egame.qq.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-qie00\"></use></svg>', NULL, 10),
(19, '微信文件传输助手', 1, 'https://filehelper.weixin.qq.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-wechat00\"></use></svg>', NULL, 10),
(20, '中国大学MOOC', 1, 'https://www.icourse163.org/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-daxue\"></use></svg>', NULL, 10),
(21, 'Office模板', 2, 'https://www.officeplus.cn/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-office00\"></use></svg>', NULL, 10),
(22, '搞定设计', 2, 'https://www.gaoding.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-gaoding00\"></use></svg>', NULL, 10),
(23, '急切网设计', 2, 'http://jiqie.zhenbi.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-ji00\"></use></svg>', NULL, 10),
(24, '千库网', 2, 'https://588ku.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-qianku00\"></use></svg>', NULL, 10),
(25, '图怪兽', 2, 'https://818ps.com', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-tu00\"></use></svg>', NULL, 10),
(26, '站酷', 2, 'https://www.zcool.com.cn/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-zhanku\"></use></svg>', NULL, 10),
(27, '阿里图标', 2, 'https://www.iconfont.cn/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-iconfont\"></use></svg>', NULL, 10),
(28, 'IconFinder', 2, 'https://www.iconfinder.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-eye\"></use></svg>', NULL, 10),
(29, '优设教程', 2, 'https://uiiiuiii.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-jiaocheng\"></use></svg>', NULL, 10),
(30, '知乎', 3, 'https://www.zhihu.com/explore', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-zhihu\"></use></svg>', NULL, 10),
(31, '豆瓣', 3, 'https://www.douban.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-douban\"></use></svg>', NULL, 10),
(32, '简书', 3, 'https://www.jianshu.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-jianshu\"></use></svg>', NULL, 10),
(33, '阿里云盘', 3, 'https://www.aliyundrive.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-alipan00\"></use></svg>', NULL, 10),
(34, '百度网盘', 3, 'https://pan.baidu.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-baidupan00\"></use></svg>', NULL, 10),
(35, '蓝奏云', 3, 'https://www.lanzou.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-lanzou00\"></use></svg>', NULL, 10),
(36, '迅雷云盘', 3, 'https://pan.xunlei.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-xunleipan00\"></use></svg>', NULL, 10),
(37, 'OneDrive', 3, 'https://onedrive.live.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-OneDrive00\"></use></svg>', NULL, 10),
(38, '天翼云盘', 3, 'https://cloud.189.cn/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-tianyipan00\"></use></svg>', NULL, 10),
(39, 'UC网盘', 3, 'https://www.yun.cn/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-yunpan\"></use></svg>', NULL, 10),
(40, 'QQ邮箱', 3, 'https://mail.qq.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-qqmail00\"></use></svg>', NULL, 10),
(41, 'Gmail', 3, 'https://mail.google.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-gmail\"></use></svg>', NULL, 10),
(42, 'Hotmail', 3, 'https://outlook.live.com/mail/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-windows\"></use></svg>', NULL, 10),
(43, '网易邮箱', 3, 'https://mail.163.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-wangyi\"></use></svg>', NULL, 10),
(44, '新浪邮箱', 3, 'https://mail.sina.com.cn/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-xinlangwang\"></use></svg>', NULL, 10),
(45, '阿里邮箱', 3, 'https://mail.aliyun.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-yunyouxiang\"></use></svg>', NULL, 10),
(46, '在线工具', 4, 'https://tool.lu/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-ai-tool\"></use></svg>', NULL, 10),
(47, 'IP查询', 4, 'https://ip.cn/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-wangluo\"></use></svg>', NULL, 10),
(48, '文档在线转换', 4, 'https://xpdf.net/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-docto\"></use></svg>', NULL, 10),
(49, '谷歌翻译', 4, 'https://translate.google.cn/?hl=zh-CN', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-fanyi\"></use></svg>', NULL, 10),
(50, '有道翻译', 4, 'https://fanyi.youdao.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-youdao00\"></use></svg>', NULL, 10),
(51, 'HTML在线运行', 4, 'https://c.runoob.com/front-end/61/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-html00\"></use></svg>', NULL, 10),
(52, 'MD编辑器', 4, 'https://pandao.github.io/editor.md/index.html', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-md\"></use></svg>', NULL, 10),
(53, '微PE工具', 4, 'http://www.wepe.com.cn/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-wepe00\"></use></svg>', NULL, 10),
(54, '在线代码格式化', 4, 'https://tool.oschina.net/codeformat/html/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-base64\"></use></svg>', NULL, 10),
(55, 'JS混淆器', 4, 'http://tool.chinaz.com/tools/jscodeconfusion.aspx', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-jshunxiao\"></use></svg>', NULL, 10),
(56, '站长工具', 4, 'http://tool.chinaz.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-zhanzhang00\"></use></svg>', NULL, 10),
(57, '在线Ping', 4, 'https://ping.chinaz.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-wangluo1\"></use></svg>', NULL, 10),
(58, 'ICP备案查询', 4, 'https://icp.chinaz.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-icp00\"></use></svg>', NULL, 10),
(59, '在线PS', 4, 'https://www.photopea.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-ps00\"></use></svg>', NULL, 10),
(60, 'W3school', 5, 'http://www.w3school.com.cn/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-h5\"></use></svg>', NULL, 10),
(61, 'Github', 5, 'https://github.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-github\"></use></svg>', NULL, 10),
(62, '码云Gitee', 5, 'https://gitee.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-gitee00\"></use></svg>', NULL, 10),
(63, '吾爱破解', 5, 'https://www.52pojie.cn/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-theater-masks\"></use></svg>', NULL, 10),
(64, 'CSDN', 5, 'https://www.csdn.net/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-csdn\"></use></svg>', NULL, 10),
(65, 'CdnJs', 5, 'https://cdnjs.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-cdnjs\"></use></svg>', NULL, 10),
(66, '字节跳动CDN', 5, 'https://cdn.bytedance.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-zjtd00\"></use></svg>', NULL, 10),
(67, 'Font Awesome', 5, 'https://fontawesome.com/icons?https://fontawesome.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-font-awesome\"></use></svg>', NULL, 10),
(68, 'MSDN我告诉你', 5, 'https://msdn.itellyou.cn/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-msdn00\"></use></svg>', NULL, 10),
(69, '腾讯云', 5, 'https://cloud.tencent.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-qcloud00\"></use></svg>', NULL, 10),
(70, '阿里云', 5, 'https://www.aliyun.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-alicloud00\"></use></svg>', NULL, 10),
(71, '4399小游戏', 6, 'http://www.4399.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-439900\"></use></svg>', NULL, 10),
(72, '7k7k小游戏', 6, 'http://www.7k7k.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-7k7k00\"></use></svg>', NULL, 10),
(73, '英雄联盟', 6, 'https://lol.qq.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-lol00\"></use></svg>', NULL, 10),
(74, '永劫无间', 6, 'https://www.yjwujian.cn/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-yjwj00\"></use></svg>', NULL, 10),
(75, 'STEAM', 6, 'https://store.steampowered.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-steam00\"></use></svg>', NULL, 10),
(76, '王者荣耀', 6, 'https://pvp.qq.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-wzry00\"></use></svg>', NULL, 10),
(77, '3DM GAME', 6, 'https://www.3dmgame.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-games00\"></use></svg>', NULL, 10),
(78, '官方主页', 7, 'https://www.lylme.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-home00\"></use></svg>', NULL, 10),
(79, '申请收录', 7, 'https://blog.lylme.com/archives/included.html', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-sq00\"></use></svg>', NULL, 10),
(80, '建议&反馈', 7, 'https://support.qq.com/products/356339', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-fk00\"></use></svg>', NULL, 10);

SET FOREIGN_KEY_CHECKS = 1;

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
(6, 'weibo', '微博搜索', '随时随地发现新鲜事', '#ff5722', 'https://s.weibo.com/weibo/', '', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-weibo\"></use></svg>', 1, 6),
(7, 'google', '谷歌搜索', '值得信任的搜索引擎', '#3B83FA', 'https://www.google.com.hk/search?hl=zh-CN&q=', '', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-google00\"></use></svg>', 1, 7),
(8, 'fanyi', '在线翻译', '输入翻译内容（自动检测语言）', '#0084fe', 'https://translate.google.cn/?hl=zh-CN&sl=auto&tl=zh-CN&text=', NULL, '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-fanyi\"></use></svg>', 1, 8);

--
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
(1, '主页', 'https://www.lylme.com/', 0),
(2, '博客', 'https://blog.lylme.com/', 1),
(3, 'Github', 'https://github.com/lylme', 1),
(4, '关于', 'https://gitee.com/LyLme/lylme_spage/blob/master/README.md', 1);
ALTER TABLE `lylme_tags`
  ADD PRIMARY KEY (`tag_id`);
ALTER TABLE `lylme_tags`
  MODIFY `tag_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;