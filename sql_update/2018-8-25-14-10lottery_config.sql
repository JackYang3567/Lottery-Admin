﻿# Host: localhost  (Version: 5.5.53)
# Date: 2018-08-25 14:10:53
# Generator: MySQL-Front 5.3  (Build 4.234)

/*!40101 SET NAMES utf8 */;

#
# Structure for table "lottery_config"
#

CREATE TABLE `lottery_config` (
  `type` int(2) NOT NULL DEFAULT '0' COMMENT '彩种编码',
  `basic_config` text NOT NULL COMMENT '彩种基础配置',
  `time_config` varchar(100) DEFAULT NULL COMMENT '彩种时间设置',
  `return` text COMMENT '彩种返点设置',
  `name` varchar(10) NOT NULL DEFAULT '' COMMENT '彩种名字',
  `note` varchar(255) DEFAULT NULL COMMENT '彩票描述注释',
  `switch` int(1) NOT NULL DEFAULT '1' COMMENT '彩种开关',
  `explain` text COMMENT '彩种规则说明',
  PRIMARY KEY (`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='彩种配置文件';

#
# Data for table "lottery_config"
#

/*!40000 ALTER TABLE `lottery_config` DISABLE KEYS */;
INSERT INTO `lottery_config` VALUES (55,'{\"img_dir\":\"/game/lh3/static/egame/tiger/ninja\",\"room\":{\"0\":\"51\",\"1\":\"20\",\"2\":\"100\"},\"bonus\":{\"1\":{\"3\":\"5\",\"4\":\"20\",\"5\":\"100\"},\"2\":{\"3\":\"5\",\"4\":\"20\",\"5\":\"100\"},\"3\":{\"3\":\"5\",\"4\":\"20\",\"5\":\"100\"},\"4\":{\"3\":\"5\",\"4\":\"20\",\"5\":\"100\"},\"5\":{\"3\":\"10\",\"4\":\"40\",\"5\":\"200\"},\"6\":{\"3\":\"20\",\"4\":\"100\",\"5\":\"500\"},\"7\":{\"3\":\"30\",\"4\":\"150\",\"5\":\"800\"},\"8\":{\"3\":\"50\",\"4\":\"200\",\"5\":\"1500\"},\"9\":{\"3\":\"75\",\"4\":\"300\",\"5\":\"2000\"},\"10\":{\"3\":\"100\",\"4\":\"400\",\"5\":\"3000\"},\"11\":{\"3\":\"150\",\"4\":\"600\",\"5\":\"4500\"}}}','{\"start_time\":\"00:00\",\"cha\":\"1\",\"num\":\"0\",\"desc\":\"10\"}','','忍者神龟','想开就开',0,''),(54,'{\n    \"room\": [5, 20, 100],\n    \"img_dir\": \"/game/lh2/static/egame/tiger/beauty\",\n    \"bonus\": {\n        \"1\": {\n            \"3\": 5,\n            \"4\": 20,\n            \"5\": 100\n        },\n        \"2\": {\n            \"3\": 5,\n            \"4\": 20,\n            \"5\": 100\n        },\n        \"3\": {\n            \"3\": 5,\n            \"4\": 20,\n            \"5\": 100\n        },\n        \"4\": {\n            \"3\": 5,\n            \"4\": 20,\n            \"5\": 100\n        },\n        \"5\": {\n            \"3\": 10,\n            \"4\": 40,\n            \"5\": 200\n        },\n        \"6\": {\n            \"3\": 20,\n            \"4\": 100,\n            \"5\": 500\n        },\n        \"7\": {\n            \"3\": 30,\n            \"4\": 150,\n            \"5\": 800\n        },\n        \"8\": {\n            \"3\": 50,\n            \"4\": 200,\n            \"5\": 1500\n        },\n        \"9\": {\n            \"3\": 75,\n            \"4\": 300,\n            \"5\": 2000\n        },\n        \"10\": {\n            \"3\": 100,\n            \"4\": 400,\n            \"5\": 3000\n        },\n        \"11\": {\n            \"3\": 150,\n            \"4\": 600,\n            \"5\": 4500\n        }\n    }\n}\n','{\"start_time\":\"00:00\",\"cha\":\"1\",\"num\":\"0\",\"desc\":\"10\"}','','闭月羞花','想开就开',0,''),(53,'{\n    \"room\": [5, 20, 100],\n    \"img_dir\": \"/game/lh/static/egame/tiger/fruit\",\n    \"bonus\": {\n        \"1\": {\n            \"3\": 5,\n            \"4\": 20,\n            \"5\": 100\n        },\n        \"2\": {\n            \"3\": 5,\n            \"4\": 20,\n            \"5\": 100\n        },\n        \"3\": {\n            \"3\": 5,\n            \"4\": 20,\n            \"5\": 100\n        },\n        \"4\": {\n            \"3\": 5,\n            \"4\": 20,\n            \"5\": 100\n        },\n        \"5\": {\n            \"3\": 10,\n            \"4\": 40,\n            \"5\": 200\n        },\n        \"6\": {\n            \"3\": 20,\n            \"4\": 100,\n            \"5\": 500\n        },\n        \"7\": {\n            \"3\": 30,\n            \"4\": 150,\n            \"5\": 800\n        },\n        \"8\": {\n            \"3\": 50,\n            \"4\": 200,\n            \"5\": 1500\n        },\n        \"9\": {\n            \"3\": 75,\n            \"4\": 300,\n            \"5\": 2000\n        },\n        \"10\": {\n            \"3\": 100,\n            \"4\": 400,\n            \"5\": 3000\n        },\n        \"11\": {\n            \"3\": 150,\n            \"4\": 600,\n            \"5\": 4500\n        }\n    }\n}','{\"start_time\":\"00:00\",\"cha\":\"1\",\"num\":\"0\",\"desc\":\"10\"}',NULL,'水果拉霸','想开就开',1,NULL);
/*!40000 ALTER TABLE `lottery_config` ENABLE KEYS */;
