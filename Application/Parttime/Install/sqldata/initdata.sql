--
-- 转存表中的数据 `qs_parttime_category`
--

INSERT INTO `qs_parttime_category`(`id`,`alias`,`name`,`ordid`) VALUES 
(NULL,'QS_wage_type','天',0),
(NULL,'QS_wage_type','周',0),
(NULL,'QS_wage_type','小时',0),
(NULL,'QS_wage_type','次',0),
(NULL,'QS_wage_type','个',0),
(NULL,'QS_wage_type','件',0),
(NULL,'QS_settlement','日结',0),
(NULL,'QS_settlement','次日结',0),
(NULL,'QS_settlement','周结',0),
(NULL,'QS_settlement','半月结',0),
(NULL,'QS_settlement','月结',0),
(NULL,'QS_settlement','完工结',0),
(NULL,'QS_identity','仅限学生',0),
(NULL,'QS_identity','学生优先',0),
(NULL,'QS_identity','仅限社会人员',0),
(NULL,'QS_height','155cm以上',0),
(NULL,'QS_height','160cm以上',0),
(NULL,'QS_height','165cm以上',0),
(NULL,'QS_height','170cm以上',0),
(NULL,'QS_height','175cm以上',0),
(NULL,'QS_height','180cm以上',0),
(NULL,'QS_identity_apply','学生',0),
(NULL,'QS_identity_apply','社会人员',0),
(NULL,'QS_category_jobs','促销导购',0),
(NULL,'QS_category_jobs','服务员',0),
(NULL,'QS_category_jobs','传单派发',0),
(NULL,'QS_category_jobs','打包分拣',0),
(NULL,'QS_category_jobs','礼仪模特',0),
(NULL,'QS_category_jobs','话务客服',0),
(NULL,'QS_category_jobs','家教老师',0),
(NULL,'QS_category_jobs','美工设计',0),
(NULL,'QS_category_jobs','审核录入',0),
(NULL,'QS_category_jobs','挂号排队',0),
(NULL,'QS_category_jobs','快递送餐',0),
(NULL,'QS_category_jobs','会展执行',0),
(NULL,'QS_category_jobs','活动安保',0),
(NULL,'QS_category_jobs','技工普工',0),
(NULL,'QS_category_jobs','其他',0);

--
-- 转存表中的数据 `qs_config`
--

INSERT INTO `qs_config` (`id`, `name`, `value`, `note`, `type`) VALUES
(NULL, 'parttime_max', '10', '发布兼职最大条数', 'Parttime'),
(NULL, 'audit_edit_parttime', '-1', '修改兼职后审核状态', 'Parttime'),
(NULL, 'contact_img_parttime', '1', '联系方式展现方式', 'Parttime');

--
-- 转存表中的数据 `qs_page`
--

INSERT INTO `qs_page` VALUES
(NULL,1,1,'QS_parttime','兼职首页','Parttime','Index','index','',0,0,'parttime','兼职招聘 - {site_name}','','','','Home'),
(NULL,1,3,'QS_parttime_show','兼职招聘详细页','Parttime','Index','show','',0,0,'parttime','{jobs_name} - {category_cn} - {site_name}','{jobs_name}','{jobs_name},{category_cn},{district_cn}','a:8:{s:9:\\\"jobs_name\\\";s:12:\\\"岗位名称\\\";s:11:\\\"category_cn\\\";s:12:\\\"兼职类别\\\";}','Home');