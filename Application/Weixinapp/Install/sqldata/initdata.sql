--
-- 转存表中的数据 `qs_config`
--

INSERT INTO `qs_config` (`name`, `value`, `note`, `type`) VALUES 
('weixinapp_appid', '', '微信小程序appid', 'Weixinapp'),
('weixinapp_appsecret', '', '微信小程序appsecret', 'Weixinapp'),
('weixinapp_top_color', '#0066FF', '微信小程序顶部背景色', 'Weixinapp'),
('weixinapp_index_jobs_type', 'new', '微信小程序首页职位推荐类型', 'Weixinapp'),
('weixinapp_index_login_recommend', '1', '微信小程序登录开启千人千面', 'Weixinapp'),
('weixinapp_secretkey', '', '微信小程序secretkey', 'Weixinapp'),
('weixinapp_qrcode', '', '微信小程序二维码', 'Weixinapp');



--
-- 转存表中的数据 `qs_menu`
--
INSERT INTO `qs_navigation_weixinapp`(`id`,`sys_name`,`show_name`,`nav_img`,`display`,`ordid`,`alias`) values 
(NULL,'地图搜索','地图搜索','',0,0,'map'),
(NULL,'找企业','找企业','',0,2,'companylist'),
(NULL,'附近职位','附近职位','',0,0,'nearbyjobs'),
(NULL,'网络招聘会','网络招聘会','',0,0,'subject');
