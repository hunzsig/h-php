

DROP TABLE IF EXISTS `data_feedback`;
CREATE TABLE `data_feedback` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `type` char(255) NOT NULL COMMENT '反馈问题类型',
  `content` varchar(1024) COMMENT '反馈问题类型',
  `ip` char(255) COMMENT 'IP地址',
  `url` char(255) COMMENT '反馈地址',
  `contact_name` char(255) COMMENT '联系人',
  `contact_phone` char(255) COMMENT '联系电话',
  `remarks` varchar(1024) COMMENT '处理备注',
  `create_time` datetime COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=INNODB COMMENT '客户反馈表';