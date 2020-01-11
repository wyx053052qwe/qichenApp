<?php
namespace Mobile\Controller;
use Mobile\Controller\MobileController;
class MembersController extends MobileController {
    
    public function _initialize() {
        parent::_initialize();
        //访问者控制
        
        if (!$this->visitor->is_login) {
            if (in_array(ACTION_NAME, array('index'))) {
                IS_AJAX && $this->ajaxReturn(0, L('login_please'), '', 1);

                if(!$this->openid){
                    $this->redirect('members/login');
                }else{
                    $this->redirect('members/apilogin_binding');
                }
            }
        } else {
            $urls = array('1' => 'company/index', '2' => 'personal/index');
            !IS_AJAX && !in_array(ACTION_NAME, array('logout', 'choose_members', 'switch_utype','mobile_auth')) && $this->redirect($urls[C('visitor.utype')], array('uid' => $this->visitor->info['uid']));
        }

    }
  
  	

    /**
     * [login 用户登录]
     */
    public function login() {
        if (IS_POST) {
            $expire = I('post.expire', 1, 'intval');
            $index_login = I('post.index_login', 0, 'intval');
            if (C('qscms_mobile_captcha_open') == 1 && (C('qscms_wap_captcha_config.user_login') == 0 || (session('?error_login_count') && session('error_login_count') >= C('qscms_wap_captcha_config.user_login')))) {
                if (true !== $reg = \Common\qscmslib\captcha::verify('mobile')) $this->ajaxReturn(0, $reg);
            }
            $passport = $this->_user_server();
            if ($mobile = I('post.mobile', '', 'trim')) {//手机验证码登录
                !fieldRegex($mobile, 'mobile') && $this->ajaxReturn(0, '手机号格式错误！');
                $smsVerify = session('login_smsVerify');
                if(true !== $tip = verify_mobile($mobile,$smsVerify,I('post.mobile_vcode', 0, 'intval'))) $this->ajaxReturn(0, $tip);
                $user = M('Members')->where(array('mobile'=>$smsVerify['mobile']))->find();
                if($user){
                    $uid = $user['uid'];
                    if ($user['utype'] == 1 && !$user['sitegroup_uid']) {
                        $company = M('CompanyProfile')->field('companyname,contact,landline_tel')->where(array('uid' => $user['uid']))->find();
                        $company && $user = array_merge($user, $company);
                    }
                    if (!$user['sitegroup_uid'] && $passport->is_sitegroup()) {
                        $temp = $passport->uc('sitegroup')->register($user);
                        $temp && M('Members')->where(array('uid' => $user['uid']))->setfield('sitegroup_uid', $temp['sitegroup_uid']);
                    }
                    session('login_smsVerify', null);
                } elseif ($passport->is_sitegroup() && false !== $sitegroup_user = $passport->uc('sitegroup')->get($smsVerify['mobile'], 'mobile')) {
                    $this->_sitegroup_register($sitegroup_user, 'mobile');
                } else {
                    if (C('qscms_mobile_closereg')) $this->ajaxReturn(0, '网站暂停会员注册，请稍后再试！');
                    $data['mobile'] = $mobile;
                    $data['utype'] = I('utype',0,'intval');
                    $data['p_id'] = I('p_id',0,'intval');
                    if (false === $user = $passport->register($data)) {
                        $this->ajaxReturn(0, $passport->get_error());
                    }
                    $uid = $user['uid'];
                    $sendSms['tpl']='set_register_resume';
                    $sendSms['data']=array('username'=>$user['username'].'','password'=>$user['password']);
                    $sendSms['mobile']=$user['mobile'];
                    if(true !== $reg = D('Sms')->sendSms('captcha',$sendSms)) $this->ajaxReturn(0,$reg);
                    
                    session('reg_smsVerify', null);
                    D('Members')->user_register($user);//积分、套餐、分配客服等初始化操作
                    $points_rule = D('Task')->get_task_cache($user['utype'], 'reg');
                    $urls = array('0' => U('members/choose_members'),'1' => U('company/index'),'2' => U('personal/resume_add', array('points' => $points_rule['points'], 'first' => 1)));
                    $login_url = $urls[$user['utype']];
                }
            }else{
                $username = I('post.username', '', 'trim');
                $password = I('post.password', '', 'trim');
                if (false === $uid = $passport->uc('default')->auth($username, $password)) {
                    $err = $passport->get_error();
                    if ($err == L('auth_null')) {
                        if ($passport->is_sitegroup()) {
                            if (false === $passport->uc('sitegroup')->auth($username, $password)) {
                                $err = $passport->get_error();
                            } else {
                                $this->_sitegroup_register($passport->_user);
                            }
                        }
                    }
                } else {
                    $user = $passport->_user;
                    if ($user['utype'] == 1 && (!$user['sitegroup_uid'])) {
                        $company = M('CompanyProfile')->field('companyname,contact,landline_tel')->where(array('uid' => $user['uid']))->find();
                        $company && $user = array_merge($user, $company);
                    }
                    if (!$user['sitegroup_uid'] && $passport->is_sitegroup()) {
                        $temp = $passport->uc('sitegroup')->register($user);
                        $temp && M('Members')->where(array('uid' => $user['uid']))->setfield('sitegroup_uid', $temp['sitegroup_uid']);
                    }
                }
            }
            if($uid){
                if(false === $this->visitor->login($uid, $expire)) $this->ajaxReturn(0,$this->visitor->getError());
                //第三方帐号注册
                if ('bind' == I('post.org', '', 'trim') && cookie('members_bind_info')) {
                    $user_bind_info = object_to_array(cookie('members_bind_info'));
                    if ($user_bind_info['type'] == 'weixin') {
                        $openid = $user_bind_info['openid'] ?:$this->openid;
                        $reg = \Common\qscmslib\weixin::bind($openid, $user);
                        if (!$reg['state']) $this->ajaxReturn(0,$reg['tip']);
                        if (!$this->visitor->get('avatars') && $user_bind_info['keyavatar_big']) $reg = M('Members')->where(array('uid' => $uid))->setfield('avatars', $user_bind_info['keyavatar_big']);//临时头像转换
                    } else {
                        $oauth = new \Common\qscmslib\oauth($user_bind_info['type']);
                        $bind_user = $oauth->_checkBind($user_bind_info['type'], $user_bind_info);
                        if ($bind_user['uid'] && $bind_user['uid'] != $uid) $this->ajaxReturn(0,'此帐号已经绑定过本站！');
                        $user_bind_info['uid'] = $uid;
                        if (false === $oauth->bindUser($user_bind_info)) $this->ajaxReturn(0,'帐号绑定失败，请重新操作！');
                        if (!$this->visitor->get('avatars') && $user_bind_info['temp_avatar']) $this->_save_avatar($user_bind_info['temp_avatar'], $uid);//临时头像转换
                    }
                    cookie('members_bind_info', NULL);//清理绑定COOKIE
                }elseif($this->is_weixin && $this->openid){//微信中打开绑定
                    $reg = \Common\qscmslib\weixin::bind($this->openid, $user);
                    if (!$reg['state']) $this->ajaxReturn(0,$reg['tip']);
                    if (!$this->visitor->get('avatars') && $reg['data']['keyavatar_big']) $reg = M('Members')->where(array('uid' => $uid))->setfield('avatars', $reg['data']['keyavatar_big']);//临时头像转换
                }
                if(!$login_url && !$login_url = cookie('return_url')){
                    $urls = array('1'=>'company/index','2'=>'personal/index');
                    $login_url = U($urls[$this->visitor->info['utype']],array('uid'=>$this->visitor->info['uid']));
                }else{
                    cookie('return_url',null);
                }
                //同步登陆
                $passport->synlogin($uid,$expire);
                $this->ajaxReturn(1,'登录成功！',$login_url);
            }
            //记录登录错误次数
            if(C('qscms_mobile_captcha_open')==1){
                if(C('qscms_wap_captcha_config.user_login')>0){
                    $error_login_count = session('?error_login_count')?(session('error_login_count')+1):1;
                    session('error_login_count',$error_login_count);
                    if(session('error_login_count')>=C('qscms_wap_captcha_config.user_login')){
                        $verify_userlogin = 1;
                    }else{
                        $verify_userlogin = 0;
                    }
                }else{
                    $verify_userlogin = 1;
                }
            }else{
                $verify_userlogin = 0;
            }
            $this->ajaxReturn(0,$err,$verify_userlogin);
        } else {
            //来路
            $return_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : __APP__;
            if (!IS_AJAX && !strpos(strtolower($return_url), 'login')) {
                $s = true;
                cookie('return_url', $return_url);
            }
            if ($this->visitor->is_login) {
                cookie('return_url', null);
                $urls = array('1' => 'company/index', '2' => 'personal/index');
                $url = $s ? $return_url : U($urls[C('visitor.utype')], array('uid' => $this->visitor->info['uid']));
                $this->redirect($url);
            }
            if(false === $oauth_list = F('oauth_list')){
                $oauth_list = D('Oauth')->oauth_cache();
            }
            $this->assign('oauth_list',$oauth_list);
            $this->assign('verify_userlogin', $this->check_captcha_open(C('qscms_wap_captcha_config.user_login'), 'error_login_count'));
            $tpl = I('get.type','login','trim');
            $this->display($tpl);
        }
    }
    public function register(){
        if(false === $oauth_list = F('oauth_list')){
            $oauth_list = D('Oauth')->oauth_cache();
        }
        $this->assign('oauth_list',$oauth_list);
        $page_seo['title']='会员注册';
        $this->assign('page_seo',$page_seo);
        $this->display();
    }

    public function apilogin_binding_select(){
        $utype = I('get.utype');
//        dump($utype);die;
        $this->assign('utype',$utype);
        $this->_bind_info();
        $this->assign('p_id',$_GET['p_id']);
        $this->_config_seo(array('title' => '帐号绑定', 'header_title' => '帐号绑定'));
        $this->display();
    }

    /**
     * [选择登录身份]
     */
    public function choose_members() {
        $this->display();
    }

    // 注册发送短信/找回密码 短信
    public function reg_send_sms() {
        if (C('qscms_mobile_captcha_open') && C('qscms_wap_captcha_config.varify_mobile') && true !== $reg = \Common\qscmslib\captcha::verify('mobile')) $this->ajaxReturn(0, $reg);
        if ($uid = I('post.uid', 0, 'intval')) {
            $mobile = M('Members')->where(array('uid' => $uid))->getfield('mobile');
            !$mobile && $this->ajaxReturn(0, '用户不存在！');
        } else {
            $mobile = I('post.mobile', '', 'trim');
            !$mobile && $this->ajaxReturn(0, '请填手机号码！');
        }
        if (!fieldRegex($mobile, 'mobile')) $this->ajaxReturn(0, '手机号错误！');
        $sms_type = I('post.sms_type', 'reg', 'trim');
        $rand = getmobilecode();
        switch ($sms_type) {
            case 'reg':
                $sendSms['tpl'] = 'set_register';
                $sendSms['data'] = array('rand' => $rand . '', 'sitename' => C('qscms_site_name'));
                break;
            case 'gsou_reg':
                $sendSms['tpl'] = 'set_register';
                $sendSms['data'] = array('rand' => $rand . '', 'sitename' => C('qscms_site_name'));
                break;
            case 'getpass':
                $sendSms['tpl'] = 'set_retrieve_password';
                $sendSms['data'] = array('rand' => $rand . '', 'sitename' => C('qscms_site_name'));
                break;
            case 'login':
                if (!$uid = M('Members')->where(array('mobile' => $mobile))->getfield('uid')) {
                    $passport = $this->_user_server();
                    if (false === $sitegroup_user = $passport->uc('sitegroup')->get($smsVerify['mobile'], 'mobile')) {
                        $this->ajaxReturn(0, '您输入的手机号未注册会员');
                    }
                }
                $sendSms['tpl'] = 'set_login';
                $sendSms['data'] = array('rand' => $rand . '', 'sitename' => C('qscms_site_name'));
                break;
        }
        $smsVerify = session($sms_type . '_smsVerify');
        if ($smsVerify && $smsVerify['mobile'] == $mobile && time() < $smsVerify['time'] + 60) $this->ajaxReturn(0, '60秒内仅能获取一次短信验证码,请稍后重试');
        $sendSms['mobile'] = $mobile;
        if (true === $reg = D('Sms')->sendSms('captcha', $sendSms)) {
            session($sms_type . '_smsVerify', array('rand' => substr(md5($rand), 8, 16), 'time' => time(), 'mobile' => $mobile));
            session('_verify_num_check',null);
            $this->ajaxReturn(1, '验证码发送成功！');
        } else {
            $this->ajaxReturn(0, $reg);
        }
    }

    /**
     * 用户退出
     */
    public function logout() {
        $this->visitor->logout();
        //同步退出
        $passport = $this->_user_server();
        $synlogout = $passport->synlogout();
        $this->redirect('members/login');
    }
    /**
     * 检测用户信息是否存在或合法
     */
    public function ajax_check() {
        $type = I('post.type', 'trim', 'email');
        $param = I('post.param', '', 'trim');
        if (in_array($type, array('username', 'mobile', 'email'))) {
            $type != 'username' && !fieldRegex($param, $type) && $this->ajaxReturn(0, L($type) . '格式错误！');
            $where[$type] = $param;
            $reg = M('Members')->field('uid,status')->where($where)->find();
            if ($reg['uid'] && $reg['status'] != 0) {
                $this->ajaxReturn(0, L($type) . '已经注册');
            } else {
                $passport = $this->_user_server();
                $name = 'check_' . $type;
                if (false === $passport->$name($param)) {
                    $this->ajaxReturn(0, $passport->get_error());
                }
            }
            $this->ajaxReturn(1);
        } elseif ($type == 'companyname') {
            if (C('qscms_company_repeat') == 0) {
                $reg = M('CompanyProfile')->where(array('companyname' => $param))->getfield('id');
                $reg ? $this->ajaxReturn(0, '企业名称已经注册') : $this->ajaxReturn(1);
            } else {
                $this->ajaxReturn(1);
            }
        }
    }

    /**
     * [save_username 修改帐户名]
     */
    public function save_username() {
        if (IS_POST) {
            $user['username'] = I('post.username', '', 'trim,badword');
            $passport = $this->_user_server();
            if (false === $uid = $passport->edit(C('visitor.uid'), $user)) $this->ajaxReturn(0, $passport->get_error());
            $this->visitor->update();//刷新会话
            $this->ajaxReturn(1, '用户名修改成功！');
        }
    }

    /**
     * [save_password 修改密码]
     */
    public function save_password() {
        if (IS_POST) {
            $oldpassword = I('post.oldpassword', '', 'trim,badword');
            !$oldpassword && $this->ajaxReturn(0, '请输入原始密码!');
            $password = I('post.password', '', 'trim,badword');
            !$password && $this->ajaxReturn(0, '请输入新密码！');
            if ($password != I('post.password1', '', 'trim,badword')) $this->ajaxReturn(0, '两次输入密码不相同，请重新输入！');
            $data['oldpassword'] = $oldpassword;
            $data['password'] = $password;
            $reg = D('Members')->save_password($data, C('visitor'));
            !$reg['state'] && $this->ajaxReturn(0, $reg['error']);
            $this->ajaxReturn(1, '密码修改成功！');
        }
    }
    public function mobile_auth(){
        $this->_config_seo(array('title' => '手机认证', 'header_title' => '手机认证'));
        $this->display();
    }
    /**
     * [send_mobile_code 发送手机验证码]
     */
    public function send_mobile_code() {
        $mobile = I('post.mobile', '', 'trim,badword');
        if (!fieldRegex($mobile, 'mobile')) $this->ajaxReturn(0, '手机格式错误!');
        $user = M('Members')->field('uid,mobile')->where(array('mobile' => $mobile))->find();
        $user['uid'] && $user['uid'] <> C('visitor.uid') && $this->ajaxReturn(0, '手机号已经存在,请填写其他手机号!');
        if ($user['mobile'] && $user['mobile'] == $mobile) $this->ajaxReturn(0, "你的手机号 {$mobile} 已经通过验证！");
        if (session('verify_mobile.time') && (time() - session('verify_mobile.time')) < 60) $this->ajaxReturn(0, '请60秒后再进行验证！');
        $rand = getmobilecode();
        $sendSms = array('mobile' => $mobile, 'tpl' => 'set_mobile_verify', 'data' => array('rand' => $rand . '', 'sitename' => C('qscms_site_name')));
        if (true === $reg = D('Sms')->sendSms('captcha', $sendSms)) {
            session('verify_mobile',array('mobile'=>$mobile,'rand'=>substr(md5($rand), 8,16),'time'=>time()));
            session('_verify_num_check',null);
            $this->ajaxReturn(1, '验证码发送成功！');
        } else {
            $this->ajaxReturn(0, $reg);
        }
    }

    /**
     * [verify_mobile_code 验证手机验证码]
     */
    public function verify_mobile_code() {
        $verify = session('verify_mobile');
        if(true !== $tip = verify_mobile($verify['mobile'],$verify,I('post.verifycode', 0, 'intval'))) $this->ajaxReturn(0, $tip);
        $setsqlarr['mobile'] = $verify['mobile'];
        $uid = C('visitor.uid');
        $user = M('Members')->where(array('uid' => $uid, 'mobile' => $verify['mobile']))->find();
        if ($user) $this->ajaxReturn(0, "你的手机 {$verify['mobile']} 已经通过验证！");
        $passport = $this->_user_server();
        if (false === $passport->edit($uid, $setsqlarr)) $this->ajaxReturn(0, '手机验证失败!');
        D('Members')->update_user_info($setsqlarr, C('visitor'));
        write_members_log(C('visitor'), '', '手机验证通过（手机号：' . $verify['mobile'] . '）');
        session('verify_mobile', null);
        $this->ajaxReturn(1, '手机验证通过!', array('mobile' => $verify['mobile']));
    }

    /**
     * [user_getpass 忘记密码]
     */
    public function user_getpass() {
        if (IS_POST) {
            $type = I('post.type', 0, 'intval');
            $array = array(1 => 'mobile', 2 => 'email');
            if (!$reg = $array[$type]) $this->ajaxReturn(0, '请正确选择找回密码方式！');
            $retrievePassword = session('retrievePassword');
            if ($retrievePassword['token'] != I('post.token', '', 'trim')) $this->ajaxReturn(0, '非法参数！');
            $mobile = I('post.mobile', 0, 'trim');
            if (!$user = M('Members')->field('uid,username')->where(array('mobile' => $mobile))->find()) $this->error('该手机号没有绑定帐号！');
            $smsVerify = session('getpass_smsVerify');
            if ($mobile != $smsVerify['mobile']) $this->ajaxReturn(0, '手机号不一致！');//手机号不一致
            if (time() > $smsVerify['time'] + 600) $this->ajaxReturn(0, '验证码过期！');//验证码过期
            $vcode_sms = I('post.mobile_vcode', 0, 'intval');
            $mobile_rand = substr(md5($vcode_sms), 8, 16);
            if ($mobile_rand != $smsVerify['rand']) $this->ajaxReturn(0, '验证码错误！');//验证码错误！
            session('smsVerify', null);
            $token = substr(md5(getmobilecode()), 8, 16);
            session('retrievePassword', array('uid' => $user['uid'], 'token' => $token));
            $this->ajaxReturn(1, '', array('url' => U('Members/user_setpass', array('token' => $token))));
        }
        $token = substr(md5(getmobilecode()), 8, 16);
        session('retrievePassword', array('uid' => $user['uid'], 'token' => $token));
        $this->assign('token', $token);
        $this->_config_seo(array('title' => '找回密码', 'header_title' => '找回密码'));
        $this->display();
    }

    /**
     * [find_pwd 重置密码]
     */
    public function user_setpass() {
        if (IS_POST) {
            $retrievePassword = session('retrievePassword');
            if ($retrievePassword['token'] != I('post.token', '', 'trim')) $this->ajaxReturn(0, '非法参数！');
            $user['password'] = I('post.password', '', 'trim,badword');
            !$user['password'] && $this->ajaxReturn(0, '请输入新密码！');
            if ($user['password'] != I('post.password1', '', 'trim,badword')) $this->ajaxReturn(0, '两次输入密码不相同，请重新输入！');
            $passport = $this->_user_server();
            if (false === $passport->edit($retrievePassword['uid'], $user)) $this->ajaxReturn(0, $passport->get_error());
            session('retrievePassword', null);
            $this->ajaxReturn(1, '重置密码成功！', array('url' => U('Members/user_setpass_success')));
        } else {
            $key = I('get.key', '', 'trim');
            if ($key) {
                parse_str(decrypt($key, C('PWDHASH')), $data);
                !fieldRegex($data['e'], 'email') && $this->error('找回密码失败,邮箱格式错误！', 'user_getpass');
                $end_time = $data['t'] + 24 * 3600;
                if ($end_time < time()) $this->error('找回密码失败,链接过期!', 'user_getpass');
                $key_str = substr(md5($data['e'] . $data['t']), 8, 16);
                if ($key_str != $data['k']) $this->error('找回密码失败,key错误!', 'user_getpass');
                if (!$uid = M('Members')->where(array('email' => $data['e']))->getfield('uid')) $this->error('找回密码失败,帐号不存在!', 'user_getpass');
                $token = substr(md5(getmobilecode()), 8, 16);
                session('retrievePassword', array('uid' => $uid, 'token' => $token));
                $this->assign('token', $token);
            } else {
                $token = I('get.token', '', 'trim');
                $this->assign('token', $token);
            }
        }
        $this->_config_seo(array('title' => '找回密码', 'header_title' => '找回密码'));
        $this->display($tpl);
    }

    /**
     * [user_retrieve_email description]
     */
    public function user_retrieve_email() {
        $email = I('get.email', '', 'trim');
        $this->assign('email', $email);
        $this->_config_seo(array('title' => '找回密码', 'header_title' => '找回密码'));
        $this->display();
    }

    /**
     * 重置密码成功
     */
    public function user_setpass_success() {
        $this->_config_seo(array('title' => '找回密码', 'header_title' => '找回密码'));
        $this->display();
    }

    /**
     * 账号申诉
     */
    public function appeal_user() {
        $mod = D('MembersAppeal');
        if (IS_POST && IS_AJAX) {
            if (false === $data = $mod->create()) {
                $this->ajaxReturn(0, $mod->getError());
            }
            if (false !== $mod->add($data)) {
                $this->ajaxReturn(1, L('operation_success'));
            } else {
                $this->ajaxReturn(0, L('operation_failure'));
            }
        }
        $this->_config_seo(array('title' => '账号申诉', 'header_title' => '账号申诉'));
        $this->display();
    }

    /**
     * [sign_in 签到]
     */
    public function sign_in() { 
        if (IS_AJAX) {
            $reg = D('Members')->sign_in(C('visitor'));
            if ($reg['state']) {
                write_members_log(C('visitor'), '', '成功签到');
                $this->ajaxReturn(1, '成功签到！', $reg['points']);
            } else {
                $this->ajaxReturn(0, $reg['error']);
            }
        }

        
    }

    /**
     * [binding 第三方绑定]
     */
    public function apilogin_binding() { 
        $this->_bind_info();
        $utype = I('get.utype');
        $this->assign('utype',$utype);
        if(empty($utype)){
            $this->display('apilogin_binding_select');die;
        }
        $this->assign('p_id',$_GET['p_id']);
        $this->_config_seo(array('title' => '帐号绑定', 'header_title' => '帐号绑定'));
        $this->display();
    }
    protected function _bind_info() {
        if ('qianfanyunapp' == I('get.qianfan', '', 'trim')) {
            $user_bind_info = object_to_array(cookie('members_bind_info'));
            if (!$this->visitor->is_login && !$user_bind_info) $this->redirect('index/index');
        } else if ('magappx' == I('get.type', '', 'trim')) {
            $user_bind_info = object_to_array(cookie('members_bind_info'));
            if (!$this->visitor->is_login && !$user_bind_info) $this->redirect('index/index');
        } else if ($this->is_weixin && $this->openid) {
            $reg = \Common\qscmslib\weixin::get_user_info($this->openid);
            $user_bind_info = $reg['data'];
        } else {
            $user_bind_info = object_to_array(cookie('members_bind_info'));
            if (!$this->visitor->is_login && !$user_bind_info) $this->redirect('members/login');
        }
        if (false === $oauth_list = F('oauth_list')) {
            $oauth_list = D('Oauth')->oauth_cache();
        }
        if (C('apply.Magappx')) {
            $oauth_list['magappx']['name'] = C('qscms_magappx_app_name') ?: '马甲APP';
        }
        if (C('apply.Qianfanyunapp')) {
            $oauth_list['qianfanyunapp']['name'] = C('qscms_qianfanyunapp_name') ?: '千帆APP';
        }
        $this->assign('third_name', $oauth_list[$user_bind_info['type']]['name']);
        $this->assign('user_bind_info', $user_bind_info);
    }

    /**
     * [_save_avatar 第三方头像保存]
     */
    protected function _save_avatar($avatar, $uid) {
        $path = C('qscms_attach_path') . 'avatar/temp/' . $avatar;
        $image = new \Common\ORG\ThinkImage();
        $date = date('ym/d/');
        $save_avatar = C('qscms_attach_path') . 'avatar/' . $date;//图片存储路径
        if (!is_dir($save_avatar)) mkdir($save_avatar, 0777, true);
        $savePicName = md5($uid . time()) . ".jpg";
        $filename = $save_avatar . $savePicName;
        $size = explode(',', C('qscms_avatar_size'));
        copy($path, $filename);
        foreach ($size as $val) {
            $image->open($path)->thumb($val, $val, 3)->save("{$filename}._{$val}x{$val}.jpg");
        }
        M('Members')->where(array('uid' => $uid))->setfield('avatars', $date . $savePicName);
        @unlink($path);
    }

    /**
     * [get_weixin 获取微信]
     */
    public function get_weixin() {
        if (!C('qscms_weixin_apiopen') || !C('qscms_weixin_mpname')) $this->ajaxReturn(0, '未配置微信参数！');
        $this->ajaxReturn(1, '请微信关注  ' . C('qscms_weixin_mpname') . ' 公众号进行账号绑定');
    }

    /**
     * 切换会员类型
     */
    public function switch_utype() {
        //访问者控制
        IS_AJAX && $this->ajaxReturn(0,'管理员不允许进行切换操作');
        $this->error('管理员不允许进行切换操作');
        if (!$this->visitor->is_login) {
            $this->redirect('members/login');
        }
        $utype = I('request.utype', 0, 'intval');
        if(!in_array($utype, array(1, 2))){
            IS_AJAX && $this->ajaxReturn(0,'会员类型选择错误！');
            $this->error('会员类型选择错误！');
        }
        // 防止旧帐号（兼职等模块转入members表中的用户）没有进行注册后的初始化
        if (null === D('MembersSetmeal')->get_user_setmeal($this->visitor->info['uid'])) {
            D('Members')->user_register($this->visitor->info);//积分、套餐、分配客服等初始化操作
        }
        if (false !== D('Members')->where(array('uid' => C('visitor.uid')))->setField('utype', $utype)) {
            $this->visitor->update();
            $url_arr = array('1' => 'company/index', '2' => 'personal/index');
            IS_AJAX && $this->ajaxReturn(1,'会员类型切换成功！',U($url_arr[$this->visitor->info['utype']], array('uid' => $this->visitor->info['uid'])));
            $this->redirect($url_arr[$this->visitor->info['utype']], array('uid' => $this->visitor->info['uid']));
        }
    }
}

?>