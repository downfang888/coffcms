ALTER TABLE `ey_download_file` ADD COLUMN `update_time`  int(11) NULL DEFAULT 0 COMMENT '更新时间' AFTER `add_time`;
ALTER TABLE `ey_download_file` ADD COLUMN `downcount`  int(10) NULL DEFAULT 0 COMMENT '下载次数' AFTER `is_remote`;
ALTER TABLE `ey_images_upload` ADD COLUMN `update_time`  int(11) NULL DEFAULT 0 COMMENT '更新时间' AFTER `add_time`;
ALTER TABLE `ey_product_img` ADD COLUMN `update_time`  int(11) NULL DEFAULT 0 COMMENT '更新时间' AFTER `add_time`;
ALTER TABLE `ey_archives` ADD COLUMN `downcount`  int(10) NULL DEFAULT 0 COMMENT '下载次数' AFTER `joinaid`;

DROP TABLE IF EXISTS `ey_setting`;
CREATE TABLE `ey_setting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT '' COMMENT '配置的key键名',
  `value` text,
  `inc_type` varchar(64) DEFAULT '' COMMENT '配置分组',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `inc_type` (`inc_type`,`lang`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='系统非全局配置表';

DROP TABLE IF EXISTS `ey_download_log`;
CREATE TABLE `ey_download_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `users_id` int(10) DEFAULT '0' COMMENT '用户ID',
  `aid` int(10) DEFAULT '0' COMMENT '文档ID',
  `file_id` int(10) DEFAULT '0' COMMENT '附件ID',
  `ip` varchar(20) DEFAULT '' COMMENT 'ip',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '编辑时间',
  PRIMARY KEY (`log_id`),
  KEY `file_id` (`file_id`,`aid`,`users_id`) USING BTREE,
  KEY `aid` (`aid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='下载记录表';

DROP TABLE IF EXISTS `ey_quickentry`;
CREATE TABLE `ey_quickentry` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(20) DEFAULT '' COMMENT '名称',
  `laytext` varchar(50) DEFAULT '' COMMENT '完整标题',
  `type` smallint(5) DEFAULT '0' COMMENT '归类，1=快捷入口，2=内容统计',
  `controller` varchar(20) DEFAULT '' COMMENT '控制器名',
  `action` varchar(20) DEFAULT '' COMMENT '操作名',
  `vars` varchar(100) DEFAULT '' COMMENT 'URL参数字符串',
  `groups` smallint(5) DEFAULT '0' COMMENT '分组，1=模型',
  `checked` tinyint(4) DEFAULT '0' COMMENT '选中，0=否，1=是',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态，1=有效，0=无效',
  `sort_order` int(10) DEFAULT '0' COMMENT '排序',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `type` (`type`,`status`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COMMENT='快捷入口表';

INSERT INTO `ey_quickentry` VALUES ('1', '产品', '产品列表', '1', 'Product', 'index', 'channel=2', '1', '0', '1', '3', '1569232484', '1571899582');
INSERT INTO `ey_quickentry` VALUES ('2', '下载', '下载列表', '1', 'Download', 'index', 'channel=4', '1', '0', '1', '4', '1569232484', '1571899582');
INSERT INTO `ey_quickentry` VALUES ('3', '文章', '文章列表', '1', 'Article', 'index', 'channel=1', '1', '0', '1', '6', '1569232484', '1571899582');
INSERT INTO `ey_quickentry` VALUES ('4', '图集', '图集列表', '1', 'Images', 'index', 'channel=3', '1', '0', '1', '7', '1569232484', '1571899582');
INSERT INTO `ey_quickentry` VALUES ('5', '内容管理', '内容列表', '1', 'Archives', 'index', '', '0', '0', '1', '13', '1569232484', '1571893529');
INSERT INTO `ey_quickentry` VALUES ('7', '回收站', '回收站', '1', 'RecycleBin', 'archives_index', '', '0', '1', '1', '4', '1569232484', '1571893529');
INSERT INTO `ey_quickentry` VALUES ('8', '栏目管理', '栏目管理', '1', 'Arctype', 'index', '', '0', '0', '1', '5', '1569232484', '1571893529');
INSERT INTO `ey_quickentry` VALUES ('9', '留言', '留言列表', '1', 'Guestbook', 'index', 'channel=8', '1', '0', '1', '6', '1569232484', '1571899582');
INSERT INTO `ey_quickentry` VALUES ('10', '网站信息', '网站信息', '1', 'System', 'web', '', '0', '0', '1', '7', '1569232484', '1571893529');
INSERT INTO `ey_quickentry` VALUES ('11', '水印配置', '水印配置', '1', 'System', 'water', '', '0', '1', '1', '8', '1569232484', '1571893529');
INSERT INTO `ey_quickentry` VALUES ('12', '缩略图配置', '缩略图配置', '1', 'System', 'thumb', '', '0', '1', '1', '9', '1569232484', '1571893529');
INSERT INTO `ey_quickentry` VALUES ('13', '数据备份', '数据备份', '1', 'Tools', 'index', '', '0', '0', '1', '11', '1569232484', '1571893529');
INSERT INTO `ey_quickentry` VALUES ('14', 'URL配置', 'URL配置', '1', 'Seo', 'index', 'inc_type=seo', '0', '1', '1', '1', '1569232484', '1571893529');
INSERT INTO `ey_quickentry` VALUES ('15', '模板管理', '模板管理', '1', 'Filemanager', 'index', '', '0', '1', '1', '6', '1569232484', '1571893529');
INSERT INTO `ey_quickentry` VALUES ('16', 'SiteMap', 'SiteMap', '1', 'Seo', 'index', 'inc_type=sitemap', '0', '1', '1', '12', '1569232484', '1571893529');
INSERT INTO `ey_quickentry` VALUES ('17', '频道模型', '频道模型', '1', 'Channeltype', 'index', '', '0', '1', '1', '2', '1569232484', '1571893529');
INSERT INTO `ey_quickentry` VALUES ('18', '广告管理', '广告管理', '1', 'AdPosition', 'index', '', '0', '0', '1', '3', '1569232484', '1571893529');
INSERT INTO `ey_quickentry` VALUES ('19', '友情链接', '友情链接', '1', 'Links', 'index', '', '0', '0', '1', '10', '1569232484', '1571893529');
INSERT INTO `ey_quickentry` VALUES ('20', 'Tags管理', 'Tags管理', '1', 'Tags', 'index', '', '0', '1', '1', '14', '1569232484', '1571893529');
INSERT INTO `ey_quickentry` VALUES ('21', '管理员管理', '管理员管理', '1', 'Admin', 'index', '', '0', '0', '1', '15', '1569232484', '1571893529');
INSERT INTO `ey_quickentry` VALUES ('22', '邮件配置', '邮件配置', '1', 'System', 'smtp', '', '0', '1', '1', '16', '1569232484', '1571893529');
INSERT INTO `ey_quickentry` VALUES ('23', '文章', '文章列表', '2', 'Article', 'index', 'channel=1', '1', '1', '1', '1', '1569310798', '1571899582');
INSERT INTO `ey_quickentry` VALUES ('24', '产品', '产品列表', '2', 'Product', 'index', 'channel=2', '1', '0', '1', '2', '1569310798', '1571899582');
INSERT INTO `ey_quickentry` VALUES ('25', '下载', '下载列表', '2', 'Download', 'index', 'channel=4', '1', '0', '1', '4', '1569310798', '1571899582');
INSERT INTO `ey_quickentry` VALUES ('26', '图集', '图集列表', '2', 'Images', 'index', 'channel=3', '1', '0', '1', '3', '1569310798', '1571899582');
INSERT INTO `ey_quickentry` VALUES ('27', '留言', '留言列表', '2', 'Guestbook', 'index', 'channel=8', '1', '0', '1', '5', '1569310798', '1571899582');
INSERT INTO `ey_quickentry` VALUES ('28', '广告', '广告管理', '2', 'AdPosition', 'index', '', '0', '1', '1', '8', '1569232484', '1571898872');
INSERT INTO `ey_quickentry` VALUES ('29', '友情链接', '友情链接', '2', 'Links', 'index', '', '0', '1', '1', '9', '1569232484', '1571898872');
INSERT INTO `ey_quickentry` VALUES ('30', 'Tags标签', 'Tags管理', '2', 'Tags', 'index', '', '0', '1', '1', '10', '1569232484', '1571898872');
INSERT INTO `ey_quickentry` VALUES ('31', '会员', '会员管理', '2', 'Member', 'users_index', '', '0', '0', '1', '7', '1569232484', '1571899582');
INSERT INTO `ey_quickentry` VALUES ('32', '插件应用', '插件应用', '1', 'Weapp', 'index', '', '0', '0', '1', '17', '1569232484', '1571899582');
INSERT INTO `ey_quickentry` VALUES ('33', '会员中心', '会员中心', '1', 'Member', 'users_index', '', '0', '0', '1', '18', '1569232484', '1571899582');
INSERT INTO `ey_quickentry` VALUES ('34', '商城中心', '商城中心', '1', 'Shop', 'index', '', '0', '0', '1', '19', '1569232484', '1571899582');
INSERT INTO `ey_quickentry` VALUES ('35', '订单', '订单管理', '2', 'Shop', 'index', '', '0', '0', '1', '6', '1569232484', '1571899582');

INSERT INTO `ey_channelfield` (`name`, `channel_id`, `title`, `dtype`, `define`, `maxlength`, `dfvalue`, `dfvalue_unit`, `remark`, `is_screening`, `is_release`, `ifeditable`, `ifrequire`, `ifsystem`, `ifmain`, `ifcontrol`, `sort_order`, `status`, `add_time`, `update_time`) VALUES ('downcount', '0', '下载次数', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1557042574', '1557042574');
INSERT INTO `ey_channelfield` (`name`, `channel_id`, `title`, `dtype`, `define`, `maxlength`, `dfvalue`, `dfvalue_unit`, `remark`, `is_screening`, `is_release`, `ifeditable`, `ifrequire`, `ifsystem`, `ifmain`, `ifcontrol`, `sort_order`, `status`, `add_time`, `update_time`) VALUES ('downcount', '4', '下载次数', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1557042574', '1557042574');