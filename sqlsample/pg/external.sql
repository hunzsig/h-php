
CREATE TABLE "default".external_config
(
    uid bigint NOT NULL,
    data jsonb,
    PRIMARY KEY (uid)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".external_config IS '第三方配置表';
COMMENT ON COLUMN "default".external_config.uid IS '所属会员';
COMMENT ON COLUMN "default".external_config.data IS '数据';

CREATE TABLE "default".external_log (
  create_time timestamp without time zone NOT NULL,
  behaviour text NOT NULL,
  config text NOT NULL,
  config_actual text,
  params jsonb,
  result jsonb
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".external_log IS '第三方日志表';
COMMENT ON COLUMN "default".external_log.create_time IS '创建时间';
COMMENT ON COLUMN "default".external_log.behaviour IS '行为';
COMMENT ON COLUMN "default".external_log.config IS '请求的配置';
COMMENT ON COLUMN "default".external_log.config_actual IS '实际的配置';
COMMENT ON COLUMN "default".external_log.params IS '参数';
COMMENT ON COLUMN "default".external_log.result IS '结果';
SELECT create_hypertable ('default.external_log','create_time');

CREATE TABLE "default".external_trade_token (
  create_time timestamp without time zone NOT NULL,
  out_trade_no text NOT NULL,
  order_no text NOT NULL,
  type text NOT NULL,
  amount numeric(20,6) NOT NULL,
  config text NOT NULL,
  config_actual text,
  params jsonb,
  callback jsonb,
  is_pay smallint DEFAULT -1 NOT NULL,
  pay_account text,
  pay_time timestamp without time zone,
  UNIQUE (create_time,out_trade_no)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".external_trade_token IS '第三方交易表';
COMMENT ON COLUMN "default".external_trade_token.create_time IS '创建时间';
COMMENT ON COLUMN "default".external_trade_token.out_trade_no IS '对外交易号';
COMMENT ON COLUMN "default".external_trade_token.order_no IS '对内订单号';
COMMENT ON COLUMN "default".external_trade_token.type IS '类型';
COMMENT ON COLUMN "default".external_trade_token.amount IS '金额';
COMMENT ON COLUMN "default".external_trade_token.config IS '请求的配置';
COMMENT ON COLUMN "default".external_trade_token.config_actual IS '实际的配置';
COMMENT ON COLUMN "default".external_trade_token.params IS '请求数据';
COMMENT ON COLUMN "default".external_trade_token.callback IS '回调数据';
COMMENT ON COLUMN "default".external_trade_token.is_pay IS '是否已支付';
COMMENT ON COLUMN "default".external_trade_token.pay_account IS '支付账号';
COMMENT ON COLUMN "default".external_trade_token.pay_time IS '支付时间';
SELECT create_hypertable ('default.external_trade_token','create_time');

CREATE TABLE "default".external_wxpay_promotion_transfers (
  out_trade_no text NOT NULL,
  create_time timestamp without time zone NOT NULL,
  uid bigint NOT NULL,
  open_id text NOT NULL,
  amount numeric(20,6) NOT NULL,
  config text NOT NULL,
  config_actual text,
  params jsonb,
  callback jsonb,
  PRIMARY KEY (out_trade_no)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".external_wxpay_promotion_transfers IS '微信企业付款表';
COMMENT ON COLUMN "default".external_wxpay_promotion_transfers.out_trade_no IS '交易号';
COMMENT ON COLUMN "default".external_wxpay_promotion_transfers.create_time IS '创建时间';
COMMENT ON COLUMN "default".external_wxpay_promotion_transfers.uid IS 'UID';
COMMENT ON COLUMN "default".external_wxpay_promotion_transfers.open_id IS 'openid';
COMMENT ON COLUMN "default".external_wxpay_promotion_transfers.amount IS '金额';
COMMENT ON COLUMN "default".external_wxpay_promotion_transfers.config IS '请求的配置';
COMMENT ON COLUMN "default".external_wxpay_promotion_transfers.config_actual IS '实际的配置';
COMMENT ON COLUMN "default".external_wxpay_promotion_transfers.params IS '请求数据';
COMMENT ON COLUMN "default".external_wxpay_promotion_transfers.callback IS '回调数据';

CREATE TABLE "default".external_wx_user_info (
  config bigint NOT NULL,
  open_id text NOT NULL,
  unionid text,
  sex text,
  nickname text,
  login_name text,
  avatar text,
  language text,
  city text,
  province text,
  country text,
  PRIMARY KEY (config,open_id)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".external_wx_user_info IS '微信账号信息表';
COMMENT ON COLUMN "default".external_wx_user_info.config IS '对应配置';
COMMENT ON COLUMN "default".external_wx_user_info.open_id IS '微信 OPEN ID';
COMMENT ON COLUMN "default".external_wx_user_info.unionid IS '微信 UNIONID';
COMMENT ON COLUMN "default".external_wx_user_info.sex IS '微信性别 -1未设置1男2女';
COMMENT ON COLUMN "default".external_wx_user_info.nickname IS '微信昵称';
COMMENT ON COLUMN "default".external_wx_user_info.login_name IS '微信登录名';
COMMENT ON COLUMN "default".external_wx_user_info.avatar IS '微信头像URL';
COMMENT ON COLUMN "default".external_wx_user_info.language IS '微信客户端语言';
COMMENT ON COLUMN "default".external_wx_user_info.city IS '微信所在城市';
COMMENT ON COLUMN "default".external_wx_user_info.province IS '微信所在省';
COMMENT ON COLUMN "default".external_wx_user_info.country IS '微信所在国家';

ALTER TABLE "default"."external_wx_user_info" ADD FOREIGN KEY ("config") REFERENCES "default"."external_config" ("uid") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;