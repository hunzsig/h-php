
DROP TABLE IF EXISTS `finance_commission_installment`;
CREATE TABLE `finance_commission_installment` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `uid` bigint unsigned NOT NULL COMMENT 'uid',
  `type` char(255) NOT NULL COMMENT '类型',
  `description` varchar(1024) COMMENT '描述',
  `balance` numeric(20,6) unsigned NOT NULL COMMENT '每期分 余额',
  `balance_lock` numeric(20,6) unsigned NOT NULL COMMENT '每期分 绑定余额',
  `credit` numeric(20,6) unsigned NOT NULL COMMENT '每期分 积分',
  `base_balance` numeric(20,6) unsigned NOT NULL COMMENT '分佣基础 余额',
  `base_balance_lock` numeric(20,6) unsigned NOT NULL COMMENT '分佣基础 绑定余额',
  `base_credit` numeric(20,6) unsigned NOT NULL COMMENT '分佣基础 积分',
  `base_percent_balance` numeric(8,5) unsigned NOT NULL COMMENT '计算比例 余额',
  `base_percent_balance_lock` numeric(8,5) unsigned NOT NULL COMMENT '计算比例 绑定余额',
  `base_percent_credit` numeric(8,5) unsigned NOT NULL COMMENT '计算比例 积分',
  `start_number_of_installments` smallint unsigned NOT NULL DEFAULT 0 COMMENT '开始期数',
  `current_number_of_installments` smallint unsigned NOT NULL COMMENT '当前期数',
  `installments_unit` char(255) NOT NULL DEFAULT 'month' COMMENT '期单位 year month week day等',
  `installments_unit_length` bigint unsigned NOT NULL DEFAULT 1 COMMENT '分期总期数,最少为1',
  `number_of_installments` bigint NOT NULL COMMENT '是否有效',
  `is_enable` smallint NOT NULL DEFAULT 1 COMMENT '是否有效',
  `is_over` smallint NOT NULL DEFAULT -1 COMMENT '是否已结束',
  `from_uid` bigint unsigned COMMENT '从何人来',
  `order_no` char(255) COMMENT '订单号',
  `data` json COMMENT '记录数据',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `estimated_time` datetime NOT NULL COMMENT '预计下次分佣时间',
  `last_commission_time` datetime COMMENT '最后一次分佣时间',
  PRIMARY KEY (`id`)
) ENGINE=INNODB COMMENT '分佣表';

ALTER TABLE `finance_commission_installment` ADD FOREIGN KEY (`uid`) REFERENCES `user` (`uid`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `finance_commission_installment` ADD FOREIGN KEY (`from_uid`) REFERENCES `user` (`uid`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;