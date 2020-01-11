--
-- 删除数据表 `qs_ad_weixinapp`
--

DROP TABLE IF EXISTS `qs_ad_weixinapp`;

--
-- 删除数据表 `qs_navigation_weixinapp`
--

DROP TABLE IF EXISTS `qs_navigation_weixinapp`;

--
-- 删除数据 `qs_config`
--

DELETE FROM `qs_config` WHERE `type`='Weixinapp';