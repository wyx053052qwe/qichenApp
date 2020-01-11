--
-- 表的结构 `qs_weixin_config`
--

DROP TABLE IF EXISTS `qs_weixin_config`;
CREATE TABLE `qs_weixin_config` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `value` varchar(200) NOT NULL,
  `info` varchar(255) NOT NULL,
  `ordid` smallint(4) NOT NULL,
  `template_title` varchar(30) NOT NULL,
  `template_trade` varchar(30) NOT NULL,
  `template_no` varchar(30) NOT NULL,
  `template_id` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `qs_weixin_menu`
--

DROP TABLE IF EXISTS `qs_weixin_menu`;
CREATE TABLE `qs_weixin_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parentid` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(30) NOT NULL,
  `key` varchar(30) NOT NULL,
  `type` varchar(30) NOT NULL,
  `url` varchar(255) NOT NULL,
  `menu_order` smallint(5) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL,
  `appid` varchar(100) NOT NULL,
  `pagepath` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `qs_weixin_msg_list`
--

DROP TABLE IF EXISTS `qs_weixin_msg_list`;
CREATE TABLE `qs_weixin_msg_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `utype` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `username` varchar(60) NOT NULL DEFAULT '',
  `weixin_openid` varchar(50) NOT NULL,
  `content` text NOT NULL,
  `sendtime` int(10) unsigned NOT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `qs_weixin_msg_list`
--

DROP TABLE IF EXISTS `qs_weixin_msg_queue_list`;
CREATE TABLE `qs_weixin_msg_queue_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `utype` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `username` varchar(60) NOT NULL DEFAULT '',
  `openid` varchar(50) DEFAULT NULL,
  `content` text NOT NULL,
  `sendtime` int(10) unsigned NOT NULL,
  `type` tinyint(1) DEFAULT NULL,
  `addtime` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
