
DROP TABLE IF EXISTS `dom`;
CREATE TABLE `dom` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `uid` bigint unsigned NOT NULL COMMENT '创建的用户ID',
  `type` char(255) NOT NULL COMMENT '文档类型',
  `category_id` bigint unsigned COMMENT '文档分类id',
  `ordering` integer NOT NULL DEFAULT 0 COMMENT '排序，数值越大优先级越高',
  `status` smallint NOT NULL DEFAULT 1 COMMENT '文档状态',
  `views` bigint unsigned NOT NULL DEFAULT 0 COMMENT '浏览量',
  `data` json COMMENT '文档数据（json）',
  `create_time` datetime COMMENT '创建时间',
  `update_time` datetime COMMENT '更新时间',
  `verify_uid` bigint NOT NULL COMMENT '审核人UID',
  PRIMARY KEY (`id`)
) ENGINE=INNODB COMMENT '文档';


DROP TABLE IF EXISTS `dom_category`;
CREATE TABLE `dom_category` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `parent_id` bigint unsigned COMMENT '父级id',
  `name` char(255) NOT NULL COMMENT '分类名称',
  `level` bigint unsigned NOT NULL DEFAULT 1 COMMENT '分类等级',
  `description` varchar (1024) COMMENT '描述',
  `pic` json COMMENT '分类图片',
  `status` smallint NOT NULL DEFAULT 1 COMMENT '是否开启 -1不开启 1开启',
  PRIMARY KEY (`id`),
  UNIQUE KEY (`parent_id`,`name`)
) ENGINE=INNODB COMMENT '文档分类';


ALTER TABLE `dom` ADD FOREIGN KEY (`uid`) REFERENCES `user` (`uid`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `dom` ADD FOREIGN KEY (`category_id`) REFERENCES `dom_category` (`id`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;

insert into `dom_category` (name,level,description,pic,status) values ('协议',1,NULL,NULL,'1');
insert into `dom_category` (name,level,description,pic,status) values ('PC首页广告',1,NULL,NULL,'1');
insert into `dom_category` (name,level,description,pic,status) values ('手机首页广告',1,NULL,NULL,'1');
insert into `dom_category` (name,level,description,pic,status) values ('公司信息',1,NULL,NULL,'1');
insert into `dom_category` (name,level,description,pic,status) values ('帮助',1,NULL,NULL,'1');
insert into `dom_category` (name,level,description,pic,status) values ('客服',1,NULL,NULL,'1');
insert into `dom_category` (name,level,description,pic,status) values ('文化',1,NULL,NULL,'1');
insert into `dom_category` (name,level,description,pic,status) values ('新闻',1,NULL,NULL,'1');
insert into `dom_category` (name,level,description,pic,status) values ('活动',1,NULL,NULL,'1');
insert into `dom_category` (name,level,description,pic,status) values ('公告',1,NULL,NULL,'1');

