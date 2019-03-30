
DROP TABLE IF EXISTS `emc_category`;
CREATE TABLE `emc_category` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `name` char(255) NOT NULL COMMENT '分类名',
  PRIMARY KEY (`id`),
  UNIQUE KEY (`name`)
) ENGINE=INNODB COMMENT 'EMC产品分类';

DROP TABLE IF EXISTS `emc_question_lib`;
CREATE TABLE `emc_question_lib` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `category_id` bigint unsigned NOT NULL COMMENT '产品分类',
  `name` char(255) COMMENT '题库名称',
  `score_max` int COMMENT '最高分',
  `create_time` datetime NOT NULL COMMENT '创建日期',
  PRIMARY KEY (`id`),
  UNIQUE KEY (`name`)
) ENGINE=INNODB COMMENT 'EMC题库';
ALTER TABLE `emc_question_lib` ADD FOREIGN KEY (`category_id`) REFERENCES `emc_category` (`id`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;

DROP TABLE IF EXISTS `emc_question_struct`;
CREATE TABLE `emc_question_struct` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `lib_id` bigint unsigned NOT NULL COMMENT '所属题库',
  `name` char(255) NOT NULL COMMENT '结构要素名称',
  `create_time` datetime NOT NULL COMMENT '创建日期',
  PRIMARY KEY (`id`)
) ENGINE=INNODB COMMENT 'EMC题目结构要素';
ALTER TABLE `emc_question_struct` ADD FOREIGN KEY (`lib_id`) REFERENCES `emc_question_lib` (`id`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;

DROP TABLE IF EXISTS `emc_question`;
CREATE TABLE `emc_question` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `struct_id` bigint unsigned NOT NULL COMMENT '结构要素ID',
  `title` char(255) NOT NULL COMMENT '标题',
  `answers` json COMMENT '答案，每题的分值及label,是否总经理特定，总工特定',
  `create_time` datetime NOT NULL COMMENT '创建日期',
  PRIMARY KEY (`id`)
) ENGINE=INNODB COMMENT 'EMC题目';
ALTER TABLE `emc_question` ADD FOREIGN KEY (`struct_id`) REFERENCES `emc_question_struct` (`id`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;

DROP TABLE IF EXISTS `emc`;
CREATE TABLE `emc` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `category_id` varchar(512) NOT NULL COMMENT '产品分类',
  `lib_id` bigint unsigned NOT NULL COMMENT '选择题库',
  `name` char(255) NOT NULL COMMENT '产品名称',
  `model` char(255) NOT NULL COMMENT '产品型号',
  `status` char(255) NOT NULL DEFAULT 'product' COMMENT '状态',
  `product_uid` bigint unsigned NOT NULL COMMENT '产品设计师',
  `view_uid` bigint unsigned NOT NULL COMMENT '核对设计师',
  `review_uid` bigint unsigned NOT NULL COMMENT '复核设计师',
  `manager_uid` bigint unsigned NOT NULL COMMENT '总经理',
  `master_uid` bigint unsigned NOT NULL COMMENT '总工',
  `client` char(255) NOT NULL COMMENT '客户',
  `pic` json COMMENT '图片',
  `version` char(255) COMMENT '版本',
  `create_time` datetime NOT NULL COMMENT '创建日期',
  `product_answers` json COMMENT '设计做题数据',
  `view_answers` json COMMENT '核对做题数据',
  `review_answers` json COMMENT '复核做题数据',
  `manager_answers` json COMMENT '总经理做题数据',
  `master_answers` json COMMENT '总工做题数据',
  `product_score` numeric(6,2) NOT NULL DEFAULT 0 COMMENT '设计做题分数',
  `view_score` numeric(6,2) NOT NULL DEFAULT 0 COMMENT '核对做题分数',
  `review_score` numeric(6,2) NOT NULL DEFAULT 0 COMMENT '复核做题分数',
  `manager_score` numeric(6,2) NOT NULL DEFAULT 0 COMMENT '总经理做题分数',
  `master_score` numeric(6,2) NOT NULL DEFAULT 0 COMMENT '总工做题分数',
  `product_remark` varchar(1024) COMMENT '设计备注',
  `view_remark` varchar(1024) COMMENT '核对备注',
  `review_remark` varchar(1024) COMMENT '复核备注',
  `manager_remark` varchar(1024) COMMENT '总经理备注',
  `master_remark` varchar(1024) COMMENT '总工备注',
  `product_time` datetime COMMENT '设计日期',
  `view_time` datetime COMMENT '核对日期',
  `review_time` datetime COMMENT '复核日期',
  `manager_time` datetime COMMENT '总经理处理日期',
  `master_time` datetime COMMENT '总工处理日期',
  PRIMARY KEY (`id`),
  UNIQUE KEY (`name`)
) ENGINE=INNODB COMMENT 'EMC产品';