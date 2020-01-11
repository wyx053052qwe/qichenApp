--
-- 删除数据表 `qs_parttime_category`
--

DROP TABLE IF EXISTS `qs_parttime_category`;

--
-- 删除数据表 `qs_parttime_jobs`
--

DROP TABLE IF EXISTS `qs_parttime_jobs`;

--
-- 删除数据表 `qs_parttime_jobs_apply`
--

DROP TABLE IF EXISTS `qs_parttime_jobs_apply`;

--
-- 删除数据表 `qs_parttime_jobs_search`
--

DROP TABLE IF EXISTS `qs_parttime_jobs_search`;

--
-- 删除 `qs_config` 中配置项
--

DELETE FROM `qs_config` WHERE `type`='Parttime';
