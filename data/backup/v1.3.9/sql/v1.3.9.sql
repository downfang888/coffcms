ALTER TABLE `ey_admin` ADD COLUMN `syn_users_id`  int(10) NULL DEFAULT 0 COMMENT '同步注册到会员表' AFTER `status`;
ALTER TABLE `ey_archives` ADD COLUMN `old_price`  decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT '产品旧价' AFTER `users_price`;
ALTER TABLE `ey_archives` ADD COLUMN `stock_count`  int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '商品库存量' AFTER `old_price`;
ALTER TABLE `ey_archives` ADD COLUMN `stock_show`  tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '商品库存在产品详情页是否显示，1为显示，0为不显示' AFTER `stock_count`;
ALTER TABLE `ey_archives` ADD COLUMN `joinaid`  int(10) NULL DEFAULT 0 COMMENT '关联文档ID' AFTER `del_method`;
ALTER TABLE `ey_images_upload` ADD COLUMN `intro`  varchar(2000) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '图集描述' AFTER `image_url`;
ALTER TABLE `ey_product_img` ADD COLUMN `intro`  varchar(2000) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '图集描述' AFTER `image_url`;
ALTER TABLE `ey_users` MODIFY COLUMN `head_pic`  varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '头像' AFTER `login_count`;
ALTER TABLE `ey_users_level` MODIFY COLUMN `discount`  int(10) NULL DEFAULT 100 COMMENT '折扣率，初始值为100即100%，无折扣' AFTER `amount`;
ALTER TABLE `ey_users_level` ADD COLUMN `ask_is_review`  tinyint(1) NULL DEFAULT 0 COMMENT '在问答中发布问题或回答是否需要审核，1=是，0=否' AFTER `posts_count`;
ALTER TABLE `ey_users_level` ADD COLUMN `ask_is_release`  tinyint(1) NULL DEFAULT 1 COMMENT '允许在问答中发布问题，1=是，0=否' AFTER `posts_count`;
ALTER TABLE `ey_shop_cart` ADD COLUMN `spec_value_id`  varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '规格值ID' AFTER `product_num`;
ALTER TABLE `ey_arctype` ADD COLUMN `is_release`  tinyint(1) NULL DEFAULT 0 COMMENT '栏目是否应用于会员投稿发布，1是，0否' AFTER `status`;
ALTER TABLE `ey_arctype` ADD COLUMN `weapp_code`  varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '插件栏目唯一标识' AFTER `is_release`;
ALTER TABLE `ey_channeltype` ADD COLUMN `is_release`  tinyint(1) NULL DEFAULT 0 COMMENT '模型是否允许应用于会员投稿发布，1是，0否' AFTER `is_repeat_title`;
UPDATE `ey_archives` SET `stock_count` = '99999';
UPDATE `ey_arctype` SET `is_release` = '1' WHERE `current_channel` = '1';
UPDATE `ey_channeltype` SET `is_release` = '1' WHERE `nid` = 'article';
INSERT INTO `ey_channelfield` (`name`, `channel_id`, `title`, `dtype`, `define`, `maxlength`, `dfvalue`, `dfvalue_unit`, `remark`, `is_screening`, `is_release`, `ifeditable`, `ifrequire`, `ifsystem`, `ifmain`, `ifcontrol`, `sort_order`, `status`, `add_time`, `update_time`) VALUES ('weapp_code', '-99', '插件栏目唯一标识', 'text', 'varchar(200)', '200', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1557042574', '1557042574');
INSERT INTO `ey_channelfield` (`name`, `channel_id`, `title`, `dtype`, `define`, `maxlength`, `dfvalue`, `dfvalue_unit`, `remark`, `is_screening`, `is_release`, `ifeditable`, `ifrequire`, `ifsystem`, `ifmain`, `ifcontrol`, `sort_order`, `status`, `add_time`, `update_time`) VALUES ('is_release', '-99', '栏目是否应用于会员投稿发布，1是，0否', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1557042574', '1557042574');
INSERT INTO `ey_channelfield` (`name`, `channel_id`, `title`, `dtype`, `define`, `maxlength`, `dfvalue`, `dfvalue_unit`, `remark`, `is_screening`, `is_release`, `ifeditable`, `ifrequire`, `ifsystem`, `ifmain`, `ifcontrol`, `sort_order`, `status`, `add_time`, `update_time`) VALUES ('old_price', '0', '产品旧价', 'decimal', 'decimal(10,2)', '10', '0.00', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1557042574', '1557042574');
INSERT INTO `ey_channelfield` (`name`, `channel_id`, `title`, `dtype`, `define`, `maxlength`, `dfvalue`, `dfvalue_unit`, `remark`, `is_screening`, `is_release`, `ifeditable`, `ifrequire`, `ifsystem`, `ifmain`, `ifcontrol`, `sort_order`, `status`, `add_time`, `update_time`) VALUES ('stock_count', '0', '商品库存量', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1557042574', '1557042574');
INSERT INTO `ey_channelfield` (`name`, `channel_id`, `title`, `dtype`, `define`, `maxlength`, `dfvalue`, `dfvalue_unit`, `remark`, `is_screening`, `is_release`, `ifeditable`, `ifrequire`, `ifsystem`, `ifmain`, `ifcontrol`, `sort_order`, `status`, `add_time`, `update_time`) VALUES ('stock_show', '0', '商品库存在产品详情页是否显示，1为显示，0为不显示', 'switch', 'tinyint(1)', '1', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1557042574', '1557042574');
INSERT INTO `ey_channelfield` (`name`, `channel_id`, `title`, `dtype`, `define`, `maxlength`, `dfvalue`, `dfvalue_unit`, `remark`, `is_screening`, `is_release`, `ifeditable`, `ifrequire`, `ifsystem`, `ifmain`, `ifcontrol`, `sort_order`, `status`, `add_time`, `update_time`) VALUES ('joinaid', '0', '关联文档ID', 'int', 'int(10)', '10', '0', '', '', '0', '0', '1', '0', '1', '1', '1', '100', '1', '1557042574', '1557042574');

DROP TABLE IF EXISTS `ey_product_spec_data`;
CREATE TABLE `ey_product_spec_data` (
  `spec_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `aid` int(10) DEFAULT '0' COMMENT '产品ID',
  `spec_mark_id` int(10) DEFAULT '0' COMMENT '规格标记ID',
  `spec_name` varchar(100) DEFAULT '' COMMENT '规格名称',
  `spec_value_id` int(10) DEFAULT '0' COMMENT '规格值ID',
  `spec_value` varchar(100) DEFAULT '' COMMENT '规格值',
  `spec_is_select` tinyint(1) DEFAULT '0' COMMENT '是否选中（0=否，1=是）',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`spec_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='产品规格数据表';

DROP TABLE IF EXISTS `ey_product_spec_preset`;
CREATE TABLE `ey_product_spec_preset` (
  `preset_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `preset_mark_id` int(10) DEFAULT '0' COMMENT '预设参数标记ID',
  `preset_name` varchar(100) DEFAULT '' COMMENT '规格名称',
  `preset_value` varchar(100) DEFAULT '' COMMENT '规格值',
  `sort_order` int(10) DEFAULT '100' COMMENT '排序号',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`preset_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='产品规格预设表';

INSERT INTO `ey_product_spec_preset` VALUES ('1', '1', '产品颜色', '红', '100', 'cn', '1565752372', '1565752623');
INSERT INTO `ey_product_spec_preset` VALUES ('2', '1', '产品颜色', '蓝', '100', 'cn', '1565752372', '1565752623');
INSERT INTO `ey_product_spec_preset` VALUES ('3', '1', '产品颜色', '黄', '100', 'cn', '1565752372', '1565752623');

DROP TABLE IF EXISTS `ey_product_spec_value`;
CREATE TABLE `ey_product_spec_value` (
  `value_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `aid` int(10) NOT NULL DEFAULT '0' COMMENT '产品ID',
  `spec_value_id` varchar(100) NOT NULL DEFAULT '' COMMENT '规格值ID',
  `spec_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '规格价格',
  `spec_stock` int(10) NOT NULL DEFAULT '0' COMMENT '规格库存',
  `spec_sales_num` int(10) NOT NULL DEFAULT '0' COMMENT '销售量',
  `lang` varchar(50) DEFAULT 'cn' COMMENT '语言标识',
  `add_time` int(11) DEFAULT '0' COMMENT '新增时间',
  `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`value_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='产品多规格组装表';