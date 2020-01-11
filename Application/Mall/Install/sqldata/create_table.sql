--
-- 表的结构 `qs_mall_category`
--

DROP TABLE IF EXISTS `qs_mall_category`;
CREATE TABLE `qs_mall_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parentid` smallint(5) unsigned NOT NULL,
  `categoryname` varchar(80) NOT NULL,
  `category_order` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `parentid` (`parentid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `qs_mall_exchange`
--

DROP TABLE IF EXISTS `qs_mall_exchange`;
CREATE TABLE `qs_mall_exchange` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `goods_id` int(10) unsigned NOT NULL,
  `goods_title` varchar(255) NOT NULL,
  `points` int(10) unsigned NOT NULL,
  `num` int(10) NOT NULL,
  `uid` int(10) unsigned NOT NULL,
  `username` varchar(60) NOT NULL,
  `addtime` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `company_uid` (`uid`),
  KEY `goods_id` (`goods_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `qs_mall_goods`
--

DROP TABLE IF EXISTS `qs_mall_goods`;
CREATE TABLE `qs_mall_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category` smallint(5) NOT NULL,
  `scategory` smallint(5) NOT NULL,
  `category_cn` varchar(50) NOT NULL,
  `goods_number` varchar(20) NOT NULL,
  `goods_title` varchar(255) NOT NULL,
  `goods_brand` varchar(50) NOT NULL,
  `goods_stock` int(10) NOT NULL,
  `goods_customer` int(10) NOT NULL,
  `goods_points` int(10) NOT NULL,
  `goods_img` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `recommend` smallint(2) NOT NULL DEFAULT '0',
  `addtime` int(10) NOT NULL,
  `click` int(10) unsigned NOT NULL,
  `ex_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `click` (`click`),
  KEY `goods_points` (`goods_points`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `qs_mall_hotword`
--

DROP TABLE IF EXISTS `qs_mall_hotword`;
CREATE TABLE `qs_mall_hotword` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `w_word` varchar(20) NOT NULL,
  `w_hot` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `s_hot` (`w_hot`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `qs_mall_order`
--

DROP TABLE IF EXISTS `qs_mall_order`;
CREATE TABLE `qs_mall_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `username` varchar(50) NOT NULL,
  `goods_id` int(10) NOT NULL,
  `goods_title` varchar(255) NOT NULL,
  `goods_points` int(10) NOT NULL,
  `goods_num` int(10) NOT NULL,
  `order_points` int(10) NOT NULL,
  `contact` varchar(30) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `address` varchar(100) NOT NULL,
  `status` smallint(3) NOT NULL DEFAULT '0',
  `addtime` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `goods_id` (`goods_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
