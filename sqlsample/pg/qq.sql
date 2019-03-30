
CREATE TABLE "default".data_qq_group (
  id bigserial NOT NULL,
  name text NOT NULL,
  number text NOT NULL,
  build_year text,
  description text,
  pic jsonb,
  link text,
  qty integer NOT NULL DEFAULT 0 CHECK(qty >= 0),
  ordering integer NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE (number)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".data_qq_group IS 'QQ群';
COMMENT ON COLUMN "default".data_qq_group.id IS 'id';
COMMENT ON COLUMN "default".data_qq_group.name IS 'QQ群名';
COMMENT ON COLUMN "default".data_qq_group.number IS 'QQ群号';
COMMENT ON COLUMN "default".data_qq_group.build_year IS 'QQ群建立年';
COMMENT ON COLUMN "default".data_qq_group.description IS '描述';
COMMENT ON COLUMN "default".data_qq_group.pic IS '图片';
COMMENT ON COLUMN "default".data_qq_group.link IS '加群链接';
COMMENT ON COLUMN "default".data_qq_group.qty IS '群里人员';
COMMENT ON COLUMN "default".data_qq_group.ordering IS '排序';
