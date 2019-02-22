# Host: localhost  (Version: 5.5.53)
# Date: 2018-09-10 11:21:36
# Generator: MySQL-Front 5.3  (Build 4.234)

/*!40101 SET NAMES utf8 */;

#
# Structure for table "lottery_config"
#

DROP TABLE IF EXISTS `lottery_config`;
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
INSERT INTO `lottery_config` VALUES (56,'{\"room\":{\"0\":\"5\",\"1\":\"20\",\"2\":\"100\",\"3\":\"300\"},\"percent\":\"80\"}','{\"start_time\":\"00:00\",\"cha\":\"1\",\"num\":\"0\",\"desc\":\"10\"}',NULL,'老虎机','想开就开',1,NULL);
/*!40000 ALTER TABLE `lottery_config` ENABLE KEYS */;
