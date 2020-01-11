--
-- 删除数据表 `qs_qq_float`
--

DROP TABLE IF EXISTS `qs_qq_float`;

--
-- 删除 `qs_config` 中配置项
--

DELETE FROM `qs_config` WHERE `name`='qq_float_open';
DELETE FROM `qs_config` WHERE `name`='qq_float_position';
DELETE FROM `qs_config` WHERE `name`='qq_float_theme';