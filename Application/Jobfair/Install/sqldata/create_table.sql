--
-- 表的结构 `qs_jobfair`
--

DROP TABLE IF EXISTS `qs_jobfair`;
CREATE TABLE `qs_jobfair` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `display` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `predetermined_status` tinyint(1) NOT NULL,
  `predetermined_web` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `predetermined_tel` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `predetermined_point` int(10) NOT NULL,
  `title` varchar(200) NOT NULL,
  `introduction` text NOT NULL,
  `address` varchar(200) NOT NULL,
  `contact` varchar(100) NOT NULL,
  `phone` varchar(100) NOT NULL,
  `bus` varchar(200) NOT NULL,
  `holddate_start` int(10) unsigned NOT NULL,
  `holddate_end` int(10) unsigned NOT NULL,
  `predetermined_start` int(10) unsigned NOT NULL DEFAULT '0',
  `predetermined_end` int(10) unsigned NOT NULL DEFAULT '0',
  `number` varchar(200) NOT NULL,
  `addtime` int(10) NOT NULL,
  `ordid` int(10) unsigned NOT NULL DEFAULT '0',
  `click` int(10) unsigned NOT NULL DEFAULT '1',
  `participants_object` text NOT NULL,
  `price` text NOT NULL,
  `registration_method` text NOT NULL,
  `thumb` varchar(250) NOT NULL,
  `intro_img` varchar(250) NOT NULL,
  `map_x` decimal(9,6) NOT NULL,
  `map_y` decimal(9,6) NOT NULL,
  `map_zoom` tinyint(3) unsigned NOT NULL,
  `tpl_id` int(10) unsigned DEFAULT 0 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `qs_jobfair_area`
--

DROP TABLE IF EXISTS `qs_jobfair_area`;
CREATE TABLE `qs_jobfair_area` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `jobfair_id` int(10) unsigned NOT NULL,
  `area` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `qs_jobfair_exhibitors`
--

DROP TABLE IF EXISTS `qs_jobfair_exhibitors`;
CREATE TABLE `qs_jobfair_exhibitors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `audit` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `etype` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `companyname` varchar(200) NOT NULL,
  `company_id` int(10) unsigned NOT NULL DEFAULT '0',
  `company_addtime` int(10) unsigned NOT NULL DEFAULT '0',
  `eaddtime` int(10) unsigned NOT NULL,
  `jobfair_id` int(10) unsigned NOT NULL,
  `jobfair_title` varchar(200) NOT NULL,
  `jobfair_addtime` int(10) unsigned NOT NULL,
  `position_id` int(10) unsigned NOT NULL,
  `position` varchar(10) NOT NULL,
  `note` varchar(200) NOT NULL,
  `recommend` tinyint(1) unsigned NOT NULL,
  `contact` varchar(30) NOT NULL DEFAULT '',
  `mobile` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `etypr` (`etype`),
  KEY `uid` (`uid`),
  KEY `jobfairid` (`jobfair_id`),
  KEY `eaddtime` (`eaddtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `qs_jobfair_position`
--

DROP TABLE IF EXISTS `qs_jobfair_position`;
CREATE TABLE `qs_jobfair_position` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `jobfair_id` int(10) unsigned NOT NULL,
  `area_id` varchar(10) NOT NULL,
  `position` varchar(10) NOT NULL,
  `company_id` int(10) unsigned NOT NULL,
  `company_uid` int(10) unsigned NOT NULL,
  `company_name` varchar(30) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL COMMENT '0可预订 1已预订 2审核中 3暂停预定',
  `orderid` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `qs_jobfair_position_img`
--

DROP TABLE IF EXISTS `qs_jobfair_position_img`;
CREATE TABLE `qs_jobfair_position_img` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `jobfair_id` int(10) unsigned NOT NULL,
  `img` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `qs_jobfair_retrospect`
--

DROP TABLE IF EXISTS `qs_jobfair_retrospect`;
CREATE TABLE `qs_jobfair_retrospect` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `img` varchar(250) NOT NULL,
  `jobfair_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `qs_jobfair_position_tpl`
--

DROP TABLE IF EXISTS `qs_jobfair_position_tpl`;
CREATE TABLE `qs_jobfair_position_tpl` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL,
  `area` text NOT NULL,
  `position` text NOT NULL,
  `position_img` text NOT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
