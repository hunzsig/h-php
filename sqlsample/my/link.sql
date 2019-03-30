

DROP TABLE IF EXISTS `data_link`;
CREATE TABLE `data_link` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `category_id` bigint unsigned NOT NULL COMMENT '分类',
  `name` char(255) NOT NULL COMMENT '名称',
  `url` char(255) COMMENT '链接',
  `pic` json COMMENT '图片链接',
  `ordering` int NOT NULL DEFAULT 0 COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=INNODB COMMENT '链接';


DROP TABLE IF EXISTS `data_link_category`;
CREATE TABLE `data_link_category` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `parent_id` bigint unsigned NOT NULL DEFAULT 0 COMMENT '父级id',
  `name` char(255) NOT NULL COMMENT '分类名称',
  `level` int unsigned NOT NULL DEFAULT 1 COMMENT '分类等级',
  `description` varchar(2048) COMMENT '描述',
  `pic` json COMMENT '分类图片',
  `status` smallint NOT NULL DEFAULT 1 COMMENT '是否开启 -1不开启 1开启',
  PRIMARY KEY (`id`),
  UNIQUE KEY (`parent_id`,`name`)
) ENGINE=INNODB COMMENT '链接分类';

ALTER TABLE `data_link` ADD FOREIGN KEY (`category_id`) REFERENCES `data_link_category` (`id`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;

insert into `data_link_category` (name,level,description,pic,status) values ('合作伙伴',1,NULL,NULL,'1');

