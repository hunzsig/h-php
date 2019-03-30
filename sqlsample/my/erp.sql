
DROP TABLE IF EXISTS `erp_shop`;
CREATE TABLE `erp_shop` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `owner_uid` bigint unsigned NOT NULL COMMENT '拥有者uid',
  `code` char(255) NOT NULL COMMENT '唯一码',
  `name` char(255) NOT NULL COMMENT '店铺名称',
  `ip` char(255) COMMENT 'ip',
  `region` char(255) COMMENT '地区',
  `region_label` char(255) COMMENT '地区文本',
  `address` char(255) COMMENT '地址',
  `pic` json COMMENT '店铺图片',
  `status` char(255) default 'planning' NOT NULL COMMENT '店铺状态',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY (`code`)
) ENGINE=INNODB COMMENT 'ERP-店铺表';


DROP TABLE IF EXISTS `erp_ticket_mapping`;
CREATE TABLE `erp_ticket_mapping` (
  `ticket_code` char(255) NOT NULL COMMENT '码',
  `uid` bigint unsigned NOT NULL COMMENT '录入UID',
  `batch` char(255) NOT NULL COMMENT '录入批次',
  `shop_id` bigint unsigned NOT NULL COMMENT '店铺ID',
  `goods_id` bigint unsigned NOT NULL COMMENT '商品ID',
  `price_cover` numeric(20,6) COMMENT '销售价(覆盖商城价)',
  `price_cover_reason` char(255) COMMENT '销售价(覆盖理由)(不一定要写理由，例如写个特供版)',
  `is_pay` smallint NOT NULL DEFAULT -1 COMMENT '是否已支付',
  `customer_name` char(255) COMMENT '买家名称',
  `enable_date` date NOT NULL COMMENT '有效期至',
  `pay_time` datetime COMMENT '支付时间',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`ticket_code`)
) ENGINE=INNODB COMMENT 'ERP-映射表';


DROP TABLE IF EXISTS `erp_shop_log`;
CREATE TABLE `erp_shop_log` (
  `uid` bigint unsigned NOT NULL COMMENT 'uid',
  `shop_id` bigint unsigned NOT NULL COMMENT '哪个店',
  `behaviour` char(255) NOT NULL COMMENT '行为 如 openDoor',
  `create_time` datetime NOT NULL COMMENT '创建时间'
) ENGINE=INNODB COMMENT 'ERP-店铺日志';


DROP TABLE IF EXISTS `erp_shop_unpay`;
CREATE TABLE `erp_shop_unpay` (
  `ticket_code` char(255) NOT NULL COMMENT 'ticket_code码',
  `shop_id` bigint unsigned NOT NULL COMMENT '店铺ID',
  `record_time` datetime NOT NULL COMMENT '记录时间',
  `record_timestamp` bigint NOT NULL COMMENT '记录时间戳',
  PRIMARY KEY (`ticket_code`)
) ENGINE=INNODB COMMENT 'ERP-店铺未支付表';


DROP TABLE IF EXISTS `erp_active_ticket`;
CREATE TABLE `erp_active_ticket` (
  `ticket_code` char(255) NOT NULL COMMENT 'ticket_code码',
  `shop_id` bigint unsigned NOT NULL COMMENT '店铺ID',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`ticket_code`,`shop_id`)
) ENGINE=INNODB COMMENT 'ERP-活动中的ticket表';


ALTER TABLE `erp_shop` ADD FOREIGN KEY (`owner_uid`) REFERENCES `user` (`uid`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `erp_ticket_mapping` ADD FOREIGN KEY (`shop_id`) REFERENCES `erp_shop` (`id`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `erp_ticket_mapping` ADD FOREIGN KEY (`goods_id`) REFERENCES `goods` (`id`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `erp_shop_unpay` ADD FOREIGN KEY (`ticket_code`) REFERENCES `erp_ticket_mapping` (`ticket_code`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `erp_shop_log` ADD FOREIGN KEY (`uid`) REFERENCES `user` (`uid`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `erp_shop_log` ADD FOREIGN KEY (`shop_id`) REFERENCES `erp_shop` (`id`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;