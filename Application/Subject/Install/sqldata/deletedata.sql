--
-- 删除数据表 `qs_subject`
--

DROP TABLE IF EXISTS `qs_subject`;

--
-- 删除数据表 `qs_subject_company`
--

DROP TABLE IF EXISTS `qs_subject_company`;

--
-- 删除数据表 `qs_tpl`
--

DELETE FROM `qs_tpl` WHERE `tpl_type`='3';