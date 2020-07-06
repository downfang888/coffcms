UPDATE `ey_config` SET `value` = '0' WHERE `name` = 'web_exception' AND `inc_type` = 'web';
UPDATE `ey_guestbook` SET `channel` = '8' WHERE `aid` > 0;

DROP TABLE IF EXISTS `ey_users_menu`;
CREATE TABLE `ey_users_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `title` varchar(30) DEFAULT '' COMMENT '导航名称',
  `mca` varchar(50) DEFAULT '' COMMENT '分组/控制器/操作名',
  `is_userpage` tinyint(1) DEFAULT '0' COMMENT '默认会员首页',
  `sort_order` int(10) DEFAULT '0' COMMENT '排序号',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态，1=显示，0=隐藏',
  `lang` varchar(20) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='会员菜单表';

INSERT INTO `ey_users_menu` VALUES ('1', '个人信息', 'user/Users/index', '1', '100', '1', 'cn', '1555904190', '1555917737');
INSERT INTO `ey_users_menu` VALUES ('2', '账户充值', 'user/Pay/pay_consumer_details', '0', '100', '1', 'cn', '1555904190', '1555917761');

DROP TABLE IF EXISTS `ey_search_word`;
CREATE TABLE `ey_search_word` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `word` varchar(50) DEFAULT '' COMMENT '关键词',
  `searchNum` int(10) DEFAULT '1' COMMENT '搜索次数',
  `sort_order` int(10) DEFAULT '0' COMMENT '排序号',
  `lang` varchar(30) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`),
  KEY `word` (`word`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='搜索词统计表';