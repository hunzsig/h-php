

CREATE TABLE "default".data_link (
  id bigserial NOT NULL,
  category_id bigint NOT NULL,
  name text NOT NULL,
  url text,
  pic jsonb,
  ordering integer NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".data_link IS '链接';
COMMENT ON COLUMN "default".data_link.id IS 'id';
COMMENT ON COLUMN "default".data_link.category_id IS '分类';
COMMENT ON COLUMN "default".data_link.name IS '名称';
COMMENT ON COLUMN "default".data_link.url IS '链接';
COMMENT ON COLUMN "default".data_link.pic IS '图片链接';
COMMENT ON COLUMN "default".data_link.ordering IS '排序';

/*Table structure for table link_category */

CREATE TABLE "default".data_link_category (
  id bigserial NOT NULL,
  parent_id bigint NOT NULL DEFAULT 0,
  name text NOT NULL,
  level integer NOT NULL DEFAULT 1 CHECK(level>=1),
  description text,
  pic jsonb,
  status "default".is_enable NOT NULL DEFAULT '1',
  PRIMARY KEY (id),
  UNIQUE (parent_id,name)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".data_link_category IS '链接分类';
COMMENT ON COLUMN "default".data_link_category.id IS 'id';
COMMENT ON COLUMN "default".data_link_category.parent_id IS '父级id';
COMMENT ON COLUMN "default".data_link_category.name IS '分类名称';
COMMENT ON COLUMN "default".data_link_category.level IS '分类等级';
COMMENT ON COLUMN "default".data_link_category.description IS '描述';
COMMENT ON COLUMN "default".data_link_category.pic IS '分类图片';
COMMENT ON COLUMN "default".data_link_category.status IS '是否开启 -1不开启 1开启';

ALTER TABLE "default"."data_link" ADD FOREIGN KEY ("category_id") REFERENCES "default".data_link_category (id) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
insert into "default".data_link_category(name,level,description,pic,status) values ('合作伙伴',1,NULL,NULL,'1');

