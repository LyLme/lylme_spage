SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
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
  `apply_desc` varchar(255) DEFAULT NULL COMMENT '链接描述',
  `apply_time` datetime NOT NULL,
  `apply_status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='收录申请';

DROP TABLE IF EXISTS `lylme_config`;
CREATE TABLE `lylme_config` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `k` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '键',
  `v` text COLLATE utf8mb4_unicode_ci COMMENT '值',
  `description` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '描述'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='导航配置' ROW_FORMAT=COMPACT;

INSERT INTO `lylme_config` VALUES
(1, 'admin_pwd', '1ef987f238b7a80eaf3689cfc42aad2d', '管理员密码'),
(2, 'admin_user', 'admin', '管理员账号'),
(3, 'apply', '0', '收录申请'),
(4, 'apply_gg', '<b>收录说明：</b><br>1. 禁止提交违规违法站点<br>2. 页面整洁，无多个弹窗广告和恶意跳转<br>3. 非盈利性网站，网站正常访问<br>4. 添加本站友链或网站已ICP备案优先收录<br>', '收录公告'),
(5, 'background', './assets/img/background.jpg', '背景图片'),
(6, 'title', '上网导航 - LyLme Spage', '网站标题'),
(7, 'cdnpublic', NULL, 'CDN地址'),
(8, 'copyright', 'Copyright ©2022-2024 <a href=\"/\">LyLme Spage</a>.  All Rights Reserved.', '版权代码'),
(9, 'description', '六零导航页(LyLme Spage)致力于简洁高效无广告的上网导航和搜索入口，沉淀最具价值链接，全站无商业推广，简约而不简单。', '网站描述'),
(10, 'home-title', '<h2 class=\"title\">上网，从这里开始！</h2>', '首页标题'),
(11, 'icp', '', '备案号'),
(12, 'keywords', '六零导航页,百度搜索,哔哩哔哩搜索,知乎搜索,六零导航,LyLme Spage,六零,LyLme,网站导航,上网导航', '关键字'),
(13, 'logo', './assets/img/logo.png', '网站LOGO'),
(14, 'md5pass', '1', '启用md5加密密码'),
(15, 'template', 'default', '网站模板'),
(16, 'wztj', '', '网站统计代码(自定义footer)'),
(17, 'tq', 'true', '天气显示开关'),
(18, 'version', 'v1.9.5', '程序版本'),
(19, 'wap_background', NULL, '手机端背景'),
(20, 'wxplus', '', '微信推送密钥'),
(21, 'wxplustime', '20:00', '微信推送时间'),
(22, 'yan', 'true', '随机一言开关'),
(23, 'about', '1', '新版关于页面'),
(24, 'about_content', '<h3>关于本站</h3>\r\n<p>感谢来访，本站致力于简洁高效的上网导航和搜索入口，安全快捷。</p>\r\n<p>如果您喜欢我们的网站，请将本站添加到收藏夹（快捷键<code>Ctrl+D</code>），并<a href=\"https://jingyan.baidu.com/article/4dc40848868eba89d946f1c0.html\" target=\"_blank\">设为浏览器主页</a>，方便您的下次访问，感谢支持。<p>\r\n<hr>\r\n<h3>本站承诺</h3>\r\n<p><strong>绝对不会收集用户的隐私信息</strong><p>\r\n<p>区别于部分导航网站，本站链接直接跳转目标，不会对链接处理再后跳转，不会收集用户的隐藏信息，包括但不限于点击记录，访问记录和搜索记录，请放心使用</p>\r\n<hr>\r\n<h3>申请收录</h3>\r\n<p>请点<a href=\"../apply\" target=\"_blank\">这里</a></p>\r\n<hr>\r\n<h3>联系我们</h3>\r\n<p>若您在使用本站时遇到了包括但不限于以下问题：</p>\r\n<li>图标缺失</li>\r\n<li>目标网站无法打开</li>\r\n<li>描述错误</li>\r\n<li>网站违规</li>\r\n<li>收录加急处理</li>\r\n<li>链接删除</li>\r\n<p><strong>请发邮件与我们联系</strong></p>\r\n<h5>联系邮箱</h5>\r\n<p><a href=\"mailto:无\">无</a></p>\r\n<h5>联系说明</h5>\r\n<p>为了您的问题能快速被处理，建议在邮件主题添加【反馈】【投诉】【推荐】【友链】</p>', '关于页面');

DROP TABLE IF EXISTS `lylme_groups`;
CREATE TABLE `lylme_groups` (
  `group_id` int(2) NOT NULL COMMENT '分组ID',
  `group_name` varchar(10) NOT NULL COMMENT '分组名称',
  `group_icon` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '分组图标',
  `group_order` int(4) NOT NULL DEFAULT '5' COMMENT '分组排序',
  `group_status` int(1) NOT NULL DEFAULT '1' COMMENT '分组状态',
  `group_pwd` int(2) NOT NULL DEFAULT '0' COMMENT '加密组ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

INSERT INTO `lylme_groups` VALUES
(1, '常用导航', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-remen\"></use></svg>', 1, 1, 0),
(2, '设计视觉', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-sheji\"></use></svg>', 2, 1, 0),
(3, '社交&存储', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-msg\"></use></svg>', 3, 1, 0),
(4, '工具', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-ai-tool\"></use></svg>', 4, 1, 0),
(5, '开发', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-kongzhitai\"></use></svg>', 5, 1, 0),
(6, '游戏娱乐', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-game00\"></use></svg>', 6, 1, 0),
(7, '网站公告', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-gg00\"></use></svg>', 7, 1, 0);

DROP TABLE IF EXISTS `lylme_links`;
CREATE TABLE `lylme_links` (
  `id` int(4) NOT NULL COMMENT '链接ID',
  `name` varchar(255) NOT NULL COMMENT '链接标题',
  `group_id` int(2) NOT NULL DEFAULT '1' COMMENT '分组名称',
  `url` varchar(255) NOT NULL COMMENT '链接地址',
  `icon` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT '链接图标',
  `link_desc` varchar(255) DEFAULT NULL COMMENT '链接描述',
  `link_order` int(4) DEFAULT '10' COMMENT '链接排序',
  `link_status` int(1) NOT NULL DEFAULT '1' COMMENT '链接状态',
  `link_pwd` int(2) DEFAULT '0' COMMENT '加密组ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

INSERT INTO `lylme_links` VALUES
(1, '百度', 1, 'https://www.baidu.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-icon_baidulogo\"></use></svg>', NULL, 10, 1, 0),
(2, '腾讯视频', 1, 'https://v.qq.com', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-tengxunshipin\"></use></svg>', NULL, 10, 1, 0),
(3, '优酷', 1, 'https://www.youku.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-youku\"></use></svg>', NULL, 10, 1, 0),
(4, '爱奇艺', 1, 'https://www.iqiyi.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-aiqiyi\"></use></svg>', NULL, 10, 1, 0),
(5, '淘宝', 1, 'https://www.taobao.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-taobao\"></use></svg>', NULL, 10, 1, 0),
(6, '哔哩哔哩', 1, 'https://www.bilibili.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-bili\"></use></svg>', NULL, 10, 1, 0),
(7, '微博', 1, 'https://www.weibo.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-weibo\"></use></svg>', NULL, 10, 1, 0),
(8, 'QQ邮箱', 1, 'https://mail.qq.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-qqmail00\"></use></svg>', NULL, 10, 1, 0),
(9, '百度贴吧', 1, 'https://tieba.baidu.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-tieba00\"></use></svg>', NULL, 10, 1, 0),
(10, 'CCTV直播', 1, 'https://tv.cctv.com/live/index.shtml', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-cctv\"></use></svg>', NULL, 10, 1, 0),
(11, '抖音网页版', 1, 'https://www.douyin.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-douyin00\"></use></svg>', NULL, 10, 1, 0),
(12, '快手网页版', 1, 'https://www.kuaishou.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-kuaishou00\"></use></svg>', NULL, 10, 1, 0),
(13, '网易云音乐', 1, 'https://music.163.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-wyyyy00\"></use></svg>', NULL, 10, 1, 0),
(14, 'QQ音乐', 1, 'https://y.qq.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-qqmusic00\"></use></svg>', NULL, 10, 1, 0),
(15, '酷狗音乐', 1, 'https://www.kugou.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-kugou00\"></use></svg>', NULL, 10, 1, 0),
(16, '虎牙直播', 1, 'https://www.huya.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-huya00\"></use></svg>', NULL, 10, 1, 0),
(17, '斗鱼直播', 1, 'https://www.douyu.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-douyu00\"></use></svg>', NULL, 10, 1, 0),
(18, '企鹅电竞', 1, 'https://egame.qq.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-qie00\"></use></svg>', NULL, 10, 1, 0),
(19, '微信文件传输助手', 1, 'https://filehelper.weixin.qq.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-wechat00\"></use></svg>', NULL, 10, 1, 0),
(20, '今日热点', 1, 'https://60s.lylme.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-remen\"></use></svg>', NULL, 10, 1, 0),
(21, 'Office模板', 2, 'https://www.officeplus.cn/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-office00\"></use></svg>', NULL, 10, 1, 0),
(22, '搞定设计', 2, 'https://www.gaoding.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-gaoding00\"></use></svg>', NULL, 10, 1, 0),
(23, '素材天下', 2, 'http://www.sucaitianxia.net/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-video\"></use></svg>', NULL, 10, 1, 0),
(24, '千库网', 2, 'https://588ku.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-qianku00\"></use></svg>', NULL, 10, 1, 0),
(25, '图怪兽', 2, 'https://818ps.com', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-tu00\"></use></svg>', NULL, 10, 1, 0),
(26, '站酷', 2, 'https://www.zcool.com.cn/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-zhanku\"></use></svg>', NULL, 10, 1, 0),
(27, '阿里图标', 2, 'https://www.iconfont.cn/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-iconfont\"></use></svg>', NULL, 10, 1, 0),
(28, 'IconFinder', 2, 'https://www.iconfinder.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-eye\"></use></svg>', NULL, 10, 1, 0),
(29, '优设教程', 2, 'https://uiiiuiii.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-jiaocheng\"></use></svg>', NULL, 10, 1, 0),
(30, '知乎', 3, 'https://www.zhihu.com/explore', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-zhihu\"></use></svg>', NULL, 10, 1, 0),
(31, '豆瓣', 3, 'https://www.douban.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-douban\"></use></svg>', NULL, 10, 1, 0),
(32, '简书', 3, 'https://www.jianshu.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-jianshu\"></use></svg>', NULL, 10, 1, 0),
(33, '阿里云盘', 3, 'https://www.aliyundrive.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-alipan00\"></use></svg>', NULL, 10, 1, 0),
(34, '百度网盘', 3, 'https://pan.baidu.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-baidupan00\"></use></svg>', NULL, 10, 1, 0),
(35, '蓝奏云', 3, 'https://www.lanzou.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-lanzou00\"></use></svg>', NULL, 10, 1, 0),
(36, '迅雷云盘', 3, 'https://pan.xunlei.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-xunleipan00\"></use></svg>', NULL, 10, 1, 0),
(37, 'OneDrive', 3, 'https://onedrive.live.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-OneDrive00\"></use></svg>', NULL, 10, 1, 0),
(38, '天翼云盘', 3, 'https://cloud.189.cn/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-tianyipan00\"></use></svg>', NULL, 10, 1, 0),
(39, 'UC网盘', 3, 'https://www.yun.cn/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-yunpan\"></use></svg>', NULL, 10, 1, 0),
(40, 'QQ邮箱', 3, 'https://mail.qq.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-qqmail00\"></use></svg>', NULL, 10, 1, 0),
(41, 'Gmail', 3, 'https://mail.google.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-gmail\"></use></svg>', NULL, 10, 1, 0),
(42, 'Hotmail', 3, 'https://outlook.live.com/mail/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-windows\"></use></svg>', NULL, 10, 1, 0),
(43, '网易邮箱', 3, 'https://mail.163.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-wangyi\"></use></svg>', NULL, 10, 1, 0),
(44, '新浪邮箱', 3, 'https://mail.sina.com.cn/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-xinlangwang\"></use></svg>', NULL, 10, 1, 0),
(45, '阿里邮箱', 3, 'https://mail.aliyun.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-yunyouxiang\"></use></svg>', NULL, 10, 1, 0),
(46, '在线工具', 4, 'https://tool.lu/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-ai-tool\"></use></svg>', NULL, 10, 1, 0),
(47, 'IP查询', 4, 'https://ip.cn/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-wangluo\"></use></svg>', NULL, 10, 1, 0),
(48, '文档在线转换', 4, 'https://xpdf.net/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-docto\"></use></svg>', NULL, 10, 1, 0),
(49, '谷歌翻译', 4, 'https://translate.google.cn/?hl=zh-CN', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-fanyi\"></use></svg>', NULL, 10, 1, 0),
(50, '有道翻译', 4, 'https://fanyi.youdao.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-youdao00\"></use></svg>', NULL, 10, 1, 0),
(51, 'HTML在线运行', 4, 'https://c.runoob.com/front-end/61/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-html00\"></use></svg>', NULL, 10, 1, 0),
(52, 'MD编辑器', 4, 'https://www.lylme.com/html/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-md\"></use></svg>', NULL, 10, 1, 0),
(53, '微PE工具', 4, 'http://www.wepe.com.cn/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-wepe00\"></use></svg>', NULL, 10, 1, 0),
(54, '在线代码格式化', 4, 'https://tool.oschina.net/codeformat/html/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-base64\"></use></svg>', NULL, 10, 1, 0),
(55, 'JS混淆器', 4, 'http://tool.chinaz.com/tools/jscodeconfusion.aspx', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-jshunxiao\"></use></svg>', NULL, 10, 1, 0),
(56, '站长工具', 4, 'http://tool.chinaz.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-zhanzhang00\"></use></svg>', NULL, 10, 1, 0),
(57, '在线Ping', 4, 'https://ping.chinaz.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-wangluo1\"></use></svg>', NULL, 10, 1, 0),
(58, 'ICP备案查询', 4, 'https://icp.chinaz.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-icp00\"></use></svg>', NULL, 10, 1, 0),
(59, '在线PS', 4, 'https://www.photopea.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-ps00\"></use></svg>', NULL, 10, 1, 0),
(60, 'W3school', 5, 'http://www.w3school.com.cn/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-h5\"></use></svg>', NULL, 10, 1, 0),
(61, 'Github', 5, 'https://github.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-github\"></use></svg>', NULL, 10, 1, 0),
(62, '码云Gitee', 5, 'https://gitee.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-gitee00\"></use></svg>', NULL, 10, 1, 0),
(63, 'Linux命令查询', 5, 'https://linux.lylme.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-linux\"></use></svg>', NULL, 10, 1, 0),
(64, 'CSDN', 5, 'https://www.csdn.net/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-csdn\"></use></svg>', NULL, 10, 1, 0),
(65, 'CdnJs', 5, 'https://cdnjs.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-cdnjs\"></use></svg>', NULL, 10, 1, 0),
(66, '字节跳动CDN', 5, 'https://cdn.bytedance.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-zjtd00\"></use></svg>', NULL, 10, 1, 0),
(67, 'Font Awesome', 5, 'https://fontawesome.com/icons?https://fontawesome.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-font-awesome\"></use></svg>', NULL, 10, 1, 0),
(68, 'MSDN我告诉你', 5, 'https://msdn.itellyou.cn/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-msdn00\"></use></svg>', NULL, 10, 1, 0),
(69, '腾讯云', 5, 'https://cloud.tencent.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-qcloud00\"></use></svg>', NULL, 10, 1, 0),
(70, '阿里云', 5, 'https://www.aliyun.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-alicloud00\"></use></svg>', NULL, 10, 1, 0),
(71, '4399小游戏', 6, 'http://www.4399.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-439900\"></use></svg>', NULL, 10, 1, 0),
(72, '7k7k小游戏', 6, 'http://www.7k7k.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-7k7k00\"></use></svg>', NULL, 10, 1, 0),
(73, '英雄联盟', 6, 'https://lol.qq.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-lol00\"></use></svg>', NULL, 10, 1, 0),
(74, '永劫无间', 6, 'https://www.yjwujian.cn/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-yjwj00\"></use></svg>', NULL, 10, 1, 0),
(75, 'STEAM', 6, 'https://store.steampowered.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-steam00\"></use></svg>', NULL, 10, 1, 0),
(76, '王者荣耀', 6, 'https://pvp.qq.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-wzry00\"></use></svg>', NULL, 10, 1, 0),
(77, '3DM GAME', 6, 'https://www.3dmgame.com/', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-games00\"></use></svg>', NULL, 10, 1, 0),
(78, '申请收录', 7, '/apply', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-sq00\"></use></svg>', NULL, 10, 1, 0);

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

INSERT INTO `lylme_sou` VALUES
(1, 'baidu', '百度一下', '百度一下，你就知道', '#0c498c', 'https://www.baidu.com/s?word=', 'https://m.baidu.com/s?word=', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-icon_baidulogo\"></use></svg>', 1, 1),
(2, 'sogou', '搜狗搜索', '上网从搜狗开始', '#696a6d', 'https://www.sogou.com/web?query=', NULL, '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-sougou\"></use></svg>', 1, 2),
(3, 'bing', 'Bing必应', '微软必应搜索', '#696a6d', 'https://cn.bing.com/search?q=', NULL, '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-bing\"></use></svg>', 1, 3),
(4, 'zhihu', '知乎搜索', '有问题，上知乎', '#0084fe', 'https://www.zhihu.com/search?q=', NULL, '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-zhihu\"></use></svg>', 1, 4),
(5, 'bilibili', '哔哩哔哩', '(゜-゜)つロ 干杯~', '#00aeec', 'https://search.bilibili.com/all?keyword=', NULL, '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-bili\"></use></svg>', 1, 5),
(6, 'weibo', '微博搜索', '随时随地发现新鲜事', '#ff5722', 'https://s.weibo.com/weibo/', '', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-weibo\"></use></svg>', 1, 6),
(7, 'google', '谷歌搜索', '值得信任的搜索引擎', '#3B83FA', 'https://www.google.com/search?q=', '', '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-google00\"></use></svg>', 1, 7),
(8, 'fanyi', '在线翻译', '输入翻译内容（自动检测语言）', '#0084fe', 'https://fanyi.baidu.com/#auto/zh/', NULL, '<svg class=\"icon\" aria-hidden=\"true\"><use xlink:href=\"#icon-fanyi\"></use></svg>', 1, 8);

DROP TABLE IF EXISTS `lylme_tags`;
CREATE TABLE `lylme_tags` (
  `tag_id` int(11) NOT NULL COMMENT '导航菜单ID',
  `tag_name` varchar(30) NOT NULL,
  `tag_link` varchar(60) NOT NULL,
  `tag_target` int(1) NOT NULL DEFAULT '1',
  `sort` int(11) NOT NULL DEFAULT '10' COMMENT '权重'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `lylme_tags` VALUES
(1, '关于本站', '/about', 1, 10),
(2, '申请收录', '/apply', 1, 10),
(3, '访问管理', '/pwd', 0, 10);


ALTER TABLE `lylme_apply`
  ADD PRIMARY KEY (`apply_id`);

ALTER TABLE `lylme_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `k` (`k`);

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

ALTER TABLE `lylme_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=23;

ALTER TABLE `lylme_groups`
  MODIFY `group_id` int(2) NOT NULL AUTO_INCREMENT COMMENT '分组ID', AUTO_INCREMENT=8;

ALTER TABLE `lylme_links`
  MODIFY `id` int(4) NOT NULL AUTO_INCREMENT COMMENT '链接ID', AUTO_INCREMENT=81;

ALTER TABLE `lylme_pwd`
  MODIFY `pwd_id` int(2) NOT NULL AUTO_INCREMENT COMMENT '加密组ID';

ALTER TABLE `lylme_sou`
  MODIFY `sou_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '搜索引擎ID', AUTO_INCREMENT=9;

ALTER TABLE `lylme_tags`
  MODIFY `tag_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '导航菜单ID', AUTO_INCREMENT=7;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
