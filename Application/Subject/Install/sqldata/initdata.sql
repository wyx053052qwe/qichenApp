--
--
-- 转存表中的数据 `qs_page`
--

INSERT INTO `qs_page` VALUES
(NULL,1,2,'QS_subject_list','网络专题招聘会列表','Subject','Index','index','',0,0,'Subject','网络专题招聘会列表 - {site_name}','','','','Home'),
(NULL,1,3,'QS_subject_show','网络专题招聘会详情页','Subject','Index','Subject_show','',0,0,'Subject','网络专题招聘会详情页 - {site_name}','','','','Home');

--
--
-- 转存表中的数据 `qs_tpl`
--
INSERT INTO `qs_tpl` (`tpl_id`, `tpl_type`, `tpl_name`, `tpl_display`, `tpl_dir`, `tpl_val`) VALUES
('NULL', '3', '专题模板1', '1', 'default', '0'),
('NULL', '3', '专题模板2', '1', 'default2', '0'),
('NULL', '3', '专题模板3', '1', 'default3', '0'),
('NULL', '3', '专题模板4', '1', 'default4', '0');

