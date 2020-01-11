--
-- 转存表中的数据 `qs_rpo_category`
--

INSERT INTO `qs_rpo_category` (`cid`, `title`, `desc`, `img`, `type`, `sort`, `display`) VALUES
(1, '通用性岗位RPO', '行政、客服、基层管理、财务、物流等', '', 'job', 0, 1),
(2, '技术型人才RPO', '汽车、电子、机械设备制造、酒店餐饮等', '', 'job', 0, 1),
(3, '高端人才RPO', '销售经理、财务总监、项目主管、总经理等', '', 'job', 0, 1),
(4, '包面试', '包面试', '', 'stage', 0, 1),
(5, '包入职', '包入职', '', 'stage', 0, 1),
(6, '包入职满1年', '包入职满1年', '', 'stage', 0, 1);


--
-- 转存表中的数据 `qs_category_group`
--

INSERT INTO `qs_category_group` (`g_id`, `g_alias`, `g_name`, `g_sys`) VALUES
(NULL, 'QS_rpo_status', 'RPO服务状态', 1);

--
-- 转存表中的数据 `qs_category`
--

INSERT INTO `qs_category` (`c_id`, `c_parentid`, `c_alias`, `c_name`, `c_order`, `c_index`, `c_note`, `stat_jobs`, `stat_resume`) VALUES
(NULL, 0, 'QS_rpo_status', '待了解需求', 0, '', '', '', ''),
(NULL, 0, 'QS_rpo_status', '有意向，跟进中', 0, '', '', '', ''),
(NULL, 0, 'QS_rpo_status', '无意向，可删除', 0, '', '', '', ''),
(NULL, 0, 'QS_rpo_status', '已签订合同，服务中', 0, '', '', '', ''),
(NULL, 0, 'QS_rpo_status', '合同完成', 0, '', '', '', '');
