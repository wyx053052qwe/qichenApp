--
-- 表的结构 `qs_rpo`
--

DROP TABLE IF EXISTS `qs_rpo`;
CREATE TABLE `qs_rpo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `com_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '公司id',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员uid',
  `contact` char(20) NOT NULL DEFAULT '' COMMENT '联系人',
  `phone` char(50) NOT NULL DEFAULT '' COMMENT '联系电话',
  `job` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '申请的服务岗位',
  `stage` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '申请的服务阶段',
  `status` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '服务状态',
  `notes` varchar(255) NOT NULL DEFAULT '' COMMENT '申请备注',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '申请时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `qs_rpo_category`
--

DROP TABLE IF EXISTS `qs_rpo_category`;
CREATE TABLE `qs_rpo_category` (
  `cid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类ID',
  `title` varchar(20) NOT NULL DEFAULT '' COMMENT '分类名称',
  `desc` varchar(50) NOT NULL DEFAULT '' COMMENT '描述',
  `img` varchar(200) NOT NULL DEFAULT '' COMMENT '分类图标',
  `type` char(20) NOT NULL DEFAULT '' COMMENT '所属组别',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `display` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  PRIMARY KEY (`cid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `qs_rpo_follow`
--

DROP TABLE IF EXISTS `qs_rpo_follow`;
CREATE TABLE `qs_rpo_follow` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '跟进记录id',
  `rpo_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '申请记录id',
  `com_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '公司id',
  `com_uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '公司会员uid',
  `admin_uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '客服uid',
  `comment` varchar(255) NOT NULL DEFAULT '' COMMENT '内容',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '跟进时间',
  PRIMARY KEY (`id`),
  KEY `rpo_id` (`rpo_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
