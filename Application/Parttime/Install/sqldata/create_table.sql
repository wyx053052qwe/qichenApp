-- --------------------------------------------------------

--
-- 表的结构 `qs_parttime_category`
--

DROP TABLE IF EXISTS `qs_parttime_category`;
CREATE TABLE `qs_parttime_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(30) NOT NULL,
  `name` varchar(30) NOT NULL,
  `ordid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `qs_parttime_jobs`
--

DROP TABLE IF EXISTS `qs_parttime_jobs`;
CREATE TABLE `qs_parttime_jobs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `audit` tinyint(1) unsigned NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `jobs_name` varchar(30) NOT NULL,
  `category` int(10) unsigned NOT NULL,
  `category_cn` varchar(30) NOT NULL,
  `amount` int(10) unsigned NOT NULL,
  `district` varchar(100) NOT NULL,
  `district_cn` varchar(200) NOT NULL,
  `wage` int(10) unsigned NOT NULL,
  `wage_type` int(10) unsigned NOT NULL,
  `wage_type_cn` varchar(30) NOT NULL,
  `settlement` int(10) unsigned NOT NULL,
  `settlement_cn` varchar(30) NOT NULL,
  `wage_remark` varchar(200) NOT NULL,
  `cycle_starttime` int(10) unsigned NOT NULL,
  `cycle_endtime` int(10) unsigned NOT NULL,
  `long_period` tinyint(1) unsigned NOT NULL,
  `worktime` text NOT NULL,
  `address` varchar(200) NOT NULL,
  `contents` text NOT NULL,
  `sex` tinyint(1) unsigned NOT NULL,
  `sex_cn` varchar(30) NOT NULL,
  `identity` int(10) unsigned NOT NULL,
  `identity_cn` varchar(30) NOT NULL,
  `height` int(10) unsigned NOT NULL,
  `height_cn` varchar(30) NOT NULL,
  `minage` int(10) unsigned NOT NULL,
  `maxage` int(10) unsigned NOT NULL,
  `contact` varchar(30) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `addtime` int(10) unsigned NOT NULL,
  `refreshtime` int(10) unsigned NOT NULL,
  `click` int(10) unsigned NOT NULL,
  `apply_num` int(10) unsigned NOT NULL,
  `like_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '点赞数',
  PRIMARY KEY (`id`),
  KEY `refreshtime` (`refreshtime`),
  KEY `uid_refreshtime` (`uid`,`refreshtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `qs_parttime_jobs_apply`
--

DROP TABLE IF EXISTS `qs_parttime_jobs_apply`;
CREATE TABLE `qs_parttime_jobs_apply` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `pid` int(10) unsigned NOT NULL,
  `realname` varchar(30) NOT NULL,
  `sex` tinyint(1) unsigned NOT NULL,
  `sex_cn` varchar(30) NOT NULL,
  `birthdate` int(4) unsigned NOT NULL,
  `identity` int(10) unsigned NOT NULL,
  `identity_cn` varchar(30) NOT NULL,
  `mobile` varchar(11) NOT NULL,
  `addtime` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `qs_parttime_jobs_search`
--

DROP TABLE IF EXISTS `qs_parttime_jobs_search`;
CREATE TABLE `qs_parttime_jobs_search` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `category` int(10) unsigned NOT NULL,
  `district1` varchar(30) NOT NULL,
  `district2` varchar(30) NOT NULL,
  `district3` varchar(30) NOT NULL,
  `district4` varchar(30) NOT NULL,
  `district5` varchar(30) NOT NULL,
  `district6` varchar(30) NOT NULL,
  `refreshtime` int(10) unsigned NOT NULL,
  `settlement` int(10) unsigned NOT NULL,
  `key` varchar(200) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `refreshtime` (`refreshtime`),
  KEY `key_refreshtime` (`key`,`refreshtime`),
  KEY `settlement_refreshtime` (`settlement`,`refreshtime`),
  KEY `category_refreshtime` (`category`,`refreshtime`),
  KEY `d1_refreshtime` (`district1`,`refreshtime`),
  KEY `d2_refreshtime` (`district2`,`refreshtime`),
  KEY `d3_refreshtime` (`district3`,`refreshtime`),
  KEY `d4_refreshtime` (`district4`,`refreshtime`),
  KEY `d5_refreshtime` (`district5`,`refreshtime`),
  KEY `d6_refreshtime` (`district6`,`refreshtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;