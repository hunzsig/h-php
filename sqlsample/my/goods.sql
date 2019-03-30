
DROP TABLE IF EXISTS `goods_brand`;
CREATE TABLE `goods_brand` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `name` char(255) NOT NULL COMMENT '品牌名称',
  `name_eng` char(255) COMMENT '品牌名称(英文)',
  `country` char(255) COMMENT '品牌国家',
  `pic` json COMMENT '展示图片',
  PRIMARY KEY (`id`),
  UNIQUE KEY (`name`),
  UNIQUE KEY (`name_eng`)
) ENGINE=INNODB COMMENT '商品牌子';


DROP TABLE IF EXISTS `goods_attr_class`;
CREATE TABLE `goods_attr_class` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `name` char(255) NOT NULL COMMENT '属性名称',
  PRIMARY KEY (`id`),
  UNIQUE KEY (`name`)
) ENGINE=INNODB COMMENT '商品属性库';


DROP TABLE IF EXISTS `goods_attr_value`;
CREATE TABLE `goods_attr_value` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `class_id` bigint unsigned NOT NULL COMMENT 'classID',
  `name` char(255) NOT NULL COMMENT '属性值',
  `pic` json COMMENT '图片',
  PRIMARY KEY (`id`),
  UNIQUE KEY (`class_id`,`name`)
) ENGINE=INNODB COMMENT '商品属性库值';


DROP TABLE IF EXISTS `goods_category`;
CREATE TABLE `goods_category` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `pid` bigint unsigned NOT NULL COMMENT '所属分类id',
  `status` smallint NOT NULL DEFAULT 1 COMMENT '状态 1有效 -1无效',
  `name` char(255) NOT NULL COMMENT '分类名称',
  `level` smallint unsigned NOT NULL DEFAULT 1 COMMENT '分类层级',
  `pic` json COMMENT '展示图片',
  `ordering` int NOT NULL DEFAULT 0 COMMENT '排序 越大越靠前',
  PRIMARY KEY (`id`),
  UNIQUE KEY (`name`)
) ENGINE=INNODB COMMENT '商品分类';


DROP TABLE IF EXISTS `goods`;
CREATE TABLE `goods` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `uid` bigint unsigned NOT NULL COMMENT '卖家uid',
  `status` smallint NOT NULL DEFAULT 1 COMMENT '商品状态 -10删除 -1下架 1上架',
  `name` char(255) NOT NULL COMMENT '商品名称',
  `groups` char(255) NOT NULL COMMENT '联合',
  `category_id` char(255) NOT NULL COMMENT '分类ID组',
  `brand_id` bigint unsigned COMMENT '品牌id',
  `attr_value` char(255) COMMENT '相关属性值ID',
  `attr_value_label` char(255) COMMENT '相关属性值',
  `detail` json COMMENT '商品详情（如introduce简介，各类型文章详情等）',
  `pic` json COMMENT '图集',
  `tag` char(255) COMMENT '商品标签',
  `origin_region` char(255) COMMENT '产地地区ID',
  `origin_region_label` char(255) COMMENT '产地地区',
  `origin_address` char(255) COMMENT '产地详细',
  `unit` char(255) COMMENT '单位',
  `price_sell` numeric(10,2) unsigned NOT NULL COMMENT '销售价',
  `price_cost` numeric(10,2) unsigned COMMENT '成本价',
  `price_advice` numeric(10,2) NOT NULL DEFAULT -1 COMMENT '建议售价 -1 代表无',
  `qty_stock` bigint NOT NULL DEFAULT 0 COMMENT '库存量',
  `qty_view` bigint unsigned NOT NULL DEFAULT 0 COMMENT '浏览次数',
  `qty_sale` bigint unsigned NOT NULL DEFAULT 0 COMMENT '销量',
  `qty_like` bigint unsigned NOT NULL DEFAULT 0 COMMENT '赞次数',
  `weight` numeric(10,3) unsigned NOT NULL DEFAULT 0.000 COMMENT '重量,单位千克kg',
  `barcode` char(255) COMMENT '条码',
  `recommend` json NOT NULL COMMENT '推荐判断组',
  `ordering` int NOT NULL DEFAULT 0 COMMENT '排序 越大越靠前',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=INNODB COMMENT '商品表';


DROP TABLE IF EXISTS `goods_evaluation`;
CREATE TABLE `goods_evaluation` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `prev_id` bigint unsigned NOT NULL COMMENT '上级评论ID 用于追加',
  `uid` bigint unsigned NOT NULL COMMENT '评分人uid',
  `goods_id` bigint unsigned NOT NULL COMMENT '商品ID',
  `seller_uid` bigint unsigned COMMENT '卖家uid',
  `evaluate` char(255) NOT NULL DEFAULT 'positive' COMMENT '评价 好中差评 positive/moderate/negative',
  `point` numeric(3,1) NOT NULL DEFAULT 5.0 COMMENT '是次得分',
  `order_id` bigint unsigned NOT NULL COMMENT '订单ID',
  `order_no` char(255) NOT NULL COMMENT '订单号',
  `comment` varchar(1024) COMMENT '评价内容',
  `status` smallint NOT NULL DEFAULT -1 COMMENT '有效状态 -1无 1有',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=INNODB COMMENT '商品评分表';



DROP TABLE IF EXISTS `goods_collection`;
CREATE TABLE `goods_collection` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `uid` bigint unsigned NOT NULL COMMENT '收藏人uid',
  `goods_id` bigint unsigned NOT NULL COMMENT '商品ID',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY (`uid`,`goods_id`)
) ENGINE=INNODB COMMENT '商品收藏表';


ALTER TABLE `goods_attr_value` ADD FOREIGN KEY (`class_id`) REFERENCES `goods_attr_class` (`id`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `goods_collection` ADD FOREIGN KEY (`uid`) REFERENCES `user` (`uid`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `goods_collection` ADD FOREIGN KEY (`goods_id`) REFERENCES `goods` (`id`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `goods_evaluation` ADD FOREIGN KEY (`uid`) REFERENCES `user` (`uid`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `goods_evaluation` ADD FOREIGN KEY (`goods_id`) REFERENCES `goods` (`id`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;