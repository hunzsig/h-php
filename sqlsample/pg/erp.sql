

/*Table structure for table `erp_shop` */
CREATE TABLE "default".erp_shop (
  id bigserial NOT NULL,
  owner_uid bigint NOT NULL,
  code text NOT NULL,
  name text NOT NULL,
  ip text,
  region text[],
  region_label text,
  address text,
  pic jsonb,
  status text default 'planning' NOT NULL,
  create_time timestamp without time zone NOT NULL,
  PRIMARY KEY (id),
  UNIQUE (code)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".erp_shop IS 'ERP-店铺表';
COMMENT ON COLUMN "default".erp_shop.id IS 'id';
COMMENT ON COLUMN "default".erp_shop.owner_uid IS '拥有者uid';
COMMENT ON COLUMN "default".erp_shop.code IS '唯一码';
COMMENT ON COLUMN "default".erp_shop.name IS '店铺名称';
COMMENT ON COLUMN "default".erp_shop.ip IS 'ip';
COMMENT ON COLUMN "default".erp_shop.region IS '地区';
COMMENT ON COLUMN "default".erp_shop.region_label IS '地区文本';
COMMENT ON COLUMN "default".erp_shop.address IS '地址';
COMMENT ON COLUMN "default".erp_shop.pic IS '店铺图片';
COMMENT ON COLUMN "default".erp_shop.status IS '店铺状态';
COMMENT ON COLUMN "default".erp_shop.create_time IS '创建时间';


/*Table structure for table `erp_ticket_mapping` */
CREATE TABLE "default".erp_ticket_mapping (
  ticket_code text NOT NULL,
  uid bigint NOT NULL,
  batch text NOT NULL,
  shop_id bigint NOT NULL,
  goods_id bigint NOT NULL,
  price_cover numeric(20,6) CHECK(price_cover>=-1),
  price_cover_reason text,
  is_pay smallint NOT NULL DEFAULT -1,
  customer_name text,
  enable_date date NOT NULL,
  pay_time timestamp without time zone,
  create_time timestamp without time zone NOT NULL,
  PRIMARY KEY (ticket_code)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".erp_ticket_mapping IS 'ERP-映射表';
COMMENT ON COLUMN "default".erp_ticket_mapping.ticket_code IS '码';
COMMENT ON COLUMN "default".erp_ticket_mapping.uid IS '录入UID';
COMMENT ON COLUMN "default".erp_ticket_mapping.batch IS '录入批次';
COMMENT ON COLUMN "default".erp_ticket_mapping.shop_id IS '店铺ID';
COMMENT ON COLUMN "default".erp_ticket_mapping.goods_id IS '商品ID';
COMMENT ON COLUMN "default".erp_ticket_mapping.price_cover IS '销售价(覆盖商城价)';
COMMENT ON COLUMN "default".erp_ticket_mapping.price_cover_reason IS '销售价(覆盖理由)(不一定要写理由，例如写个特供版)';
COMMENT ON COLUMN "default".erp_ticket_mapping.is_pay IS '是否已支付';
COMMENT ON COLUMN "default".erp_ticket_mapping.customer_name IS '买家名称';
COMMENT ON COLUMN "default".erp_ticket_mapping.enable_date IS '有效期至';
COMMENT ON COLUMN "default".erp_ticket_mapping.pay_time IS '支付时间';
COMMENT ON COLUMN "default".erp_ticket_mapping.create_time IS '创建时间';


/*Table structure for table `erp_shop_log` */
CREATE TABLE "default".erp_shop_log (
  uid bigint NOT NULL,
  shop_id bigint NOT NULL,
  behaviour text NOT NULL,
  create_time timestamp without time zone NOT NULL
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".erp_shop_log IS 'ERP-店铺日志';
COMMENT ON COLUMN "default".erp_shop_log.uid IS 'uid';
COMMENT ON COLUMN "default".erp_shop_log.shop_id IS '哪个店';
COMMENT ON COLUMN "default".erp_shop_log.behaviour IS '行为 如 openDoor';
COMMENT ON COLUMN "default".erp_shop_log.create_time IS '创建时间';
SELECT create_hypertable ('default.erp_shop_log','create_time');


/*Table structure for table `erp_shop_unpay` */
CREATE TABLE "default".erp_shop_unpay (
  ticket_code text NOT NULL,
  shop_id bigint NOT NULL,
  record_time timestamp without time zone NOT NULL,
  record_timestamp bigint NOT NULL,
  PRIMARY KEY (ticket_code)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".erp_shop_unpay IS 'ERP-店铺未支付表';
COMMENT ON COLUMN "default".erp_shop_unpay.ticket_code IS 'ticket_code';
COMMENT ON COLUMN "default".erp_shop_unpay.shop_id IS '店铺ID';
COMMENT ON COLUMN "default".erp_shop_unpay.record_time IS '记录时间';
COMMENT ON COLUMN "default".erp_shop_unpay.record_timestamp IS '记录时间戳';


/*Table structure for table `erp_ticket` */
CREATE unlogged TABLE "default".erp_active_ticket (
  ticket_code text NOT NULL,
  shop_id bigint NOT NULL,
  create_time timestamp without time zone NOT NULL,
  PRIMARY KEY (ticket_code,shop_id)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".erp_active_ticket IS 'ERP-活动中的ticket表';
COMMENT ON COLUMN "default".erp_active_ticket.ticket_code IS '码';
COMMENT ON COLUMN "default".erp_active_ticket.shop_id IS '商铺ID';
COMMENT ON COLUMN "default".erp_active_ticket.create_time IS '创建时间';


ALTER TABLE "default"."erp_shop" ADD FOREIGN KEY ("owner_uid") REFERENCES "default"."user" ("uid") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."erp_ticket_mapping" ADD FOREIGN KEY ("shop_id") REFERENCES "default"."erp_shop" ("id") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."erp_ticket_mapping" ADD FOREIGN KEY ("goods_id") REFERENCES "default"."goods" ("id") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."erp_shop_unpay" ADD FOREIGN KEY ("ticket_code") REFERENCES "default"."erp_ticket_mapping" ("ticket_code") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."erp_shop_log" ADD FOREIGN KEY ("uid") REFERENCES "default"."user" ("uid") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."erp_shop_log" ADD FOREIGN KEY ("shop_id") REFERENCES "default"."erp_shop" ("id") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;