--
-- 转存表中的数据 `qs_weixin_config`
--

INSERT INTO `qs_weixin_config` (`id`,`alias`,`name`,`value`,`info`,`ordid`,`template_title`,`template_trade`,`template_no`,`template_id`) VALUES
('1', 'set_invite', '邀请面试', '0', '企业邀请个人面试是否通知个人', '255', '面试通知提醒', 'IT科技/互联网|电子商务', 'TM00262', ''),
('2', 'set_order', '新增订单', '0', '会员下充值订单是否通知', '255', '订单支付提醒', '商业服务/中介服务', 'OPENTM202819271', ''),
('3', 'set_payment', '充值成功', '0', '会员充值成功是否通知', '255', '支付成功通知', 'IT科技/互联网|电子商务', 'OPENTM207128229', ''),
('4', 'set_editpwd', '修改密码', '0', '会员修改密码后是否通知', '255', '找回密码通知', 'IT科技/互联网|电子商务', 'OPENTM202110965', ''),
('5', 'set_jobsallow', '职位审核结果通知', '0', '职位审核通过/未通过是否通知', '255', '职位审核提醒', 'IT科技/互联网|电子商务', 'OPENTM204819096', ''),
('7', 'set_resumeallow', '简历审核结果通知', '0', '个人简历审核通过/未通过是否通知', '255', '简历审核结果通知', 'IT科技/互联网|电子商务', 'OPENTM201786390', ''),
('9', 'set_applyjobs', '申请职位', '0', '个人申请了职位是否通知企业', '255', '职位申请提醒', 'IT科技/互联网|电子商务', 'OPENTM207350149', ''),
('10', 'set_licenseallow', '营业执照审核结果通知', '0', '营业执照审核通过/未通过是否通知', '255', '营业执照审核提醒', 'IT科技/互联网|电子商务', 'OPENTM401873748', ''),
('12', 'set_resume_photoallow', '简历头像审核结果通知', '0', '简历头像审核通过/未通过是否通知', '255', '营业执照审核提醒', 'IT科技/互联网|电子商务', 'OPENTM401873748', ''),
('13', 'set_rongyun_pms_value', '融云消息提醒', '0', '融云聊天信息是否通知', '255', '用户咨询提醒', 'IT科技/互联网|电子商务', 'OPENTM202119578', '');

--
-- 转存表中的数据 `qs_weixin_menu`
--

INSERT INTO `qs_weixin_menu` (`id`,`parentid`,`title`,`key`,`type`,`url`,`menu_order`,`status`) VALUES
(1,0,'个人服务','','click','',0,1),
(2,1,'刷新简历','resume_refresh','click','',0,1),
(3,1,'面试邀请','','view','https://open.weixin.qq.com/connect/oauth2/authorize?appid=|appid|&redirect_uri=|domain|/mobile/personal/jobs_interview&response_type=code&scope=snsapi_base',0,1),
(4,1,'投递反馈','','view','https://open.weixin.qq.com/connect/oauth2/authorize?appid=|appid|&redirect_uri=|domain|/mobile/personal/jobs_apply&response_type=code&scope=snsapi_base',0,1),
(5,1,'职位搜索','','view','https://open.weixin.qq.com/connect/oauth2/authorize?appid=|appid|&redirect_uri=|domain|/mobile/jobs/index&response_type=code&scope=snsapi_base',0,1),
(6,0,'企业服务','','click','',0,1),
(7,6,'刷新职位','jobs_refresh','click','',0,1),
(8,6,'职位管理','','view','https://open.weixin.qq.com/connect/oauth2/authorize?appid=|appid|&redirect_uri=|domain|/mobile/company/jobs_list&response_type=code&scope=snsapi_base',0,1),
(9,0,'更多精彩','','click','',0,1),
(10,6,'应聘简历','','view','https://open.weixin.qq.com/connect/oauth2/authorize?appid=|appid|&redirect_uri=|domain|/mobile/company/jobs_apply&response_type=code&scope=snsapi_base',0,1),
(11,1,'周边职位','nearby_jobs','click','',0,1),
(12,6,'简历搜索','','view','https://open.weixin.qq.com/connect/oauth2/authorize?appid=|appid|&redirect_uri=|domain|/mobile/resume/index&response_type=code&scope=snsapi_base',0,1),
(13,9,'每日签到','sign_day','click','',0,1),
(14,9,'帐号绑定','binding','click','',0,1),
(15,9,'最近招聘会','','view','https://open.weixin.qq.com/connect/oauth2/authorize?appid=|appid|&redirect_uri=|domain|/mobile/Jobfair/index&response_type=code&scope=snsapi_base',0,1);


