

DROP TABLE IF EXISTS `data_express`;
CREATE TABLE `data_express` (
  `code` char(255) NOT NULL COMMENT '代码',
  `name` varchar(1024)NOT NULL COMMENT '名称',
  `ordering` int DEFAULT 0 NOT NULL COMMENT '排序',
  PRIMARY KEY (`code`)
) ENGINE=INNODB COMMENT '快递';


DROP TABLE IF EXISTS `order_freight_rule`;
CREATE TABLE `order_freight_rule` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  status smallint NOT NULL DEFAULT -1 COMMENT '状态',
  pri smallint NOT NULL DEFAULT 0 COMMENT '优先级',
  seller_uid bigint unsigned NOT NULL COMMENT '指定的卖家uid',
  region char(255) NOT NULL DEFAULT '' COMMENT '指定的地区',
  is_free_shipping smallint NOT NULL DEFAULT -1 COMMENT '是否包邮',
  rule_type char(255) COMMENT '邮费规则类型',
  first_kilo numeric(20,3) COMMENT '首重重量',
  fee_first_kilo numeric(20,3) COMMENT '首重费用，优先级高的覆盖低的',
  fee_per_kilo numeric(20,3) COMMENT '每kg多少费用，如果有首重，从首重后1kg开始算，优先级高的覆盖低的',
  fee_first_qty numeric(20,3) COMMENT '首件费用',
  fee_per_qty numeric(20,3) COMMENT '续件费用',
  volume_var bigint COMMENT '邮费计算 - 体积参数（长*宽*高）/var = 重量',
  free_shipping_amount numeric(20,3) COMMENT '包邮金额(超过的话)',
  free_shipping_qty bigint COMMENT '包邮购买数量(超过的话)',
  free_shipping_kilo numeric(20,3) COMMENT '包邮重量(不超过的话)',
  create_time datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=INNODB COMMENT '订单运费规则';


DROP TABLE IF EXISTS `order`;
CREATE TABLE `order` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `uid` bigint unsigned NOT NULL COMMENT '下单人',
  `no` char(255) NOT NULL COMMENT '订单号',
  `type` char(255) NOT NULL COMMENT '订单类型',
  `name` char(255) COMMENT '订单交易名',
  `description` char(255) COMMENT '订单简述',
  `status` smallint NOT NULL DEFAULT -1 COMMENT '订单状态',
  `trade_place` char(255) COMMENT '交易地点',
  `trade_terminal` char(255) COMMENT '交易终端型号',
  `total_amount` numeric(20,2) unsigned NOT NULL COMMENT '总金额',
  `total_freight` numeric(20,2) unsigned NOT NULL COMMENT '总运费',
  `total_favorable` numeric(20,2) unsigned NOT NULL COMMENT '总优惠',
  `total_weight` numeric(20,2) unsigned NOT NULL COMMENT '总重量',
  `total_qty` bigint unsigned NOT NULL COMMENT '总件数',
  `pay_amount` numeric(20,2) unsigned NOT NULL COMMENT '需要支付的金额',
  `pay_status` smallint NOT NULL DEFAULT -1 COMMENT '支付无',
  `pay_type` char(255) COMMENT '支付类型',
  `pic` varchar(1024) COMMENT '订单图片',
  `shop_id` bigint unsigned COMMENT '店铺id',
  `buyer_remarks` char(255) COMMENT '买家备注',
  `seller_remarks` char(255) COMMENT '卖家备注',
  `pay_return_data` json COMMENT '回调信息（json）',
  `shipping_region` char(255) COMMENT '地区',
  `shipping_address` char(255) COMMENT '具体地址',
  `contact_user` char(255) COMMENT '联系人',
  `contact_mobile` char(255) COMMENT '联系电话',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `cancel_time` datetime COMMENT '取消时间',
  `auto_cancel_time` datetime COMMENT '自动取消时间',
  `pay_time` datetime COMMENT '支付时间',
  `sent_time` datetime COMMENT '发货时间',
  `sent_operator_uid` bigint unsigned COMMENT '发货操作人uid',
  `sent_express_code` char(255) COMMENT '发货快递代码',
  `sent_express_no` char(255) COMMENT '发货快递号',
  `sent_remarks` char(255) COMMENT '发货备注',
  `received_time` datetime COMMENT '收货时间',
  `evaluate_time` datetime COMMENT '评价时间',
  PRIMARY KEY (`id`)
) ENGINE=INNODB COMMENT '订单';

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `order_id` bigint unsigned NOT NULL COMMENT '订单id',
  `seller_uid` bigint unsigned NOT NULL COMMENT '卖家uid',
  `seller_name` char(255) NOT NULL COMMENT '卖家名称',
  `item_amount` numeric(20,2) unsigned NOT NULL COMMENT '物件金额',
  `item_amount_origin` numeric(20,2) unsigned COMMENT '物件金额（源）',
  `item_amount_before_favour` numeric(20,2) unsigned COMMENT '物件金额（优惠之前）',
  `item_weight` numeric(20,2) unsigned NOT NULL COMMENT '物件重量',
  `item_qty` bigint unsigned NOT NULL COMMENT '物件数量，大于等于1',
  `item_name` char(255) NOT NULL COMMENT '物件名称，如商品则是商品名称',
  `item_total_amount` numeric(20,2) unsigned NOT NULL COMMENT '物件总金额',
  `item_total_weight` numeric(20,2) unsigned NOT NULL COMMENT '物件总重量',
  `item_data` json COMMENT '物件数据',
  `goods_id` bigint unsigned COMMENT '商品ID，方便查询',
  `is_evaluation` smallint NOT NULL DEFAULT '-1' COMMENT '是否已评价',
  `refund_qty` bigint unsigned NOT NULL DEFAULT 0 COMMENT '已退款退货数量'
) ENGINE=INNODB COMMENT '订单内项';

DROP TABLE IF EXISTS `order_log`;
CREATE TABLE `order_log` (
  `order_id` bigint unsigned NOT NULL COMMENT '订单id',
  `operator_uid` bigint unsigned COMMENT '操作人id',
  `operator` char(255) NOT NULL COMMENT '操作',
  `data` json COMMENT '数据',
  `log_time` datetime NOT NULL COMMENT '日志时间'
) ENGINE=INNODB COMMENT '订单日志';


DROP TABLE IF EXISTS `order_refund`;
CREATE TABLE `order_refund` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `uid` bigint unsigned NOT NULL COMMENT '申请售后单人',
  `from_order_id` bigint unsigned NOT NULL COMMENT '从哪个订单来',
  `from_order_no` char(255) NOT NULL COMMENT '从哪个订单号来',
  `no` char(255) NOT NULL COMMENT '退单号',
  `type` char(255) NOT NULL COMMENT '订单类型',
  `name` char(255) COMMENT '订单交易名',
  `description` char(255) COMMENT '订单简述',
  `status` smallint NOT NULL DEFAULT -1 COMMENT '订单状态',
  `item_id` bigint unsigned NOT NULL COMMENT '物件在订单中的itemid',
  `total_qty` bigint unsigned NOT NULL COMMENT '退货退款货物数量',
  `total_amount` numeric(20,2) unsigned NOT NULL COMMENT '退货退款货物价值',
  `cancel_time` datetime COMMENT '取消时间',
  `auto_cancel_time` datetime COMMENT '自动取消时间',
  `apply_time` datetime NOT NULL COMMENT '退款申请时间',
  `apply_remark` char(255) COMMENT '退款申请备注',
  `replace_or_repair_remark` char(255) COMMENT '换货维修备注',
  `agree_time` datetime COMMENT '退款同意时间',
  `agree_remark` char(255) COMMENT '退款同意备注',
  `agree_operator_uid` bigint unsigned COMMENT '退款同意操作人uid',
  `reject_time` datetime COMMENT '退款不同意时间',
  `reject_remark` char(255) COMMENT '退款不同意备注',
  `reject_operator_uid` bigint unsigned COMMENT '退款不同意操作人uid',
  `sent_time` datetime COMMENT '发货时间',
  `sent_express_code` char(255) COMMENT '退款货快递代码',
  `sent_express_no` char(255) COMMENT '退款货快递号',
  `received_time` datetime COMMENT '收货时间',
  `sent_back_time` datetime COMMENT '返货时间',
  `sent_back_express_code` char(255) COMMENT '售后返回快递码（换货/维修）',
  `sent_back_express_no` char(255) COMMENT '售后返回快递单号（换货/维修）',
  `sent_back_remarks` char(255) COMMENT '售后返回备注',
  `finish_time` datetime COMMENT '退款完成时间',
  PRIMARY KEY (`id`)
) ENGINE=INNODB COMMENT '退款单';



DROP TABLE IF EXISTS `order_refund_log`;
CREATE TABLE `order_refund_log` (
  `order_id` bigint unsigned NOT NULL COMMENT '订单id',
  `operator_uid` bigint unsigned COMMENT '操作人id',
  `operator` char(255) NOT NULL COMMENT '操作',
  `data` json COMMENT '数据',
  `log_time` datetime NOT NULL COMMENT '日志时间'
) ENGINE=INNODB COMMENT '退款单日志';


DROP TABLE IF EXISTS `order_shopping_cart`;
CREATE TABLE `order_shopping_cart` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `uid` bigint unsigned NOT NULL COMMENT '用户id',
  `goods_id` bigint unsigned NOT NULL COMMENT '商品id',
  `qty` bigint unsigned NOT NULL COMMENT '购买数量',
  PRIMARY KEY (`id`),
  UNIQUE KEY(`uid`,`goods_id`)
) ENGINE=INNODB COMMENT '购物车表';


DROP TABLE IF EXISTS `user_shipping_address`;
CREATE TABLE `user_shipping_address` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `uid` bigint unsigned NOT NULL COMMENT '用户id',
  `region` char(255) NOT NULL COMMENT '地区',
  `address` char(255) NOT NULL COMMENT '具体地址',
  `contact_user` char(255) NOT NULL COMMENT '联系人',
  `contact_mobile` char(255) NOT NULL COMMENT '联系电话',
  `tag` char(255) COMMENT '自定义标签',
  `is_default` smallint NOT NULL DEFAULT -1 COMMENT '是否默认',
  PRIMARY KEY (`id`)
) ENGINE=INNODB COMMENT '收货地址表';


ALTER TABLE `order` ADD FOREIGN KEY (`uid`) REFERENCES `user` (`uid`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `order` ADD FOREIGN KEY (`sent_express_code`) REFERENCES `data_express` (`code`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `order_items` ADD FOREIGN KEY (`order_id`) REFERENCES `order` (`id`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `order_items` ADD FOREIGN KEY (`seller_uid`) REFERENCES `user` (`uid`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `order_log` ADD FOREIGN KEY (`order_id`) REFERENCES `order` (`id`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `order_refund` ADD FOREIGN KEY (`uid`) REFERENCES `user` (`uid`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `order_refund` ADD FOREIGN KEY (`from_order_id`) REFERENCES `order` (`id`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `order_refund` ADD FOREIGN KEY (`sent_express_code`) REFERENCES `data_express` (`code`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `order_refund` ADD FOREIGN KEY (`sent_back_express_code`) REFERENCES `data_express` (`code`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `order_refund_log` ADD FOREIGN KEY (`order_id`) REFERENCES `order_refund` (`id`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `order_shopping_cart` ADD FOREIGN KEY (`uid`) REFERENCES `user` (`uid`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `order_shopping_cart` ADD FOREIGN KEY (`goods_id`) REFERENCES `goods` (`id`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `user_shipping_address` ADD FOREIGN KEY (`uid`) REFERENCES `user` (`uid`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;


insert into data_express(`code`,`name`) values ('shunfeng',',,,,,顺丰速运');
insert into data_express(`code`,`name`) values ('yuantong',',,,,,圆通快递');
insert into data_express(`code`,`name`) values ('shentong',',,,,,申通快递');
insert into data_express(`code`,`name`) values ('yunda',',,,,,韵达快递');
insert into data_express(`code`,`name`) values ('tiantian',',,,,,天天快递');
insert into data_express(`code`,`name`) values ('zhongtong',',,,,,中通快递');
insert into data_express(`code`,`name`) values ('anxindakuaixi',',,,,,安信达');
insert into data_express(`code`,`name`) values ('huitongkuaidi',',,,,,百世汇通');
insert into data_express(`code`,`name`) values ('debangwuliu',',,,,,德邦物流');
insert into data_express(`code`,`name`) values ('ems',',,,,,EMS');
insert into data_express(`code`,`name`) values ('youshuwuliu',',,,,,优速物流');
insert into data_express(`code`,`name`) values ('xinfengwuliu',',,,,,信丰物流');
insert into data_express(`code`,`name`) values ('kuaijiesudi',',,,,,快捷速递');
