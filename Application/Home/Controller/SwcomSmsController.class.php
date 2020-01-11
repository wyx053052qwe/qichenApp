<?php

namespace Home\Controller;
use Common\Controller\FrontendController;
class SwcomSmsController extends FrontendController{
	
    public function _initialize() {
        if(file_exists('data/SwcomSms_install.lock'))
		{
			exit('您已经安装过深信短信平台插件，如果想重新安装，请删除data目录下SwcomSms_install.lock文件');
		}			
    }
	public function install(){
$sql=<<<EOF
DELETE FROM `__PREFIX__sms` WHERE alias = 'SwcomSms';
INSERT INTO `__PREFIX__sms` (`name`, `config`, `alias`, `replace`, `filing`, `remark`, `create_time`, `update_time`, `ordid`, `status`) VALUES ('深信','a:3:{s:6:\"appkey\";s:11:\"\";s:9:\"secretKey\";s:18:\"\";s:9:\"signature\";s:9:\"人才港\";}','SwcomSms','',0,'申请地址：深信官网http://sms.swcom.cn',1456373436, 1469004894,998,1);
DELETE FROM `__PREFIX__sms_templates` WHERE type = 'SwcomSms';
INSERT INTO `__PREFIX__sms_templates` (`alias`, `name`, `value`, `type`, `tpl_id`, `params`, `module`) VALUES
('set_applyjobs','申请职位','{sitename}提醒您:{personalfullname}申请了您发布的职位{jobsname}，请登录{sitedomain}查看','SwcomSms','','【网站名称】{sitename}【申请人姓名】{personalfullname}【职位名称】{jobsname}',''),
('set_invite','邀请面试','{sitename}提醒您：{companyname}对您发起了面试邀请，请登录{sitedomain}查看','SwcomSms','','【网站名称】{sitename}【企业名称】{companyname}【职位名称】{jobsname}',''),
('set_order','申请充值','{sitename}提醒您：订单{oid}已经添加成功，付款方式为：{paymenttpye}，应付金额{amount}。请登录{sitedomain}查看','SwcomSms','','【网站名称】{sitename}【订单号】{oid}【付款方式】{paymenttpye}【应付金额】{amount}',''),
('set_payment','充值成功','{sitename}提醒您：充值成功，系统已为您开通服务，请登录{sitedomain}查看','SwcomSms','','【网站名称】{sitename}',''),
('set_editpwd','修改密码','{sitename}提醒您：您的密码修改成功，新密码为：{newpassword}','SwcomSms','','【网站名称】{sitename}【新密码】{newpassword}',''),
('set_jobsallow','职位审核通过','{sitename}提醒您：职位({jobsname})已经通过审核！请登录{sitedomain}查看','SwcomSms','','【网站名称】{sitename}【职位名称】{jobsname}',''),
('set_jobsnotallow','职位审核未通过','{sitename}提醒您：职位({jobsname})未通过审核，请修改后再次提交审核！请登录{sitedomain}查看','SwcomSms','','【网站名称】{sitename}【职位名称】{jobsname}',''),
('set_licenseallow','营业执照审核通过','{sitename}提醒您：您的企业资料已认证通过！请登录{sitedomain}查看','SwcomSms','','【网站名称】{sitename}',''),
('set_licensenotallow','营业执照审核未通过','{sitename}提醒您：你的企业认证未通过，请重新上传营业执照！请登录{sitedomain}查看','SwcomSms','','【网站名称】{sitename}',''),
('set_resumeallow','简历审核通过','{sitename}提醒您：您的简历已通过审核！请登录{sitedomain}查看','SwcomSms','','【网站名称】{sitename}',''),
('set_resumenotallow','简历审核未通过','{sitename}提醒您：您的简历未通过审核，请修改后再次提交审核！请登录{sitedomain}查看','SwcomSms','','【网站名称】{sitename}',''),
('set_login','手机登录验证','您正在登录{sitename}的会员,手机验证码为:{rand},此验证码有效期为10分钟','SwcomSms','','【网站名称】{sitename}【验证码】{rand}',''),
('set_testing','测试','您好！这是一条检测短信模块配置的短信。收到此短信，意味着您的短信模块设置正确！您可以进行其它操作了！','SwcomSms','','',''),
('set_retrieve_password','找回密码','您正在找回{sitename}的会员密码,手机验证码为:{rand},此验证码有效期为10分钟','SwcomSms','','【网站名称】{sitename}【验证码】{rand}',''),
('set_register','注册账号','您正在注册{sitename}的会员,手机验证码为:{rand},此验证码有效期为10分钟','SwcomSms','','【网站名称】{sitename}【验证码】{rand}',''),
('set_register_resume','快速注册简历','欢迎您注册{sitename}，用户名：{username}，密码：{password}。您也可以直接用手机号登录。','SwcomSms','','【网站名称】{sitename}【用户名】{username}【密码】{password}',''),
('set_mobile_auth','手机认证','感谢您使用{sitename}手机认证,验证码为:{rand}','SwcomSms','','【网站名称】{sitename}【验证码】{rand}',''),
('set_mobile_verify','手机验证','感谢您使用{sitename}手机验证,验证码为:{rand}','SwcomSms','','【网站名称】{sitename}【验证码】{rand}','');
EOF;

$sql2=<<<EOF
DELETE FROM `__PREFIX__sms` WHERE alias = 'SwcomSms';
INSERT INTO `__PREFIX__sms` (`name`, `config`, `alias`, `replace`, `filing`, `remark`, `create_time`, `update_time`, `ordid`, `status`) VALUES ('深信','a:3:{s:6:\"appkey\";s:11:\"\";s:9:\"secretKey\";s:18:\"\";s:9:\"signature\";s:9:\"人才港\";}','SwcomSms','',0,'申请地址：深信官网http://sms.swcom.cn',1456373436, 1469004894,998,1);
DELETE FROM `__PREFIX__sms_templates` WHERE type = 'SwcomSms';
INSERT INTO `__PREFIX__sms_templates` (`alias`, `name`, `value`, `type`, `tpl_id`, `params`) VALUES
('set_applyjobs','申请职位','{sitename}提醒您:{personalfullname}申请了您发布的职位{jobsname}，请登录{sitedomain}查看','SwcomSms','','【网站名称】{sitename}【申请人姓名】{personalfullname}【职位名称】{jobsname}'),
('set_invite','邀请面试','{sitename}提醒您：{companyname}对您发起了面试邀请，请登录{sitedomain}查看','SwcomSms','','【网站名称】{sitename}【企业名称】{companyname}【职位名称】{jobsname}'),
('set_order','申请充值','{sitename}提醒您：订单{oid}已经添加成功，付款方式为：{paymenttpye}，应付金额{amount}。请登录{sitedomain}查看','SwcomSms','','【网站名称】{sitename}【订单号】{oid}【付款方式】{paymenttpye}【应付金额】{amount}'),
('set_payment','充值成功','{sitename}提醒您：充值成功，系统已为您开通服务，请登录{sitedomain}查看','SwcomSms','','【网站名称】{sitename}'),
('set_editpwd','修改密码','{sitename}提醒您：您的密码修改成功，新密码为：{newpassword}','SwcomSms','','【网站名称】{sitename}【新密码】{newpassword}'),
('set_jobsallow','职位审核通过','{sitename}提醒您：职位({jobsname})已经通过审核！请登录{sitedomain}查看','SwcomSms','','【网站名称】{sitename}【职位名称】{jobsname}'),
('set_jobsnotallow','职位审核未通过','{sitename}提醒您：职位({jobsname})未通过审核，请修改后再次提交审核！请登录{sitedomain}查看','SwcomSms','','【网站名称】{sitename}【职位名称】{jobsname}'),
('set_licenseallow','营业执照审核通过','{sitename}提醒您：您的企业资料已认证通过！请登录{sitedomain}查看','SwcomSms','','【网站名称】{sitename}'),
('set_licensenotallow','营业执照审核未通过','{sitename}提醒您：你的企业认证未通过，请重新上传营业执照！请登录{sitedomain}查看','SwcomSms','','【网站名称】{sitename}'),
('set_resumeallow','简历审核通过','{sitename}提醒您：您的简历已通过审核！请登录{sitedomain}查看','SwcomSms','','【网站名称】{sitename}'),
('set_resumenotallow','简历审核未通过','{sitename}提醒您：您的简历未通过审核，请修改后再次提交审核！请登录{sitedomain}查看','SwcomSms','','【网站名称】{sitename}'),
('set_login','手机登录验证','您正在登录{sitename}的会员,手机验证码为:{rand},此验证码有效期为10分钟','SwcomSms','','【网站名称】{sitename}【验证码】{rand}'),
('set_testing','测试','您好！这是一条检测短信模块配置的短信。收到此短信，意味着您的短信模块设置正确！您可以进行其它操作了！','SwcomSms','',''),
('set_retrieve_password','找回密码','您正在找回{sitename}的会员密码,手机验证码为:{rand},此验证码有效期为10分钟','SwcomSms','','【网站名称】{sitename}【验证码】{rand}'),
('set_register','注册账号','您正在注册{sitename}的会员,手机验证码为:{rand},此验证码有效期为10分钟','SwcomSms','','【网站名称】{sitename}【验证码】{rand}'),
('set_register_resume','快速注册简历','欢迎您注册{sitename}，用户名：{username}，密码：{password}。您也可以直接用手机号登录。','SwcomSms','','【网站名称】{sitename}【用户名】{username}【密码】{password}'),
('set_mobile_auth','手机认证','感谢您使用{sitename}手机认证,验证码为:{rand}','SwcomSms','','【网站名称】{sitename}【验证码】{rand}'),
('set_mobile_verify','手机验证','感谢您使用{sitename}手机验证,验证码为:{rand}','SwcomSms','','【网站名称】{sitename}【验证码】{rand}');
EOF;
    	$Model = new \Think\Model;
    	$res=$Model->execute($sql);
    	if($res){
			
			if(is_writable('data'))
			{
				$fp = @fopen('data/SwcomSms_install.lock', 'wb+');
				fwrite($fp, 'OK');
				fclose($fp);
			}
			echo '深信插件安装成功,请手动删除/Application/Home/Controller/SwcomSmsController.class.php 这1个文件';
			
    	}else{
			
			$res2=$Model->execute($sql2);
        	if($res2){
				
				if(is_writable('data'))
				{
					$fp = @fopen('data/SwcomSms_install.lock', 'wb+');
					fwrite($fp, 'OK');
					fclose($fp);
				}
				echo '深信插件安装成功,请手动删除/Application/Home/Controller/SwcomSmsController.class.php 这1个文件';
			  
    		}else{
					
        		echo '深信插件安装失败';
				
    		}
			
    	}
	
	
	}
}
?>