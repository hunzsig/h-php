
DROP TABLE IF EXISTS `data_download`;
CREATE TABLE `data_download` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `belong` char(255) NOT NULL COMMENT '归属',
  `name` char(255) NOT NULL COMMENT '下载文件名',
  `introduce` char(255) COMMENT '简介',
  `description` varchar (1024) COMMENT '描述',
  `pic` json COMMENT '图片',
  `dl_link` char(255) COMMENT '下载链接',
  `dl_qty` int unsigned DEFAULT 0 NOT NULL COMMENT '下载次数',
  `dl_last_time` datetime COMMENT '最后一次下载时间',
  `version` char(255) COMMENT '版本',
  `support` char(255) COMMENT '使用支持',
  `create_time` datetime COMMENT '创建日期',
  `update_time` datetime COMMENT '更新日期',
  PRIMARY KEY (`id`),
  UNIQUE KEY (`name`)
) ENGINE=INNODB COMMENT '下载';

