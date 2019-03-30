
CREATE TABLE "default".assets(
    hash_id text NOT NULL,
    hash_file_name text NOT NULL,
    file_name text NOT NULL,
    file_ext text NOT NULL,
    file_size bigint DEFAULT 0 NOT NULL CHECK(file_size >= 0),
    content_type text NOT NULL,
    path text NOT NULL,
    from_url text NOT NULL,
    download_url text NOT NULL,
    call_qty integer DEFAULT 0 NOT NULL CHECK(call_qty >= 0),
    call_last_time timestamp without time zone,
    uid bigint[] NOT NULL,
    create_time timestamp without time zone,
    update_time timestamp without time zone,
    PRIMARY KEY (hash_id)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".assets IS '资源';
COMMENT ON COLUMN "default".assets.hash_id IS 'hash_id';
COMMENT ON COLUMN "default".assets.hash_file_name IS 'hash文件名';
COMMENT ON COLUMN "default".assets.file_name IS '文件名';
COMMENT ON COLUMN "default".assets.file_ext IS '文件后缀';
COMMENT ON COLUMN "default".assets.file_size IS '文件大小';
COMMENT ON COLUMN "default".assets.content_type IS '内容类型';
COMMENT ON COLUMN "default".assets.path IS '存在路径';
COMMENT ON COLUMN "default".assets.from_url IS '来源地址';
COMMENT ON COLUMN "default".assets.download_url IS '下载地址';
COMMENT ON COLUMN "default".assets.call_qty IS '调用次数';
COMMENT ON COLUMN "default".assets.call_last_time IS '最后一次调用时间';
COMMENT ON COLUMN "default".assets.uid IS '拥有者';
COMMENT ON COLUMN "default".assets.create_time IS '创建日期';
COMMENT ON COLUMN "default".assets.update_time IS '更新日期';

CREATE TABLE "default".assets_call_level(
    lv text NOT NULL,
    lv_as integer NOT NULL DEFAULT 1 CHECK(lv_as>=1),
    lv_desc text NULL,
    file_size_unit text NOT NULL,
    file_max_total_size bigint NOT NULL,
    file_max_simple_size bigint NOT NULL,
    file_max_qty bigint NOT NULL,
    file_ext text[] NULL,
    PRIMARY KEY (lv)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".assets_call_level IS '资源等级参数';
COMMENT ON COLUMN "default".assets_call_level.lv IS '等级 ，最高理论无限';
COMMENT ON COLUMN "default".assets_call_level.lv_as IS '等级等价';
COMMENT ON COLUMN "default".assets_call_level.lv_desc IS '等级描述';
COMMENT ON COLUMN "default".assets_call_level.file_size_unit IS '文件大小单位';
COMMENT ON COLUMN "default".assets_call_level.file_max_total_size IS '允许的总体文件大小';
COMMENT ON COLUMN "default".assets_call_level.file_max_simple_size IS '允许的单个文件大小';
COMMENT ON COLUMN "default".assets_call_level.file_max_qty IS '允许的文件数';
COMMENT ON COLUMN "default".assets_call_level.file_ext IS '允许的文件后缀';

DELETE FROM "default".assets_call_level;
INSERT INTO "default".assets_call_level (lv, lv_as, lv_desc, file_size_unit, file_max_total_size, file_max_simple_size, file_max_qty, file_ext)
VALUES ('try',1,'体验资源包','KB','1024','100',3, '{txt}');
INSERT INTO "default".assets_call_level (lv, lv_as, lv_desc, file_size_unit, file_max_total_size, file_max_simple_size, file_max_qty, file_ext)
VALUES ('rookie',2,'入门资源包','MB','200','1',50, '{txt,gif,jpeg,jpg,bmp,png,pdf,doc,docx,ppt,pptx,xls,xlsx,csv,psd}');
INSERT INTO "default".assets_call_level (lv, lv_as, lv_desc, file_size_unit, file_max_total_size, file_max_simple_size, file_max_qty, file_ext)
VALUES ('low',3,'初级资源包','MB','1024','10',100, '{txt,gif,jpeg,jpg,bmp,png,pdf,doc,docx,ppt,pptx,xls,xlsx,csv,psd,rar,7z,zip}');
INSERT INTO "default".assets_call_level (lv, lv_as, lv_desc, file_size_unit, file_max_total_size, file_max_simple_size, file_max_qty, file_ext)
VALUES ('low2',3,'初级大文件包','MB','1024','50',50, '{txt,gif,jpeg,jpg,bmp,png,pdf,doc,docx,ppt,pptx,xls,xlsx,csv,psd,rar,7z,zip}');
INSERT INTO "default".assets_call_level (lv, lv_as, lv_desc, file_size_unit, file_max_total_size, file_max_simple_size, file_max_qty, file_ext)
VALUES ('normal',4,'中级资源包','MB','2048','30',300, '{txt,gif,jpeg,jpg,bmp,png,pdf,doc,docx,ppt,pptx,xls,xlsx,csv,psd,rar,7z,zip,mp4,rmvb,mkv,avi,wmv,mov,mpg,3gp,mp3,wma,ape,flac,wav,ogg,m4a}');
INSERT INTO "default".assets_call_level (lv, lv_as, lv_desc, file_size_unit, file_max_total_size, file_max_simple_size, file_max_qty, file_ext)
VALUES ('normal2',4,'中级大文件包','MB','2048','100',100, '{txt,gif,jpeg,jpg,bmp,png,pdf,doc,docx,ppt,pptx,xls,xlsx,csv,psd,rar,7z,zip,mp4,rmvb,mkv,avi,wmv,mov,mpg,3gp,mp3,wma,ape,flac,wav,ogg,m4a}');
INSERT INTO "default".assets_call_level (lv, lv_as, lv_desc, file_size_unit, file_max_total_size, file_max_simple_size, file_max_qty, file_ext)
VALUES ('super',9,'无尽资源包','GB','1024','1',10000, '{mp4,rmvb,mkv,avi,wmv,mov,mpg,3gp,mp3,wma,ape,flac,wav,ogg,m4a,gif,jpeg,jpg,bmp,png,ico,tga,txt,pdf,doc,docx,ppt,pptx,xls,xlsx,csv,psd,rar,7z,zip,iso,cso}');


ALTER TABLE "default".user_info ADD COLUMN call_level text;
COMMENT ON COLUMN "default".user_info.call_level  IS '调用级别，影响用户不登录调用接口的次数';
