

/*1对公帐号 2对私帐号 3个人储蓄卡 4个人信用卡*/
CREATE TYPE "default".bank_account_type AS ENUM('1', '2', '3', '4');

/*提现申请状态 -1不通过 1审核中 2审核通过 10提现完毕*/
CREATE TYPE "default".withdraw_apply_status AS ENUM('-1','1','2','10');


/*Table structure for table data_bank_lib */

CREATE TABLE "default".data_bank_lib
(
    code text NOT NULL,
    name text NOT NULL,
    icon_square text,
    icon_rectangle text,
    icon_circular text,
    pay_code text,
    status smallint DEFAULT -1,
    ordering integer DEFAULT 0 NOT NULL,
    PRIMARY KEY (code),
    UNIQUE (name),
    UNIQUE (pay_code)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".data_bank_lib IS '银行库';
COMMENT ON COLUMN "default".data_bank_lib.code IS '银行代码';
COMMENT ON COLUMN "default".data_bank_lib.name IS '银行名称';
COMMENT ON COLUMN "default".data_bank_lib.icon_square IS '图标路径（正方）';
COMMENT ON COLUMN "default".data_bank_lib.icon_rectangle IS '图标路径（长方）';
COMMENT ON COLUMN "default".data_bank_lib.icon_circular IS '图标路径（圆形）';
COMMENT ON COLUMN "default".data_bank_lib.pay_code IS '支付代码';
COMMENT ON COLUMN "default".data_bank_lib.status IS '状态 -1不可用 1可用';
COMMENT ON COLUMN "default".data_bank_lib.ordering IS '排序';

/*Table structure for table finance_bank_account */

CREATE TABLE "default".finance_bank_account (
  id bigserial NOT NULL,
  uid bigint NOT NULL,
  account_bank_code text,
  account_holder text,
  account_no text,
  account_type "default".bank_account_type,
  is_default "default".is_sure NOT NULL DEFAULT '-1',
  PRIMARY KEY (id),
  UNIQUE (account_bank_code,account_holder,account_no)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".finance_bank_account IS '银行卡记录表';
COMMENT ON COLUMN "default".finance_bank_account.id IS 'id';
COMMENT ON COLUMN "default".finance_bank_account.uid IS '用户uid';
COMMENT ON COLUMN "default".finance_bank_account.account_bank_code IS '开户银行代码';
COMMENT ON COLUMN "default".finance_bank_account.account_holder IS '银行帐号持有人';
COMMENT ON COLUMN "default".finance_bank_account.account_no IS '银行账户';
COMMENT ON COLUMN "default".finance_bank_account.account_type IS '帐号类型 1对公帐号 2对私帐号 3个人储蓄卡 4个人信用卡';
COMMENT ON COLUMN "default".finance_bank_account.is_default IS '是否默认帐号 -1否 1是';


CREATE TABLE "default".finance_wallet (
  id bigserial NOT NULL,
  uid bigint NOT NULL,
  pay_password text,
  pay_password_level smallint,
  status "default".is_enable NOT NULL DEFAULT '1',
  balance numeric(20,6) NOT NULL DEFAULT 0.000000,
  balance_lock numeric(20,6) NOT NULL DEFAULT 0.000000,
  credit numeric(20,6) NOT NULL DEFAULT 0.000000,
  freeze_balance numeric(20,6) NOT NULL DEFAULT 0.000000,
  freeze_balance_lock numeric(20,6) NOT NULL DEFAULT 0.000000,
  freeze_credit numeric(20,6) NOT NULL DEFAULT 0.000000,
  create_time timestamp without time zone NOT NULL,
  PRIMARY KEY (id),
  UNIQUE (uid)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".finance_wallet IS '钱包表';
COMMENT ON COLUMN "default".finance_wallet.id IS 'id';
COMMENT ON COLUMN "default".finance_wallet.uid IS '用户ID';
COMMENT ON COLUMN "default".finance_wallet.pay_password IS '支付密码';
COMMENT ON COLUMN "default".finance_wallet.pay_password_level IS '密码等级';
COMMENT ON COLUMN "default".finance_wallet.status IS '状态 1有效 -1无效';
COMMENT ON COLUMN "default".finance_wallet.balance IS '余额（一般只有余额可以提现）';
COMMENT ON COLUMN "default".finance_wallet.balance_lock IS '绑定余额';
COMMENT ON COLUMN "default".finance_wallet.credit IS '积分';
COMMENT ON COLUMN "default".finance_wallet.freeze_balance IS '冻结 余额';
COMMENT ON COLUMN "default".finance_wallet.freeze_balance_lock IS '冻结 绑定余额';
COMMENT ON COLUMN "default".finance_wallet.freeze_credit IS '冻结 积分';
COMMENT ON COLUMN "default".finance_wallet.create_time IS '创建时间';

/*Table structure for table finance_wallet_log */

CREATE TABLE "default".finance_wallet_log (
  uid bigint NOT NULL,
  wallet_id bigint NOT NULL,
  type text NOT NULL,
  description text,
  data jsonb,
  operator_uid bigint,
  balance numeric(20,6) NOT NULL DEFAULT 0.000000,
  balance_lock numeric(20,6) NOT NULL DEFAULT 0.000000,
  credit numeric(20,6) NOT NULL DEFAULT 0.000000,
  freeze_balance numeric(20,6) NOT NULL DEFAULT 0.000000,
  freeze_balance_lock numeric(20,6) NOT NULL DEFAULT 0.000000,
  freeze_credit numeric(20,6) NOT NULL DEFAULT 0.000000,
  create_time timestamp without time zone NOT NULL
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".finance_wallet_log IS '钱包日志表';
COMMENT ON COLUMN "default".finance_wallet_log.uid IS 'uid';
COMMENT ON COLUMN "default".finance_wallet_log.wallet_id IS '对应的钱包ID';
COMMENT ON COLUMN "default".finance_wallet_log.type IS '操作类型';
COMMENT ON COLUMN "default".finance_wallet_log.description IS '描述';
COMMENT ON COLUMN "default".finance_wallet_log.data IS 'json化数据';
COMMENT ON COLUMN "default".finance_wallet_log.operator_uid IS '操作人uid';
COMMENT ON COLUMN "default".finance_wallet_log.balance IS '变动 余额';
COMMENT ON COLUMN "default".finance_wallet_log.balance_lock IS '变动 绑定余额';
COMMENT ON COLUMN "default".finance_wallet_log.credit IS '变动 积分';
COMMENT ON COLUMN "default".finance_wallet_log.freeze_balance IS '变动 冻结余额';
COMMENT ON COLUMN "default".finance_wallet_log.freeze_balance_lock IS '变动 冻结绑定余额';
COMMENT ON COLUMN "default".finance_wallet_log.freeze_credit IS '变动 冻结积分';
COMMENT ON COLUMN "default".finance_wallet_log.create_time IS '创建时间';
SELECT create_hypertable ('default.finance_wallet_log','create_time');


CREATE TABLE "default".finance_withdraw (
  uid bigint NOT NULL,
  pre_min_limit numeric(20,3) NOT NULL DEFAULT 0.010,
  pre_max_limit numeric(20,3) NOT NULL DEFAULT 1000.000,
  day_max_limit numeric(20,3) NOT NULL DEFAULT 10000.000,
  cooling_period integer NOT NULL DEFAULT 0,
  status "default".is_enable DEFAULT '1',
  create_time timestamp without time zone NOT NULL,
  last_apply_time timestamp without time zone,
  PRIMARY KEY (uid)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".finance_withdraw IS '用户提现规范表';
COMMENT ON COLUMN "default".finance_withdraw.uid IS '用户ID';
COMMENT ON COLUMN "default".finance_withdraw.pre_min_limit IS '提现下限,低于不允许提现，默认0.01';
COMMENT ON COLUMN "default".finance_withdraw.pre_max_limit IS '单次提现上限，默认1000';
COMMENT ON COLUMN "default".finance_withdraw.day_max_limit IS '单日提现上限，默认10000';
COMMENT ON COLUMN "default".finance_withdraw.cooling_period IS '提现冷却天数，默认为0';
COMMENT ON COLUMN "default".finance_withdraw.status IS '是否允许提现 1允许 -1不允许';
COMMENT ON COLUMN "default".finance_withdraw.create_time IS '创建时间';
COMMENT ON COLUMN "default".finance_withdraw.last_apply_time IS '最后一次申请提现日期';


CREATE TABLE "default".finance_withdraw_apply (
  id bigserial NOT NULL,
  uid bigint NOT NULL,
  bank_card_info jsonb,
  apply_amount numeric(20,2) NOT NULL DEFAULT 0.01,
  last_handle_time timestamp without time zone,
  status "default".withdraw_apply_status NOT NULL DEFAULT '1',
  reason text,
  create_time timestamp without time zone NOT NULL,
  PRIMARY KEY (id)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".finance_withdraw_apply IS '用户提现申请表';
COMMENT ON COLUMN "default".finance_withdraw_apply.id IS 'ID';
COMMENT ON COLUMN "default".finance_withdraw_apply.uid IS '用户ID';
COMMENT ON COLUMN "default".finance_withdraw_apply.bank_card_info IS '提现银行卡信息';
COMMENT ON COLUMN "default".finance_withdraw_apply.apply_amount IS '申请金额';
COMMENT ON COLUMN "default".finance_withdraw_apply.last_handle_time IS '最后一次处理日期';
COMMENT ON COLUMN "default".finance_withdraw_apply.status IS '申请状态 -1不通过 1审核中 2审核通过 10提现完毕';
COMMENT ON COLUMN "default".finance_withdraw_apply.reason IS '原因';
COMMENT ON COLUMN "default".finance_withdraw_apply.create_time IS '创建时间';


CREATE TABLE "default".finance_withdraw_log (
  uid bigint NOT NULL,
  apply_id bigint NOT NULL,
  operator_uid bigint NOT NULL,
  apply_amount numeric(20,2) NOT NULL DEFAULT 0.00,
  type smallint NOT NULL,
  description text,
  wallet_id bigint,
  data jsonb,
  create_time timestamp without time zone NOT NULL
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".finance_withdraw_log IS '提现日志表';
COMMENT ON COLUMN "default".finance_withdraw_log.uid IS 'uid';
COMMENT ON COLUMN "default".finance_withdraw_log.apply_id IS '申请ID';
COMMENT ON COLUMN "default".finance_withdraw_log.operator_uid IS '操作人ID 如用户ID 管理员ID';
COMMENT ON COLUMN "default".finance_withdraw_log.apply_amount IS '申请金额';
COMMENT ON COLUMN "default".finance_withdraw_log.type IS '操作类型';
COMMENT ON COLUMN "default".finance_withdraw_log.description IS '描述';
COMMENT ON COLUMN "default".finance_withdraw_log.wallet_id IS '对应的钱包ID';
COMMENT ON COLUMN "default".finance_withdraw_log.data IS '其他JSON化数据';
COMMENT ON COLUMN "default".finance_withdraw_log.create_time IS '创建时间';
SELECT create_hypertable ('default.finance_withdraw_log','create_time');

/* FOREIGN KEY */
ALTER TABLE "default"."finance_bank_account" ADD FOREIGN KEY ("uid") REFERENCES "default"."user" ("uid") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."finance_bank_account" ADD FOREIGN KEY ("account_bank_code") REFERENCES "default"."data_bank_lib" ("code") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."finance_wallet" ADD FOREIGN KEY ("uid") REFERENCES "default"."user" ("uid") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."finance_wallet_log" ADD FOREIGN KEY ("wallet_id") REFERENCES "default"."finance_wallet" ("id") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."finance_wallet_log" ADD FOREIGN KEY ("operator_uid") REFERENCES "default"."user" ("uid") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."finance_withdraw" ADD FOREIGN KEY ("uid") REFERENCES "default"."user" ("uid") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."finance_withdraw_apply" ADD FOREIGN KEY ("uid") REFERENCES "default"."user" ("uid") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."finance_withdraw_log" ADD FOREIGN KEY ("apply_id") REFERENCES "default"."finance_withdraw_apply" ("id") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."finance_withdraw_log" ADD FOREIGN KEY ("operator_uid") REFERENCES "default"."user" ("uid") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."finance_withdraw_log" ADD FOREIGN KEY ("wallet_id") REFERENCES "default"."finance_wallet" ("id") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;


/* data */
insert into "default".data_bank_lib(code,name,icon_square,icon_rectangle,icon_circular,pay_code,status) values ('abc','农业银行','/assets/img/bank/icon_square/abc.png','/assets/img/bank/icon_rectangle/abc.png',NULL,'ABC','1');
insert into "default".data_bank_lib(code,name,icon_square,icon_rectangle,icon_circular,pay_code,status) values ('alipay','支付宝','/assets/img/bank/icon_square/alipay.png','/assets/img/bank/icon_rectangle/alipay.png',NULL,'ALIPAY','1');
insert into "default".data_bank_lib(code,name,icon_square,icon_rectangle,icon_circular,pay_code,status) values ('bjb','北京银行','/assets/img/bank/icon_square/bjb.png','/assets/img/bank/icon_rectangle/bjb.png',NULL,NULL,'1');
insert into "default".data_bank_lib(code,name,icon_square,icon_rectangle,icon_circular,pay_code,status) values ('boc','中国银行','/assets/img/bank/icon_square/boc.png','/assets/img/bank/icon_rectangle/boc.png',NULL,'BOC-DEBIT','1');
insert into "default".data_bank_lib(code,name,icon_square,icon_rectangle,icon_circular,pay_code,status) values ('ccb','建设银行','/assets/img/bank/icon_square/ccb.png','/assets/img/bank/icon_rectangle/ccb.png',NULL,'CCB','1');
insert into "default".data_bank_lib(code,name,icon_square,icon_rectangle,icon_circular,pay_code,status) values ('ceb','光大银行','/assets/img/bank/icon_square/ceb.png','/assets/img/bank/icon_rectangle/ceb.png',NULL,'CEB-DEBIT','1');
insert into "default".data_bank_lib(code,name,icon_square,icon_rectangle,icon_circular,pay_code,status) values ('cgb','广发银行','/assets/img/bank/icon_square/cgb.png','/assets/img/bank/icon_rectangle/cgb.png',NULL,'GDB','1');
insert into "default".data_bank_lib(code,name,icon_square,icon_rectangle,icon_circular,pay_code,status) values ('cib','兴业银行','/assets/img/bank/icon_square/cib.png','/assets/img/bank/icon_rectangle/cib.png',NULL,'CIB','1');
insert into "default".data_bank_lib(code,name,icon_square,icon_rectangle,icon_circular,pay_code,status) values ('citic','中信银行','/assets/img/bank/icon_square/citic.png','/assets/img/bank/icon_rectangle/citic.png',NULL,'CITIC','1');
insert into "default".data_bank_lib(code,name,icon_square,icon_rectangle,icon_circular,pay_code,status) values ('cmb','招商银行','/assets/img/bank/icon_square/cmb.png','/assets/img/bank/icon_rectangle/cmb.png',NULL,'CMB-DEBIT','1');
insert into "default".data_bank_lib(code,name,icon_square,icon_rectangle,icon_circular,pay_code,status) values ('cmbc','民生银行','/assets/img/bank/icon_square/cmbc.png','/assets/img/bank/icon_rectangle/cmbc.png',NULL,'CMBC','1');
insert into "default".data_bank_lib(code,name,icon_square,icon_rectangle,icon_circular,pay_code,status) values ('comm','交通银行','/assets/img/bank/icon_square/comm.png','/assets/img/bank/icon_rectangle/comm.png',NULL,'COMM','1');
insert into "default".data_bank_lib(code,name,icon_square,icon_rectangle,icon_circular,pay_code,status) values ('czb','浙商银行','/assets/img/bank/icon_square/czb.png','/assets/img/bank/icon_rectangle/czb.png',NULL,NULL,'1');
insert into "default".data_bank_lib(code,name,icon_square,icon_rectangle,icon_circular,pay_code,status) values ('ecb','恒丰银行','/assets/img/bank/icon_square/ecb.png','/assets/img/bank/icon_rectangle/ecb.png',NULL,NULL,'1');
insert into "default".data_bank_lib(code,name,icon_square,icon_rectangle,icon_circular,pay_code,status) values ('guangzhou','广州银行','/assets/img/bank/icon_square/guangzhou.png','/assets/img/bank/icon_rectangle/guangzhou.png',NULL,NULL,'1');
insert into "default".data_bank_lib(code,name,icon_square,icon_rectangle,icon_circular,pay_code,status) values ('hxb','华夏银行','/assets/img/bank/icon_square/hxb.png','/assets/img/bank/icon_rectangle/hxb.png',NULL,NULL,'1');
insert into "default".data_bank_lib(code,name,icon_square,icon_rectangle,icon_circular,pay_code,status) values ('icbc','工商银行','/assets/img/bank/icon_square/icbc.png','/assets/img/bank/icon_rectangle/icbc.png',NULL,'ICBCB2C','1');
insert into "default".data_bank_lib(code,name,icon_square,icon_rectangle,icon_circular,pay_code,status) values ('nanchang','南昌银行','/assets/img/bank/icon_square/nanchang.png','/assets/img/bank/icon_rectangle/nanchang.png',NULL,NULL,'1');
insert into "default".data_bank_lib(code,name,icon_square,icon_rectangle,icon_circular,pay_code,status) values ('njcb','南京银行','/assets/img/bank/icon_square/njcb.png','/assets/img/bank/icon_rectangle/njcb.png',NULL,NULL,'1');
insert into "default".data_bank_lib(code,name,icon_square,icon_rectangle,icon_circular,pay_code,status) values ('pab','平安银行','/assets/img/bank/icon_square/pab.png','/assets/img/bank/icon_rectangle/pab.png',NULL,'SPABANK','1');
insert into "default".data_bank_lib(code,name,icon_square,icon_rectangle,icon_circular,pay_code,status) values ('psbc','中国邮政储蓄银行','/assets/img/bank/icon_square/psbc.png','/assets/img/bank/icon_rectangle/psbc.png',NULL,'POSTGC','1');
insert into "default".data_bank_lib(code,name,icon_square,icon_rectangle,icon_circular,pay_code,status) values ('shenzhenfazhan','深圳发展银行','/assets/img/bank/icon_square/shenzhenfazhan.png','/assets/img/bank/icon_rectangle/shenzhenfazhan.png',NULL,NULL,'1');
insert into "default".data_bank_lib(code,name,icon_square,icon_rectangle,icon_circular,pay_code,status) values ('spdb','上海浦东发展银行','/assets/img/bank/icon_square/spdb.png','/assets/img/bank/icon_rectangle/spdb.png',NULL,'SPDB','1');
insert into "default".data_bank_lib(code,name,icon_square,icon_rectangle,icon_circular,pay_code,status) values ('tenpay','财付通','/assets/img/bank/icon_square/tenpay.png','/assets/img/bank/icon_rectangle/tenpay.png',NULL,'TENPAY','-1');
insert into "default".data_bank_lib(code,name,icon_square,icon_rectangle,icon_circular,pay_code,status) values ('unionpay','中国银联','/assets/img/bank/icon_square/unionpay.png','/assets/img/bank/icon_rectangle/unionpay.png',NULL,NULL,'1');
insert into "default".data_bank_lib(code,name,icon_square,icon_rectangle,icon_circular,pay_code,status) values ('wxpay','微信支付','/assets/img/bank/icon_square/wxpay.png','/assets/img/bank/icon_rectangle/wxpay.png',NULL,'WXPAY','-1');
insert into "default".data_bank_lib(code,name,icon_square,icon_rectangle,icon_circular,pay_code,status) values ('zgncsyb','广州农村商业银行','/assets/img/bank/icon_square/zgncsyb.png','/assets/img/bank/icon_rectangle/zgncsyb.png',NULL,NULL,'1');
