# Host: localhost  (Version: 5.5.53)
# Date: 2018-09-11 10:44:38
# Generator: MySQL-Front 5.3  (Build 4.234)

/*!40101 SET NAMES utf8 */;

#
# Structure for table "system_config"
#

DROP TABLE IF EXISTS `system_config`;
CREATE TABLE `system_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `value` text,
  `note` varchar(255) DEFAULT NULL COMMENT '注释用的',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8 COMMENT='系统配置';

#
# Data for table "system_config"
#

INSERT INTO `system_config` VALUES (50,'login_phone','1','注册是否启用手机号');
