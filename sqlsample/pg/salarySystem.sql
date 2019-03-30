
CREATE TABLE "default".salary_employer
(
    id bigserial NOT NULL,
    name text NOT NULL,
    create_time timestamp without time zone NOT NULL,
    update_time timestamp without time zone,
    PRIMARY KEY (id),
    UNIQUE (name)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".salary_employer IS '雇主单位表';
COMMENT ON COLUMN "default".salary_employer.name IS '单位名称';
COMMENT ON COLUMN "default".salary_employer.create_time IS '创建时间';
COMMENT ON COLUMN "default".salary_employer.update_time IS '更新时间';


CREATE TABLE "default".salary_department
(
    id bigserial NOT NULL,
    employer_id bigint NOT NULL,
    name text NOT NULL,
    create_time timestamp without time zone NOT NULL,
    update_time timestamp without time zone,
    PRIMARY KEY (id),
    UNIQUE (employer_id,name)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".salary_department IS '单位部门表';
COMMENT ON COLUMN "default".salary_department.employer_id IS '所属单位';
COMMENT ON COLUMN "default".salary_department.name IS '部门名称';
COMMENT ON COLUMN "default".salary_department.create_time IS '创建时间';
COMMENT ON COLUMN "default".salary_department.update_time IS '更新时间';
ALTER TABLE "default"."salary_department" ADD FOREIGN KEY ("employer_id") REFERENCES "default"."salary_employer" ("id") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;


CREATE TABLE "default".salary_employee_class
(
    id bigserial NOT NULL,
    name text NOT NULL,
    create_time timestamp without time zone NOT NULL,
    update_time timestamp without time zone,
    PRIMARY KEY (id),
    UNIQUE (name)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".salary_employee_class IS '聘员类别表';
COMMENT ON COLUMN "default".salary_employee_class.name IS '聘员类别名称';
COMMENT ON COLUMN "default".salary_employee_class.create_time IS '创建时间';
COMMENT ON COLUMN "default".salary_employee_class.update_time IS '更新时间';


CREATE TABLE "default".salary_position
(
    id bigserial NOT NULL,
    employee_class_id bigint,
    name text NOT NULL,
    create_time timestamp without time zone NOT NULL,
    update_time timestamp without time zone,
    PRIMARY KEY (id),
    UNIQUE (employee_class_id,name)
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".salary_position IS '职务表';
COMMENT ON COLUMN "default".salary_position.employee_class_id IS '所属聘员类别';
COMMENT ON COLUMN "default".salary_position.name IS '职务名称';
COMMENT ON COLUMN "default".salary_position.create_time IS '创建时间';
COMMENT ON COLUMN "default".salary_position.update_time IS '更新时间';
ALTER TABLE "default"."salary_position" ADD FOREIGN KEY ("employee_class_id") REFERENCES "default"."salary_employee_class" ("id") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;


CREATE TABLE "default".salary_wages
(
    uid bigint NOT NULL,
	  batch text NOT NULL,
	  pay_type text NOT NULL,
    pay_date date NOT NULL,
    pay_status text NOT NULL DEFAULT 'built', /* built -> distribution -> supplement -> submit -> (un_pass / pass)  */
    pay_data jsonb NOT NULL,
    pay_money numeric(12,2) NOT NULL check(pay_money>=0),
    employer_id bigint NOT NULL,
    department_id bigint NOT NULL,
    employee_class_id bigint NOT NULL,
    position_id bigint NOT NULL,
    create_time timestamp without time zone NOT NULL,
    update_time timestamp without time zone
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".salary_wages IS '薪酬发放表';
COMMENT ON COLUMN "default".salary_wages.uid IS 'uid';
COMMENT ON COLUMN "default".salary_wages.batch IS '批次码';
COMMENT ON COLUMN "default".salary_wages.pay_type IS '这笔钱类型';
COMMENT ON COLUMN "default".salary_wages.pay_date IS '这笔钱发放时间（前）';
COMMENT ON COLUMN "default".salary_wages.pay_status IS '这笔钱状态';
COMMENT ON COLUMN "default".salary_wages.pay_data IS '这笔钱数据';
COMMENT ON COLUMN "default".salary_wages.pay_money IS '应发金额(最终)';
COMMENT ON COLUMN "default".salary_wages.employer_id IS '雇佣ID';
COMMENT ON COLUMN "default".salary_wages.department_id IS '部门ID';
COMMENT ON COLUMN "default".salary_wages.employee_class_id IS '聘员类别ID';
COMMENT ON COLUMN "default".salary_wages.position_id IS '职务ID';
COMMENT ON COLUMN "default".salary_wages.create_time IS '创建时间';
COMMENT ON COLUMN "default".salary_wages.update_time IS '更新时间';
ALTER TABLE "default"."salary_wages" ADD FOREIGN KEY ("uid") REFERENCES "default"."user" ("uid") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."salary_wages" ADD FOREIGN KEY ("employer_id") REFERENCES "default"."salary_employer" ("id") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."salary_wages" ADD FOREIGN KEY ("department_id") REFERENCES "default"."salary_department" ("id") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."salary_wages" ADD FOREIGN KEY ("employee_class_id") REFERENCES "default"."salary_employee_class" ("id") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."salary_wages" ADD FOREIGN KEY ("position_id") REFERENCES "default"."salary_position" ("id") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
SELECT create_hypertable ('default.salary_wages','create_time');


/* salary_wages_recovery */
CREATE TABLE "default".salary_wages_recovery
(
    uid bigint NOT NULL,
	  batch text NOT NULL,
	  pay_type text NOT NULL,
    pay_date date NOT NULL,
    pay_data jsonb NOT NULL,
    pay_money numeric(12,2) NOT NULL,
    employer_id bigint NOT NULL,
    department_id bigint NOT NULL,
    employee_class_id bigint NOT NULL,
    position_id bigint NOT NULL,
    create_time timestamp without time zone NOT NULL
) WITH (OIDS = FALSE);
COMMENT ON TABLE  "default".salary_wages_recovery IS '薪酬发放表(回收)';
COMMENT ON COLUMN "default".salary_wages_recovery.uid IS 'uid';
COMMENT ON COLUMN "default".salary_wages_recovery.batch IS '批次码';
COMMENT ON COLUMN "default".salary_wages_recovery.pay_type IS '这笔钱类型';
COMMENT ON COLUMN "default".salary_wages_recovery.pay_date IS '这笔钱发放时间（前）';
COMMENT ON COLUMN "default".salary_wages_recovery.pay_data IS '这笔钱数据';
COMMENT ON COLUMN "default".salary_wages_recovery.pay_money IS '应发金额(最终)';
COMMENT ON COLUMN "default".salary_wages_recovery.employer_id IS '雇佣ID';
COMMENT ON COLUMN "default".salary_wages_recovery.department_id IS '部门ID';
COMMENT ON COLUMN "default".salary_wages_recovery.employee_class_id IS '聘员类别ID';
COMMENT ON COLUMN "default".salary_wages_recovery.position_id IS '职务ID';
COMMENT ON COLUMN "default".salary_wages_recovery.create_time IS '创建时间';
SELECT create_hypertable ('default.salary_wages_recovery','create_time');


ALTER TABLE "default".user_info ADD COLUMN birthday date;
ALTER TABLE "default".user_info ADD COLUMN native_place text[];
ALTER TABLE "default".user_info ADD COLUMN native_place_label text;
ALTER TABLE "default".user_info ADD COLUMN political text;
ALTER TABLE "default".user_info ADD COLUMN education text;
ALTER TABLE "default".user_info ADD COLUMN education_record text[];
ALTER TABLE "default".user_info ADD COLUMN degree text;
ALTER TABLE "default".user_info ADD COLUMN work_exp text[];
ALTER TABLE "default".user_info ADD COLUMN work_employer_id bigint[];
ALTER TABLE "default".user_info ADD COLUMN work_department_id bigint[];
ALTER TABLE "default".user_info ADD COLUMN work_entry_date date[];
ALTER TABLE "default".user_info ADD COLUMN is_special_class "default".is_sure default '-1' NOT NULL;
ALTER TABLE "default".user_info ADD COLUMN is_retirement "default".is_sure default '-1' NOT NULL;
ALTER TABLE "default".user_info ADD COLUMN retire_date date;
ALTER TABLE "default".user_info ADD COLUMN is_probationary "default".is_sure default '-1' NOT NULL;
ALTER TABLE "default".user_info ADD COLUMN probationary_during_month integer NOT NULL default 0 check(probationary_during_month >=0);
ALTER TABLE "default".user_info ADD COLUMN probationary_end_date date;
ALTER TABLE "default".user_info ADD COLUMN ordering text NOT NULL default '';
ALTER TABLE "default".user_info ADD COLUMN bank_account text;
ALTER TABLE "default".user_info ADD COLUMN appraisal text;
ALTER TABLE "default".user_info ADD COLUMN wages_employer_id bigint;
ALTER TABLE "default".user_info ADD COLUMN wages_department_id bigint;
ALTER TABLE "default".user_info ADD COLUMN wages_employee_class_id bigint;
ALTER TABLE "default".user_info ADD COLUMN wages_position_id bigint;
ALTER TABLE "default".user_info ADD COLUMN wages_entry_date date;
ALTER TABLE "default".user_info ADD COLUMN wages_profess text;
COMMENT ON COLUMN "default".user_info.birthday IS '生日';
COMMENT ON COLUMN "default".user_info.native_place IS '籍贯';
COMMENT ON COLUMN "default".user_info.native_place_label IS '籍贯文本';
COMMENT ON COLUMN "default".user_info.political IS '政治面貌';
COMMENT ON COLUMN "default".user_info.education IS '学历';
COMMENT ON COLUMN "default".user_info.education_record IS '学历记录';
COMMENT ON COLUMN "default".user_info.degree IS '学位';
COMMENT ON COLUMN "default".user_info.work_exp IS '工作经历';
COMMENT ON COLUMN "default".user_info.work_employer_id IS '工作单位';
COMMENT ON COLUMN "default".user_info.work_department_id IS '工作部门';
COMMENT ON COLUMN "default".user_info.work_entry_date IS '工作入职日期';
COMMENT ON COLUMN "default".user_info.is_special_class IS '是否四类';
COMMENT ON COLUMN "default".user_info.is_retirement IS '是否退休';
COMMENT ON COLUMN "default".user_info.retire_date IS '退休日期';
COMMENT ON COLUMN "default".user_info.is_probationary  IS '是否试用';
COMMENT ON COLUMN "default".user_info.probationary_during_month  IS '试用期持续时间（月）';
COMMENT ON COLUMN "default".user_info.probationary_end_date  IS '试用期结束日期';
COMMENT ON COLUMN "default".user_info.ordering IS '排序编号';
COMMENT ON COLUMN "default".user_info.bank_account IS '银行帐号';
COMMENT ON COLUMN "default".user_info.appraisal IS '考核';
COMMENT ON COLUMN "default".user_info.wages_employer_id IS '薪酬单位';
COMMENT ON COLUMN "default".user_info.wages_department_id IS '薪酬部门';
COMMENT ON COLUMN "default".user_info.wages_employee_class_id IS '薪酬聘员类别';
COMMENT ON COLUMN "default".user_info.wages_position_id IS '薪酬职务待遇';
COMMENT ON COLUMN "default".user_info.wages_entry_date IS '薪酬入职日期';
COMMENT ON COLUMN "default".user_info.wages_profess IS '薪酬职称';

/* FOREIGN KEY */
ALTER TABLE "default"."salary_department" ADD FOREIGN KEY ("employer_id") REFERENCES "default".salary_employer ("id") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."salary_position" ADD FOREIGN KEY ("employee_class_id") REFERENCES "default".salary_employee_class ("id") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;

ALTER TABLE "default"."user_info" ADD FOREIGN KEY ("wages_employer_id") REFERENCES "default"."salary_employer" ("id") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."user_info" ADD FOREIGN KEY ("wages_department_id") REFERENCES "default"."salary_department" ("id") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."user_info" ADD FOREIGN KEY ("wages_employee_class_id") REFERENCES "default"."salary_employee_class" ("id") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE "default"."user_info" ADD FOREIGN KEY ("wages_position_id") REFERENCES "default"."salary_position" ("id") MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;

