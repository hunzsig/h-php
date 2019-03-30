
CREATE TABLE "default".data_download(
    id bigserial NOT NULL,
    belong text NOT NULL,
    name text NOT NULL,
    introduce text,
    description text,
    pic jsonb,
    dl_link text,
    dl_qty integer DEFAULT 0 NOT NULL CHECK(dl_qty >= 0),
    dl_last_time timestamp without time zone,
    version text,
    support text,
    create_time timestamp without time zone,
    update_time timestamp without time zone,
    PRIMARY KEY (id),
    UNIQUE (name)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".data_download IS '下载';
COMMENT ON COLUMN "default".data_download.id IS 'id';
COMMENT ON COLUMN "default".data_download.belong IS '归属';
COMMENT ON COLUMN "default".data_download.name IS '下载文件名';
COMMENT ON COLUMN "default".data_download.introduce IS '简介';
COMMENT ON COLUMN "default".data_download.description IS '描述';
COMMENT ON COLUMN "default".data_download.pic IS '图片';
COMMENT ON COLUMN "default".data_download.dl_link IS '下载链接';
COMMENT ON COLUMN "default".data_download.dl_qty IS '下载次数';
COMMENT ON COLUMN "default".data_download.dl_last_time IS '最后一次下载时间';
COMMENT ON COLUMN "default".data_download.version IS '版本';
COMMENT ON COLUMN "default".data_download.support IS '使用支持';
COMMENT ON COLUMN "default".data_download.create_time IS '创建日期';
COMMENT ON COLUMN "default".data_download.update_time IS '更新日期';
