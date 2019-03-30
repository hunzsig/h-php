
DROP TABLE IF EXISTS `system_auth`;
CREATE TABLE `system_auth` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `uid` bigint COMMENT '用户ID，可以为空',
  `auth_name` char(255) NOT NULL COMMENT '验证名称，如手机号 邮箱地址等',
  `auth_code` char(255) NOT NULL COMMENT '验证码',
  `type` smallint NOT NULL COMMENT '验证类型',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY (`auth_code`),
  UNIQUE KEY (`auth_name`,`uid`)
) ENGINE=INNODB COMMENT '验证码记录表';


DROP TABLE IF EXISTS `system_auth_record`;
CREATE TABLE `system_auth_record` (
  `name` char(255) NOT NULL COMMENT '记录名称',
  `create_time` datetime NOT NULL COMMENT '记录时间'
) ENGINE=INNODB COMMENT '验证记录表';


DROP TABLE IF EXISTS `system_data`;
CREATE TABLE `system_data` (
  `key` char(255) NOT NULL COMMENT 'key',
  `name` char(255) COMMENT '名称',
  `data` json COMMENT '数据',
  PRIMARY KEY (`key`)
) ENGINE=INNODB COMMENT '系统json数据';


DROP TABLE IF EXISTS `system_tips_i18n`;
CREATE TABLE `system_tips_i18n` (
  `default` char(255) NOT NULL COMMENT '默认的提示',
  `zh_cn` varchar(1024) COMMENT '中国简中',
  `zh_tw` varchar(1024) COMMENT '台湾繁中',
  `zh_hk` varchar(1024) COMMENT '香港繁中',
  `en_us` varchar(1024) COMMENT '美国英语',
  `ja_jp` varchar(1024) COMMENT '日语',
  `ko_kr` varchar(1024) COMMENT '韩语',
  PRIMARY KEY (`default`)
) ENGINE=INNODB COMMENT '系统提示翻译';


DROP TABLE IF EXISTS `test`;
CREATE TABLE `test` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `data` text COMMENT '测试数据',
  PRIMARY KEY (id)
) ENGINE=INNODB COMMENT '测试';


DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `uid` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT '用户uid',
  `status` smallint NOT NULL DEFAULT -1 COMMENT '状态 -10注销 -5冻结 -2未通过 -1未审核 1正常',
  `inviter_uid` bigint COMMENT '邀请人uid',
  `login_pwd` char(255) COMMENT '登录密码，不一定有，如通过微信登录的就没有',
  `login_pwd_level` smallint unsigned NOT NULL DEFAULT 0 COMMENT '密码安全等级：1-5越大越强',
  `safe_pwd` char(255) COMMENT '[验证登录]安全码',
  `safe_pwd_level` smallint unsigned NOT NULL DEFAULT 0 COMMENT '安全码等级：1-5越大越强',
  `login_name` char(255) NOT NULL COMMENT '[可登录]个性登录名',
  `mobile` varchar (512) COMMENT '[可登录]手机号码,支持多个',
  `email` varchar (512) COMMENT '[可登录]邮箱,支持多个',
  `wx_open_id` varchar (512) COMMENT '[可登录]微信OPENID,支持多个',
  `wx_unionid` varchar (512) COMMENT '[可登录]微信UNIONID,支持多个 只有在公众号多个应用相互关联时(绑定到微信开放平台帐号)后，才会出现该字段，极不可靠',
  `identity_name` char(255) COMMENT '身份证姓名（真实姓名）',
  `identity_card_no` char(255) COMMENT '[可登录]身份证号',
  `identity_card_pic_front` json COMMENT '身份证正面',
  `identity_card_pic_back` json COMMENT '身份证背面',
  `identity_card_pic_take` json COMMENT '身份证手持',
  `identity_card_expire_date` date COMMENT '身份证过期日期',
  `identity_auth_status` smallint NOT NULL DEFAULT -1 COMMENT '实名认证状态 -1未认证 -2未通过 1认证中 10已认证',
  `identity_auth_reject_reason` varchar (1024) COMMENT '实名认证拒绝理由',
  `identity_auth_time` datetime COMMENT '实名认证时间',
  `source` text NOT NULL COMMENT '来源 -1未知',
  `register_ip` text NOT NULL COMMENT '注册ip',
  `latest_login_time` datetime COMMENT '最近一次登录帐号的时间',
  `platform` varchar (1024) COMMENT '平台',
  `permission` varchar (8192) COMMENT '权限允许项',
  `record` json COMMENT '记录',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime COMMENT '更新时间',
  `delete_time` datetime COMMENT '删除时间',
  PRIMARY KEY (uid),
  UNIQUE KEY (login_name),
  UNIQUE KEY (mobile),
  UNIQUE KEY (email),
  UNIQUE KEY (wx_open_id),
  UNIQUE KEY (wx_unionid),
  UNIQUE KEY (identity_card_no)
) ENGINE=INNODB COMMENT '用户基本信息';


DROP TABLE IF EXISTS `user_info`;
CREATE TABLE `user_info` (
  `uid` bigint unsigned NOT NULL COMMENT '用户uid',
  `sex` smallint NOT NULL DEFAULT -1 COMMENT '性别 -1未设置1男2女',
  `birthday` date COMMENT '生日',
  `nickname` char(255) COMMENT '昵称',
  `avatar` json COMMENT '头像',
  PRIMARY KEY (uid)
) ENGINE=INNODB COMMENT '用户次要信息';


DROP TABLE IF EXISTS `user_login_online`;
CREATE TABLE `user_login_online` (
  `platform` char(255) NOT NULL COMMENT '平台',
  `client_id` varchar (512) NOT NULL COMMENT '唯一键值',
  `ip` char(255) NOT NULL COMMENT 'ip',
  `uid` bigint NOT NULL COMMENT 'uid',
  `login_time` datetime NOT NULL COMMENT '登录时间',
  `expire_time` datetime NOT NULL COMMENT '过期时间',
  PRIMARY KEY (platform,client_id)
) ENGINE=INNODB COMMENT '用户登录';


DROP TABLE IF EXISTS `user_login_record`;
CREATE TABLE `user_login_record` (
  `uid` bigint unsigned NOT NULL COMMENT '用户uid',
  `ip` char(255) NOT NULL COMMENT 'ip地址',
  `platform` char(255) NOT NULL COMMENT '平台',
  `create_time` datetime NOT NULL COMMENT '创建时间'
) ENGINE=INNODB COMMENT '用户登录记录';


/* FOREIGN KEY */
ALTER TABLE `user_info` ADD FOREIGN KEY (`uid`) REFERENCES `user` (`uid`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `user_login_record` ADD FOREIGN KEY (`uid`) REFERENCES `user` (`uid`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;

/* DEFAULT DATA */
INSERT INTO user (
  login_pwd,
  login_pwd_level,
  email,
  status,
  login_name,
  platform,
  permission,
  source,
  register_ip,
  create_time
) VALUES (
'faa9a6ddddf57436961bf2d2bf4338df','1',',,,,,mzyhaohaoren@qq.com','1','admin',',,,,,admin',',,,,,admin','system','0.0.0.0',now());
