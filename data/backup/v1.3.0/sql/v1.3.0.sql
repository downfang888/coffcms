UPDATE `ey_arcrank` SET `name` = '开放浏览', `update_time` = 1552376880 WHERE `rank` = '0';
UPDATE `ey_arcrank` SET `name` = '待审核稿件', `update_time` = 1552376880 WHERE `rank` = '-1';
ALTER TABLE `ey_admin_log` MODIFY COLUMN `log_info`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '日志描述' AFTER `admin_id`;
ALTER TABLE `ey_admin_log` MODIFY COLUMN `log_ip`  varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'ip地址' AFTER `log_info`;
ALTER TABLE `ey_admin_log` MODIFY COLUMN `log_url`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'url' AFTER `log_ip`;
ALTER TABLE `ey_admin_log` MODIFY COLUMN `log_time`  int(11) NULL DEFAULT 0 COMMENT '日志时间' AFTER `log_url`;
UPDATE `ey_admin_log` SET `admin_id` = -1 WHERE `admin_id` is NULL OR `admin_id` = '' OR `admin_id` = 0;
ALTER TABLE `ey_guestbook` ADD COLUMN `md5data`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '数据序列化之后的MD5加密，提交内容的唯一性' AFTER `channel`;
ALTER TABLE `ey_channeltype` ADD COLUMN `is_repeat_title`  tinyint(1) NULL DEFAULT 1 COMMENT '文档标题重复，1=允许，0=不允许' AFTER `ifsystem`;
ALTER TABLE `ey_weapp` ADD COLUMN `position`  varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'default' COMMENT '插件位置' AFTER `thorough`;
ALTER TABLE `ey_admin_log` MODIFY COLUMN `admin_id`  int(10) NOT NULL DEFAULT '-1' COMMENT '管理员id' AFTER `log_id`;

DROP TABLE IF EXISTS `ey_smtp_record`;
CREATE TABLE `ey_smtp_record` (
  `record_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `source` tinyint(1) DEFAULT '0' COMMENT '来源，与场景ID对应：0=默认，2=注册，3=绑定邮箱，4=找回密码',
  `email` varchar(50) DEFAULT '' COMMENT '邮件地址',
  `users_id` int(10) DEFAULT '0' COMMENT '用户ID',
  `code` varchar(20) DEFAULT '' COMMENT '发送邮件内容',
  `status` tinyint(1) DEFAULT '0' COMMENT '是否使用，默认0，0为未使用，1为使用',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`record_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='邮件发送记录表';

DROP TABLE IF EXISTS `ey_smtp_tpl`;
CREATE TABLE `ey_smtp_tpl` (
  `tpl_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `tpl_name` varchar(200) DEFAULT '' COMMENT '模板名称',
  `tpl_title` varchar(200) DEFAULT '' COMMENT '邮件标题',
  `tpl_content` text COMMENT '发送邮件内容',
  `send_scene` tinyint(1) DEFAULT '0' COMMENT '邮件发送场景(1=留言表单）',
  `is_open` tinyint(1) DEFAULT '0' COMMENT '是否开启使用这个模板，1为是，0为否。',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`tpl_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='邮件模板表';

INSERT INTO `ey_smtp_tpl` VALUES ('1', '留言表单', '您有新的留言消息，请查收！', '${content}', '1', '1', 'cn', '1544763495', '1552638302');
INSERT INTO `ey_smtp_tpl` VALUES ('2', '会员注册', '验证码已发送至您的邮箱，请登录邮箱查看验证码！', '${content}', '2', '1', 'cn', '1544763495', '1552667056');
INSERT INTO `ey_smtp_tpl` VALUES ('3', '绑定邮箱', '验证码已发送至您的邮箱，请登录邮箱查看验证码！', '${content}', '3', '1', 'cn', '1544763495', '1552667400');
INSERT INTO `ey_smtp_tpl` VALUES ('4', '找回密码', '验证码已发送至您的邮箱，请登录邮箱查看验证码！', '${content}', '4', '1', 'cn', '1544763495', '1552663577');
