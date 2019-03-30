

DROP TABLE IF EXISTS `data_bank_lib`;
CREATE TABLE `data_bank_lib` (
  `code` char(255) NOT NULL COMMENT '银行代码',
  `name` char(255) NOT NULL COMMENT '银行名称',
  `icon_square` varchar(2048) COMMENT '图标路径（正方）',
  `icon_rectangle` varchar(2048) COMMENT '图标路径（长方）',
  `icon_circular` varchar(2048) COMMENT '图标路径（圆形）',
  `pay_code` char(255) COMMENT '支付代码',
  `status` smallint DEFAULT -1 NOT NULL COMMENT '状态 -1不可用 1可用',
  `ordering` integer DEFAULT 0 NOT NULL COMMENT '排序',
  PRIMARY KEY (`code`),
  UNIQUE KEY (`name`),
  UNIQUE KEY (`pay_code`)
) ENGINE=INNODB COMMENT '银行库';


DROP TABLE IF EXISTS `finance_bank_account`;
CREATE TABLE `finance_bank_account` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `uid` bigint unsigned NOT NULL COMMENT '用户ID',
  `account_bank_code` char(255) COMMENT '开户银行代码',
  `account_holder` char(255) COMMENT '银行帐号持有人',
  `account_no` char(255) COMMENT '银行账户',
  `account_type` smallint not null COMMENT '帐号类型 1对公帐号 2对私帐号 3个人储蓄卡 4个人信用卡',
  `is_default` smallint NOT NULL DEFAULT -1 COMMENT '是否默认帐号 -1否 1是',
  PRIMARY KEY (`id`),
  UNIQUE KEY (`account_bank_code`,`account_holder`,`account_no`)
) ENGINE=INNODB COMMENT '银行卡记录表';


DROP TABLE IF EXISTS `finance_wallet`;
CREATE TABLE `finance_wallet` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `uid` bigint unsigned NOT NULL COMMENT '用户ID',
  `pay_password` char(255) COMMENT '支付密码',
  `pay_password_level` smallint COMMENT '密码等级',
  `status` smallint NOT NULL DEFAULT 1 COMMENT '状态 1有效 -1无效',
  `balance` numeric(20,6) NOT NULL DEFAULT 0.000000 COMMENT '余额（一般只有余额可以提现）',
  `balance_lock` numeric(20,6) NOT NULL DEFAULT 0.000000 COMMENT '绑定余额',
  `credit` numeric(20,6) NOT NULL DEFAULT 0.000000 COMMENT '积分',
  `freeze_balance` numeric(20,6) NOT NULL DEFAULT 0.000000 COMMENT '冻结 余额',
  `freeze_balance_lock` numeric(20,6) NOT NULL DEFAULT 0.000000 COMMENT '冻结 绑定余额',
  `freeze_credit` numeric(20,6) NOT NULL DEFAULT 0.000000 COMMENT '冻结 积分',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY (`uid`)
) ENGINE=INNODB COMMENT '钱包表';


DROP TABLE IF EXISTS `finance_wallet_log`;
CREATE TABLE `finance_wallet_log` (
  `uid` bigint unsigned NOT NULL COMMENT '用户ID',
  `wallet_id` bigint unsigned NOT NULL COMMENT '对应的钱包ID',
  `type` char(255) NOT NULL COMMENT '操作类型',
  `description` char(255) COMMENT '描述',
  `data` json COMMENT 'json化数据',
  `operator_uid` bigint unsigned COMMENT '操作人uid',
  `balance` numeric(20,6) NOT NULL DEFAULT 0.000000 COMMENT '变动 余额',
  `balance_lock` numeric(20,6) NOT NULL DEFAULT 0.000000 COMMENT '变动 绑定余额',
  `credit` numeric(20,6) NOT NULL DEFAULT 0.000000 COMMENT '变动 积分',
  `freeze_balance` numeric(20,6) NOT NULL DEFAULT 0.000000 COMMENT '变动 冻结余额',
  `freeze_balance_lock` numeric(20,6) NOT NULL DEFAULT 0.000000 COMMENT '变动 冻结绑定余额',
  `freeze_credit` numeric(20,6) NOT NULL DEFAULT 0.000000 COMMENT '变动 冻结积分',
  `create_time` datetime NOT NULL COMMENT '创建时间'
) ENGINE=INNODB COMMENT '钱包日志表';



DROP TABLE IF EXISTS `finance_withdraw`;
CREATE TABLE `finance_withdraw` (
  `uid` bigint unsigned NOT NULL COMMENT '用户ID',
  `pre_min_limit` numeric(20,3) NOT NULL DEFAULT 0.010 COMMENT '提现下限,低于不允许提现，默认0.01',
  `pre_max_limit` numeric(20,3) NOT NULL DEFAULT 1000.000 COMMENT '单次提现上限，默认1000',
  `day_max_limit` numeric(20,3) NOT NULL DEFAULT 10000.000 COMMENT '单日提现上限，默认10000',
  `cooling_period` int NOT NULL DEFAULT 0 COMMENT '提现冷却天数，默认为0',
  `status` smallint not null DEFAULT 1 COMMENT '是否允许提现 1允许 -1不允许',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `last_apply_time` datetime COMMENT '最后一次申请提现日期',
  PRIMARY KEY (`uid`)
) ENGINE=INNODB COMMENT '用户提现规范表';


DROP TABLE IF EXISTS `finance_withdraw_apply`;
CREATE TABLE `finance_withdraw_apply` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `uid` bigint unsigned NOT NULL COMMENT '用户ID',
  `bank_card_info` json COMMENT '提现银行卡信息',
  `apply_amount` numeric(20,2) NOT NULL DEFAULT 0.01 COMMENT '申请金额',
  `last_handle_time` datetime COMMENT '最后一次处理日期',
  `status` smallint NOT NULL DEFAULT 1 COMMENT '提现申请状态 -1不通过 1审核中 2审核通过 10提现完毕',
  `reason` varchar(1024) COMMENT '原因',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=INNODB COMMENT '用户提现申请表';



DROP TABLE IF EXISTS `finance_withdraw_log`;
CREATE TABLE `finance_withdraw_log` (
  `uid` bigint unsigned NOT NULL COMMENT '用户ID',
  `apply_id` bigint unsigned NOT NULL COMMENT '申请ID',
  `operator_uid` bigint unsigned NOT NULL COMMENT '操作人ID 如用户ID 管理员ID',
  `apply_amount` numeric(20,2) NOT NULL DEFAULT 0.00 COMMENT '申请金额',
  `type` smallint NOT NULL COMMENT '操作类型',
  `description` varchar(1024) COMMENT '描述',
  `wallet_id` bigint unsigned COMMENT '对应的钱包ID',
  `data` json COMMENT '其他数据',
  `create_time` datetime NOT NULL COMMENT '创建时间'
) ENGINE=INNODB COMMENT '提现日志表';


/* FOREIGN KEY */
ALTER TABLE `finance_bank_account` ADD FOREIGN KEY (`uid`) REFERENCES `user` (`uid`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `finance_bank_account` ADD FOREIGN KEY (`account_bank_code`) REFERENCES `data_bank_lib` (`code`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `finance_wallet` ADD FOREIGN KEY (`uid`) REFERENCES `user` (`uid`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `finance_wallet_log` ADD FOREIGN KEY (`wallet_id`) REFERENCES `finance_wallet` (`id`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `finance_wallet_log` ADD FOREIGN KEY (`operator_uid`) REFERENCES `user` (`uid`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `finance_withdraw` ADD FOREIGN KEY (`uid`) REFERENCES `user` (`uid`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `finance_withdraw_apply` ADD FOREIGN KEY (`uid`) REFERENCES `user` (`uid`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `finance_withdraw_log` ADD FOREIGN KEY (`apply_id`) REFERENCES `finance_withdraw_apply` (`id`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `finance_withdraw_log` ADD FOREIGN KEY (`operator_uid`) REFERENCES `user` (`uid`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `finance_withdraw_log` ADD FOREIGN KEY (`wallet_id`) REFERENCES `finance_wallet` (`id`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;


/* data */
insert into `data_bank_lib` (`code`,`name`,`icon_square`,`icon_rectangle`,`icon_circular`,`pay_code`,`status`) values ('abc','农业银行','/assets/img/bank/icon_square/abc.png','/assets/img/bank/icon_rectangle/abc.png',NULL,'ABC','1');
insert into `data_bank_lib` (`code`,`name`,`icon_square`,`icon_rectangle`,`icon_circular`,`pay_code`,`status`) values ('alipay','支付宝','/assets/img/bank/icon_square/alipay.png','/assets/img/bank/icon_rectangle/alipay.png',NULL,'ALIPAY','1');
insert into `data_bank_lib` (`code`,`name`,`icon_square`,`icon_rectangle`,`icon_circular`,`pay_code`,`status`) values ('bjb','北京银行','/assets/img/bank/icon_square/bjb.png','/assets/img/bank/icon_rectangle/bjb.png',NULL,NULL,'1');
insert into `data_bank_lib` (`code`,`name`,`icon_square`,`icon_rectangle`,`icon_circular`,`pay_code`,`status`) values ('boc','中国银行','/assets/img/bank/icon_square/boc.png','/assets/img/bank/icon_rectangle/boc.png',NULL,'BOC-DEBIT','1');
insert into `data_bank_lib` (`code`,`name`,`icon_square`,`icon_rectangle`,`icon_circular`,`pay_code`,`status`) values ('ccb','建设银行','/assets/img/bank/icon_square/ccb.png','/assets/img/bank/icon_rectangle/ccb.png',NULL,'CCB','1');
insert into `data_bank_lib` (`code`,`name`,`icon_square`,`icon_rectangle`,`icon_circular`,`pay_code`,`status`) values ('ceb','光大银行','/assets/img/bank/icon_square/ceb.png','/assets/img/bank/icon_rectangle/ceb.png',NULL,'CEB-DEBIT','1');
insert into `data_bank_lib` (`code`,`name`,`icon_square`,`icon_rectangle`,`icon_circular`,`pay_code`,`status`) values ('cgb','广发银行','/assets/img/bank/icon_square/cgb.png','/assets/img/bank/icon_rectangle/cgb.png',NULL,'GDB','1');
insert into `data_bank_lib` (`code`,`name`,`icon_square`,`icon_rectangle`,`icon_circular`,`pay_code`,`status`) values ('cib','兴业银行','/assets/img/bank/icon_square/cib.png','/assets/img/bank/icon_rectangle/cib.png',NULL,'CIB','1');
insert into `data_bank_lib` (`code`,`name`,`icon_square`,`icon_rectangle`,`icon_circular`,`pay_code`,`status`) values ('citic','中信银行','/assets/img/bank/icon_square/citic.png','/assets/img/bank/icon_rectangle/citic.png',NULL,'CITIC','1');
insert into `data_bank_lib` (`code`,`name`,`icon_square`,`icon_rectangle`,`icon_circular`,`pay_code`,`status`) values ('cmb','招商银行','/assets/img/bank/icon_square/cmb.png','/assets/img/bank/icon_rectangle/cmb.png',NULL,'CMB-DEBIT','1');
insert into `data_bank_lib` (`code`,`name`,`icon_square`,`icon_rectangle`,`icon_circular`,`pay_code`,`status`) values ('cmbc','民生银行','/assets/img/bank/icon_square/cmbc.png','/assets/img/bank/icon_rectangle/cmbc.png',NULL,'CMBC','1');
insert into `data_bank_lib` (`code`,`name`,`icon_square`,`icon_rectangle`,`icon_circular`,`pay_code`,`status`) values ('comm','交通银行','/assets/img/bank/icon_square/comm.png','/assets/img/bank/icon_rectangle/comm.png',NULL,'COMM','1');
insert into `data_bank_lib` (`code`,`name`,`icon_square`,`icon_rectangle`,`icon_circular`,`pay_code`,`status`) values ('czb','浙商银行','/assets/img/bank/icon_square/czb.png','/assets/img/bank/icon_rectangle/czb.png',NULL,NULL,'1');
insert into `data_bank_lib` (`code`,`name`,`icon_square`,`icon_rectangle`,`icon_circular`,`pay_code`,`status`) values ('ecb','恒丰银行','/assets/img/bank/icon_square/ecb.png','/assets/img/bank/icon_rectangle/ecb.png',NULL,NULL,'1');
insert into `data_bank_lib` (`code`,`name`,`icon_square`,`icon_rectangle`,`icon_circular`,`pay_code`,`status`) values ('guangzhou','广州银行','/assets/img/bank/icon_square/guangzhou.png','/assets/img/bank/icon_rectangle/guangzhou.png',NULL,NULL,'1');
insert into `data_bank_lib` (`code`,`name`,`icon_square`,`icon_rectangle`,`icon_circular`,`pay_code`,`status`) values ('hxb','华夏银行','/assets/img/bank/icon_square/hxb.png','/assets/img/bank/icon_rectangle/hxb.png',NULL,NULL,'1');
insert into `data_bank_lib` (`code`,`name`,`icon_square`,`icon_rectangle`,`icon_circular`,`pay_code`,`status`) values ('icbc','工商银行','/assets/img/bank/icon_square/icbc.png','/assets/img/bank/icon_rectangle/icbc.png',NULL,'ICBCB2C','1');
insert into `data_bank_lib` (`code`,`name`,`icon_square`,`icon_rectangle`,`icon_circular`,`pay_code`,`status`) values ('nanchang','南昌银行','/assets/img/bank/icon_square/nanchang.png','/assets/img/bank/icon_rectangle/nanchang.png',NULL,NULL,'1');
insert into `data_bank_lib` (`code`,`name`,`icon_square`,`icon_rectangle`,`icon_circular`,`pay_code`,`status`) values ('njcb','南京银行','/assets/img/bank/icon_square/njcb.png','/assets/img/bank/icon_rectangle/njcb.png',NULL,NULL,'1');
insert into `data_bank_lib` (`code`,`name`,`icon_square`,`icon_rectangle`,`icon_circular`,`pay_code`,`status`) values ('pab','平安银行','/assets/img/bank/icon_square/pab.png','/assets/img/bank/icon_rectangle/pab.png',NULL,'SPABANK','1');
insert into `data_bank_lib` (`code`,`name`,`icon_square`,`icon_rectangle`,`icon_circular`,`pay_code`,`status`) values ('psbc','中国邮政储蓄银行','/assets/img/bank/icon_square/psbc.png','/assets/img/bank/icon_rectangle/psbc.png',NULL,'POSTGC','1');
insert into `data_bank_lib` (`code`,`name`,`icon_square`,`icon_rectangle`,`icon_circular`,`pay_code`,`status`) values ('shenzhenfazhan','深圳发展银行','/assets/img/bank/icon_square/shenzhenfazhan.png','/assets/img/bank/icon_rectangle/shenzhenfazhan.png',NULL,NULL,'1');
insert into `data_bank_lib` (`code`,`name`,`icon_square`,`icon_rectangle`,`icon_circular`,`pay_code`,`status`) values ('spdb','上海浦东发展银行','/assets/img/bank/icon_square/spdb.png','/assets/img/bank/icon_rectangle/spdb.png',NULL,'SPDB','1');
insert into `data_bank_lib` (`code`,`name`,`icon_square`,`icon_rectangle`,`icon_circular`,`pay_code`,`status`) values ('tenpay','财付通','/assets/img/bank/icon_square/tenpay.png','/assets/img/bank/icon_rectangle/tenpay.png',NULL,'TENPAY','-1');
insert into `data_bank_lib` (`code`,`name`,`icon_square`,`icon_rectangle`,`icon_circular`,`pay_code`,`status`) values ('unionpay','中国银联','/assets/img/bank/icon_square/unionpay.png','/assets/img/bank/icon_rectangle/unionpay.png',NULL,NULL,'1');
insert into `data_bank_lib` (`code`,`name`,`icon_square`,`icon_rectangle`,`icon_circular`,`pay_code`,`status`) values ('wxpay','微信支付','/assets/img/bank/icon_square/wxpay.png','/assets/img/bank/icon_rectangle/wxpay.png',NULL,'WXPAY','-1');
insert into `data_bank_lib` (`code`,`name`,`icon_square`,`icon_rectangle`,`icon_circular`,`pay_code`,`status`) values ('zgncsyb','广州农村商业银行','/assets/img/bank/icon_square/zgncsyb.png','/assets/img/bank/icon_rectangle/zgncsyb.png',NULL,NULL,'1');
