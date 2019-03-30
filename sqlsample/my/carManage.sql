
DROP TABLE IF EXISTS `car_department`;
CREATE TABLE `car_department` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `name` char(255) NOT NULL COMMENT '部门名称',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE (`name`)
) ENGINE=INNODB COMMENT '部门表';


DROP TABLE IF EXISTS `car_position`;
CREATE TABLE `car_position` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `department_id` bigint unsigned NOT NULL COMMENT '所属部门',
  `name` char(255) NOT NULL COMMENT '职务名称',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE (`department_id`,`name`)
) ENGINE=INNODB COMMENT '职务表';

ALTER TABLE `car_position` ADD FOREIGN KEY (`department_id`) REFERENCES `car_department` (`id`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;



DROP TABLE IF EXISTS `car`;
CREATE TABLE `car` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `department_id` bigint unsigned NOT NULL COMMENT '所属部门 设为 0 则所有可见',
  `name` char(255) COMMENT '车车名',
  `region` char(255) COMMENT '地区ID集',
  `region_label` char(255) COMMENT '地区文本',
  `license_province_sn` char(255) NOT NULL COMMENT '车牌号省SN',
  `license_city_sn` char(255) NOT NULL COMMENT '车牌号市SN',
  `license_number` char(255) NOT NULL COMMENT '车牌号码',
  `type` char(255) NOT NULL COMMENT '车种类型',
  `engine_number` char(255) COMMENT '发动机号码',
  `identification_number` char(255) COMMENT '车辆识别号',
  `status` char(255) NOT NULL DEFAULT 'disabled' COMMENT '车车状态 disabled/ready/booking/exigent_booking/working/repair',
  `is_exigent` tinyint NOT NULL DEFAULT -1 COMMENT '是否设为紧急用车储备',
  `is_share` tinyint NOT NULL DEFAULT 1 COMMENT '是否允许拼车',
  `stay_place` char(255) NOT NULL COMMENT '当前停车位置',
  `seat` smallint unsigned NOT NULL COMMENT '座位数',
  `seat_current` smallint unsigned NOT NULL COMMENT '当前座位数',
  `endurance` numeric(10,3) unsigned NOT NULL COMMENT '续航公里',
  `gasoline` numeric(10,3) unsigned NOT NULL COMMENT '汽油量升',
  `gasoline_current` numeric(10,3) unsigned NOT NULL COMMENT '当前汽油量升',
  `items_status` json COMMENT '零部件状况',
  `repair_record` text COMMENT '维护历史记录',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=INNODB COMMENT '车表';
ALTER TABLE `car` ADD FOREIGN KEY (`department_id`) REFERENCES `car_department` (`id`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;


DROP TABLE IF EXISTS `car_apply`;
CREATE TABLE `car_apply` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `uid` bigint unsigned NOT NULL COMMENT '申请人',
  `car_id` bigint unsigned NOT NULL COMMENT '申请车ID',
  `status` char(255) NOT NULL DEFAULT 'submitted' COMMENT '申请状态 submitted/objected/passed/finished',
  `number_of_people` smallint unsigned NOT NULL COMMENT '搭载人数',
  `destination` char(255) NOT NULL COMMENT '目的地',
  `set_out_time` datetime NOT NULL COMMENT '出发时间',
  `apply_reason` char(255) COMMENT '申请理由',
  `submitted_time` datetime NOT NULL COMMENT '申请时间',
  `canceled_time` datetime COMMENT '取消时间',
  `objected_time` datetime COMMENT '拒绝时间',
  `objected_reason` char(255) COMMENT '拒绝理由',
  `passed_time` datetime COMMENT '通过时间',
  `working_time` datetime COMMENT '工作时间',
  `finished_time` datetime COMMENT '完成时间',
  `finished_data` json COMMENT '完成时填写的回馈数据',
  PRIMARY KEY (`id`)
) ENGINE=INNODB COMMENT '预约申请用车表';
ALTER TABLE `car_apply` ADD FOREIGN KEY (`uid`) REFERENCES `user` (`uid`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `car_apply` ADD FOREIGN KEY (`car_id`) REFERENCES `car` (`id`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;



DROP TABLE IF EXISTS `car_exigent`;
CREATE TABLE `car_exigent` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `uid` bigint unsigned NOT NULL COMMENT '申请人',
  `car_id` bigint unsigned NOT NULL COMMENT '申请车ID',
  `status` char(255) NOT NULL COMMENT '申请状态 abort/working/finished',
  `number_of_people` smallint unsigned COMMENT '搭载人数',
  `destination` char(255) COMMENT '目的地',
  `set_out_time` datetime COMMENT '出发时间',
  `apply_reason` char(255) COMMENT '申请理由',
  `abort_time` datetime COMMENT '中止时间',
  `abort_reason` char(255) COMMENT '中止理由',
  `working_time` datetime NOT NULL COMMENT '工作时间',
  `finished_time` datetime COMMENT '完成时间',
  `finished_data` json COMMENT '完成时填写的回馈数据',
  PRIMARY KEY (`id`)
) ENGINE=INNODB COMMENT '申请用车表';
ALTER TABLE `car_exigent` ADD FOREIGN KEY (`uid`) REFERENCES `user` (`uid`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `car_exigent` ADD FOREIGN KEY (`car_id`) REFERENCES `car` (`id`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;



DROP TABLE IF EXISTS `car_share`;
CREATE TABLE `car_share` (
  `id` bigint unsigned AUTO_INCREMENT NOT NULL COMMENT 'id',
  `uid` bigint unsigned NOT NULL COMMENT '申请人',
  `apply_id` bigint unsigned NOT NULL COMMENT '原申请ID',
  `car_id` bigint unsigned NOT NULL COMMENT '车辆ID',
  `status` char(255) NOT NULL COMMENT '申请状态 submitted/objected/passed',
  `number_of_people` smallint unsigned COMMENT '搭车人数',
  `apply_reason` char(255) COMMENT '申请理由',
  `submitted_time` datetime NOT NULL COMMENT '申请时间',
  `canceled_time` datetime COMMENT '取消时间',
  `objected_time` datetime COMMENT '拒绝时间',
  `objected_reason` char(255) COMMENT '拒绝理由',
  `passed_time` datetime COMMENT '通过时间',
  PRIMARY KEY (`id`)
) ENGINE=INNODB COMMENT '拼车表';
ALTER TABLE `car_share` ADD FOREIGN KEY (`uid`) REFERENCES `user` (`uid`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `car_share` ADD FOREIGN KEY (`apply_id`) REFERENCES `car_apply` (`id`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;


ALTER TABLE `user_info` ADD `car_department_id` bigint unsigned COMMENT '部门';
ALTER TABLE `user_info` ADD `car_position_id` bigint unsigned COMMENT '职务';

ALTER TABLE `user_info` ADD FOREIGN KEY (`car_department_id`) REFERENCES `car_department` (`id`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `user_info` ADD FOREIGN KEY (`car_position_id`) REFERENCES `car_position` (`id`) MATCH SIMPLE ON UPDATE NO ACTION ON DELETE NO ACTION;

