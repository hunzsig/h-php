
CREATE TABLE "default".dom (
  id bigserial NOT NULL,
  uid bigint NOT NULL,
  type text NOT NULL,
  category_id bigint,
  ordering integer NOT NULL DEFAULT 0,
  status smallint NOT NULL DEFAULT 1,
  views bigint NOT NULL DEFAULT 0 CHECK(views >= 0),
  data jsonb,
  create_time timestamp without time zone,
  update_time timestamp without time zone,
  verify_uid bigint NOT NULL,
  PRIMARY KEY (id)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".dom IS '文档';
COMMENT ON COLUMN "default".dom.id IS 'id';
COMMENT ON COLUMN "default".dom.uid IS '创建的用户ID';
COMMENT ON COLUMN "default".dom.type IS '文档类型';
COMMENT ON COLUMN "default".dom.category_id IS '文档分类id';
COMMENT ON COLUMN "default".dom.ordering IS '排序，数值越大优先级越高';
COMMENT ON COLUMN "default".dom.status IS '文档状态';
COMMENT ON COLUMN "default".dom.views IS '浏览量';
COMMENT ON COLUMN "default".dom.data IS '文档数据（jsonb）';
COMMENT ON COLUMN "default".dom.create_time IS '创建时间';
COMMENT ON COLUMN "default".dom.update_time IS '更新时间';
COMMENT ON COLUMN "default".dom.verify_uid IS '审核人UID';

/*Table structure for table dom_category */

CREATE TABLE "default".dom_category (
  id bigserial NOT NULL,
  parent_id bigint,
  name text NOT NULL,
  level bigint NOT NULL DEFAULT 1 CHECK(level>=1),
  description text,
  pic jsonb,
  status smallint DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE (parent_id,name)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".dom_category IS '文档分类';
COMMENT ON COLUMN "default".dom_category.id IS 'id';
COMMENT ON COLUMN "default".dom_category.parent_id IS '父级id';
COMMENT ON COLUMN "default".dom_category.name IS '分类名称';
COMMENT ON COLUMN "default".dom_category.level IS '分类等级';
COMMENT ON COLUMN "default".dom_category.description IS '描述';
COMMENT ON COLUMN "default".dom_category.pic IS '分类图片';
COMMENT ON COLUMN "default".dom_category.status IS '是否开启 -1不开启 1开启';


ALTER TABLE "default"."dom" ADD FOREIGN KEY ("uid") REFERENCES "default"."user" ("uid") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."dom" ADD FOREIGN KEY ("category_id") REFERENCES "default"."dom_category" ("id") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;

insert into "default".dom_category(name,level,description,pic,status) values ('协议',1,NULL,NULL,'1');
insert into "default".dom_category(name,level,description,pic,status) values ('PC首页广告',1,NULL,NULL,'1');
insert into "default".dom_category(name,level,description,pic,status) values ('手机首页广告',1,NULL,NULL,'1');
insert into "default".dom_category(name,level,description,pic,status) values ('公司信息',1,NULL,NULL,'1');
insert into "default".dom_category(name,level,description,pic,status) values ('帮助',1,NULL,NULL,'1');
insert into "default".dom_category(name,level,description,pic,status) values ('客服',1,NULL,NULL,'1');
insert into "default".dom_category(name,level,description,pic,status) values ('文化',1,NULL,NULL,'1');
insert into "default".dom_category(name,level,description,pic,status) values ('新闻',1,NULL,NULL,'1');
insert into "default".dom_category(name,level,description,pic,status) values ('活动',1,NULL,NULL,'1');
insert into "default".dom_category(name,level,description,pic,status) values ('公告',1,NULL,NULL,'1');

