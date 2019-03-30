
/*评价 好中差评 positive/moderate/negative*/
CREATE TYPE "default".evaluate AS ENUM('positive','moderate','negative');


CREATE TABLE "default".goods_brand (
  id bigserial NOT NULL,
  name text NOT NULL,
  name_eng text,
  country text,
  pic jsonb,
  PRIMARY KEY (id),
  UNIQUE (name),
  UNIQUE (name_eng)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".goods_brand IS '商品牌子';
COMMENT ON COLUMN "default".goods_brand.id IS '自增ID';
COMMENT ON COLUMN "default".goods_brand.name IS '品牌名称';
COMMENT ON COLUMN "default".goods_brand.name_eng IS '品牌名称(英文)';
COMMENT ON COLUMN "default".goods_brand.country IS '品牌国家';
COMMENT ON COLUMN "default".goods_brand.pic IS '展示图片';

CREATE TABLE "default".goods_attr_class (
  id bigserial NOT NULL,
  name text NOT NULL,
  PRIMARY KEY (id),
  UNIQUE (name)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".goods_attr_class IS '商品属性库';
COMMENT ON COLUMN "default".goods_attr_class.id IS '自增ID';
COMMENT ON COLUMN "default".goods_attr_class.name IS '属性名称';

CREATE TABLE "default".goods_attr_value (
  id bigserial NOT NULL,
  class_id bigint NOT NULL,
  name text NOT NULL,
  pic jsonb,
  PRIMARY KEY (id),
  UNIQUE (class_id,name)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".goods_attr_value IS '商品属性库值';
COMMENT ON COLUMN "default".goods_attr_value.id IS '自增ID';
COMMENT ON COLUMN "default".goods_attr_value.class_id IS 'classID';
COMMENT ON COLUMN "default".goods_attr_value.name IS '属性值';
COMMENT ON COLUMN "default".goods_attr_value.pic IS '图片';

CREATE TABLE "default".goods_category (
  id bigserial NOT NULL,
  pid bigint NOT NULL,
  status smallint NOT NULL DEFAULT 1,
  name text NOT NULL,
  level smallint NOT NULL DEFAULT 1 CHECK(level>=1),
  pic jsonb,
  ordering integer NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE (name)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".goods_category IS '商品分类';
COMMENT ON COLUMN "default".goods_category.id IS '自增ID';
COMMENT ON COLUMN "default".goods_category.pid IS '所属分类id';
COMMENT ON COLUMN "default".goods_category.status IS '状态 1有效 -1无效';
COMMENT ON COLUMN "default".goods_category.name IS '分类名称';
COMMENT ON COLUMN "default".goods_category.level IS '分类层级';
COMMENT ON COLUMN "default".goods_category.pic IS '展示图片';
COMMENT ON COLUMN "default".goods_category.ordering IS '排序 越大越靠前';

CREATE TABLE "default".goods (
  id bigserial NOT NULL,
  uid bigint NOT NULL,
  status smallint NOT NULL DEFAULT 1,
  name text NOT NULL,
  groups text NOT NULL,
  category_id text[] NOT NULL,
  brand_id bigint,
  attr_value text[],
  attr_value_label text,
  detail jsonb,
  pic jsonb,
  tag text[],
  origin_region text[],
  origin_region_label text,
  origin_address text,
  unit text,
  price_sell numeric(10,2) NOT NULL CHECK(price_sell>=0.00),
  price_cost numeric(10,2) CHECK(price_cost>=0.00),
  price_advice numeric(10,2) NOT NULL DEFAULT -1 CHECK (price_advice>=-1),
  qty_stock bigint NOT NULL DEFAULT 0,
  qty_view bigint NOT NULL DEFAULT 0 CHECK (qty_view>=0),
  qty_sale bigint NOT NULL DEFAULT 0 CHECK (qty_sale>=0),
  qty_like bigint NOT NULL DEFAULT 0 CHECK (qty_like>=0),
  weight numeric(10,3) NOT NULL DEFAULT 0.000 CHECK(weight>=0.000),
  barcode text,
  recommend jsonb NOT NULL,
  ordering integer NOT NULL DEFAULT 0,
  create_time timestamp without time zone NOT NULL,
  update_time timestamp without time zone,
  PRIMARY KEY (id)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".goods IS '商品表';
COMMENT ON COLUMN "default".goods.id IS '自增ID';
COMMENT ON COLUMN "default".goods.uid IS '卖家uid';
COMMENT ON COLUMN "default".goods.status IS '商品状态 -10删除 -1下架 1上架';
COMMENT ON COLUMN "default".goods.name IS '商品名称';
COMMENT ON COLUMN "default".goods.groups IS '联合';
COMMENT ON COLUMN "default".goods.category_id IS '分类ID组';
COMMENT ON COLUMN "default".goods.brand_id IS '品牌id';
COMMENT ON COLUMN "default".goods.attr_value IS '相关属性值ID';
COMMENT ON COLUMN "default".goods.attr_value_label IS '相关属性值';
COMMENT ON COLUMN "default".goods.detail IS '商品详情（如introduce简介，各类型文章详情等）';
COMMENT ON COLUMN "default".goods.pic IS '图集';
COMMENT ON COLUMN "default".goods.tag IS '商品标签';
COMMENT ON COLUMN "default".goods.origin_region IS '产地地区ID';
COMMENT ON COLUMN "default".goods.origin_region_label IS '产地地区';
COMMENT ON COLUMN "default".goods.origin_address IS '产地详细';
COMMENT ON COLUMN "default".goods.unit IS '单位';
COMMENT ON COLUMN "default".goods.price_sell IS '销售价';
COMMENT ON COLUMN "default".goods.price_cost IS '成本价';
COMMENT ON COLUMN "default".goods.price_advice IS '建议售价';
COMMENT ON COLUMN "default".goods.qty_stock IS '库存量';
COMMENT ON COLUMN "default".goods.qty_view IS '浏览次数';
COMMENT ON COLUMN "default".goods.qty_sale IS '销量';
COMMENT ON COLUMN "default".goods.qty_like IS '赞次数';
COMMENT ON COLUMN "default".goods.weight IS '重量,单位千克kg';
COMMENT ON COLUMN "default".goods.barcode IS '条码';
COMMENT ON COLUMN "default".goods.recommend IS '推荐判断组';
COMMENT ON COLUMN "default".goods.ordering IS '排序 越大越靠前';
COMMENT ON COLUMN "default".goods.create_time IS '创建时间';
COMMENT ON COLUMN "default".goods.update_time IS '更新时间';

CREATE TABLE "default".goods_evaluation (
  id bigserial NOT NULL,
  prev_id bigint NOT NULL DEFAULT 0,
  uid bigint NOT NULL,
  goods_id bigint NOT NULL,
  seller_uid bigint,
  evaluate "default".evaluate NOT NULL DEFAULT 'positive',
  point numeric(3,1) NOT NULL DEFAULT 5.0,
  order_id bigint NOT NULL,
  order_no text NOT NULL,
  comment text,
  status "default".is_sure NOT NULL DEFAULT '-1',
  create_time timestamp without time zone NOT NULL,
  change_time timestamp without time zone,
  PRIMARY KEY (id)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".goods_evaluation IS '商品评分表';
COMMENT ON COLUMN "default".goods_evaluation.id IS '自增ID';
COMMENT ON COLUMN "default".goods_evaluation.prev_id IS '上级评论ID 用于追加';
COMMENT ON COLUMN "default".goods_evaluation.uid IS '评分人uid';
COMMENT ON COLUMN "default".goods_evaluation.goods_id IS '商品ID';
COMMENT ON COLUMN "default".goods_evaluation.seller_uid IS '卖家uid';
COMMENT ON COLUMN "default".goods_evaluation.evaluate IS '评价 好中差评 positive/moderate/negative';
COMMENT ON COLUMN "default".goods_evaluation.point IS '是次得分';
COMMENT ON COLUMN "default".goods_evaluation.order_id IS '订单ID';
COMMENT ON COLUMN "default".goods_evaluation.order_no IS '订单号';
COMMENT ON COLUMN "default".goods_evaluation.comment IS '评价内容';
COMMENT ON COLUMN "default".goods_evaluation.status IS '有效状态 -1无 1有';
COMMENT ON COLUMN "default".goods_evaluation.create_time IS '创建(评分)时间';
COMMENT ON COLUMN "default".goods_evaluation.change_time IS '修改时间';

CREATE TABLE "default".goods_collection (
  id bigserial NOT NULL,
  uid bigint NOT NULL,
  goods_id bigint NOT NULL,
  create_time timestamp without time zone NOT NULL,
  PRIMARY KEY (id),
  UNIQUE (uid,goods_id)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".goods_collection IS '商品收藏表';
COMMENT ON COLUMN "default".goods_collection.id IS '自增ID';
COMMENT ON COLUMN "default".goods_collection.uid IS '收藏人uid';
COMMENT ON COLUMN "default".goods_collection.goods_id IS '商品ID';
COMMENT ON COLUMN "default".goods_collection.create_time IS '创建时间';

ALTER TABLE "default"."goods_attr_value" ADD FOREIGN KEY ("class_id") REFERENCES "default"."goods_attr_class" ("id") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."goods_collection" ADD FOREIGN KEY ("uid") REFERENCES "default"."user" ("uid") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."goods_collection" ADD FOREIGN KEY ("goods_id") REFERENCES "default"."goods" ("id") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."goods_evaluation" ADD FOREIGN KEY ("uid") REFERENCES "default"."user" ("uid") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."goods_evaluation" ADD FOREIGN KEY ("goods_id") REFERENCES "default"."goods" ("id") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;