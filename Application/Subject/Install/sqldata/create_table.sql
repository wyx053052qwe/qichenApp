-- --------------------------------------------------------

--
-- 表的结构 `qs_subject`
--

DROP TABLE IF EXISTS `qs_subject`;
CREATE TABLE `qs_subject` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `content` mediumtext NOT NULL,
  `tit_color` varchar(10) DEFAULT NULL,
  `tit_b` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `small_img` varchar(255) DEFAULT NULL,
  `is_display` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `is_url` varchar(200) NOT NULL DEFAULT '0',
  `seo_keywords` varchar(100) DEFAULT NULL,
  `seo_description` varchar(200) DEFAULT NULL,
  `click` int(10) unsigned NOT NULL DEFAULT '1',
  `addtime` int(10) unsigned NOT NULL,
  `article_order` smallint(5) unsigned NOT NULL DEFAULT '0',
  `s_tpl_id` int(10) NOT NULL,
  `s_tpl_name` varchar(80) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `click` (`click`),
  KEY `focos_article_order` (`article_order`,`id`),
  KEY `addtime` (`addtime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `qs_subject_company`
--

DROP TABLE IF EXISTS `qs_subject_company`;
CREATE TABLE `qs_subject_company` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `company_uid` int(10) NOT NULL,
  `addtime` int(10) NOT NULL,
  `subject_id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
