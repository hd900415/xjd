/*
Navicat MySQL Data Transfer

Source Server         : n8
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : daikuan

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2019-08-13 17:04:59
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for cv_admin
-- ----------------------------
DROP TABLE IF EXISTS `cv_admin`;
CREATE TABLE `cv_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `type` int(1) NOT NULL DEFAULT '0',
  `last_ip` varchar(255) DEFAULT NULL,
  `last_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of cv_admin
-- ----------------------------
INSERT INTO `cv_admin` VALUES ('1', 'admin', '10470c3b4b1fed12c3baac014be15fac67c6e815', '1', '1', '127.0.0.1', '1565686719');

-- ----------------------------
-- Table structure for cv_block
-- ----------------------------
DROP TABLE IF EXISTS `cv_block`;
CREATE TABLE `cv_block` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` int(1) NOT NULL COMMENT '1:文本,2:图片地址,3:HTML',
  `remarks` varchar(255) DEFAULT NULL,
  `content` text,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of cv_block
-- ----------------------------

-- ----------------------------
-- Table structure for cv_file
-- ----------------------------
DROP TABLE IF EXISTS `cv_file`;
CREATE TABLE `cv_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `load_time` int(11) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `suffix` varchar(10) DEFAULT NULL,
  `filemd5` varchar(255) DEFAULT NULL,
  `filesize` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=468 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of cv_file
-- ----------------------------
INSERT INTO `cv_file` VALUES ('460', '1552665912', '20190316/9e2f104599aeca9235d145acf7b5e1d7.jpg', 'jpg', '5061ff8340b403da398e8fcf9e6c9e71', '12253');
INSERT INTO `cv_file` VALUES ('461', '1552665919', '20190316/614dbf16cd01fbec5fb318a03a3f56ea.jpg', 'jpg', '20474c0a436606bd0bd69e97f80ea536', '15131');
INSERT INTO `cv_file` VALUES ('462', '1552665927', '20190316/8b863fb71352baaa4cd56275111942a3.jpg', 'jpg', 'f7ae94345d96cf50725b8575fef96953', '17872');
INSERT INTO `cv_file` VALUES ('463', '1561818481', '20190629/7f8ae97cae714422360685fa0c475c51.jpg', 'jpg', '2950cc8e22ebb25cc385c58553ff1928', '28762');
INSERT INTO `cv_file` VALUES ('464', '1561818485', '20190629/da39d30af7983bbe2ce0df5303992cb8.jpg', 'jpg', '2950cc8e22ebb25cc385c58553ff1928', '28762');
INSERT INTO `cv_file` VALUES ('465', '1561818508', '20190629/66d8fdd44f070b5e7aaa939150db302d.jpg', 'jpg', '2950cc8e22ebb25cc385c58553ff1928', '28762');
INSERT INTO `cv_file` VALUES ('466', '1561818516', '20190629/36634dcaaaf047c1bde1647b309541d5.jpg', 'jpg', '2950cc8e22ebb25cc385c58553ff1928', '28762');
INSERT INTO `cv_file` VALUES ('467', '1561818521', '20190629/dd214f18f990cda64946876981616b3a.jpg', 'jpg', '2950cc8e22ebb25cc385c58553ff1928', '28762');

-- ----------------------------
-- Table structure for cv_info
-- ----------------------------
DROP TABLE IF EXISTS `cv_info`;
CREATE TABLE `cv_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `identity` longtext,
  `contacts` longtext,
  `bank` text,
  `addess` text,
  `mobile` longtext,
  `taobao` longtext,
  `status` int(1) NOT NULL DEFAULT '0',
  `sid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=230 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of cv_info
-- ----------------------------
INSERT INTO `cv_info` VALUES ('229', '1', null, null, null, null, null, null, '0', '0');

-- ----------------------------
-- Table structure for cv_infoauth
-- ----------------------------
DROP TABLE IF EXISTS `cv_infoauth`;
CREATE TABLE `cv_infoauth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) DEFAULT NULL,
  `callid` varchar(255) DEFAULT NULL,
  `uid` int(11) NOT NULL,
  `add_time` int(11) DEFAULT NULL,
  `data` longtext,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of cv_infoauth
-- ----------------------------

-- ----------------------------
-- Table structure for cv_loanbill
-- ----------------------------
DROP TABLE IF EXISTS `cv_loanbill`;
CREATE TABLE `cv_loanbill` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `toid` int(11) NOT NULL,
  `oid` varchar(255) DEFAULT NULL,
  `billnum` int(11) DEFAULT NULL,
  `money` float(11,2) DEFAULT NULL,
  `interest` float(10,2) DEFAULT NULL,
  `overdue` float(10,2) DEFAULT NULL,
  `repayment_time` int(11) DEFAULT NULL,
  `status` int(1) DEFAULT NULL,
  `add_time` int(11) DEFAULT NULL,
  `repay_time` int(11) DEFAULT '0',
  `overdue_settime` int(11) DEFAULT '0',
  `overdue_smsstatus` int(1) DEFAULT '0',
  `overdue_xq` float(11,2) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=143 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of cv_loanbill
-- ----------------------------

-- ----------------------------
-- Table structure for cv_loanorder
-- ----------------------------
DROP TABLE IF EXISTS `cv_loanorder`;
CREATE TABLE `cv_loanorder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `oid` varchar(255) DEFAULT NULL,
  `money` float(10,0) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `timetype` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `bankname` varchar(255) DEFAULT NULL,
  `banknum` varchar(255) DEFAULT NULL,
  `interest` float(10,8) DEFAULT NULL,
  `start_time` int(11) DEFAULT NULL,
  `overdue` float(10,8) DEFAULT NULL,
  `add_time` int(11) DEFAULT NULL,
  `sign` text,
  `data` longtext,
  `status` int(11) NOT NULL DEFAULT '0',
  `pending` int(1) DEFAULT '0' COMMENT '0:待审批,1:申请通过,2:驳回申请',
  `error` varchar(255) DEFAULT NULL,
  `sid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=48 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of cv_loanorder
-- ----------------------------

-- ----------------------------
-- Table structure for cv_payorder
-- ----------------------------
DROP TABLE IF EXISTS `cv_payorder`;
CREATE TABLE `cv_payorder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `billlist` varchar(255) DEFAULT NULL,
  `money` float(10,4) DEFAULT '0.0000',
  `status` int(1) NOT NULL DEFAULT '0',
  `add_time` int(11) DEFAULT '0',
  `pay_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=171 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of cv_payorder
-- ----------------------------

-- ----------------------------
-- Table structure for cv_payorderxq
-- ----------------------------
DROP TABLE IF EXISTS `cv_payorderxq`;
CREATE TABLE `cv_payorderxq` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `billlist` varchar(255) DEFAULT NULL,
  `money` float(10,4) DEFAULT '0.0000',
  `status` int(1) NOT NULL DEFAULT '0',
  `add_time` int(11) DEFAULT '0',
  `pay_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of cv_payorderxq
-- ----------------------------

-- ----------------------------
-- Table structure for cv_question
-- ----------------------------
DROP TABLE IF EXISTS `cv_question`;
CREATE TABLE `cv_question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text,
  `sort` int(11) DEFAULT '0',
  `add_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of cv_question
-- ----------------------------

-- ----------------------------
-- Table structure for cv_sms
-- ----------------------------
DROP TABLE IF EXISTS `cv_sms`;
CREATE TABLE `cv_sms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `telnum` varchar(255) NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `code` varchar(255) NOT NULL,
  `content` varchar(255) DEFAULT NULL,
  `send_time` int(11) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `isuse` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=10099 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of cv_sms
-- ----------------------------

-- ----------------------------
-- Table structure for cv_user
-- ----------------------------
DROP TABLE IF EXISTS `cv_user`;
CREATE TABLE `cv_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vipid` int(11) DEFAULT '0',
  `telnum` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `quota` float(10,2) DEFAULT '0.00',
  `reg_time` int(11) DEFAULT '0',
  `reg_city` varchar(255) DEFAULT NULL,
  `reg_ip` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=238 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Records of cv_user
-- ----------------------------
INSERT INTO `cv_user` VALUES ('1', '101', '18888888888', '08e6606fd13e2577e67b57db6ddf91f77d66adbf', '1', '50000.00', '0', null, null);
