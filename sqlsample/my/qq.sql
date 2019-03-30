
DROP TABLE IF EXISTS `qq_group`;
CREATE TABLE `qq_group` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `name` char(255) NOT NULL COMMENT 'QQ群名',
  `number` char(255) NOT NULL COMMENT 'QQ群号',
  `build_year` year COMMENT 'QQ群建立年',
  `description` char(255) COMMENT '描述',
  `pic` json COMMENT '图片',
  `link` char(255) COMMENT '加群链接',
  `qty` int unsigned NOT NULL DEFAULT 0 COMMENT '群里人员',
  `ordering` int NOT NULL DEFAULT 0 COMMENT '排序',
  PRIMARY KEY (`id`),
  UNIQUE KEY (`number`)
) ENGINE=INNODB COMMENT 'QQ群';

