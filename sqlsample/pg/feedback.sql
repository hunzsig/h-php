
CREATE TABLE "default".data_feedback (
  id bigserial NOT NULL,
  type text NOT NULL,
  content text,
  ip inet,
  url text,
  contact_name text,
  contact_phone text,
  remarks text,
  create_time timestamp without time zone,
  PRIMARY KEY (id)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".data_feedback IS '客户反馈表';
COMMENT ON COLUMN "default".data_feedback.id IS 'id';
COMMENT ON COLUMN "default".data_feedback.type IS '反馈问题类型';
COMMENT ON COLUMN "default".data_feedback.content IS '反馈内容';
COMMENT ON COLUMN "default".data_feedback.ip IS 'IP地址';
COMMENT ON COLUMN "default".data_feedback.url IS '反馈地址';
COMMENT ON COLUMN "default".data_feedback.contact_name IS '联系人';
COMMENT ON COLUMN "default".data_feedback.contact_phone IS '联系电话';
COMMENT ON COLUMN "default".data_feedback.remarks IS '处理备注';
COMMENT ON COLUMN "default".data_feedback.create_time IS '创建时间';

