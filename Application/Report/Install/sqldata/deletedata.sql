--
-- 删除数据表 `qs_company_report`
--

DROP TABLE IF EXISTS `qs_company_report`;

--
-- 删除 `qs_page` 中插件数据
--

DELETE FROM `qs_page` WHERE `alias`='QS_company_report';