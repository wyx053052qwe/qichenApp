--
-- 删除数据表 `qs_rpo`
--

DROP TABLE IF EXISTS `qs_rpo`;

--
-- 删除数据表 `qs_rpo_category`
--

DROP TABLE IF EXISTS `qs_rpo_category`;

--
-- 删除数据表 `qs_rpo_follow`
--

DROP TABLE IF EXISTS `qs_rpo_follow`;

--
-- 删除 `qs_category_group` 中插件数据
--

DELETE FROM `qs_category_group` WHERE `g_alias`='QS_rpo_status';

--
-- 删除 `qs_category` 中插件数据
--

DELETE FROM `qs_category` WHERE `c_alias`='QS_rpo_status';
