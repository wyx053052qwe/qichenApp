--
-- 转存表中的数据 `qs_mall_category`
--

INSERT INTO `qs_mall_category` (`id`,`parentid`,`categoryname`,`category_order`) VALUES
(1,0,'生活家居',0),
(2,0,'时尚数码',0),
(3,0,'手机充值',0),
(4,1,'服装配饰',0),
(5,1,'清洁护理',0),
(6,1,'灯饰照明',0),
(7,2,'厨房用品',0),
(8,2,'手机配件',0),
(9,2,'生活电器',0),
(10,3,'话费直充',0),
(11,3,'流量加油包',0);

--
-- 转存表中的数据 `qs_mall_hotword`
--

INSERT INTO `qs_mall_hotword` (`id`, `w_word`, `w_hot`) VALUES
(1,'短信叠加包',1),
(2,'手机充值卡',1),
(3,'数码电子',1);

--
-- 转存表中的数据 `qs_page`
--

INSERT INTO `qs_page` VALUES 
(NULL,1,1,'QS_mall_index','积分商城首页','Mall','Index','index','',0,0,'shop','积分商城首页','','','','Home'),
(NULL,1,2,'QS_goods_list','积分商城列表页','Mall','Index','goods_list','',0,0,'shop_list','积分商城','','','a:1:{s:3:\"key\";s:9:\"关键字\";}','Home'),
(NULL,1,3,'QS_goods_show','积分商城详细页面','Mall','Index','goods_show','',0,0,'shop_list','积分商城 - {site_name}','{content}','{category_cn},{goods_title},{goods_brand}','a:4:{s:11:\"category_cn\";s:12:\"分类名称\";s:11:\"goods_title\";s:12:\"商品名称\";s:7:\"content\";s:12:\"商品描述\";s:11:\"goods_brand\";s:12:\"商品品牌\";}','Home'),
(NULL,1,2,'QS_mall_charts','积分商城排行榜','Mall','Index','charts','',0,0,'shop_charts','积分商城 - 排行榜 - {site_name}','','','','Home');

