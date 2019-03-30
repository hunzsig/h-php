

DROP TABLE IF EXISTS `external_config`;
CREATE TABLE `external_config` (
  `uid` bigint unsigned NOT NULL COMMENT '所属会员',
  `data` json COMMENT '数据',
  PRIMARY KEY (`uid`)
) ENGINE=INNODB COMMENT '第三方配置表';


DROP TABLE IF EXISTS `external_log`;
CREATE TABLE `external_log` (
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `behaviour` char(255) NOT NULL COMMENT '行为',
  `config` char(255) NOT NULL COMMENT '请求的配置',
  `config_actual` char(255) COMMENT '实际的配置',
  `params` json COMMENT '参数',
  `result` json COMMENT '结果'
) ENGINE=INNODB COMMENT '第三方日志表';


DROP TABLE IF EXISTS `external_trade_token`;
CREATE TABLE `external_trade_token` (
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `out_trade_no` char(255) NOT NULL COMMENT '对外交易号',
  `order_no` char(255) NOT NULL COMMENT '对内订单号',
  `type` char(255) NOT NULL COMMENT '类型',
  `amount` numeric(20,6) NOT NULL COMMENT '金额',
  `config` char(255) NOT NULL COMMENT '请求的配置',
  `config_actual` char(255) COMMENT '实际的配置',
  `params` json COMMENT '请求数据',
  `callback` json COMMENT '回调数据',
  `is_pay` smallint DEFAULT -1 NOT NULL COMMENT '是否已支付',
  `pay_account` char(255) COMMENT '支付账号',
  `pay_time` datetime COMMENT '支付时间',
  UNIQUE KEY(`create_time`,`out_trade_no`)
) ENGINE=INNODB COMMENT '第三方交易表';



DROP TABLE IF EXISTS `external_wxpay_promotion_transfers`;
CREATE TABLE `external_wxpay_promotion_transfers` (
  `out_trade_no` char(255) NOT NULL COMMENT '交易号',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `uid` char(255) NOT NULL COMMENT 'uid',
  `open_id` char(255) NOT NULL COMMENT 'openid',
  `amount` numeric(20,6) NOT NULL COMMENT '金额',
  `config` char(255) NOT NULL COMMENT '请求的配置',
  `config_actual` char(255) COMMENT '实际的配置',
  `params` json COMMENT '请求数据',
  `callback` json COMMENT '回调数据',
  UNIQUE KEY(`out_trade_no`)
) ENGINE=INNODB COMMENT '微信企业付款表';



DROP TABLE IF EXISTS `external_wx_user_info`;
CREATE TABLE `external_wx_user_info` (
  `config` bigint unsigned NOT NULL COMMENT '对应配置',
  `open_id` char(255) NOT NULL COMMENT '微信 OPEN ID',
  `unionid` char(255) COMMENT '微信 UNIONID',
  `sex` char(255) COMMENT '微信性别 -1未设置1男2女',
  `nickname` char(255) COMMENT '微信昵称',
  `login_name` char(255) COMMENT '微信登录名',
  `avatar` char(255) COMMENT '微信头像URL',
  `language` char(255) COMMENT '微信客户端语言',
  `city` char(255) COMMENT '微信所在城市',
  `province` char(255) COMMENT '微信所在省',
  `country` char(255) COMMENT '微信所在国家',
  PRIMARY KEY(`config`,`open_id`)
) ENGINE=INNODB COMMENT '微信账号信息表';


ALTER TABLE `external_wx_user_info` ADD FOREIGN KEY (`config`) REFERENCES `external_config` (`uid`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;