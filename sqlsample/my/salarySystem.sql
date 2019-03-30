

DROP TABLE IF EXISTS `salary_employer`;
CREATE TABLE `salary_employer` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `name` char(255) NOT NULL COMMENT '单位名称',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE (`name`)
) ENGINE=INNODB COMMENT '雇主单位表';


DROP TABLE IF EXISTS `salary_department`;
CREATE TABLE `salary_department` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `employer_id` bigint unsigned NOT NULL COMMENT '所属单位',
  `name` char(255) NOT NULL COMMENT '部门名称',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE (`name`)
) ENGINE=INNODB COMMENT '单位部门表';

ALTER TABLE `salary_department` ADD FOREIGN KEY (`employer_id`) REFERENCES `salary_employer` (`id`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;


DROP TABLE IF EXISTS `salary_employee_class`;
CREATE TABLE `salary_employee_class` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `name` char(255) NOT NULL COMMENT '聘员类别名称',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE (`name`)
) ENGINE=INNODB COMMENT '聘员类别表';


DROP TABLE IF EXISTS `salary_position`;
CREATE TABLE `salary_position` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `employee_class_id` bigint unsigned NOT NULL COMMENT '所属聘员类别',
  `name` char(255) NOT NULL COMMENT '职务名称',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE (`name`)
) ENGINE=INNODB COMMENT '职务表';

ALTER TABLE `salary_position` ADD FOREIGN KEY (`employee_class_id`) REFERENCES `salary_employee_class` (`id`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;


DROP TABLE IF EXISTS `salary_wages`;
CREATE TABLE `salary_wages` (
  `uid` bigint unsigned NOT NULL COMMENT 'UID',
	`batch` char(255) NOT NULL COMMENT '批次码',
	`pay_type` char(255) NOT NULL COMMENT '这笔钱类型',
  `pay_date` date NOT NULL COMMENT '这笔钱发放时间（前）',
  `pay_status` char(255) NOT NULL DEFAULT 'built' COMMENT '这笔钱状态', /* built -> distribution -> supplement -> submit -> (un_pass / pass)  */
  `pay_data` json NOT NULL COMMENT '这笔钱数据',
  `pay_money` numeric(12,2) unsigned NOT NULL COMMENT '应发金额(最终)',
  `employer_id` bigint unsigned NOT NULL COMMENT '雇佣ID',
  `department_id` bigint unsigned NOT NULL COMMENT '部门ID',
  `employee_class_id` bigint unsigned NOT NULL COMMENT '聘员类别ID',
  `position_id` bigint unsigned NOT NULL COMMENT '职务ID',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime COMMENT '更新时间'
) ENGINE=INNODB COMMENT '薪酬发放表';
ALTER TABLE `salary_wages` ADD FOREIGN KEY (`uid`) REFERENCES `user` (`uid`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `salary_wages` ADD FOREIGN KEY (`employer_id`) REFERENCES `salary_employer` (`id`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `salary_wages` ADD FOREIGN KEY (`department_id`) REFERENCES `salary_department` (`id`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `salary_wages` ADD FOREIGN KEY (`employee_class_id`) REFERENCES `salary_employee_class` (`id`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `salary_wages` ADD FOREIGN KEY (`position_id`) REFERENCES `salary_position` (`id`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;


DROP TABLE IF EXISTS `salary_wages_recovery`;
CREATE TABLE `salary_wages_recovery` (
  `uid` bigint unsigned NOT NULL COMMENT 'UID',
	`batch` char(255) NOT NULL COMMENT '批次码',
	`pay_type` char(255) NOT NULL COMMENT '这笔钱类型',
  `pay_date` date NOT NULL COMMENT '这笔钱发放时间（前）',
  `pay_data` json NOT NULL COMMENT '这笔钱数据',
  `pay_money` numeric(12,2) unsigned NOT NULL COMMENT '应发金额(最终)',
  `employer_id` bigint unsigned NOT NULL COMMENT '雇佣ID',
  `department_id` bigint unsigned NOT NULL COMMENT '部门ID',
  `employee_class_id` bigint unsigned NOT NULL COMMENT '聘员类别ID',
  `position_id` bigint unsigned NOT NULL COMMENT '职务ID',
  `create_time` datetime NOT NULL COMMENT '创建时间'
) ENGINE=INNODB COMMENT '薪酬发放表(回收)';

ALTER TABLE user_info ADD native_place char(255) COMMENT '籍贯';
ALTER TABLE user_info ADD native_place_label char(255) COMMENT '籍贯文本';
ALTER TABLE user_info ADD political char(255) COMMENT '政治面貌';
ALTER TABLE user_info ADD education char(255) COMMENT '学历';
ALTER TABLE user_info ADD education_record char(255) COMMENT '学历记录';
ALTER TABLE user_info ADD degree char(255) COMMENT '学位';
ALTER TABLE user_info ADD work_exp varchar(2048) COMMENT '工作经历';
ALTER TABLE user_info ADD work_employer_id char(255) COMMENT '工作单位';
ALTER TABLE user_info ADD work_department_id char(255) COMMENT '工作部门';
ALTER TABLE user_info ADD work_entry_date char(255) COMMENT '工作入职日期';
ALTER TABLE user_info ADD is_special_class smallint default -1 NOT NULL COMMENT '是否四类';
ALTER TABLE user_info ADD is_retirement smallint default -1 NOT NULL COMMENT '是否退休';
ALTER TABLE user_info ADD retire_date date COMMENT '退休日期';
ALTER TABLE user_info ADD is_probationary smallint default -1 NOT NULL COMMENT '是否试用';
ALTER TABLE user_info ADD probationary_during_month int unsigned NOT NULL default 0 COMMENT '试用期持续时间（月）';
ALTER TABLE user_info ADD probationary_end_date date COMMENT '试用期结束日期';
ALTER TABLE user_info ADD ordering char(255) NOT NULL default '' COMMENT '排序编号';
ALTER TABLE user_info ADD bank_account char(255) COMMENT '银行帐号';
ALTER TABLE user_info ADD appraisal char(255) COMMENT '考核';
ALTER TABLE user_info ADD wages_employer_id bigint unsigned COMMENT '薪酬单位';
ALTER TABLE user_info ADD wages_department_id bigint unsigned COMMENT '薪酬部门';
ALTER TABLE user_info ADD wages_employee_class_id bigint unsigned COMMENT '薪酬聘员类别';
ALTER TABLE user_info ADD wages_position_id bigint unsigned COMMENT '薪酬职务待遇';
ALTER TABLE user_info ADD wages_entry_date date COMMENT '薪酬入职日期';
ALTER TABLE user_info ADD wages_profess char(255) COMMENT '薪酬职称';

ALTER TABLE `user_info` ADD FOREIGN KEY (`wages_employer_id`) REFERENCES `salary_employer` (`id`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `user_info` ADD FOREIGN KEY (`wages_department_id`) REFERENCES `salary_department` (`id`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `user_info` ADD FOREIGN KEY (`wages_employee_class_id`) REFERENCES `salary_employee_class` (`id`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `user_info` ADD FOREIGN KEY (`wages_position_id`) REFERENCES `salary_position` (`id`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;

