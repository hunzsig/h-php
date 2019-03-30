
CREATE TABLE "default".finance_commission_installment (
  id bigserial NOT NULL,
  uid bigint NOT NULL,
  type text NOT NULL,
  description text,
  balance numeric(20,6) NOT NULL,
  balance_lock numeric(20,6) NOT NULL,
  credit numeric(20,6) NOT NULL,
  base_balance numeric(20,6) NOT NULL,
  base_balance_lock numeric(20,6) NOT NULL,
  base_credit numeric(20,6) NOT NULL,
  base_percent_balance numeric(8,5) NOT NULL,
  base_percent_balance_lock numeric(8,5) NOT NULL,
  base_percent_credit numeric(8,5) NOT NULL,
  start_number_of_installments smallint NOT NULL DEFAULT 0 CHECK(start_number_of_installments>=0 AND start_number_of_installments<=number_of_installments),
  current_number_of_installments smallint NOT NULL CHECK(current_number_of_installments>=0 AND current_number_of_installments<=number_of_installments),
  installments_unit "default".datetime_unit NOT NULL DEFAULT 'month',
  installments_unit_length bigint NOT NULL DEFAULT 1 CHECK(installments_unit_length>=1),
  number_of_installments smallint NOT NULL CHECK(number_of_installments>=1),
  is_enable smallint NOT NULL DEFAULT 1,
  is_over smallint NOT NULL DEFAULT -1,
  from_uid bigint,
  order_no text,
  data jsonb,
  create_time timestamp without time zone NOT NULL,
  estimated_time timestamp without time zone NOT NULL,
  last_commission_time timestamp without time zone,
  PRIMARY KEY (id)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".finance_commission_installment IS '银行卡记录表';
COMMENT ON COLUMN "default".finance_commission_installment.id IS 'id';
COMMENT ON COLUMN "default".finance_commission_installment.uid IS 'uid';
COMMENT ON COLUMN "default".finance_commission_installment.type IS '类型';
COMMENT ON COLUMN "default".finance_commission_installment.description IS '描述';
COMMENT ON COLUMN "default".finance_commission_installment.balance IS '每期分 余额';
COMMENT ON COLUMN "default".finance_commission_installment.balance_lock IS '每期分 绑定余额';
COMMENT ON COLUMN "default".finance_commission_installment.credit IS '每期分 积分';
COMMENT ON COLUMN "default".finance_commission_installment.base_balance IS '分佣基础 余额';
COMMENT ON COLUMN "default".finance_commission_installment.base_balance_lock IS '分佣基础 绑定余额';
COMMENT ON COLUMN "default".finance_commission_installment.base_credit IS '分佣基础 积分';
COMMENT ON COLUMN "default".finance_commission_installment.base_percent_balance IS '计算比例 余额';
COMMENT ON COLUMN "default".finance_commission_installment.base_percent_balance_lock IS '计算比例 绑定余额';
COMMENT ON COLUMN "default".finance_commission_installment.base_percent_credit IS '计算比例 积分';
COMMENT ON COLUMN "default".finance_commission_installment.start_number_of_installments IS '开始期数';
COMMENT ON COLUMN "default".finance_commission_installment.current_number_of_installments IS '当前期数';
COMMENT ON COLUMN "default".finance_commission_installment.installments_unit IS '期单位 year month week day等';
COMMENT ON COLUMN "default".finance_commission_installment.installments_unit_length IS '期单位长度,最少为1';
COMMENT ON COLUMN "default".finance_commission_installment.number_of_installments IS '分期总期数,最少为1';
COMMENT ON COLUMN "default".finance_commission_installment.is_enable IS '是否有效';
COMMENT ON COLUMN "default".finance_commission_installment.is_over IS '是否已结束';
COMMENT ON COLUMN "default".finance_commission_installment.from_uid IS '从何人来';
COMMENT ON COLUMN "default".finance_commission_installment.order_no IS '订单号';
COMMENT ON COLUMN "default".finance_commission_installment.data IS '记录数据';
COMMENT ON COLUMN "default".finance_commission_installment.create_time IS '创建时间';
COMMENT ON COLUMN "default".finance_commission_installment.estimated_time IS '预计下次分佣时间';
COMMENT ON COLUMN "default".finance_commission_installment.last_commission_time IS '最后一次分佣时间';

ALTER TABLE "default"."finance_commission_installment" ADD FOREIGN KEY ("uid") REFERENCES "default"."user" ("uid") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."finance_commission_installment" ADD FOREIGN KEY ("from_uid") REFERENCES "default"."user" ("uid") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;