--
-- 表的结构 `qs_company_report`
--

DROP TABLE IF EXISTS `qs_company_report`;
CREATE TABLE IF NOT EXISTS `qs_company_report` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员id',
  `com_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '公司id',
  `com_name` varchar(60) NOT NULL DEFAULT '' COMMENT '企业名称',
  `corporate` varchar(30) NOT NULL DEFAULT '' COMMENT '企业法人',
  `com_type` varchar(60) NOT NULL DEFAULT '' COMMENT '主体类型',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创立时间',
  `reg_capital` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册资金',
  `reg_address` varchar(100) NOT NULL DEFAULT '' COMMENT '注册地址',
  `office_address` varchar(100) NOT NULL DEFAULT '' COMMENT '办公地址',
  `registrar` varchar(60) NOT NULL DEFAULT '' COMMENT '登记机关',
  `scope` text NOT NULL COMMENT '经营范围',
  `office_area` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '办公面积',
  `office_env` tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '办公环境（1一般2良好3优美）',
  `workplace` varchar(30) NOT NULL DEFAULT '' COMMENT '办公场所',
  `number` varchar(30) NOT NULL DEFAULT '' COMMENT '员工人数',
  `sex_ratio` varchar(30) NOT NULL DEFAULT '' COMMENT '男女比例',
  `average_age` int(3) unsigned NOT NULL DEFAULT '0' COMMENT '平均年龄',
  `route` varchar(100) NOT NULL DEFAULT '' COMMENT '乘车路线',
  `img` text COMMENT '企业照片',
  `evaluation` text NOT NULL COMMENT '评价',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '认证时间',
  `certifier` varchar(30) NOT NULL DEFAULT '' COMMENT '认证师',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;