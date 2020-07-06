ALTER TABLE `ey_users_level` MODIFY COLUMN `discount`  float(10,2) NULL DEFAULT 100.00 COMMENT '折扣率，初始值为100即100%，无折扣' AFTER `amount`;
ALTER TABLE `ey_archives` ADD COLUMN `attrlist_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '参数列表ID' AFTER `seo_description`;
ALTER TABLE `ey_archives` ADD COLUMN `sales_num` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '销售量' AFTER `old_price`;

DROP TABLE IF EXISTS `ey_minipro`;
CREATE TABLE `ey_minipro` (
  `mini_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '模板ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '模板标题',
  `app_id` varchar(50) NOT NULL DEFAULT '' COMMENT '小程序AppID',
  `app_secret` varchar(50) NOT NULL DEFAULT '' COMMENT '小程序AppSecret',
  `mchid` varchar(50) NOT NULL DEFAULT '' COMMENT '微信商户号id',
  `apikey` varchar(255) NOT NULL DEFAULT '' COMMENT '微信支付密钥',
  `cert_pem` longtext NOT NULL COMMENT '证书文件cert',
  `key_pem` longtext NOT NULL COMMENT '证书文件key',
  `litpic` varchar(250) NOT NULL DEFAULT '' COMMENT '封面图',
  `is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '设为默认',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '伪删除，1=是，0=否',
  `lang` varchar(10) NOT NULL DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`mini_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='微信小程序记录表';

DROP TABLE IF EXISTS `ey_minipro_category`;
CREATE TABLE `ey_minipro_category` (
  `cat_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类模板ID',
  `mini_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `category_style` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '分类页样式(10一级分类[大图] 11一级分类[小图] 20二级分类)',
  `nav_title` varchar(100) NOT NULL DEFAULT '' COMMENT '导航标题',
  `share_title` varchar(100) NOT NULL DEFAULT '' COMMENT '分享标题',
  `lang` varchar(10) NOT NULL DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`cat_id`),
  KEY `mini_id` (`mini_id`,`lang`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='微信小程序分类页模板';

DROP TABLE IF EXISTS `ey_minipro_help`;
CREATE TABLE `ey_minipro_help` (
  `help_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '帮助标题',
  `content` text NOT NULL COMMENT '帮助内容',
  `sort_order` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排序(数字越小越靠前)',
  `mini_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '小程序id',
  `lang` varchar(10) NOT NULL DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`help_id`),
  KEY `mini_id` (`mini_id`,`lang`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='微信小程序帮助';

DROP TABLE IF EXISTS `ey_minipro_page`;
CREATE TABLE `ey_minipro_page` (
  `page_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '页面id',
  `page_type` tinyint(3) NOT NULL DEFAULT '-1' COMMENT '页面类型(1首页 -1自定义页)',
  `page_name` varchar(255) NOT NULL DEFAULT '' COMMENT '页面名称',
  `page_data` longtext NOT NULL COMMENT '页面数据',
  `mini_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '微信小程序id',
  `is_home` tinyint(1) NOT NULL DEFAULT '0' COMMENT '设为首页：0=否，1=是',
  `is_system` tinyint(1) DEFAULT '0' COMMENT '系统内置',
  `is_del` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '伪删除，1=是，0=否',
  `lang` varchar(10) NOT NULL DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`page_id`),
  KEY `mini_id` (`mini_id`,`lang`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='微信小程序diy页面表';

DROP TABLE IF EXISTS `ey_minipro_setting`;
CREATE TABLE `ey_minipro_setting` (
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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='微信小程序多功能配置表';

DROP TABLE IF EXISTS `ey_minipro_tabbar`;
CREATE TABLE `ey_minipro_tabbar` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `text` varchar(60) NOT NULL DEFAULT '' COMMENT '菜单名称',
  `path_type` smallint(5) NOT NULL DEFAULT '0' COMMENT '页面类型',
  `path_value` varchar(50) NOT NULL DEFAULT '' COMMENT '页面路径值',
  `icon` varchar(200) NOT NULL DEFAULT '' COMMENT '默认图标',
  `selected_icon` varchar(200) NOT NULL DEFAULT '' COMMENT '选中图标',
  `mini_id` int(11) NOT NULL DEFAULT '0' COMMENT '小程序ID',
  `sort_order` int(10) NOT NULL DEFAULT '0' COMMENT '排序号',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态',
  `lang` varchar(10) NOT NULL DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `mini_id` (`mini_id`,`lang`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='微信小程序底部菜单表';

DROP TABLE IF EXISTS `ey_shop_product_attr`;
CREATE TABLE `ey_shop_product_attr` (
  `product_attr_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '产品属性id自增',
  `aid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '产品id',
  `attr_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '属性id',
  `attr_value` text NOT NULL COMMENT '属性值',
  `attr_price` varchar(255) DEFAULT '' COMMENT '属性价格',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`product_attr_id`),
  KEY `aid` (`aid`) USING BTREE,
  KEY `attr_id` (`attr_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ey_shop_product_attribute`;
CREATE TABLE `ey_shop_product_attribute` (
  `attr_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '属性id',
  `attr_name` varchar(60) NOT NULL DEFAULT '' COMMENT '属性名称',
  `list_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '栏目id',
  `attr_index` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0不需要检索 1关键字检索 2范围检索',
  `attr_input_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT ' 0=文本框，1=下拉框，2=多行文本框',
  `attr_values` text NOT NULL COMMENT '可选值列表',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态(0=禁用，1=启用)',
  `sort_order` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '属性排序',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否已删除，0=否，1=是',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`attr_id`),
  KEY `cat_id` (`list_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ey_shop_product_attrlist`;
CREATE TABLE `ey_shop_product_attrlist` (
  `list_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '列表id',
  `list_name` varchar(60) NOT NULL DEFAULT '' COMMENT '列表名称',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态(0=禁用，1=启用)',
  `attr_count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '参数数量',
  `desc` text NOT NULL COMMENT '描述备注',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否已删除，0=否，1=是',
  `sort_order` int(11) unsigned NOT NULL DEFAULT '100' COMMENT '列表排序',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`list_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

INSERT INTO `ey_quickentry` (`title`, `laytext`, `type`, `controller`, `action`, `vars`, `groups`, `checked`, `status`, `sort_order`, `add_time`, `update_time`) VALUES ('可视化小程序', '可视化小程序', '1', 'Minipro', 'index', '', '0', '0', '0', '100', '1569232484', '1571893529');
INSERT INTO `ey_channelfield` (`name`, `channel_id`, `title`, `dtype`, `define`, `maxlength`, `dfvalue`, `dfvalue_unit`, `remark`, `is_screening`, `is_release`, `ifeditable`, `ifrequire`, `ifsystem`, `ifmain`, `ifcontrol`, `sort_order`, `status`, `add_time`, `update_time`) VALUES ('attrlist_id', '0', '参数列表ID', 'int', 'int(11)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533091930', '1533091930');
INSERT INTO `ey_channelfield` (`name`, `channel_id`, `title`, `dtype`, `define`, `maxlength`, `dfvalue`, `dfvalue_unit`, `remark`, `is_screening`, `is_release`, `ifeditable`, `ifrequire`, `ifsystem`, `ifmain`, `ifcontrol`, `sort_order`, `status`, `add_time`, `update_time`) VALUES ('sales_num', '0', '销售量', 'int', 'int(10)', '250', '', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1533091930', '1533091930');
UPDATE `ey_quickentry` SET `action`='seo', `vars`='' WHERE `title`='URL配置';
UPDATE `ey_quickentry` SET `controller`='Sitemap', `vars`='' WHERE `title`='SiteMap';