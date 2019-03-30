
DROP TABLE IF EXISTS `assets`;
CREATE TABLE `assets` (
  `hash_id` CHAR(255) NOT NULL COMMENT 'hash_id',
  `hash_file_name` CHAR(255) NOT NULL COMMENT 'hash文件名',
  `file_name` CHAR(255) NOT NULL COMMENT '文件名',
  `file_ext` CHAR(255) NOT NULL COMMENT '文件后缀',
  `file_size` BIGINT UNSIGNED DEFAULT 0 NOT NULL COMMENT '文件大小',
  `content_type` CHAR(255) NOT NULL COMMENT '内容类型',
  `path` CHAR(255) NOT NULL COMMENT '存在路径',
  `from_url` CHAR(255) NOT NULL COMMENT '来源地址',
  `download_url` CHAR(255) NOT NULL COMMENT '下载地址',
  `call_qty` INT UNSIGNED DEFAULT 0 NOT NULL COMMENT '调用次数',
  `call_last_time` DATETIME COMMENT '最后一次调用时间',
  `uid` VARCHAR(4096) NOT NULL COMMENT '拥有者',
  `create_time` DATETIME COMMENT '创建日期',
  `update_time` DATETIME COMMENT '更新日期',
  PRIMARY KEY (`hash_id`)
) ENGINE=INNODB COMMENT '资源';

DROP TABLE IF EXISTS `assets_call_level`;
CREATE TABLE `assets_call_level` (
 `lv` CHAR(255) NOT NULL COMMENT '等级 ，最高理论无限',
 `lv_as` INT UNSIGNED NOT NULL DEFAULT 1 COMMENT '等级等价',
 `lv_desc` VARCHAR (1024) NULL COMMENT '等级描述',
 `file_size_unit` CHAR(255) NOT NULL COMMENT '文件大小单位',
 `file_max_total_size` BIGINT NOT NULL COMMENT '允许的总体文件大小',
 `file_max_simple_size` BIGINT NOT NULL COMMENT '允许的单个文件大小',
 `file_max_qty` BIGINT NOT NULL COMMENT '允许的文件数',
 `file_ext` VARCHAR (4096) NULL COMMENT '允许的文件后缀',
  PRIMARY KEY (`lv`)
) ENGINE=INNODB COMMENT '资源等级参数';

DELETE FROM `assets_call_level`;
INSERT INTO `assets_call_level` (lv, lv_as, lv_desc, file_size_unit, file_max_total_size, file_max_simple_size, file_max_qty, file_ext)
VALUES ('try',1,'体验资源包','KB','1024','100',3, ',,,,,txt');
INSERT INTO `assets_call_level` (lv, lv_as, lv_desc, file_size_unit, file_max_total_size, file_max_simple_size, file_max_qty, file_ext)
VALUES ('rookie',2,'入门资源包','MB','200','1',50, ',,,,,txt,gif,jpeg,jpg,bmp,png,pdf,doc,docx,ppt,pptx,xls,xlsx,csv,psd');
INSERT INTO `assets_call_level` (lv, lv_as, lv_desc, file_size_unit, file_max_total_size, file_max_simple_size, file_max_qty, file_ext)
VALUES ('low',3,'初级资源包','MB','1024','10',100, ',,,,,txt,gif,jpeg,jpg,bmp,png,pdf,doc,docx,ppt,pptx,xls,xlsx,csv,psd,rar,7z,zip');
INSERT INTO `assets_call_level` (lv, lv_as, lv_desc, file_size_unit, file_max_total_size, file_max_simple_size, file_max_qty, file_ext)
VALUES ('low2',3,'初级大文件包','MB','1024','50',50, ',,,,,txt,gif,jpeg,jpg,bmp,png,pdf,doc,docx,ppt,pptx,xls,xlsx,csv,psd,rar,7z,zip');
INSERT INTO `assets_call_level` (lv, lv_as, lv_desc, file_size_unit, file_max_total_size, file_max_simple_size, file_max_qty, file_ext)
VALUES ('normal',4,'中级资源包','MB','2048','30',300, ',,,,,txt,gif,jpeg,jpg,bmp,png,pdf,doc,docx,ppt,pptx,xls,xlsx,csv,psd,rar,7z,zip,mp4,rmvb,mkv,avi,wmv,mov,mpg,3gp,mp3,wma,ape,flac,wav,ogg,m4a');
INSERT INTO `assets_call_level` (lv, lv_as, lv_desc, file_size_unit, file_max_total_size, file_max_simple_size, file_max_qty, file_ext)
VALUES ('normal2',4,'中级大文件包','MB','2048','100',100, ',,,,,txt,gif,jpeg,jpg,bmp,png,pdf,doc,docx,ppt,pptx,xls,xlsx,csv,psd,rar,7z,zip,mp4,rmvb,mkv,avi,wmv,mov,mpg,3gp,mp3,wma,ape,flac,wav,ogg,m4a');
INSERT INTO `assets_call_level` (lv, lv_as, lv_desc, file_size_unit, file_max_total_size, file_max_simple_size, file_max_qty, file_ext)
VALUES ('super',9,'无尽资源包','GB','1024','1',10000, ',,,,,mp4,rmvb,mkv,avi,wmv,mov,mpg,3gp,mp3,wma,ape,flac,wav,ogg,m4a,gif,jpeg,jpg,bmp,png,ico,tga,txt,pdf,doc,docx,ppt,pptx,xls,xlsx,csv,psd,rar,7z,zip,iso,cso');

ALTER TABLE user_info ADD `call_level` char(255) COMMENT '调用级别，影响用户不登录调用接口的次数';
