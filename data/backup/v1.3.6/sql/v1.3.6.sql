ALTER TABLE `ey_archives` ADD COLUMN `is_litpic`  tinyint(1) NULL DEFAULT 0 COMMENT '图片（0=否，1=是）' AFTER `is_jump`;
UPDATE `ey_ad_position` SET `height` = 0 WHERE `height` is NULL OR `height` = '';
UPDATE `ey_ad_position` SET `width` = 0 WHERE `width` is NULL OR `width` = '';
UPDATE `ey_archives` SET `is_litpic` = 1;
UPDATE `ey_archives` SET `is_litpic` = 0 WHERE `litpic` is NULL OR `litpic` = '';
ALTER TABLE `ey_shop_order` ADD COLUMN `wechat_pay_type`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '微信支付时，标记使用的支付类型（扫码支付，微信内部，微信H5页面）' AFTER `pay_name`;
ALTER TABLE `ey_shop_order` DROP COLUMN `express_data`;
ALTER TABLE `ey_shop_order` DROP COLUMN `zipcode`;
ALTER TABLE `ey_users` ADD COLUMN `nickname`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '昵称' AFTER `password`;
ALTER TABLE `ey_users` ADD COLUMN `open_id`  varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '微信唯一标识openid' AFTER `register_place`;
ALTER TABLE `ey_users_money` ADD COLUMN `wechat_pay_type`  varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '微信支付时，标记使用的支付类型（扫码支付，微信内部，微信H5页面）' AFTER `pay_method`;
UPDATE `ey_users` SET `nickname` = `username`;

DROP TABLE IF EXISTS `ey_common_pic`;
CREATE TABLE `ey_common_pic` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '常用图片ID',
  `pic_path` varchar(255) NOT NULL DEFAULT '' COMMENT '图片地址',
  `lang` varchar(50) NOT NULL DEFAULT 'cn' COMMENT '多语言',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='常用图片';

ALTER TABLE `ey_ad_position` MODIFY COLUMN `title`  varchar(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '广告位置名称' AFTER `id`;
ALTER TABLE `ey_ad_position` MODIFY COLUMN `width`  smallint(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT '广告位宽度' AFTER `title`;
ALTER TABLE `ey_ad_position` MODIFY COLUMN `height`  smallint(5) UNSIGNED NOT NULL DEFAULT 0 COMMENT '广告位高度' AFTER `width`;
ALTER TABLE `ey_ad_position` MODIFY COLUMN `intro`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '广告描述' AFTER `height`;
ALTER TABLE `ey_ad_position` MODIFY COLUMN `status`  tinyint(1) NOT NULL DEFAULT 1 COMMENT '0关闭1开启' AFTER `intro`;
ALTER TABLE `ey_ad_position` MODIFY COLUMN `lang`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'cn' COMMENT '多语言' AFTER `status`;
ALTER TABLE `ey_ad_position` MODIFY COLUMN `admin_id`  int(10) NOT NULL DEFAULT 0 COMMENT '管理员ID' AFTER `lang`;
ALTER TABLE `ey_ad_position` MODIFY COLUMN `is_del`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '伪删除，1=是，0=否' AFTER `admin_id`;
ALTER TABLE `ey_ad_position` MODIFY COLUMN `add_time`  int(11) NOT NULL DEFAULT 0 COMMENT '新增时间' AFTER `is_del`;
ALTER TABLE `ey_ad_position` MODIFY COLUMN `update_time`  int(11) NOT NULL DEFAULT 0 COMMENT '更新时间' AFTER `add_time`;