--
-- 表的结构 `qs_qq_float`
--

DROP TABLE IF EXISTS `qs_qq_float`;
CREATE TABLE IF NOT EXISTS `qs_qq_float` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `qq` char(20) NOT NULL DEFAULT '' COMMENT 'QQ号',
  `name` char(15) NOT NULL DEFAULT '' COMMENT '姓名',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `display` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;