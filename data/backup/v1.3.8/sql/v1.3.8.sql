UPDATE `ey_channelfield` SET `title` = '价格' WHERE `name` = 'users_price';
ALTER TABLE `ey_archives` ADD COLUMN `arc_level_id`  int(10) NULL DEFAULT 0 COMMENT '文档会员权限ID' AFTER `admin_id`;
ALTER TABLE `ey_archives` ADD COLUMN `users_id`  int(10) NULL DEFAULT 0 COMMENT '会员ID' AFTER `admin_id`;
ALTER TABLE `ey_channelfield` ADD COLUMN `is_release`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否应用于会员投稿发布' AFTER `is_screening`;
ALTER TABLE `ey_channeltype` ADD COLUMN `is_litpic_users_release`  tinyint(1) NULL DEFAULT 1 COMMENT '缩略图是否应用于会员投稿，1=允许，0=不允许' AFTER `is_repeat_title`;
ALTER TABLE `ey_users_level` ADD COLUMN `posts_count`  int(10) NULL DEFAULT 5 COMMENT '会员投稿次数限制' AFTER `discount`;
INSERT INTO `ey_channelfield` (`name`, `channel_id`, `title`, `dtype`, `define`, `maxlength`, `dfvalue`, `dfvalue_unit`, `remark`, `is_screening`, `ifeditable`, `ifrequire`, `ifsystem`, `ifmain`, `ifcontrol`, `sort_order`, `status`, `add_time`, `update_time`) VALUES ('users_id', '0', '会员ID', 'int', 'int(11)', '10', '0', '', '', '0', '1', '0', '1', '1', '1', '100', '1', '1557042574', '1557042574');
INSERT INTO `ey_channelfield` (`name`, `channel_id`, `title`, `dtype`, `define`, `maxlength`, `dfvalue`, `dfvalue_unit`, `remark`, `is_screening`, `ifeditable`, `ifrequire`, `ifsystem`, `ifmain`, `ifcontrol`, `sort_order`, `status`, `add_time`, `update_time`) VALUES ('arc_level_id', '0', '文档会员权限ID', 'int', 'int(10)', '10', '0', '', '', '0', '1', '0', '1', '1', '1', '100', '1', '1557042574', '1557042574');
INSERT INTO `ey_channelfield` (`name`, `channel_id`, `title`, `dtype`, `define`, `maxlength`, `dfvalue`, `dfvalue_unit`, `remark`, `is_screening`, `ifeditable`, `ifrequire`, `ifsystem`, `ifmain`, `ifcontrol`, `sort_order`, `status`, `add_time`, `update_time`) VALUES ('arc_level_id', '4', '文档会员权限ID', 'int', 'int(10)', '10', '0', '', '', '0', '1', '0', '1', '1', '1', '100', '1', '1557042574', '1557042574');
ALTER TABLE `ey_download_file` ADD COLUMN `extract_code`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '文件提取码' AFTER `file_url`;
ALTER TABLE `ey_download_file` ADD COLUMN `is_remote`  tinyint(1) NULL DEFAULT 0 COMMENT '是否远程' AFTER `md5file`;
ALTER TABLE `ey_users` ADD COLUMN `open_level_time`  int(11) UNSIGNED NULL DEFAULT 0 COMMENT '开通会员级别时间' AFTER `level`;
ALTER TABLE `ey_users` ADD COLUMN `level_maturity_days`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '会员级别到期天数' AFTER `open_level_time`;
ALTER TABLE `ey_users_money` MODIFY COLUMN `money`  decimal(10,2) NULL DEFAULT 0.00 COMMENT '金额' AFTER `users_id`;
ALTER TABLE `ey_users_money` MODIFY COLUMN `cause`  text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '事由，暂时在升级消费中使用到，以serialize序列化后存入，用于后续查询。' AFTER `users_money`;
INSERT INTO `ey_users_menu` VALUES ('4', '会员升级', 'user/Level/level_centre', '0', '100', '0', 'cn', '1555904190', '1555917761');
INSERT INTO `ey_users_menu` VALUES ('5', '会员投稿', 'user/UsersRelease/release_centre', '0', '100', '0', 'cn', '1555904190', '1555917761');
UPDATE `ey_channelfield` SET `title` = '内容详情', `is_release` = '1' WHERE `name` = 'content' AND `channel_id` <= 8;
ALTER TABLE `ey_users_level` MODIFY COLUMN `discount`  int(10) NULL DEFAULT 100 COMMENT '折扣率' AFTER `amount`;
UPDATE `ey_users_level` SET `discount` = '100';
INSERT INTO `ey_users_config` (`name`, `value`, `desc`, `inc_type`, `lang`, `update_time`) VALUES ('users_reg_notallow', 'www,bbs,ftp,mail,user,users,admin,administrator,eyoucms', '不允许注册的会员名', 'users', 'cn', '1547890773');
ALTER TABLE `ey_users` ADD COLUMN `admin_id`  int(10) NULL DEFAULT 0 COMMENT '关联管理员ID' AFTER `is_lock`;
ALTER TABLE `ey_archives` MODIFY COLUMN `arcrank`  int(10) NULL DEFAULT 0 COMMENT '阅读权限：0=开放浏览，-1=待审核稿件' AFTER `click`;

DROP TABLE IF EXISTS `ey_users_type_manage`;
CREATE TABLE `ey_users_type_manage` (
  `type_id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增ID' ,
  `type_name`  varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '类型名称' ,
  `level_id`  int(10) NULL DEFAULT 0 COMMENT '会员等级ID' ,
  `price`  decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '价格' ,
  `limit_id`  int(10) NULL DEFAULT 0 COMMENT '会员期限限制，存储ID，值对应常量表的admin_member_limit_arr数组' ,
  `sort_order`  smallint(5) NOT NULL DEFAULT 0 COMMENT '排序' ,
  `lang`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'cn' COMMENT '语言标识' ,
  `add_time`  int(11) NULL DEFAULT 0 COMMENT '新增时间' ,
  `update_time`  int(11) NULL DEFAULT 0 COMMENT '更新时间' ,
  PRIMARY KEY (`type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='会员产品类型表';

DROP TABLE IF EXISTS `ey_download_attr_field`;
CREATE TABLE `ey_download_attr_field` (
  `field_id`  int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增ID' ,
  `field_name`  varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '字段名称' ,
  `field_title`  varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '字段标题' ,
  `field_use`  tinyint(1) NULL DEFAULT 0 COMMENT '字段是否使用，0未使用，1为使用' ,
  `sort_order`  smallint(5) NULL DEFAULT 0 COMMENT '排序' ,
  `lang`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'cn' COMMENT '语言标识' ,
  `add_time`  int(11) UNSIGNED NULL DEFAULT 0 COMMENT '上传时间' ,
  `update_time`  int(11) NULL DEFAULT 0 COMMENT '更新时间' ,
  PRIMARY KEY (`field_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='上传文件属性表';

INSERT INTO `ey_download_attr_field` VALUES ('1', 'extract_code', '提取码', '1', '1', 'cn', '1561001807', '1561024954');
INSERT INTO `ey_download_attr_field` VALUES ('2', 'server_name', '服务器名称', '1', '2', 'cn', '1561001807', '1561078673');