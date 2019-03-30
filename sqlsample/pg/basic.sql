
DROP SCHEMA IF EXISTS "default" CASCADE;
CREATE SCHEMA "default";

CREATE EXTENSION IF NOT EXISTS timescaledb CASCADE;

/*时间单位*/
CREATE TYPE "default".datetime_unit AS ENUM('year','month','week','day','hour','minute');

CREATE TABLE "default".system_auth (
  id bigserial NOT NULL,
  uid bigint,
  auth_name text NOT NULL,
  auth_code text NOT NULL,
  type smallint NOT NULL,
  create_time timestamp without time zone NOT NULL,
  PRIMARY KEY (id),
  UNIQUE (auth_code),
  UNIQUE (auth_name,uid)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".system_auth IS '验证码记录表';
COMMENT ON COLUMN "default".system_auth.id IS 'ID';
COMMENT ON COLUMN "default".system_auth.uid IS '用户ID，可以为空';
COMMENT ON COLUMN "default".system_auth.auth_name IS '验证名称，如手机号 邮箱地址等';
COMMENT ON COLUMN "default".system_auth.auth_code IS '验证码';
COMMENT ON COLUMN "default".system_auth.type IS '验证类型';
COMMENT ON COLUMN "default".system_auth.create_time IS '创建时间';

/*Table structure for table system_auth_record */

CREATE TABLE "default".system_auth_record (
  name text NOT NULL,
  create_time timestamp without time zone NOT NULL
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".system_auth_record IS '验证记录表';
COMMENT ON COLUMN "default".system_auth_record.name IS '记录名称';
COMMENT ON COLUMN "default".system_auth_record.create_time IS '记录时间';
SELECT create_hypertable ('default.system_auth_record','create_time');


CREATE TABLE "default".system_data
(
    key text NOT NULL,
    name text,
    data jsonb,
    PRIMARY KEY (key)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".system_data IS '系统json数据';
COMMENT ON COLUMN "default".system_data.key IS 'key';
COMMENT ON COLUMN "default".system_data.name IS '名称';
COMMENT ON COLUMN "default".system_data.data IS '数据';

/*Table structure for table system_tips_i18n */

CREATE TABLE "default".system_tips_i18n (
  "default" text NOT NULL,
  zh_cn text,
  zh_tw text,
  zh_hk text,
  en_us text,
  ja_jp text,
  ko_kr text,
  PRIMARY KEY ("default")
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".system_tips_i18n IS '系统提示翻译';
COMMENT ON COLUMN "default".system_tips_i18n."default" IS '默认的提示';
COMMENT ON COLUMN "default".system_tips_i18n.zh_cn IS '中国简中';
COMMENT ON COLUMN "default".system_tips_i18n.zh_tw IS '台湾繁中';
COMMENT ON COLUMN "default".system_tips_i18n.zh_hk IS '香港繁中';
COMMENT ON COLUMN "default".system_tips_i18n.en_us IS '美国英语';
COMMENT ON COLUMN "default".system_tips_i18n.ja_jp IS '日语';
COMMENT ON COLUMN "default".system_tips_i18n.ko_kr IS '韩语';

/*Table structure for table test */

CREATE TABLE "default".test (
  id bigserial NOT NULL,
  data text,
  PRIMARY KEY (id)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".test IS '测试';
COMMENT ON COLUMN "default".test.data IS '测试数据';

/*Table structure for table user */

CREATE TABLE "default".user (
  uid bigserial NOT NULL,
  status smallint NOT NULL DEFAULT -1,
  inviter_uid bigint,
  login_pwd text,
  login_pwd_level smallint NOT NULL DEFAULT 0 CHECK(login_pwd_level>=0),
  safe_pwd text,
  safe_pwd_level smallint NOT NULL DEFAULT 0 CHECK(safe_pwd_level>=0),
  login_name text NOT NULL,
  mobile text[],
  email text[],
  wx_open_id text[],
  wx_unionid text[],
  identity_name text,
  identity_card_no text,
  identity_card_pic_front jsonb,
  identity_card_pic_back jsonb,
  identity_card_pic_take jsonb,
  identity_card_expire_date date,
  identity_auth_status smallint NOT NULL DEFAULT -1,
  identity_auth_reject_reason text,
  identity_auth_time timestamp without time zone,
  source text NOT NULL,
  register_ip text NOT NULL,
  latest_login_time timestamp without time zone,
  platform text[],
  permission text[],
  record jsonb,
  create_time timestamp without time zone NOT NULL,
  update_time timestamp without time zone,
  delete_time timestamp without time zone,
  PRIMARY KEY (uid),
  UNIQUE (login_name),
  UNIQUE (mobile),
  UNIQUE (email),
  UNIQUE (wx_open_id),
  UNIQUE (wx_unionid),
  UNIQUE (identity_card_no)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".user IS '用户基本信息';
COMMENT ON COLUMN "default".user.uid IS '用户uid';
COMMENT ON COLUMN "default".user.status IS '状态 -10注销 -5冻结 -2未通过 -1未审核 1正常';
COMMENT ON COLUMN "default".user.inviter_uid IS '邀请人uid';
COMMENT ON COLUMN "default".user.login_pwd IS '登录密码，不一定有，如通过微信登录的就没有';
COMMENT ON COLUMN "default".user.login_pwd_level IS '密码安全等级：1-5越大越强';
COMMENT ON COLUMN "default".user.login_name IS '[可登录]个性登录名';
COMMENT ON COLUMN "default".user.safe_pwd IS '[验证登录]安全码';
COMMENT ON COLUMN "default".user.safe_pwd_level IS '安全码等级：1-5越大越强';
COMMENT ON COLUMN "default".user.mobile IS '[可登录]手机号码,支持多个';
COMMENT ON COLUMN "default".user.email IS '[可登录]邮箱,支持多个';
COMMENT ON COLUMN "default".user.wx_open_id IS '[可登录]微信OPENID,支持多个';
COMMENT ON COLUMN "default".user.wx_unionid IS '[可登录]微信UNIONID,支持多个 只有在公众号多个应用相互关联时(绑定到微信开放平台帐号)后，才会出现该字段，极不可靠';
COMMENT ON COLUMN "default".user.identity_name IS '身份证姓名（真实姓名）';
COMMENT ON COLUMN "default".user.identity_card_no IS '[可登录]身份证号';
COMMENT ON COLUMN "default".user.identity_card_pic_front IS '身份证正面';
COMMENT ON COLUMN "default".user.identity_card_pic_back IS '身份证背面';
COMMENT ON COLUMN "default".user.identity_card_pic_take IS '身份证手持';
COMMENT ON COLUMN "default".user.identity_card_expire_date IS '身份证过期日期';
COMMENT ON COLUMN "default".user.identity_auth_status IS '实名认证状态 -1未认证 -2未通过 1认证中 10已认证';
COMMENT ON COLUMN "default".user.identity_auth_reject_reason IS '实名认证拒绝理由';
COMMENT ON COLUMN "default".user.identity_auth_time IS '实名认证时间';
COMMENT ON COLUMN "default".user.source IS '来源 -1未知';
COMMENT ON COLUMN "default".user.register_ip IS '注册ip';
COMMENT ON COLUMN "default".user.latest_login_time IS '最近一次登录帐号的时间';
COMMENT ON COLUMN "default".user.platform IS '平台';
COMMENT ON COLUMN "default".user.permission IS '权限允许项';
COMMENT ON COLUMN "default".user.record IS '记录';
COMMENT ON COLUMN "default".user.create_time IS '创建时间';
COMMENT ON COLUMN "default".user.update_time IS '更新时间';
COMMENT ON COLUMN "default".user.delete_time IS '删除时间';


CREATE TABLE "default".user_info (
  uid bigint NOT NULL,
  sex smallint NOT NULL DEFAULT -1,
  birthday date,
  nickname text,
  avatar jsonb,
  PRIMARY KEY (uid)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".user_info IS '用户次要信息';
COMMENT ON COLUMN "default".user_info.uid IS '用户uid';
COMMENT ON COLUMN "default".user_info.sex IS '性别 -1未设置1男2女';
COMMENT ON COLUMN "default".user_info.birthday IS '生日';
COMMENT ON COLUMN "default".user_info.nickname IS '昵称';
COMMENT ON COLUMN "default".user_info.avatar IS '头像';

/*Table structure for table user_login_online */

CREATE unlogged TABLE "default".user_login_online (
  platform text NOT NULL,
  client_id text NOT NULL,
  ip text NOT NULL,
  uid bigint NOT NULL,
  login_time timestamp without time zone NOT NULL,
  active_time timestamp without time zone,
  expire_time timestamp without time zone NOT NULL,
  PRIMARY KEY (platform,client_id)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".user_login_online IS '用户一般会员';
COMMENT ON COLUMN "default".user_login_online.platform IS '平台';
COMMENT ON COLUMN "default".user_login_online.client_id IS '唯一键值';
COMMENT ON COLUMN "default".user_login_online.ip IS 'ip';
COMMENT ON COLUMN "default".user_login_online.uid IS 'uid';
COMMENT ON COLUMN "default".user_login_online.login_time IS '登录时间';
COMMENT ON COLUMN "default".user_login_online.active_time IS '活动时间';
COMMENT ON COLUMN "default".user_login_online.expire_time IS '过期时间';

/*Table structure for table user_login_record */

CREATE TABLE "default".user_login_record (
  uid bigint NOT NULL,
  ip text NOT NULL,
  platform text NOT NULL,
  create_time timestamp without time zone NOT NULL
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".user_login_record IS '用户一般会员';
COMMENT ON COLUMN "default".user_login_record.uid IS '用户uid';
COMMENT ON COLUMN "default".user_login_record.ip IS 'ip地址';
COMMENT ON COLUMN "default".user_login_record.platform IS '平台';
COMMENT ON COLUMN "default".user_login_record.create_time IS '创建时间';
SELECT create_hypertable ('default.user_login_record','create_time');


/* FOREIGN KEY */
ALTER TABLE "default"."user_info" ADD FOREIGN KEY ("uid") REFERENCES "default"."user" ("uid") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."user_login_record" ADD FOREIGN KEY ("uid") REFERENCES "default"."user" ("uid") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;

/* DEFAULT DATA */
INSERT INTO "default".user (
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
'faa9a6ddddf57436961bf2d2bf4338df','1','{mzyhaohaoren@qq.com}','1','admin','{admin}','{admin}','system','0.0.0.0',now());


