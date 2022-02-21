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
INSERT INTO `lylme_config` VALUES ('home-title', '上网，从这里开始！', '首页标题'),
INSERT INTO `lylme_config` VALUES ('copyright', 'Copyright ©2022 <a href=\"/\">LyLme Spage</a>.  All Rights Reserved.', '版权代码');
INSERT INTO `lylme_config` VALUES ('keywords', '六零导航页,百度搜索,哔哩哔哩搜索,知乎搜索,六零导航,LyLme Spage,六零,LyLme,网站导航,上网导航', '关键字');
INSERT INTO `lylme_config` VALUES ('logo', './assets/img/logo.png', '网站图标');
INSERT INTO `lylme_config` VALUES ('background', '/assets/img/background.jpg', '背景图片');
INSERT INTO `lylme_config` VALUES ('version', 'v1.0', '程序版本');
INSERT INTO `lylme_config` VALUES ('yan', 'true', '随机一言开关');
INSERT INTO `lylme_config` VALUES ('tq', 'true', '天气显示开关'),

-- ----------------------------
-- Table structure for lylme_groups
-- ----------------------------
DROP TABLE IF EXISTS `lylme_groups`;
CREATE TABLE `lylme_groups`  (
  `group_id` int(2) NOT NULL AUTO_INCREMENT COMMENT '分组ID',
  `group_name` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '分组名称',
  `group_icon` text COLLATE utf8mb4_unicode_ci COMMENT '分组图标',
  PRIMARY KEY (`group_id`) USING BTREE,
  UNIQUE INDEX `group_name`(`group_name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of lylme_groups
-- ----------------------------
INSERT INTO `lylme_groups` VALUES (1, '常用导航', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-remen\"></use></svg>');
INSERT INTO `lylme_groups` VALUES (2, '设计视觉', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-sheji\"></use></svg>');
INSERT INTO `lylme_groups` VALUES (3, '社交&存储', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-msg\"></use></svg>');
INSERT INTO `lylme_groups` VALUES (4, '工具', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-ai-tool\"></use></svg>');
INSERT INTO `lylme_groups` VALUES (5, '开发', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-kongzhitai\"></use></svg>');
INSERT INTO `lylme_groups` VALUES (6, '游戏娱乐', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-game00\"></use></svg>');
INSERT INTO `lylme_groups` VALUES (7, '网站公告', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-gg00\"></use></svg>');

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
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `组`(`group_id`) USING BTREE,
  CONSTRAINT `组` FOREIGN KEY (`group_id`) REFERENCES `lylme_groups` (`group_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 81 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = COMPACT;

-- ----------------------------
-- Records of lylme_links
-- ----------------------------
INSERT INTO `lylme_links` VALUES (1, '百度', 1, 'https://www.baidu.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-icon_baidulogo\"></use></svg>', '');
INSERT INTO `lylme_links` VALUES (2, '腾讯视频', 1, 'https://v.qq.com', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-tengxunshipin\"></use></svg>', '');
INSERT INTO `lylme_links` VALUES (3, '优酷', 1, 'https://www.youku.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-youku\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (4, '爱奇艺', 1, 'https://www.iqiyi.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-aiqiyi\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (5, '淘宝', 1, 'https://www.taobao.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-taobao\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (6, '哔哩哔哩', 1, 'https://www.bilibili.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-bili\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (7, '微博', 1, 'https://www.weibo.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-weibo\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (8, 'QQ邮箱', 1, 'https://mail.qq.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-qqmail00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (9, '百度贴吧', 1, 'https://tieba.baidu.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-tieba00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (10, 'CCTV直播', 1, 'https://tv.cctv.com/live/index.shtml', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-cctv\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (11, '抖音网页版', 1, 'https://www.douyin.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-douyin00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (12, '快手网页版', 1, 'https://www.kuaishou.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-kuaishou00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (13, '网易云音乐', 1, 'https://music.163.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-wyyyy00\">\r\n</use>\r\n\r\n\r\n</svg>', NULL);
INSERT INTO `lylme_links` VALUES (14, 'QQ音乐', 1, 'https://y.qq.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-qqmusic00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (15, '酷狗音乐', 1, 'https://www.kugou.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-kugou00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (16, '虎牙直播', 1, 'https://www.huya.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-huya00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (17, '斗鱼直播', 1, 'https://www.douyu.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-douyu00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (18, '企鹅电竞', 1, 'https://egame.qq.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-qie00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (19, '微信文件传输助手', 1, 'https://filehelper.weixin.qq.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-wechat00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (20, '中国大学MOOC', 1, 'https://www.icourse163.org/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-daxue\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (21, 'Office模板', 2, 'https://www.officeplus.cn/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-office00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (22, '搞定设计', 2, 'https://www.gaoding.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-gaoding00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (23, '急切网设计', 2, 'http://jiqie.zhenbi.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-ji00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (24, '千库网', 2, 'https://588ku.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-qianku00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (25, '图怪兽', 2, 'https://818ps.com', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-tu00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (26, '站酷', 2, 'https://www.zcool.com.cn/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-zhanku\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (27, '阿里图标', 2, 'https://www.iconfont.cn/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-iconfont\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (28, 'IconFinder', 2, 'https://www.iconfinder.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-eye\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (29, '优设教程', 2, 'https://uiiiuiii.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-jiaocheng\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (30, '知乎', 3, 'https://www.zhihu.com/explore', '\r\n<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-zhihu\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (31, '豆瓣', 3, 'https://www.douban.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-douban\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (32, '简书', 3, 'https://www.jianshu.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-jianshu\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (33, '阿里云盘', 3, 'https://www.aliyundrive.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-alipan00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (34, '百度网盘', 3, 'https://pan.baidu.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-baidupan00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (35, '蓝奏云', 3, 'https://www.lanzou.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-lanzou00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (36, '迅雷云盘', 3, 'https://pan.xunlei.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-xunleipan00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (37, 'OneDrive', 3, 'https://onedrive.live.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-OneDrive00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (38, '天翼云盘', 3, 'https://cloud.189.cn/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-tianyipan00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (39, 'UC网盘', 3, 'https://www.yun.cn/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-yunpan\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (40, 'QQ邮箱', 3, 'https://mail.qq.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-qqmail00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (41, 'Gmail', 3, 'https://mail.google.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-gmail\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (42, 'Hotmail', 3, 'https://outlook.live.com/mail/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-windows\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (43, '网易邮箱', 3, 'https://mail.163.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-wangyi\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (44, '新浪邮箱', 3, 'https://mail.sina.com.cn/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-xinlangwang\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (45, '阿里邮箱', 3, 'https://mail.aliyun.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-yunyouxiang\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (46, '在线工具', 4, 'https://tool.lu/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-ai-tool\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (47, 'IP查询', 4, 'https://ip.cn/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-wangluo\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (48, '文档在线转换', 4, 'https://xpdf.net/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-docto\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (49, '谷歌翻译', 4, 'https://translate.google.cn/?hl=zh-CN', '\r\n<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-fanyi\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (50, '有道翻译', 4, 'https://fanyi.youdao.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-youdao00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (51, 'HTML在线运行', 4, 'https://c.runoob.com/front-end/61/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-html00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (52, 'MD编辑器', 4, 'https://pandao.github.io/editor.md/index.html', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-md\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (53, '微PE工具', 4, 'http://www.wepe.com.cn/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-wepe00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (54, '在线代码格式化', 4, 'https://tool.oschina.net/codeformat/html/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-base64\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (55, 'JS混淆器', 4, 'http://tool.chinaz.com/tools/jscodeconfusion.aspx', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-jshunxiao\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (56, '站长工具', 4, 'http://tool.chinaz.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-zhanzhang00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (57, '在线Ping', 4, 'https://ping.chinaz.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-wangluo1\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (58, 'ICP备案查询', 4, 'https://icp.chinaz.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-icp00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (59, '在线PS', 4, 'https://www.photopea.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-ps00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (60, 'W3school', 5, 'http://www.w3school.com.cn/', '\r\n<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-h5\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (61, 'Github', 5, 'https://github.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-github\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (62, '码云Gitee', 5, 'https://gitee.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-gitee00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (63, '吾爱破解', 5, 'https://www.52pojie.cn/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-theater-masks\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (64, 'CSDN', 5, 'https://www.csdn.net/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-csdn\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (65, 'CdnJs', 5, 'https://cdnjs.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-cdnjs\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (66, '字节跳动CDN', 5, 'https://cdn.bytedance.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-zjtd00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (67, 'Font Awesome', 5, 'https://fontawesome.com/icons?https://fontawesome.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-font-awesome\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (68, 'MSDN我告诉你', 5, 'https://msdn.itellyou.cn/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-msdn00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (69, '腾讯云', 5, 'https://cloud.tencent.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-qcloud00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (70, '阿里云', 5, 'https://www.aliyun.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-alicloud00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (71, '4399小游戏', 6, 'http://www.4399.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-439900\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (72, '7k7k小游戏', 6, 'http://www.7k7k.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-7k7k00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (73, '英雄联盟', 6, 'https://lol.qq.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-lol00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (74, '永劫无间', 6, 'https://www.yjwujian.cn/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-yjwj00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (75, 'STEAM', 6, 'https://store.steampowered.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-steam00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (76, '王者荣耀', 6, 'https://pvp.qq.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-wzry00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (77, '3DM GAME', 6, 'https://www.3dmgame.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-games00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (78, '官方主页', 7, 'https://www.lylme.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-home00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (79, '申请收录', 7, 'https://blog.lylme.com/archives/included.html', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-sq00\"></use></svg>', NULL);
INSERT INTO `lylme_links` VALUES (80, '建议&反馈', 7, 'https://support.qq.com/products/356339', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-fk00\"></use></svg>', NULL);

SET FOREIGN_KEY_CHECKS = 1;
