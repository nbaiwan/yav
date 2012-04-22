/*
Navicat MySQL Data Transfer

Source Server         : root@192.168.60.218
Source Server Version : 50522
Source Host           : 192.168.60.218:3306
Source Database       : vsenho_movie

Target Server Type    : MYSQL
Target Server Version : 50522
File Encoding         : 65001

Date: 2012-04-22 15:31:43
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `sowel_movie_ad_categories`
-- ----------------------------
DROP TABLE IF EXISTS `sowel_movie_ad_categories`;
CREATE TABLE `sowel_movie_ad_categories` (
  `ad_categories_id` mediumint(5) unsigned NOT NULL AUTO_INCREMENT,
  `ad_categories_name` varchar(15) NOT NULL,
  `ad_categories_rank` mediumint(5) unsigned NOT NULL,
  PRIMARY KEY (`ad_categories_id`),
  KEY `ad_categories_id` (`ad_categories_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sowel_movie_ad_categories
-- ----------------------------

-- ----------------------------
-- Table structure for `sowel_movie_ad_data`
-- ----------------------------
DROP TABLE IF EXISTS `sowel_movie_ad_data`;
CREATE TABLE `sowel_movie_ad_data` (
  `ad_data_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '广告编号',
  `ad_position_id` int(11) unsigned NOT NULL COMMENT '广告位编号',
  `ad_data_page` varchar(100) NOT NULL COMMENT '属所页面',
  `ad_data_type` tinyint(4) unsigned NOT NULL COMMENT '广告类型',
  `ad_data_subject` char(50) NOT NULL COMMENT '广告标题',
  `ad_data_image_md5` char(40) NOT NULL COMMENT '广告图片',
  `ad_data_flash_md5` char(40) NOT NULL,
  `ad_data_link` varchar(200) NOT NULL COMMENT '广告链接',
  `ad_data_html` text NOT NULL COMMENT '广告内容',
  `ad_data_expire_start` int(11) unsigned NOT NULL COMMENT '广告开始时间',
  `ad_data_expire_end` int(11) unsigned NOT NULL COMMENT '广告结束时间',
  `ad_data_rank` tinyint(4) unsigned NOT NULL DEFAULT '255' COMMENT '广告排序',
  `ad_data_is_show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '广告是否显示',
  `ad_data_status` tinyint(1) unsigned NOT NULL,
  `ad_data_relative_id` int(11) unsigned NOT NULL COMMENT '游戏或文档联关id',
  `insert_user_id` int(11) unsigned NOT NULL COMMENT '创建人',
  `update_user_id` int(11) unsigned NOT NULL COMMENT '更新人',
  `ad_data_dateline` int(11) unsigned NOT NULL COMMENT '广告添加时间',
  PRIMARY KEY (`ad_data_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sowel_movie_ad_data
-- ----------------------------

-- ----------------------------
-- Table structure for `sowel_movie_ad_position`
-- ----------------------------
DROP TABLE IF EXISTS `sowel_movie_ad_position`;
CREATE TABLE `sowel_movie_ad_position` (
  `ad_position_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '广告位编号',
  `ad_categories_id` mediumint(5) unsigned NOT NULL COMMENT '分类',
  `ad_position_name` char(30) NOT NULL COMMENT '广告位名称',
  `ad_position_identify` char(30) NOT NULL COMMENT '广告位标识',
  `ad_position_remark` text NOT NULL COMMENT '广告位说明',
  `ad_position_width` mediumint(4) unsigned NOT NULL,
  `ad_position_height` mediumint(4) unsigned NOT NULL,
  `ad_position_type` tinyint(4) unsigned NOT NULL,
  `ad_position_rank` tinyint(4) unsigned NOT NULL COMMENT '广告位排序',
  `ad_position_target` char(10) NOT NULL COMMENT '开打链接的方式',
  `ad_position_dateline` datetime NOT NULL,
  `ad_position_system` tinyint(1) unsigned NOT NULL COMMENT '否是为系统',
  `ad_position_relative_type` char(10) NOT NULL COMMENT '联关类型',
  `ad_position_status` tinyint(4) unsigned NOT NULL COMMENT '广告位状态',
  PRIMARY KEY (`ad_position_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sowel_movie_ad_position
-- ----------------------------

-- ----------------------------
-- Table structure for `sowel_movie_collect_content`
-- ----------------------------
DROP TABLE IF EXISTS `sowel_movie_collect_content`;
CREATE TABLE `sowel_movie_collect_content` (
  `collect_content_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `collect_task_id` int(11) unsigned NOT NULL,
  `collect_content_url` varchar(255) NOT NULL,
  `is_collected` tinyint(1) unsigned NOT NULL,
  `is_published` tinyint(1) unsigned NOT NULL,
  `lasttime` int(10) unsigned NOT NULL,
  `dateline` int(10) unsigned NOT NULL,
  PRIMARY KEY (`collect_content_id`),
  KEY `media_data_id` (`is_collected`) USING BTREE,
  KEY `collect_list_day` (`dateline`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sowel_movie_collect_content
-- ----------------------------

-- ----------------------------
-- Table structure for `sowel_movie_collect_list`
-- ----------------------------
DROP TABLE IF EXISTS `sowel_movie_collect_list`;
CREATE TABLE `sowel_movie_collect_list` (
  `collect_list_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `collect_task_id` int(11) unsigned NOT NULL,
  `collect_list_url` varchar(255) NOT NULL,
  `is_collected` tinyint(1) unsigned NOT NULL,
  `is_published` tinyint(1) unsigned NOT NULL,
  `lasttime` int(10) unsigned NOT NULL,
  `dateline` int(10) unsigned NOT NULL,
  PRIMARY KEY (`collect_list_id`),
  KEY `media_data_id` (`is_collected`) USING BTREE,
  KEY `collect_list_day` (`dateline`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sowel_movie_collect_list
-- ----------------------------

-- ----------------------------
-- Table structure for `sowel_movie_collect_log`
-- ----------------------------
DROP TABLE IF EXISTS `sowel_movie_collect_log`;
CREATE TABLE `sowel_movie_collect_log` (
  `collect_log_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `collect_log_msg` varchar(255) NOT NULL,
  `collect_log_insert_time` date NOT NULL,
  PRIMARY KEY (`collect_log_id`),
  KEY `collect_log_id` (`collect_log_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sowel_movie_collect_log
-- ----------------------------

-- ----------------------------
-- Table structure for `sowel_movie_collect_model`
-- ----------------------------
DROP TABLE IF EXISTS `sowel_movie_collect_model`;
CREATE TABLE `sowel_movie_collect_model` (
  `collect_model_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '采集模型编号',
  `collect_model_name` char(30) NOT NULL COMMENT '采集模型名称',
  `collect_model_identify` char(30) NOT NULL COMMENT '采集模型标识',
  `collect_model_rank` tinyint(3) unsigned NOT NULL COMMENT '采集模型排序',
  `collect_model_lasttime` int(11) unsigned NOT NULL COMMENT '采集模型修改时间',
  `collect_model_dateline` int(11) unsigned NOT NULL COMMENT '采集模型添加时间',
  `collect_model_status` tinyint(1) unsigned NOT NULL COMMENT '采集模型状态',
  `content_model_id` int(11) NOT NULL DEFAULT '0' COMMENT '应对的内容模型ID',
  PRIMARY KEY (`collect_model_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sowel_movie_collect_model
-- ----------------------------
INSERT INTO `sowel_movie_collect_model` VALUES ('1', '电影模型', 'movie', '1', '1334546383', '1322563821', '4', '0');

-- ----------------------------
-- Table structure for `sowel_movie_collect_model_addonsmovie`
-- ----------------------------
DROP TABLE IF EXISTS `sowel_movie_collect_model_addonsmovie`;
CREATE TABLE `sowel_movie_collect_model_addonsmovie` (
  `collect_content_id` int(10) unsigned NOT NULL COMMENT '采集列表id',
  `movie_name` varchar(255) NOT NULL,
  `movie_director` varchar(255) NOT NULL,
  `movie_leading` varchar(255) NOT NULL,
  `movie_classes` varchar(255) NOT NULL,
  `movie_length` varchar(255) NOT NULL,
  `movie_region` varchar(255) NOT NULL,
  `movie_languages` varchar(255) NOT NULL,
  `movie_filming_time` varchar(255) NOT NULL,
  `movie_play_time` varchar(255) NOT NULL,
  `movie_story` text NOT NULL,
  `movie_play_url` text NOT NULL,
  `movie_posters` varchar(255) NOT NULL,
  `movie_lastdate` varchar(255) NOT NULL,
  `movie_status` varchar(255) NOT NULL,
  UNIQUE KEY `collect_list_id` (`collect_content_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='电影模型附加表';

-- ----------------------------
-- Records of sowel_movie_collect_model_addonsmovie
-- ----------------------------

-- ----------------------------
-- Table structure for `sowel_movie_collect_model_fields`
-- ----------------------------
DROP TABLE IF EXISTS `sowel_movie_collect_model_fields`;
CREATE TABLE `sowel_movie_collect_model_fields` (
  `collect_fields_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '采集字段编号',
  `collect_model_id` int(11) unsigned NOT NULL COMMENT '采集模型编号',
  `collect_fields_name` char(10) NOT NULL COMMENT '采集字段名称',
  `content_model_field_id` int(11) NOT NULL DEFAULT '0' COMMENT '与内容模型对应的字段',
  `collect_fields_identify` char(30) NOT NULL COMMENT '采集字段标识',
  `collect_fields_rank` tinyint(4) unsigned NOT NULL COMMENT '采集字段排序',
  `collect_fields_belong` tinyint(4) unsigned NOT NULL,
  `collect_fields_type` tinyint(4) unsigned NOT NULL COMMENT '采集字段类型',
  `collect_fields_system` tinyint(4) unsigned NOT NULL,
  `collect_fields_status` tinyint(4) unsigned NOT NULL COMMENT '采集字段状态',
  `collect_fields_dateline` int(11) unsigned NOT NULL COMMENT '采集字段添加时间',
  `collect_fields_lasttime` int(11) unsigned NOT NULL COMMENT '采集字段修改时间',
  PRIMARY KEY (`collect_fields_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sowel_movie_collect_model_fields
-- ----------------------------
INSERT INTO `sowel_movie_collect_model_fields` VALUES ('1', '1', '电影名称', '0', 'movie_name', '1', '0', '1', '0', '4', '1322646450', '1322655097');
INSERT INTO `sowel_movie_collect_model_fields` VALUES ('2', '1', '导演', '0', 'movie_director', '2', '0', '1', '0', '4', '1322655593', '1322655593');
INSERT INTO `sowel_movie_collect_model_fields` VALUES ('3', '1', '主演', '0', 'movie_leading', '3', '0', '1', '0', '4', '1322656283', '1322656283');
INSERT INTO `sowel_movie_collect_model_fields` VALUES ('4', '1', '影片分类', '0', 'movie_classes', '4', '0', '1', '0', '4', '1322656352', '1322656352');
INSERT INTO `sowel_movie_collect_model_fields` VALUES ('5', '1', '片长', '0', 'movie_length', '5', '0', '1', '0', '4', '1322656385', '1322656385');
INSERT INTO `sowel_movie_collect_model_fields` VALUES ('6', '1', '地区', '0', 'movie_region', '6', '0', '1', '0', '4', '1322656414', '1322656414');
INSERT INTO `sowel_movie_collect_model_fields` VALUES ('7', '1', '语言', '0', 'movie_languages', '7', '0', '1', '0', '4', '1322656435', '1322656446');
INSERT INTO `sowel_movie_collect_model_fields` VALUES ('8', '1', '拍摄时间', '0', 'movie_filming_time', '8', '0', '1', '0', '4', '1322656517', '1322656517');
INSERT INTO `sowel_movie_collect_model_fields` VALUES ('9', '1', '上映时间', '0', 'movie_play_time', '9', '0', '1', '0', '4', '1322656602', '1322656602');
INSERT INTO `sowel_movie_collect_model_fields` VALUES ('10', '1', '剧情介绍', '0', 'movie_story', '10', '0', '2', '0', '4', '1322656711', '1322656711');
INSERT INTO `sowel_movie_collect_model_fields` VALUES ('11', '1', '播放地址', '0', 'movie_play_url', '11', '0', '2', '0', '4', '1322656801', '1322656801');
INSERT INTO `sowel_movie_collect_model_fields` VALUES ('12', '1', '海报', '0', 'movie_posters', '12', '0', '1', '0', '4', '1322657210', '1322657210');
INSERT INTO `sowel_movie_collect_model_fields` VALUES ('13', '1', '更新时间', '0', 'movie_lastdate', '10', '0', '1', '0', '4', '1323186942', '1323186959');
INSERT INTO `sowel_movie_collect_model_fields` VALUES ('14', '1', '电影状态', '0', 'movie_status', '11', '0', '1', '0', '4', '1323186991', '1323186991');

-- ----------------------------
-- Table structure for `sowel_movie_collect_source`
-- ----------------------------
DROP TABLE IF EXISTS `sowel_movie_collect_source`;
CREATE TABLE `sowel_movie_collect_source` (
  `collect_source_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '采集来源编号',
  `collect_source_name` char(30) NOT NULL COMMENT '采集来源名称',
  `collect_source_website` varchar(100) NOT NULL COMMENT '采集来源网站',
  `collect_source_remark` text NOT NULL COMMENT '采集来源说明',
  `collect_source_status` tinyint(1) unsigned NOT NULL COMMENT '采集来源状态',
  `collect_source_rank` smallint(6) unsigned NOT NULL COMMENT '采集来源排序',
  `collect_source_lasttime` int(10) unsigned NOT NULL COMMENT '采集来源修改时间',
  `collect_source_dateline` int(10) unsigned NOT NULL COMMENT '采集来源添加时间',
  PRIMARY KEY (`collect_source_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sowel_movie_collect_source
-- ----------------------------
INSERT INTO `sowel_movie_collect_source` VALUES ('1', '快播资源采集站', 'http://www.hacow.me/', '快播资源采集站', '4', '1', '1334511097', '1322559463');
INSERT INTO `sowel_movie_collect_source` VALUES ('2', '百度影音资源站', 'http://www.baduzy.com/', '百度影音资源站', '4', '2', '1322564433', '1322564433');

-- ----------------------------
-- Table structure for `sowel_movie_collect_task`
-- ----------------------------
DROP TABLE IF EXISTS `sowel_movie_collect_task`;
CREATE TABLE `sowel_movie_collect_task` (
  `collect_task_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `collect_task_name` char(30) NOT NULL,
  `collect_template_id` int(11) unsigned NOT NULL COMMENT '板模id',
  `collect_task_urls` text NOT NULL COMMENT '采集列表地址',
  `collect_task_list_rules` text NOT NULL COMMENT '列表规则',
  `collect_task_addons_rules` text NOT NULL COMMENT '附加规则',
  `collect_task_rank` smallint(6) unsigned NOT NULL,
  `collect_task_status` tinyint(3) unsigned NOT NULL,
  `collect_task_lastcollecttime` int(11) unsigned NOT NULL COMMENT '次上采集时间',
  `collect_task_lasttime` int(11) unsigned NOT NULL,
  `collect_task_dateline` int(11) unsigned NOT NULL,
  PRIMARY KEY (`collect_task_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sowel_movie_collect_task
-- ----------------------------
INSERT INTO `sowel_movie_collect_task` VALUES ('1', '快播哈库电影', '1', 'http://www.hacow.me/?p=<1,766,1,False>', 'null', 'null', '1', '4', '0', '1334565829', '1322845267');
INSERT INTO `sowel_movie_collect_task` VALUES ('2', '百度影音资源电影', '2', 'http://www.baduzy.com/list/?0-<1,146,1,False>.html\r\n', '{\"begin\":\"\",\"end\":\"\"}', '', '2', '4', '0', '1323186011', '1323186011');

-- ----------------------------
-- Table structure for `sowel_movie_collect_template`
-- ----------------------------
DROP TABLE IF EXISTS `sowel_movie_collect_template`;
CREATE TABLE `sowel_movie_collect_template` (
  `collect_template_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '采集模版编号',
  `collect_source_id` int(11) unsigned NOT NULL COMMENT '采集来源编号',
  `collect_model_id` int(11) unsigned NOT NULL COMMENT '采集模型编号',
  `collect_template_charset` int(3) unsigned NOT NULL COMMENT '码编',
  `collect_template_name` char(30) NOT NULL COMMENT '采集模版名称',
  `collect_template_remark` text NOT NULL COMMENT '采集模版说明',
  `collect_template_list_rules` text NOT NULL COMMENT '采集模版列表规则',
  `collect_template_addons_rules` text NOT NULL COMMENT '采集模版附加规则',
  `collect_template_status` tinyint(4) unsigned NOT NULL COMMENT '采集模板状态',
  `collect_template_rank` tinyint(4) unsigned NOT NULL COMMENT '采集模板排序',
  `insert_user_id` int(10) unsigned NOT NULL COMMENT '创建人id',
  `update_user_id` int(10) unsigned NOT NULL COMMENT '更新人id',
  `collect_template_dateline` int(11) unsigned NOT NULL COMMENT '采集模板添加时间',
  `collect_template_lasttime` int(11) unsigned NOT NULL COMMENT '采集模板修改时间',
  PRIMARY KEY (`collect_template_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sowel_movie_collect_template
-- ----------------------------
INSERT INTO `sowel_movie_collect_template` VALUES ('1', '1', '1', '0', '快播哈库电影', '电影', '{\"begin\":\"<!--\\u5217\\u8868\\u4ee3\\u7801\\u5f00\\u59cb-->\",\"end\":\"<!--\\u5217\\u8868\\u4ee3\\u7801\\u7ed3\\u675f-->\"}', '{\"movie_name\":{\"filter\":\"\",\"hander\":\"\",\"collect\":\"\",\"is_repeat\":\"\",\"is_empty\":\"\",\"include\":\"\",\"exclude\":\"\",\"begin\":\"<!--\\u5f71\\u7247\\u540d\\u79f0\\u5f00\\u59cb\\u4ee3\\u7801-->\",\"end\":\"<!--\\u5f71\\u7247\\u540d\\u79f0\\u7ed3\\u675f\\u4ee3\\u7801-->\"},\"movie_director\":{\"filter\":\"\",\"hander\":\"\",\"collect\":\"\",\"is_repeat\":\"\",\"is_empty\":\"\",\"include\":\"\",\"exclude\":\"\",\"begin\":\"\",\"end\":\"\"},\"movie_leading\":{\"filter\":\"\",\"hander\":\"\",\"collect\":\"\",\"is_repeat\":\"\",\"is_empty\":\"\",\"include\":\"\",\"exclude\":\"\",\"begin\":\"\",\"end\":\"\"},\"movie_classes\":{\"filter\":\"\",\"hander\":\"\",\"collect\":\"\",\"is_repeat\":\"\",\"is_empty\":\"\",\"include\":\"\",\"exclude\":\"\",\"begin\":\"<!--\\u5f71\\u7247\\u7c7b\\u578b\\u5f00\\u59cb\\u4ee3\\u7801-->\",\"end\":\"<!--\\u5f71\\u7247\\u7c7b\\u578b\\u7ed3\\u675f\\u4ee3\\u7801-->\"},\"movie_length\":{\"filter\":\"\",\"hander\":\"\",\"collect\":\"\",\"is_repeat\":\"\",\"is_empty\":\"\",\"include\":\"\",\"exclude\":\"\",\"begin\":\"\",\"end\":\"\"},\"movie_region\":{\"filter\":\"\",\"hander\":\"\",\"collect\":\"\",\"is_repeat\":\"\",\"is_empty\":\"\",\"include\":\"\",\"exclude\":\"\",\"begin\":\"<!--\\u5f71\\u7247\\u5730\\u533a\\u5f00\\u59cb\\u4ee3\\u7801-->\",\"end\":\"<!--\\u5f71\\u7247\\u5730\\u533a\\u7ed3\\u675f\\u4ee3\\u7801-->\"},\"movie_languages\":{\"filter\":\"\",\"hander\":\"\",\"collect\":\"\",\"is_repeat\":\"\",\"is_empty\":\"\",\"include\":\"\",\"exclude\":\"\",\"begin\":\"\",\"end\":\"\"},\"movie_filming_time\":{\"filter\":\"\",\"hander\":\"\",\"collect\":\"\",\"is_repeat\":\"\",\"is_empty\":\"\",\"include\":\"\",\"exclude\":\"\",\"begin\":\"\",\"end\":\"\"},\"movie_play_time\":{\"filter\":\"\",\"hander\":\"\",\"collect\":\"\",\"is_repeat\":\"\",\"is_empty\":\"\",\"include\":\"\",\"exclude\":\"\",\"begin\":\"\",\"end\":\"\"},\"movie_story\":{\"filter\":\"\",\"hander\":\"\",\"collect\":\"\",\"is_repeat\":\"\",\"is_empty\":\"\",\"include\":\"\",\"exclude\":\"\",\"begin\":\"<!--\\u5f71\\u7247\\u4ecb\\u7ecd\\u5f00\\u59cb\\u4ee3\\u7801-->\",\"end\":\"<!--\\u5f71\\u7247\\u4ecb\\u7ecd\\u7ed3\\u675f\\u4ee3\\u7801-->\"},\"movie_play_url\":{\"filter\":\"\",\"hander\":\"baidu_play_list\",\"collect\":\"\",\"is_repeat\":\"\",\"is_empty\":\"\",\"include\":\"\",\"exclude\":\"\",\"begin\":\"<!--\\u64ad\\u653e\\u5217\\u8868\\u5f00\\u59cb\\u4ee3\\u7801-->\",\"end\":\"<!--\\u64ad\\u653e\\u5217\\u8868\\u7ed3\\u675f\\u4ee3\\u7801-->\"},\"movie_posters\":{\"filter\":\"\",\"hander\":\"\",\"collect\":\"\",\"is_repeat\":\"\",\"is_empty\":\"\",\"include\":\"\",\"exclude\":\"\",\"begin\":\"<!--\\u5f71\\u7247\\u56fe\\u7247\\u5f00\\u59cb\\u4ee3\\u7801-->\",\"end\":\"<!--\\u5f71\\u7247\\u56fe\\u7247\\u7ed3\\u675f\\u4ee3\\u7801-->\"},\"movie_lastdate\":{\"filter\":\"\",\"hander\":\"\",\"collect\":\"\",\"is_repeat\":\"\",\"is_empty\":\"\",\"include\":\"\",\"exclude\":\"\",\"begin\":\"<!--\\u5f71\\u7247\\u66f4\\u65b0\\u65f6\\u95f4\\u5f00\\u59cb\\u4ee3\\u7801-->\",\"end\":\"<!--\\u5f71\\u7247\\u66f4\\u65b0\\u65f6\\u95f4\\u7ed3\\u675f\\u4ee3\\u7801-->\"},\"movie_status\":{\"filter\":\"\",\"hander\":\"\",\"collect\":\"\",\"is_repeat\":\"\",\"is_empty\":\"\",\"include\":\"\",\"exclude\":\"\",\"begin\":\"<!--\\u5f71\\u7247\\u72b6\\u6001\\u5f00\\u59cb\\u4ee3\\u7801-->\",\"end\":\"<!--\\u5f71\\u7247\\u72b6\\u6001\\u7ed3\\u675f\\u4ee3\\u7801-->\"}}', '4', '1', '1', '1', '1322564702', '1334543835');
INSERT INTO `sowel_movie_collect_template` VALUES ('2', '2', '1', '0', '百度影音电影', '百度影音电影', '{\"begin\":\"<!--\\u9876\\u90e8\\u622a\\u53d6\\u6807\\u7b7e -->\",\"end\":\"<!-- \\u5e95\\u90e8\\u622a\\u53d6\\u6807\\u7b7e-->\"}', '{\"movie_name\":{\"is_repeat\":\"\",\"filter\":\"\",\"hander\":\"\",\"collect\":\"\",\"allow_repeat\":\"\",\"allow_empty\":\"\",\"include\":\"\",\"exclude\":\"\",\"begin\":\"<!--\\u5f71\\u7247\\u540d\\u79f0\\u5f00\\u59cb\\u4ee3\\u7801-->\",\"end\":\"<!--\\u5f71\\u7247\\u540d\\u79f0\\u7ed3\\u675f\\u4ee3\\u7801-->\"},\"movie_director\":{\"is_repeat\":\"\",\"filter\":\"\",\"hander\":\"\",\"collect\":\"\",\"allow_repeat\":\"\",\"allow_empty\":\"\",\"include\":\"\",\"exclude\":\"\",\"begin\":\"\",\"end\":\"\"},\"movie_leading\":{\"is_repeat\":\"\",\"filter\":\"\",\"hander\":\"\",\"collect\":\"\",\"allow_repeat\":\"\",\"allow_empty\":\"\",\"include\":\"\",\"exclude\":\"\",\"begin\":\"<!--\\u5f71\\u7247\\u6f14\\u5458\\u5f00\\u59cb\\u4ee3\\u7801-->\",\"end\":\"<!--\\u5f71\\u7247\\u6f14\\u5458\\u7ed3\\u675f\\u4ee3\\u7801-->\"},\"movie_classes\":{\"is_repeat\":\"\",\"filter\":\"\",\"hander\":\"\",\"collect\":\"\",\"allow_repeat\":\"\",\"allow_empty\":\"\",\"include\":\"\",\"exclude\":\"\",\"begin\":\"<!--\\u5f71\\u7247\\u7c7b\\u578b\\u5f00\\u59cb\\u4ee3\\u7801-->\",\"end\":\"<!--\\u5f71\\u7247\\u7c7b\\u578b\\u7ed3\\u675f\\u4ee3\\u7801-->\"},\"movie_length\":{\"is_repeat\":\"\",\"filter\":\"\",\"hander\":\"\",\"collect\":\"\",\"allow_repeat\":\"\",\"allow_empty\":\"\",\"include\":\"\",\"exclude\":\"\",\"begin\":\"\",\"end\":\"\"},\"movie_region\":{\"is_repeat\":\"\",\"filter\":\"\",\"hander\":\"\",\"collect\":\"\",\"allow_repeat\":\"\",\"allow_empty\":\"\",\"include\":\"\",\"exclude\":\"\",\"begin\":\"<!--\\u5f71\\u7247\\u5730\\u533a\\u5f00\\u59cb\\u4ee3\\u7801-->\",\"end\":\"<!--\\u5f71\\u7247\\u5730\\u533a\\u7ed3\\u675f\\u4ee3\\u7801-->\"},\"movie_languages\":{\"is_repeat\":\"\",\"filter\":\"\",\"hander\":\"\",\"collect\":\"\",\"allow_repeat\":\"\",\"allow_empty\":\"\",\"include\":\"\",\"exclude\":\"\",\"begin\":\"\",\"end\":\"\"},\"movie_filming_time\":{\"is_repeat\":\"\",\"filter\":\"\",\"hander\":\"\",\"collect\":\"\",\"allow_repeat\":\"\",\"allow_empty\":\"\",\"include\":\"\",\"exclude\":\"\",\"begin\":\"\",\"end\":\"\"},\"movie_play_time\":{\"is_repeat\":\"\",\"filter\":\"\",\"hander\":\"\",\"collect\":\"\",\"allow_repeat\":\"\",\"allow_empty\":\"\",\"include\":\"\",\"exclude\":\"\",\"begin\":\"\",\"end\":\"\"},\"movie_story\":{\"is_repeat\":\"\",\"filter\":\"\",\"hander\":\"\",\"collect\":\"\",\"allow_repeat\":\"\",\"allow_empty\":\"\",\"include\":\"\",\"exclude\":\"\",\"begin\":\"<!--\\u5f71\\u7247\\u4ecb\\u7ecd\\u5f00\\u59cb\\u4ee3\\u7801-->\",\"end\":\"<!--\\u5f71\\u7247\\u4ecb\\u7ecd\\u7ed3\\u675f\\u4ee3\\u7801-->\"},\"movie_play_url\":{\"is_repeat\":\"1\",\"filter\":\"\",\"hander\":\"\",\"collect\":\"\",\"allow_repeat\":\"\",\"allow_empty\":\"\",\"include\":\"\",\"exclude\":\"\",\"begin\":\"<tr><td><a>\",\"end\":\"<\\/a><!--\\u5206\\u96c6\\u540d\\u5f00\\u59cb(*)\\u5206\\u96c6\\u540d\\u7ed3\\u675f-->\"},\"movie_posters\":{\"is_repeat\":\"\",\"filter\":\"\",\"hander\":\"\",\"collect\":\"\",\"allow_repeat\":\"\",\"allow_empty\":\"\",\"include\":\"\",\"exclude\":\"\",\"begin\":\"<!--\\u5f71\\u7247\\u56fe\\u7247\\u5f00\\u59cb\\u4ee3\\u7801-->\",\"end\":\"<!--\\u5f71\\u7247\\u56fe\\u7247\\u7ed3\\u675f\\u4ee3\\u7801-->\"},\"movie_lastdate\":{\"is_repeat\":\"\",\"filter\":\"\",\"hander\":\"\",\"collect\":\"\",\"allow_repeat\":\"\",\"allow_empty\":\"\",\"include\":\"\",\"exclude\":\"\",\"begin\":\"<!--\\u5f71\\u7247\\u66f4\\u65b0\\u65f6\\u95f4\\u5f00\\u59cb\\u4ee3\\u7801-->\",\"end\":\"<!--\\u5f71\\u7247\\u66f4\\u65b0\\u65f6\\u95f4\\u7ed3\\u675f\\u4ee3\\u7801-->\"},\"movie_status\":{\"is_repeat\":\"\",\"filter\":\"\",\"hander\":\"\",\"collect\":\"\",\"allow_repeat\":\"\",\"allow_empty\":\"\",\"include\":\"\",\"exclude\":\"\",\"begin\":\"<!--\\u5f71\\u7247\\u72b6\\u6001\\u5f00\\u59cb\\u4ee3\\u7801-->\",\"end\":\"<!--\\u5f71\\u7247\\u72b6\\u6001\\u7ed3\\u675f\\u4ee3\\u7801-->\"}}', '4', '2', '1', '1', '1323184791', '1323184791');

-- ----------------------------
-- Table structure for `sowel_movie_content_archives`
-- ----------------------------
DROP TABLE IF EXISTS `sowel_movie_content_archives`;
CREATE TABLE `sowel_movie_content_archives` (
  `content_archives_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '档案编号',
  `content_channel_id` int(10) unsigned NOT NULL COMMENT '内容频道编号',
  `admin_id` int(10) unsigned NOT NULL COMMENT '管理员编号',
  `content_archives_subject` char(120) NOT NULL COMMENT '档案标题',
  `content_archives_short_subject` varchar(60) NOT NULL COMMENT '档案短标题',
  `content_archives_color` char(7) NOT NULL COMMENT '档案标题颜色',
  `content_archives_flag` char(12) NOT NULL COMMENT '档案自定义属性',
  `content_archives_jump_url` varchar(120) NOT NULL COMMENT '档案跳转地址',
  `game_id` int(10) unsigned NOT NULL COMMENT '相关游戏',
  `content_archives_source` char(20) NOT NULL COMMENT '档案来源',
  `content_archives_author` char(20) NOT NULL COMMENT '档案作者',
  `content_archives_thumb` varchar(40) NOT NULL COMMENT '档案缩略图',
  `content_archives_keywords` varchar(64) NOT NULL COMMENT '档案SEO关键字',
  `content_archives_summary` varchar(255) NOT NULL COMMENT '档案摘要',
  `content_archives_rank` tinyint(3) unsigned NOT NULL COMMENT '档案排序',
  `content_archives_status` tinyint(3) unsigned NOT NULL COMMENT '档案状态',
  `content_archives_is_build` tinyint(4) NOT NULL COMMENT '档案是否生成静态页',
  `content_archives_pubtime` int(10) unsigned NOT NULL COMMENT '档案发布时间',
  `content_archives_lasttime` int(10) unsigned NOT NULL COMMENT '档案修改时间',
  `insert_user_id` int(11) unsigned NOT NULL,
  `update_user_id` int(11) unsigned NOT NULL,
  `content_archives_dateline` int(10) unsigned NOT NULL COMMENT '档案创建时间',
  PRIMARY KEY (`content_archives_id`),
  KEY `content_archives_rank` (`content_archives_status`,`content_archives_rank`,`content_archives_dateline`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sowel_movie_content_archives
-- ----------------------------

-- ----------------------------
-- Table structure for `sowel_movie_content_archives_classes`
-- ----------------------------
DROP TABLE IF EXISTS `sowel_movie_content_archives_classes`;
CREATE TABLE `sowel_movie_content_archives_classes` (
  `class_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类编号',
  `content_model_id` int(11) NOT NULL COMMENT '内容模型编号',
  `class_parent_id` int(10) unsigned NOT NULL COMMENT '上级分类',
  `class_name` char(30) NOT NULL COMMENT '分类名称',
  `class_identify` char(20) NOT NULL COMMENT '标识',
  `class_is_default` tinyint(3) unsigned NOT NULL COMMENT '列表选项',
  `class_default` varchar(20) NOT NULL COMMENT '默认页',
  `class_is_part` tinyint(3) unsigned NOT NULL COMMENT '属性',
  `class_tempindex` varchar(100) NOT NULL COMMENT '封面模板',
  `class_templist` varchar(100) NOT NULL COMMENT '列表模版',
  `class_temparticle` varchar(100) NOT NULL COMMENT '内页模板',
  `class_seo_keywords` varchar(120) NOT NULL COMMENT 'SEO关键字',
  `class_seo_description` varchar(200) NOT NULL COMMENT 'SEO描述',
  `class_rank` tinyint(3) unsigned NOT NULL COMMENT '排序',
  `class_is_show` tinyint(3) unsigned NOT NULL COMMENT '是否显示',
  `class_is_system` tinyint(1) unsigned NOT NULL COMMENT '是否系统分类',
  `class_status` tinyint(3) unsigned NOT NULL COMMENT '状态',
  `class_lasttime` int(10) unsigned NOT NULL COMMENT '修改时间',
  `class_dateline` int(10) unsigned NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`class_id`),
  KEY `content_model_id` (`content_model_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sowel_movie_content_archives_classes
-- ----------------------------

-- ----------------------------
-- Table structure for `sowel_movie_content_archives_classes_relating`
-- ----------------------------
DROP TABLE IF EXISTS `sowel_movie_content_archives_classes_relating`;
CREATE TABLE `sowel_movie_content_archives_classes_relating` (
  `classes_relating_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `content_archives_id` int(10) unsigned NOT NULL COMMENT '档案编号',
  `class_id` int(10) unsigned NOT NULL COMMENT '分类编号',
  PRIMARY KEY (`classes_relating_id`,`content_archives_id`,`class_id`),
  UNIQUE KEY `content_archives_id` (`content_archives_id`,`class_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sowel_movie_content_archives_classes_relating
-- ----------------------------

-- ----------------------------
-- Table structure for `sowel_movie_content_archives_tags`
-- ----------------------------
DROP TABLE IF EXISTS `sowel_movie_content_archives_tags`;
CREATE TABLE `sowel_movie_content_archives_tags` (
  `tags_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '档案标签编号',
  `content_archives_id` int(10) unsigned NOT NULL COMMENT '档案编号',
  `tags_name` varchar(12) NOT NULL COMMENT '档案标签名称',
  PRIMARY KEY (`tags_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sowel_movie_content_archives_tags
-- ----------------------------

-- ----------------------------
-- Table structure for `sowel_movie_content_model`
-- ----------------------------
DROP TABLE IF EXISTS `sowel_movie_content_model`;
CREATE TABLE `sowel_movie_content_model` (
  `content_model_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '内容模型编号',
  `content_model_name` char(10) NOT NULL COMMENT '内容模型名称',
  `content_model_identify` char(20) NOT NULL COMMENT '内容模型标识',
  `content_model_edit_template` char(30) NOT NULL COMMENT '内容模型编辑模版',
  `content_model_list_template` char(30) NOT NULL COMMENT '内容模型列表模版',
  `content_model_is_default` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否默认模型',
  `content_model_is_system` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否系统模型',
  `content_model_rank` tinyint(3) unsigned NOT NULL COMMENT '内容模型排序',
  `content_model_status` tinyint(3) unsigned NOT NULL COMMENT '内容模型状态',
  `content_model_lasttime` int(10) unsigned NOT NULL COMMENT '内容模型修改时间',
  `content_model_dateline` int(10) unsigned NOT NULL COMMENT '内容模型添加时间',
  PRIMARY KEY (`content_model_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sowel_movie_content_model
-- ----------------------------

-- ----------------------------
-- Table structure for `sowel_movie_content_model_fields`
-- ----------------------------
DROP TABLE IF EXISTS `sowel_movie_content_model_fields`;
CREATE TABLE `sowel_movie_content_model_fields` (
  `content_model_field_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '内容编号',
  `content_model_id` int(10) unsigned NOT NULL COMMENT '内容模型编号',
  `content_model_field_name` char(10) NOT NULL COMMENT '内容名称',
  `content_model_field_identify` char(30) NOT NULL COMMENT '内容标识',
  `content_model_field_type` tinyint(3) unsigned NOT NULL COMMENT '内容模型字段数据类型',
  `content_model_field_default` text NOT NULL COMMENT '内容模型字段默认值',
  `content_model_field_tips` varchar(255) NOT NULL COMMENT '内容模型字段表单提示信息',
  `content_model_field_max_length` int(10) unsigned NOT NULL COMMENT '内容模型字段最大长度',
  `content_model_field_rank` tinyint(3) unsigned NOT NULL COMMENT '内容排序',
  `content_model_field_is_show` tinyint(3) unsigned NOT NULL COMMENT '是否显示',
  `content_model_field_is_system` tinyint(1) unsigned NOT NULL COMMENT '是否系统字段',
  `content_model_field_status` tinyint(3) unsigned NOT NULL COMMENT '状态',
  `content_model_field_lasttime` int(10) unsigned NOT NULL COMMENT '修改时间',
  `content_model_field_dateline` int(10) unsigned NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`content_model_field_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sowel_movie_content_model_fields
-- ----------------------------

-- ----------------------------
-- Table structure for `sowel_movie_group`
-- ----------------------------
DROP TABLE IF EXISTS `sowel_movie_group`;
CREATE TABLE `sowel_movie_group` (
  `group_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_name` char(20) NOT NULL,
  `parent_id` int(10) unsigned NOT NULL COMMENT '上级角色编号',
  `purviews` text NOT NULL COMMENT '角色权限列表，JSON存储',
  `is_system` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否系统角色，1、系统角色，不能更改 0、普通角色',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示：1、显示 0、隐藏',
  `group_rank` smallint(5) unsigned NOT NULL DEFAULT '255' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：1、正常 0、删除 -1、锁定',
  `lasttime` int(10) unsigned NOT NULL COMMENT '角色最后修改时间',
  `dateline` int(10) unsigned NOT NULL COMMENT '角色添加时间',
  PRIMARY KEY (`group_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sowel_movie_group
-- ----------------------------
INSERT INTO `sowel_movie_group` VALUES ('1', '超级管理员', '0', 'all', '1', '1', '1', '4', '1319446870', '1319446870');
INSERT INTO `sowel_movie_group` VALUES ('2', '编辑总监', '0', '[\"2\",\"6\",\"43\",\"44\",\"45\",\"189\",\"191\",\"192\",\"193\",\"190\",\"153\",\"216\",\"217\",\"218\",\"219\",\"154\",\"156\",\"157\",\"158\",\"155\",\"159\",\"160\",\"161\"]', '0', '1', '2', '0', '1334552798', '1322226081');

-- ----------------------------
-- Table structure for `sowel_movie_movie_classes`
-- ----------------------------
DROP TABLE IF EXISTS `sowel_movie_movie_classes`;
CREATE TABLE `sowel_movie_movie_classes` (
  `class_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类编号',
  `parent_id` int(10) unsigned NOT NULL COMMENT '上级分类',
  `class_name` char(30) NOT NULL COMMENT '分类名称',
  `class_identify` char(20) NOT NULL COMMENT '标识',
  `class_is_default` tinyint(3) unsigned NOT NULL COMMENT '列表选项',
  `class_default` varchar(20) NOT NULL COMMENT '默认页',
  `class_is_part` tinyint(3) unsigned NOT NULL COMMENT '属性',
  `class_tempindex` varchar(100) NOT NULL COMMENT '封面模板',
  `class_templist` varchar(100) NOT NULL COMMENT '列表模版',
  `class_temparticle` varchar(100) NOT NULL COMMENT '内页模板',
  `class_seo_keywords` varchar(120) NOT NULL COMMENT 'SEO关键字',
  `class_seo_description` varchar(200) NOT NULL COMMENT 'SEO描述',
  `class_rank` tinyint(3) unsigned NOT NULL COMMENT '排序',
  `class_is_show` tinyint(3) unsigned NOT NULL COMMENT '是否显示',
  `class_is_system` tinyint(1) unsigned NOT NULL COMMENT '是否系统分类',
  `class_status` tinyint(3) unsigned NOT NULL COMMENT '状态',
  `class_lasttime` int(10) unsigned NOT NULL COMMENT '修改时间',
  `class_dateline` int(10) unsigned NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`class_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sowel_movie_movie_classes
-- ----------------------------
INSERT INTO `sowel_movie_movie_classes` VALUES ('1', '0', '电视剧', 'tv', '0', '', '0', '', '', '', '', '', '1', '0', '0', '4', '1334478221', '1323191871');
INSERT INTO `sowel_movie_movie_classes` VALUES ('2', '0', '电影', 'movie', '0', '', '0', '', '', '', '', '', '2', '0', '0', '4', '1334478221', '1323191871');
INSERT INTO `sowel_movie_movie_classes` VALUES ('3', '0', '综艺2', 'zy', '0', '', '0', '', '', '', '', '', '3', '0', '0', '4', '1334478221', '1323191871');
INSERT INTO `sowel_movie_movie_classes` VALUES ('4', '0', '动漫', 'cartoon', '0', '', '0', '', '', '', '', '', '4', '0', '0', '4', '1334478440', '1323191871');
INSERT INTO `sowel_movie_movie_classes` VALUES ('5', '1', '喜剧', 'xiju', '0', '', '0', '', '', '', '', '', '1', '0', '0', '4', '1334478221', '1323191969');
INSERT INTO `sowel_movie_movie_classes` VALUES ('6', '1', '爱情', 'aiqing', '0', '', '0', '', '', '', '', '', '2', '0', '0', '4', '1334478221', '1323191969');
INSERT INTO `sowel_movie_movie_classes` VALUES ('7', '1', '神话', 'shenhua', '0', '', '0', '', '', '', '', '', '3', '0', '0', '4', '1334478221', '1323192023');

-- ----------------------------
-- Table structure for `sowel_movie_movie_districts`
-- ----------------------------
DROP TABLE IF EXISTS `sowel_movie_movie_districts`;
CREATE TABLE `sowel_movie_movie_districts` (
  `district_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类编号',
  `parent_id` int(10) unsigned NOT NULL COMMENT '上级分类',
  `district_name` char(30) NOT NULL COMMENT '分类名称',
  `district_identify` char(20) NOT NULL COMMENT '标识',
  `district_is_default` tinyint(3) unsigned NOT NULL COMMENT '列表选项',
  `district_rank` tinyint(3) unsigned NOT NULL COMMENT '排序',
  `district_is_show` tinyint(3) unsigned NOT NULL COMMENT '是否显示',
  `district_is_system` tinyint(1) unsigned NOT NULL COMMENT '是否系统分类',
  `district_status` tinyint(3) unsigned NOT NULL COMMENT '状态',
  `district_lasttime` int(10) unsigned NOT NULL COMMENT '修改时间',
  `district_dateline` int(10) unsigned NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`district_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sowel_movie_movie_districts
-- ----------------------------
INSERT INTO `sowel_movie_movie_districts` VALUES ('1', '0', '大陆', 'china', '0', '1', '0', '0', '4', '1323278212', '1323278054');
INSERT INTO `sowel_movie_movie_districts` VALUES ('2', '0', '台湾', 'taiwan', '0', '2', '0', '0', '4', '1323278103', '1323278103');
INSERT INTO `sowel_movie_movie_districts` VALUES ('3', '0', '香港', 'hongkong', '0', '3', '0', '0', '4', '1323278103', '1323278103');
INSERT INTO `sowel_movie_movie_districts` VALUES ('4', '0', '韩国', 'korea', '0', '4', '0', '0', '4', '1323278133', '1323278103');
INSERT INTO `sowel_movie_movie_districts` VALUES ('5', '0', '日本', 'japan', '0', '5', '0', '0', '4', '1323278103', '1323278103');
INSERT INTO `sowel_movie_movie_districts` VALUES ('6', '0', '美国', 'america', '0', '6', '0', '0', '4', '1323278205', '1323278205');
INSERT INTO `sowel_movie_movie_districts` VALUES ('7', '0', '英国', 'england', '0', '7', '0', '0', '4', '1323278205', '1323278205');
INSERT INTO `sowel_movie_movie_districts` VALUES ('8', '0', '其他', 'other', '0', '10', '0', '0', '4', '1323278205', '1323278205');

-- ----------------------------
-- Table structure for `sowel_movie_movie_rundates`
-- ----------------------------
DROP TABLE IF EXISTS `sowel_movie_movie_rundates`;
CREATE TABLE `sowel_movie_movie_rundates` (
  `rundate_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类编号',
  `rundate_date` char(30) NOT NULL COMMENT '分类名称',
  `rundate_is_default` tinyint(3) unsigned NOT NULL COMMENT '列表选项',
  `rundate_is_show` tinyint(3) unsigned NOT NULL COMMENT '是否显示',
  `rundate_is_system` tinyint(1) unsigned NOT NULL COMMENT '是否系统分类',
  `rundate_rank` tinyint(3) unsigned NOT NULL COMMENT '权重',
  `rundate_status` tinyint(3) unsigned NOT NULL COMMENT '状态',
  `rundate_lasttime` int(10) unsigned NOT NULL COMMENT '修改时间',
  `rundate_dateline` int(10) unsigned NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`rundate_id`)
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sowel_movie_movie_rundates
-- ----------------------------
INSERT INTO `sowel_movie_movie_rundates` VALUES ('1', '2011', '0', '0', '0', '1', '4', '1323281046', '1323281004');
INSERT INTO `sowel_movie_movie_rundates` VALUES ('2', '2010', '0', '0', '0', '2', '4', '1323362578', '1323281057');
INSERT INTO `sowel_movie_movie_rundates` VALUES ('3', '2009', '0', '0', '0', '3', '4', '1323362578', '1323362578');
INSERT INTO `sowel_movie_movie_rundates` VALUES ('4', '2008', '0', '0', '0', '4', '4', '1323362578', '1323362578');
INSERT INTO `sowel_movie_movie_rundates` VALUES ('5', '2007', '0', '0', '0', '5', '4', '1323362578', '1323362578');
INSERT INTO `sowel_movie_movie_rundates` VALUES ('6', '2006', '0', '0', '0', '6', '4', '1323362578', '1323362578');
INSERT INTO `sowel_movie_movie_rundates` VALUES ('7', '2005', '0', '0', '0', '7', '4', '1323362578', '1323362578');
INSERT INTO `sowel_movie_movie_rundates` VALUES ('8', '2004', '0', '0', '0', '8', '4', '1323362578', '1323362578');
INSERT INTO `sowel_movie_movie_rundates` VALUES ('9', '2003', '0', '0', '0', '9', '4', '1323362578', '1323362578');
INSERT INTO `sowel_movie_movie_rundates` VALUES ('10', '2002', '0', '0', '0', '10', '4', '1323362578', '1323362578');
INSERT INTO `sowel_movie_movie_rundates` VALUES ('11', '2001', '0', '0', '0', '11', '4', '1323362578', '1323362578');
INSERT INTO `sowel_movie_movie_rundates` VALUES ('12', '2000', '0', '0', '0', '12', '4', '1323362578', '1323362578');
INSERT INTO `sowel_movie_movie_rundates` VALUES ('13', '1990', '0', '0', '0', '22', '4', '1323362669', '1323362669');
INSERT INTO `sowel_movie_movie_rundates` VALUES ('14', '1980', '0', '0', '0', '23', '4', '1323362701', '1323362669');
INSERT INTO `sowel_movie_movie_rundates` VALUES ('15', '1970', '0', '0', '0', '24', '4', '1323362716', '1323362669');

-- ----------------------------
-- Table structure for `sowel_movie_movie_stars`
-- ----------------------------
DROP TABLE IF EXISTS `sowel_movie_movie_stars`;
CREATE TABLE `sowel_movie_movie_stars` (
  `star_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '明星编号',
  `star_name` char(30) NOT NULL COMMENT '明星名称',
  `star_english_name` char(20) NOT NULL COMMENT '英文名称',
  `star_rank` tinyint(3) unsigned NOT NULL COMMENT '排序',
  `star_is_show` tinyint(3) unsigned NOT NULL COMMENT '是否显示',
  `star_status` tinyint(3) unsigned NOT NULL COMMENT '状态',
  `star_lasttime` int(10) unsigned NOT NULL COMMENT '修改时间',
  `star_dateline` int(10) unsigned NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`star_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sowel_movie_movie_stars
-- ----------------------------
INSERT INTO `sowel_movie_movie_stars` VALUES ('1', '刘德华', 'Andy Lau', '1', '0', '4', '1323279656', '1323279645');
INSERT INTO `sowel_movie_movie_stars` VALUES ('2', '成龙', 'Jackie Chan', '2', '0', '4', '1323279656', '1323279645');
INSERT INTO `sowel_movie_movie_stars` VALUES ('3', '333', '', '255', '0', '0', '1334486497', '1334486408');
INSERT INTO `sowel_movie_movie_stars` VALUES ('4', '', '', '255', '0', '0', '1334486502', '1334486502');

-- ----------------------------
-- Table structure for `sowel_movie_purview`
-- ----------------------------
DROP TABLE IF EXISTS `sowel_movie_purview`;
CREATE TABLE `sowel_movie_purview` (
  `purview_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL COMMENT '上级分类',
  `purview_name` char(20) NOT NULL COMMENT '权限名称，用于后台显示',
  `identify` char(30) NOT NULL COMMENT '权限的英文标识，用于权限判定',
  `status` tinyint(1) unsigned NOT NULL COMMENT '状态：1、正常 0、删除',
  `purview_rank` smallint(6) unsigned NOT NULL,
  `lasttime` int(10) unsigned NOT NULL COMMENT '最后修改时间',
  `dateline` int(10) unsigned NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`purview_id`)
) ENGINE=MyISAM AUTO_INCREMENT=251 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sowel_movie_purview
-- ----------------------------
INSERT INTO `sowel_movie_purview` VALUES ('1', '0', '系统设置', 'Setting', '1', '1', '1319442184', '0');
INSERT INTO `sowel_movie_purview` VALUES ('5', '0', '工具', 'Tools', '1', '7', '1319442184', '0');
INSERT INTO `sowel_movie_purview` VALUES ('7', '2', '权限管理', 'Purview', '1', '2', '1319442184', '0');
INSERT INTO `sowel_movie_purview` VALUES ('2', '0', '管理员管理', 'Admin', '1', '2', '1319442631', '0');
INSERT INTO `sowel_movie_purview` VALUES ('6', '2', '管理员管理', 'Admin', '1', '1', '1319442184', '0');
INSERT INTO `sowel_movie_purview` VALUES ('41', '1', '基本设置', 'Base', '1', '1', '1319442184', '0');
INSERT INTO `sowel_movie_purview` VALUES ('43', '6', '添加', 'Create', '1', '1', '1319442184', '0');
INSERT INTO `sowel_movie_purview` VALUES ('44', '6', '修改', 'Modify', '1', '2', '1319442184', '0');
INSERT INTO `sowel_movie_purview` VALUES ('45', '6', '删除', 'Delete', '1', '3', '1319442184', '0');
INSERT INTO `sowel_movie_purview` VALUES ('50', '5', '缓存管理', 'Cache', '1', '2', '1319442184', '0');
INSERT INTO `sowel_movie_purview` VALUES ('62', '1', '缓存设置', 'Cache', '1', '2', '1319442184', '0');
INSERT INTO `sowel_movie_purview` VALUES ('63', '1', '其他设置', 'Other', '1', '6', '1319442184', '0');
INSERT INTO `sowel_movie_purview` VALUES ('69', '1', '充值设置', 'Pay', '0', '3', '0', '0');
INSERT INTO `sowel_movie_purview` VALUES ('76', '5', '静态生成', 'Static', '0', '4', '0', '0');
INSERT INTO `sowel_movie_purview` VALUES ('77', '76', '生成', 'Builder', '1', '1', '0', '0');
INSERT INTO `sowel_movie_purview` VALUES ('78', '5', '用户日志', 'UserLogs', '0', '3', '0', '0');
INSERT INTO `sowel_movie_purview` VALUES ('106', '50', '更新', 'UpdateCache', '1', '1', '1319442184', '0');
INSERT INTO `sowel_movie_purview` VALUES ('153', '0', '广告管理', 'Ad', '1', '6', '1319442184', '0');
INSERT INTO `sowel_movie_purview` VALUES ('154', '153', '广告位', 'Position', '1', '2', '1320221592', '0');
INSERT INTO `sowel_movie_purview` VALUES ('155', '153', '广告素材', 'Data', '1', '3', '1320221592', '0');
INSERT INTO `sowel_movie_purview` VALUES ('156', '154', '添加', 'Create', '1', '1', '1319701230', '0');
INSERT INTO `sowel_movie_purview` VALUES ('157', '154', '修改', 'Modify', '1', '2', '1319442184', '0');
INSERT INTO `sowel_movie_purview` VALUES ('158', '154', '删除', 'Delete', '1', '3', '1319442184', '0');
INSERT INTO `sowel_movie_purview` VALUES ('159', '155', '添加', 'Create', '1', '1', '1319701230', '0');
INSERT INTO `sowel_movie_purview` VALUES ('160', '155', '修改', 'Modify', '1', '2', '1319442184', '0');
INSERT INTO `sowel_movie_purview` VALUES ('161', '155', '删除', 'Delete', '1', '3', '1319442184', '0');
INSERT INTO `sowel_movie_purview` VALUES ('189', '2', '角色管理', 'Role', '4', '3', '1319442184', '1319442141');
INSERT INTO `sowel_movie_purview` VALUES ('190', '2', '管理日志', 'Logs', '4', '4', '1319442184', '1319442141');
INSERT INTO `sowel_movie_purview` VALUES ('191', '189', '添加', 'Create', '4', '1', '1319701303', '1319442184');
INSERT INTO `sowel_movie_purview` VALUES ('192', '189', '修改', 'Modify', '4', '2', '1319701303', '1319442184');
INSERT INTO `sowel_movie_purview` VALUES ('193', '189', '删除', 'Delete', '4', '3', '1319701303', '1319442184');
INSERT INTO `sowel_movie_purview` VALUES ('194', '5', '生成静态页', 'Static', '4', '3', '1319527661', '1319527661');
INSERT INTO `sowel_movie_purview` VALUES ('214', '7', '删除', 'Delete', '4', '0', '1319773848', '1319773848');
INSERT INTO `sowel_movie_purview` VALUES ('216', '153', '广告分类', 'Categories', '4', '1', '1320221592', '1320221592');
INSERT INTO `sowel_movie_purview` VALUES ('217', '216', '添加', 'Create', '4', '1', '1320221704', '1320221704');
INSERT INTO `sowel_movie_purview` VALUES ('218', '216', '修改', 'Modify', '4', '2', '1320221704', '1320221704');
INSERT INTO `sowel_movie_purview` VALUES ('219', '216', '删除', 'Delete', '4', '3', '1320221704', '1320221704');
INSERT INTO `sowel_movie_purview` VALUES ('224', '0', '电影管理', 'Movie', '4', '4', '1322304605', '1322304605');
INSERT INTO `sowel_movie_purview` VALUES ('225', '0', '采集管理', 'Collect', '4', '5', '1322304605', '1322304605');
INSERT INTO `sowel_movie_purview` VALUES ('226', '224', '电影管理', 'Movie', '4', '1', '1322304703', '1322304703');
INSERT INTO `sowel_movie_purview` VALUES ('227', '224', '电影分类', 'Class', '4', '2', '1322304703', '1322304703');
INSERT INTO `sowel_movie_purview` VALUES ('228', '224', '地区', 'District', '4', '3', '1323189576', '1322304703');
INSERT INTO `sowel_movie_purview` VALUES ('229', '224', '上映时间', 'RunDate', '4', '4', '1322304703', '1322304703');
INSERT INTO `sowel_movie_purview` VALUES ('230', '224', '电影明星', 'Star', '4', '5', '1323188655', '1322304703');
INSERT INTO `sowel_movie_purview` VALUES ('231', '225', '采集来源', 'Source', '4', '1', '1322304703', '1322304703');
INSERT INTO `sowel_movie_purview` VALUES ('232', '225', '采集模版', 'Template', '4', '4', '1322639637', '1322304703');
INSERT INTO `sowel_movie_purview` VALUES ('233', '225', '采集任务', 'Task', '4', '5', '1322639637', '1322304703');
INSERT INTO `sowel_movie_purview` VALUES ('234', '231', '修改', 'Modify', '4', '2', '1322304763', '1322304763');
INSERT INTO `sowel_movie_purview` VALUES ('235', '231', '删除', 'Delete', '4', '3', '1322304763', '1322304763');
INSERT INTO `sowel_movie_purview` VALUES ('236', '231', '添加', 'Create', '4', '1', '1322304763', '1322304763');
INSERT INTO `sowel_movie_purview` VALUES ('237', '232', '添加', 'Create', '4', '1', '1322304763', '1322304763');
INSERT INTO `sowel_movie_purview` VALUES ('238', '232', '修改', 'Modify', '4', '2', '1322304763', '1322304763');
INSERT INTO `sowel_movie_purview` VALUES ('239', '232', '删除', 'Delete', '4', '3', '1322304763', '1322304763');
INSERT INTO `sowel_movie_purview` VALUES ('240', '233', '添加', 'Create', '4', '1', '1322304763', '1322304763');
INSERT INTO `sowel_movie_purview` VALUES ('241', '233', '修改', 'Modify', '4', '2', '1322304763', '1322304763');
INSERT INTO `sowel_movie_purview` VALUES ('242', '233', '删除', 'Delete', '4', '3', '1322304763', '1322304763');
INSERT INTO `sowel_movie_purview` VALUES ('243', '225', '采集模型', 'Model', '4', '2', '1322560519', '1322560519');
INSERT INTO `sowel_movie_purview` VALUES ('244', '243', '添加', 'Create', '4', '1', '1322560539', '1322560539');
INSERT INTO `sowel_movie_purview` VALUES ('245', '243', '修改', 'Modify', '4', '2', '1322560539', '1322560539');
INSERT INTO `sowel_movie_purview` VALUES ('246', '243', '删除', 'Delete', '4', '3', '1322560539', '1322560539');
INSERT INTO `sowel_movie_purview` VALUES ('247', '225', '字段管理', 'Fields', '4', '3', '1322639637', '1322639637');
INSERT INTO `sowel_movie_purview` VALUES ('248', '247', '添加', 'Create', '4', '1', '1322639671', '1322639671');
INSERT INTO `sowel_movie_purview` VALUES ('249', '247', '修改', 'Modify', '4', '2', '1322639671', '1322639671');
INSERT INTO `sowel_movie_purview` VALUES ('250', '247', '删除', 'Delete', '4', '3', '1322639671', '1322639671');

-- ----------------------------
-- Table structure for `sowel_movie_setting`
-- ----------------------------
DROP TABLE IF EXISTS `sowel_movie_setting`;
CREATE TABLE `sowel_movie_setting` (
  `setting_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `setting_name` char(100) NOT NULL COMMENT '配置名称',
  `setting_group` enum('base','cache','pay','other') NOT NULL DEFAULT 'base' COMMENT '设置组',
  `setting_identify` varchar(50) NOT NULL COMMENT '配置标识',
  `setting_type` enum('text','radio','select','textarea') NOT NULL DEFAULT 'text' COMMENT '类型',
  `setting_message` varchar(255) NOT NULL COMMENT '说明',
  `setting_options` varchar(255) NOT NULL COMMENT '设置选项',
  `setting_value` text NOT NULL COMMENT '配置的值',
  `is_system` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否系统配置',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示，1、显示 0、不显示',
  `rank` smallint(6) NOT NULL DEFAULT '255' COMMENT '排序',
  PRIMARY KEY (`setting_id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sowel_movie_setting
-- ----------------------------
INSERT INTO `sowel_movie_setting` VALUES ('1', '网站名称', 'base', 'SiteName', 'text', '', '', '快播VIP系统', '1', '1', '1');
INSERT INTO `sowel_movie_setting` VALUES ('2', '网站URL', 'base', 'SiteUrl', 'text', '', '', 'http://v.vsenho.com', '1', '1', '2');
INSERT INTO `sowel_movie_setting` VALUES ('3', '管理员邮箱', 'base', 'AdminEmail', 'text', '', '', 'webmaster@vsenho.com', '1', '1', '3');
INSERT INTO `sowel_movie_setting` VALUES ('4', '网站备案信息代码', 'base', 'SiteIcp', 'text', '', '', '', '1', '1', '4');
INSERT INTO `sowel_movie_setting` VALUES ('5', '统计代码', 'base', 'StatCode', 'textarea', '', '', '', '1', '1', '5');
INSERT INTO `sowel_movie_setting` VALUES ('6', '客服QQ', 'base', 'HELP_SERVICE_QQ', 'text', '客服QQ', '', '', '1', '1', '6');
INSERT INTO `sowel_movie_setting` VALUES ('7', '生成站点静态HTML', 'other', 'SITE_BUILD_HTML', 'select', '生成站点静态HTML', '{\"1\":\"是\",\"0\":\"否\"}', '0', '1', '1', '31');
INSERT INTO `sowel_movie_setting` VALUES ('11', '静态资源地址', 'other', 'STATIC_RESOURCE_URL', 'text', '静态资源地址', '', 'http://stc.v.vsenho.com', '1', '1', '12');
INSERT INTO `sowel_movie_setting` VALUES ('10', '动态资源地址', 'other', 'DYNAMIC_RESOURCE_URL', 'text', '动态资源地址', '', 'http://dyn.v.vsenho.com', '1', '1', '11');

-- ----------------------------
-- Table structure for `sowel_movie_uploadfiles`
-- ----------------------------
DROP TABLE IF EXISTS `sowel_movie_uploadfiles`;
CREATE TABLE `sowel_movie_uploadfiles` (
  `file_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '编号',
  `md5_value` char(32) NOT NULL COMMENT '文件MD5值',
  `file_ext` char(10) NOT NULL COMMENT '文件扩展名',
  `number` int(11) unsigned NOT NULL COMMENT '文件数量',
  `lasttime` int(10) unsigned NOT NULL COMMENT '最后修改日期',
  `dateline` int(10) unsigned NOT NULL COMMENT '添加日期',
  PRIMARY KEY (`file_id`),
  KEY `Md5Value` (`md5_value`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sowel_movie_uploadfiles
-- ----------------------------

-- ----------------------------
-- Table structure for `sowel_movie_user`
-- ----------------------------
DROP TABLE IF EXISTS `sowel_movie_user`;
CREATE TABLE `sowel_movie_user` (
  `user_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` char(20) NOT NULL COMMENT '管理员用户名',
  `realname` char(20) NOT NULL COMMENT '管理员真实姓名',
  `email` varchar(50) NOT NULL COMMENT '管理员邮箱',
  `password` char(32) NOT NULL,
  `salt` char(6) NOT NULL COMMENT '随机加密附加码',
  `group_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户角色ID',
  `purviews` text NOT NULL COMMENT '用户权限列表，JSON存储',
  `logintimes` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登录次数',
  `lastip` int(10) unsigned NOT NULL COMMENT '最后登录IP',
  `lastvisit` int(10) unsigned NOT NULL COMMENT '最后登录时间',
  `user_rank` smallint(4) unsigned NOT NULL DEFAULT '999' COMMENT '用户排序',
  `is_system` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否系统内置',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：0、删除 1、锁定  4、正常',
  `lasttime` int(10) unsigned NOT NULL COMMENT '最后修改时间',
  `dateline` int(10) unsigned NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sowel_movie_user
-- ----------------------------
INSERT INTO `sowel_movie_user` VALUES ('1', 'admin', '超级管理员', 'zhangbaolin@qvod.com', 'da14b024528812998a2aa0e37121855b', '27e7f8', '1', '[]', '465', '3232240870', '1300913640', '1', '1', '4', '1335077712', '1300913640');
INSERT INTO `sowel_movie_user` VALUES ('2', 'jacky', 'Jacky Zhang', 'nbaiwan@163.com', '10589461a53beacbd5cd9b12fd8aec13', '8g6lAL', '1', '[]', '0', '0', '0', '2', '0', '0', '1322219877', '1322219389');

-- ----------------------------
-- Table structure for `sowel_movie_user_logs`
-- ----------------------------
DROP TABLE IF EXISTS `sowel_movie_user_logs`;
CREATE TABLE `sowel_movie_user_logs` (
  `log_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL COMMENT '操作人',
  `log_type` varchar(30) NOT NULL COMMENT '类型',
  `log_item_id` int(11) unsigned NOT NULL COMMENT '操作ID',
  `log_action` enum('Login','Logout','Insert','Modify','Delete','View','Audit','Charge','Revert') NOT NULL COMMENT '动作',
  `log_result` enum('success','failure') NOT NULL COMMENT '操作结果',
  `log_message` varchar(255) NOT NULL COMMENT '备注',
  `log_data` text NOT NULL COMMENT '附加数据',
  `user_ip` int(11) unsigned NOT NULL COMMENT '操作人IP',
  `lasttime` int(11) unsigned NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of sowel_movie_user_logs
-- ----------------------------
