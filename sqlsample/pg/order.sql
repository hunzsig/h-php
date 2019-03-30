

CREATE TABLE "default".data_express (
  code text NOT NULL,
  name text[] NOT NULL,
  ordering integer DEFAULT 0 NOT NULL,
  PRIMARY KEY (code)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".data_express IS '快递';
COMMENT ON COLUMN "default".data_express.code IS '代码';
COMMENT ON COLUMN "default".data_express.name IS '名称';
COMMENT ON COLUMN "default".data_express.ordering IS '排序';


CREATE TABLE "default".order_freight_rule (
  id bigserial NOT NULL,
  status smallint NOT NULL DEFAULT -1,
  pri smallint NOT NULL DEFAULT 0,
  seller_uid bigint NOT NULL,
  region text NOT NULL DEFAULT '',
  is_free_shipping smallint NOT NULL DEFAULT -1,
  rule_type text,
  first_kilo numeric(20,3),
  fee_first_kilo numeric(20,3),
  fee_per_kilo numeric(20,3),
  fee_first_qty numeric(20,3),
  fee_per_qty numeric(20,3),
  volume_var bigint,
  free_shipping_amount numeric(20,3),
  free_shipping_qty bigint,
  free_shipping_kilo numeric(20,3),
  create_time timestamp without time zone NOT NULL,
  PRIMARY KEY (id)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".order_freight_rule IS '订单运费规则';
COMMENT ON COLUMN "default".order_freight_rule.id IS '自增ID';
COMMENT ON COLUMN "default".order_freight_rule.status IS '状态';
COMMENT ON COLUMN "default".order_freight_rule.pri IS '优先级';
COMMENT ON COLUMN "default".order_freight_rule.seller_uid IS '指定的卖家uid';
COMMENT ON COLUMN "default".order_freight_rule.region IS '指定的地区';
COMMENT ON COLUMN "default".order_freight_rule.is_free_shipping IS '是否包邮';
COMMENT ON COLUMN "default".order_freight_rule.rule_type IS '邮费规则类型';
COMMENT ON COLUMN "default".order_freight_rule.first_kilo IS '首重重量';
COMMENT ON COLUMN "default".order_freight_rule.fee_first_kilo IS '首重费用，优先级高的覆盖低的';
COMMENT ON COLUMN "default".order_freight_rule.fee_per_kilo IS '每kg多少费用，如果有首重，从首重后1kg开始算，优先级高的覆盖低的';
COMMENT ON COLUMN "default".order_freight_rule.fee_first_qty IS '首件费用';
COMMENT ON COLUMN "default".order_freight_rule.fee_per_qty IS '续件费用';
COMMENT ON COLUMN "default".order_freight_rule.volume_var IS '邮费计算 - 体积参数（长*宽*高）/var = 重量';
COMMENT ON COLUMN "default".order_freight_rule.free_shipping_amount IS '包邮金额(超过的话)';
COMMENT ON COLUMN "default".order_freight_rule.free_shipping_qty IS '包邮购买数量(超过的话)';
COMMENT ON COLUMN "default".order_freight_rule.free_shipping_kilo IS '包邮重量(不超过的话)';
COMMENT ON COLUMN "default".order_freight_rule.create_time IS '创建时间';


CREATE TABLE "default"."order" (
  id bigserial NOT NULL,
  uid bigint NOT NULL,
  no text NOT NULL,
  type text NOT NULL,
  name text,
  description text,
  status smallint NOT NULL DEFAULT -1,
  trade_place text,
  trade_terminal text,
  total_amount numeric(20,2) NOT NULL CHECK(total_amount >= 0),
  total_freight numeric(20,2) NOT NULL CHECK(total_freight >= 0),
  total_favorable numeric(20,2) NOT NULL CHECK(total_favorable >= 0),
  total_weight numeric(20,2) NOT NULL CHECK(total_weight >= 0),
  total_qty bigint NOT NULL CHECK(total_qty >= 0),
  pay_amount numeric(20,2) NOT NULL CHECK(pay_amount >= 0),
  pay_status smallint NOT NULL DEFAULT -1,
  pay_type text,
  pic text,
  shop_id bigint,
  buyer_remarks text,
  seller_remarks text,
  pay_return_data json,
  shipping_region text,
  shipping_address text,
  contact_user text,
  contact_mobile text,
  create_time timestamp without time zone NOT NULL,
  cancel_time timestamp without time zone,
  auto_cancel_time timestamp without time zone,
  pay_time timestamp without time zone,
  sent_time timestamp without time zone,
  sent_operator_uid bigint,
  sent_express_code text,
  sent_express_no text,
  sent_remarks text,
  received_time timestamp without time zone,
  evaluate_time timestamp without time zone,
  PRIMARY KEY (id)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default"."order" IS '订单';
COMMENT ON COLUMN "default"."order".id IS '自增ID';
COMMENT ON COLUMN "default"."order".uid IS '下单人';
COMMENT ON COLUMN "default"."order".no IS '订单号';
COMMENT ON COLUMN "default"."order".type IS '订单类型';
COMMENT ON COLUMN "default"."order".name IS '订单交易名';
COMMENT ON COLUMN "default"."order".description IS '订单简述';
COMMENT ON COLUMN "default"."order".status IS '订单状态';
COMMENT ON COLUMN "default"."order".trade_place IS '交易地点';
COMMENT ON COLUMN "default"."order".trade_terminal IS '交易终端型号';
COMMENT ON COLUMN "default"."order".total_amount IS '总金额';
COMMENT ON COLUMN "default"."order".total_freight IS '总运费';
COMMENT ON COLUMN "default"."order".total_favorable IS '总优惠';
COMMENT ON COLUMN "default"."order".total_weight IS '总重量';
COMMENT ON COLUMN "default"."order".total_qty IS '总件数';
COMMENT ON COLUMN "default"."order".pay_amount IS '需要支付的金额';
COMMENT ON COLUMN "default"."order".pay_status IS '支付无';
COMMENT ON COLUMN "default"."order".pay_type IS '支付类型';
COMMENT ON COLUMN "default"."order".pic IS '订单图片';
COMMENT ON COLUMN "default"."order".shop_id IS '店铺id';
COMMENT ON COLUMN "default"."order".buyer_remarks IS '买家备注';
COMMENT ON COLUMN "default"."order".seller_remarks IS '卖家备注';
COMMENT ON COLUMN "default"."order".pay_return_data IS '回调信息（序列化）';
COMMENT ON COLUMN "default"."order".shipping_region IS '地区';
COMMENT ON COLUMN "default"."order".shipping_address IS '具体地址';
COMMENT ON COLUMN "default"."order".contact_user IS '联系人';
COMMENT ON COLUMN "default"."order".contact_mobile IS '联系电话';
COMMENT ON COLUMN "default"."order".create_time IS '创建时间';
COMMENT ON COLUMN "default"."order".cancel_time IS '取消时间';
COMMENT ON COLUMN "default"."order".auto_cancel_time IS '自动取消时间';
COMMENT ON COLUMN "default"."order".pay_time IS '支付时间';
COMMENT ON COLUMN "default"."order".sent_time IS '发货时间';
COMMENT ON COLUMN "default"."order".sent_operator_uid IS '发货操作人uid';
COMMENT ON COLUMN "default"."order".sent_express_code IS '发货快递代码';
COMMENT ON COLUMN "default"."order".sent_express_no IS '发货快递号';
COMMENT ON COLUMN "default"."order".sent_remarks IS '发货备注';
COMMENT ON COLUMN "default"."order".received_time IS '收货时间';
COMMENT ON COLUMN "default"."order".evaluate_time IS '评价时间';


CREATE TABLE "default".order_items (
  order_id bigint NOT NULL,
  seller_uid bigint NOT NULL,
  seller_name text NOT NULL,
  item_amount numeric(20,2) NOT NULL CHECK (item_amount>=0),
  item_amount_origin numeric(20,2) CHECK (item_amount_origin>=0),
  item_amount_before_favour numeric(20,2) CHECK (item_amount_before_favour>=0),
  item_weight numeric(20,2) NOT NULL CHECK (item_weight>=0),
  item_qty bigint NOT NULL CHECK (item_qty>=1),
  item_name text NOT NULL,
  item_total_amount numeric(20,2) NOT NULL CHECK (item_total_amount>=0),
  item_total_weight numeric(20,2) NOT NULL CHECK (item_total_weight>=0),
  item_data text,
  goods_id bigint,
  is_evaluation "default".is_sure NOT NULL DEFAULT '-1',
  refund_qty bigint NOT NULL DEFAULT 0 CHECK (refund_qty>=0)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".order_items IS '订单内项';
COMMENT ON COLUMN "default".order_items.order_id IS '订单id';
COMMENT ON COLUMN "default".order_items.seller_uid IS '卖家uid';
COMMENT ON COLUMN "default".order_items.seller_name IS '卖家名称';
COMMENT ON COLUMN "default".order_items.item_amount IS '物件金额';
COMMENT ON COLUMN "default".order_items.item_amount_origin IS '物件金额（源）';
COMMENT ON COLUMN "default".order_items.item_amount_before_favour IS '物件金额（优惠之前）';
COMMENT ON COLUMN "default".order_items.item_weight IS '物件重量';
COMMENT ON COLUMN "default".order_items.item_qty IS '物件数量，大于等于1';
COMMENT ON COLUMN "default".order_items.item_name IS '物件名称，如商品则是商品名称';
COMMENT ON COLUMN "default".order_items.item_total_amount IS '物件总金额';
COMMENT ON COLUMN "default".order_items.item_total_weight IS '物件总重量';
COMMENT ON COLUMN "default".order_items.item_data IS '物件数据';
COMMENT ON COLUMN "default".order_items.goods_id IS '商品ID，方便查询';
COMMENT ON COLUMN "default".order_items.is_evaluation IS '是否已评价';
COMMENT ON COLUMN "default".order_items.refund_qty IS '已退款退货数量';


CREATE TABLE "default".order_log (
  order_id bigint NOT NULL,
  operator_uid bigint,
  operator text NOT NULL,
  data jsonb,
  log_time timestamp without time zone NOT NULL
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".order_log IS '订单日志';
COMMENT ON COLUMN "default".order_log.order_id IS '订单id';
COMMENT ON COLUMN "default".order_log.operator_uid IS '操作人id';
COMMENT ON COLUMN "default".order_log.operator IS '操作';
COMMENT ON COLUMN "default".order_log.data IS '数据';
COMMENT ON COLUMN "default".order_log.log_time IS '日志时间';
SELECT create_hypertable ('default.order_log','log_time');


CREATE TABLE "default".order_refund (
  id bigserial NOT NULL,
  uid bigint NOT NULL,
  from_order_id bigint NOT NULL,
  from_order_no text NOT NULL,
  no text NOT NULL,
  type text NOT NULL,
  name text,
  description text,
  status smallint NOT NULL DEFAULT -1,
  item_id bigint NOT NULL,
  total_qty bigint NOT NULL,
  total_amount numeric(20,2) NOT NULL,
  cancel_time timestamp without time zone,
  auto_cancel_time timestamp without time zone,
  apply_time timestamp without time zone NOT NULL,
  apply_remark text,
  replace_or_repair_remark text,
  agree_time timestamp without time zone,
  agree_remark text,
  agree_operator_uid bigint,
  reject_time timestamp without time zone,
  reject_remark text,
  reject_operator_uid bigint,
  sent_time timestamp without time zone,
  sent_express_code text,
  sent_express_no text,
  received_time timestamp without time zone,
  sent_back_time timestamp without time zone,
  sent_back_express_code text,
  sent_back_express_no text,
  sent_back_remarks text,
  finish_time timestamp without time zone,
  PRIMARY KEY (id)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".order_refund IS '退款单';
COMMENT ON COLUMN "default".order_refund.id IS '自增ID';
COMMENT ON COLUMN "default".order_refund.uid IS '申请售后单人';
COMMENT ON COLUMN "default".order_refund.from_order_id IS '从哪个订单来';
COMMENT ON COLUMN "default".order_refund.from_order_no IS '从哪个订单号来';
COMMENT ON COLUMN "default".order_refund.no IS '退单号';
COMMENT ON COLUMN "default".order_refund.type IS '订单类型';
COMMENT ON COLUMN "default".order_refund.name IS '订单交易名';
COMMENT ON COLUMN "default".order_refund.description IS '订单简述';
COMMENT ON COLUMN "default".order_refund.status IS '订单状态';
COMMENT ON COLUMN "default".order_refund.item_id IS '物件在订单中的itemid';
COMMENT ON COLUMN "default".order_refund.total_qty IS '退货退款货物数量';
COMMENT ON COLUMN "default".order_refund.total_amount IS '退货退款货物价值';
COMMENT ON COLUMN "default".order_refund.cancel_time IS '取消时间';
COMMENT ON COLUMN "default".order_refund.auto_cancel_time IS '自动取消时间';
COMMENT ON COLUMN "default".order_refund.apply_time IS '退款申请时间';
COMMENT ON COLUMN "default".order_refund.apply_remark IS '退款申请备注';
COMMENT ON COLUMN "default".order_refund.replace_or_repair_remark IS '换货维修备注';
COMMENT ON COLUMN "default".order_refund.agree_time IS '退款同意时间';
COMMENT ON COLUMN "default".order_refund.agree_remark IS '退款同意备注';
COMMENT ON COLUMN "default".order_refund.agree_operator_uid IS '退款同意操作人uid';
COMMENT ON COLUMN "default".order_refund.reject_time IS '退款不同意时间';
COMMENT ON COLUMN "default".order_refund.reject_remark IS '退款不同意备注';
COMMENT ON COLUMN "default".order_refund.reject_operator_uid IS '退款不同意操作人uid';
COMMENT ON COLUMN "default".order_refund.sent_time IS '发货时间';
COMMENT ON COLUMN "default".order_refund.sent_express_code IS '退款货快递代码';
COMMENT ON COLUMN "default".order_refund.sent_express_no IS '退款货快递号';
COMMENT ON COLUMN "default".order_refund.received_time IS '收货时间';
COMMENT ON COLUMN "default".order_refund.sent_back_time IS '返货时间';
COMMENT ON COLUMN "default".order_refund.sent_back_express_code IS '售后返回快递码（换货/维修）';
COMMENT ON COLUMN "default".order_refund.sent_back_express_no IS '售后返回快递单号（换货/维修）';
COMMENT ON COLUMN "default".order_refund.sent_back_remarks IS '售后返回备注';
COMMENT ON COLUMN "default".order_refund.finish_time IS '退款完成时间';


CREATE TABLE "default".order_refund_log (
  order_id bigint NOT NULL,
  operator_uid bigint,
  operator text NOT NULL,
  data jsonb,
  log_time timestamp without time zone NOT NULL
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".order_refund_log IS '退款单日志';
COMMENT ON COLUMN "default".order_refund_log.order_id IS '订单id';
COMMENT ON COLUMN "default".order_refund_log.operator_uid IS '操作人id';
COMMENT ON COLUMN "default".order_refund_log.operator IS '操作';
COMMENT ON COLUMN "default".order_refund_log.data IS '数据';
COMMENT ON COLUMN "default".order_refund_log.log_time IS '日志时间';
SELECT create_hypertable ('default.order_refund_log','log_time');


CREATE TABLE "default".order_shopping_cart (
  id bigserial NOT NULL,
  uid bigint NOT NULL,
  goods_id bigint NOT NULL,
  qty bigint NOT NULL,
  PRIMARY KEY (id),
  UNIQUE (uid,goods_id)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".order_shopping_cart IS '购物车表';
COMMENT ON COLUMN "default".order_shopping_cart.uid IS '用户id';
COMMENT ON COLUMN "default".order_shopping_cart.goods_id IS '商品id';
COMMENT ON COLUMN "default".order_shopping_cart.qty IS '购买数量';

/*Table structure for table user_shipping_address */

CREATE TABLE "default".user_shipping_address (
  id bigserial NOT NULL,
  uid bigint NOT NULL,
  region text NOT NULL,
  address text NOT NULL,
  contact_user text NOT NULL,
  contact_mobile text NOT NULL,
  tag text,
  is_default smallint NOT NULL DEFAULT -1,
  PRIMARY KEY (id)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".user_shipping_address IS '收货地址表';
COMMENT ON COLUMN "default".user_shipping_address.id IS '自增ID';
COMMENT ON COLUMN "default".user_shipping_address.uid IS '用户uid';
COMMENT ON COLUMN "default".user_shipping_address.region IS '地区';
COMMENT ON COLUMN "default".user_shipping_address.address IS '具体地址';
COMMENT ON COLUMN "default".user_shipping_address.contact_user IS '联系人';
COMMENT ON COLUMN "default".user_shipping_address.contact_mobile IS '联系电话';
COMMENT ON COLUMN "default".user_shipping_address.tag IS '自定义标签';
COMMENT ON COLUMN "default".user_shipping_address.is_default IS '是否默认';


ALTER TABLE "default"."order" ADD FOREIGN KEY ("uid") REFERENCES "default"."user" ("uid") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."order" ADD FOREIGN KEY ("sent_express_code") REFERENCES "default"."data_express" ("code") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."order_items" ADD FOREIGN KEY ("order_id") REFERENCES "default"."order" ("id") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."order_items" ADD FOREIGN KEY ("seller_uid") REFERENCES "default"."user" ("uid") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."order_log" ADD FOREIGN KEY ("order_id") REFERENCES "default"."order" ("id") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."order_refund" ADD FOREIGN KEY ("uid") REFERENCES "default"."user" ("uid") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."order_refund" ADD FOREIGN KEY ("from_order_id") REFERENCES "default"."order" ("id") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."order_refund" ADD FOREIGN KEY ("sent_express_code") REFERENCES "default"."data_express" ("code") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."order_refund" ADD FOREIGN KEY ("sent_back_express_code") REFERENCES "default"."data_express" ("code") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."order_refund_log" ADD FOREIGN KEY ("order_id") REFERENCES "default"."order_refund" ("id") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."order_shopping_cart" ADD FOREIGN KEY ("uid") REFERENCES "default"."user" ("uid") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."order_shopping_cart" ADD FOREIGN KEY ("goods_id") REFERENCES "default"."goods" ("id") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."user_shipping_address" ADD FOREIGN KEY ("uid") REFERENCES "default"."user" ("uid") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;


insert into "default".data_express(code,name) values ('shunfeng','{顺丰速运}');
insert into "default".data_express(code,name) values ('yuantong','{圆通快递}');
insert into "default".data_express(code,name) values ('shentong','{申通快递}');
insert into "default".data_express(code,name) values ('yunda','{韵达快递}');
insert into "default".data_express(code,name) values ('tiantian','{天天快递}');
insert into "default".data_express(code,name) values ('zhongtong','{中通快递}');
insert into "default".data_express(code,name) values ('anxindakuaixi','{安信达}');
insert into "default".data_express(code,name) values ('huitongkuaidi','{百世汇通}');
insert into "default".data_express(code,name) values ('debangwuliu','{德邦物流}');
insert into "default".data_express(code,name) values ('ems','{EMS}');
insert into "default".data_express(code,name) values ('youshuwuliu','{优速物流}');
insert into "default".data_express(code,name) values ('xinfengwuliu','{信丰物流}');
insert into "default".data_express(code,name) values ('kuaijiesudi','{快捷速递}');
