DROP TABLE IF EXISTS `ey_diyminipro`;
CREATE TABLE `ey_diyminipro` (
  `mini_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '模板ID',
  `categoryid` int(11) NOT NULL DEFAULT '0' COMMENT '分类ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '模板标题',
  `litpic` varchar(250) NOT NULL DEFAULT '' COMMENT '封面图',
  `component` text NOT NULL COMMENT '组件库',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态：4=审核中，5=发布',
  `cloud_id` int(10) NOT NULL DEFAULT '0' COMMENT '云ID',
  `config` text NOT NULL COMMENT '相关序列化信息',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '伪删除，1=是，0=否',
  `lang` varchar(10) NOT NULL DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`mini_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信小程序记录表';

DROP TABLE IF EXISTS `ey_diyminipro_page`;
CREATE TABLE `ey_diyminipro_page` (
  `page_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '页面id',
  `page_type` tinyint(3) NOT NULL DEFAULT '-1' COMMENT '页面类型(1首页 -1自定义页)',
  `page_name` varchar(255) NOT NULL DEFAULT '' COMMENT '页面名称',
  `page_data` longtext NOT NULL COMMENT '页面数据',
  `mini_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '微信小程序id',
  `is_home` tinyint(1) NOT NULL DEFAULT '0' COMMENT '设为首页：0=否，1=是',
  `is_system` tinyint(1) NOT NULL DEFAULT '0' COMMENT '系统内置',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示：1=显示，0=隐藏',
  `is_del` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '伪删除，1=是，0=否',
  `lang` varchar(10) NOT NULL DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`page_id`),
  KEY `mini_id` (`mini_id`,`lang`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信小程序diy页面表';

DROP TABLE IF EXISTS `ey_diyminipro_setting`;
CREATE TABLE `ey_diyminipro_setting` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(60) NOT NULL DEFAULT '' COMMENT '页面组',
  `value` text NOT NULL COMMENT '组装之后的值',
  `mini_id` int(11) NOT NULL DEFAULT '0' COMMENT '小程序ID',
  `lang` varchar(10) NOT NULL DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `type` (`name`) USING BTREE,
  KEY `mini_id` (`mini_id`,`lang`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='微信小程序多功能配置表';

ALTER TABLE `ey_weapp` ADD COLUMN `is_buy`  tinyint(1) NULL DEFAULT 0 COMMENT '0-本地,1-线上购买 2-线上购买,但已删除,不显示在我的插件列表' AFTER `position`;
DELETE FROM `ey_quickentry` WHERE `title` = '可视化小程序';
INSERT INTO `ey_field_type` (`name`, `title`, `ifoption`, `sort_order`, `add_time`, `update_time`) VALUES ('file', '附件类型', '0', '11', '1532485708', '1532485708');
INSERT INTO `ey_field_type` (`name`, `title`, `ifoption`, `sort_order`, `add_time`, `update_time`) VALUES ('media', '多媒体类型', '0', '11', '1532485708', '1532485708');

DROP TABLE IF EXISTS `ey_sms_template`;
CREATE TABLE `ey_sms_template` (
  `tpl_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `tpl_title` varchar(128) NOT NULL DEFAULT '' COMMENT '短信标题',
  `sms_sign` varchar(50) NOT NULL DEFAULT '' COMMENT '短信签名',
  `sms_tpl_code` varchar(100) NOT NULL DEFAULT '' COMMENT '短信模板ID',
  `tpl_content` varchar(1000) NOT NULL DEFAULT '' COMMENT '发送短信内容',
  `send_scene` varchar(100) NOT NULL DEFAULT '' COMMENT '短信发送场景',
  `is_open` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否开启使用这个模板，1为是，0为否。',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) NOT NULL COMMENT '添加时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`tpl_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='手机短信发送模板';

DROP TABLE IF EXISTS `ey_sms_log`;
CREATE TABLE `ey_sms_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '表id',
  `source` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '发送来源，与场景ID对应：0=注册，1=绑定，2=登录密码，3=支付密码，4=找回密码',
  `mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '手机号',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '发送时间',
  `code` varchar(10) NOT NULL DEFAULT '' COMMENT '验证码',
  `status` int(1) unsigned NOT NULL DEFAULT '0' COMMENT '发送状态,1:成功,0:失败',
  `is_use` int(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否使用，1:是，0:否',
  `msg` varchar(255) NOT NULL DEFAULT '' COMMENT '短信内容',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `error_msg` text NOT NULL COMMENT '发送短信异常内容',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='手机短信发送记录';

DROP TABLE IF EXISTS `ey_minipro`;
DROP TABLE IF EXISTS `ey_minipro_category`;
DROP TABLE IF EXISTS `ey_minipro_help`;
DROP TABLE IF EXISTS `ey_minipro_page`;
DROP TABLE IF EXISTS `ey_minipro_setting`;
DROP TABLE IF EXISTS `ey_minipro_tabbar`;