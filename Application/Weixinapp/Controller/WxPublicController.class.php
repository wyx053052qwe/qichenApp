<?php
namespace Weixinapp\Controller;

use Common\Controller\FrontendController;

class WxPublicController extends FrontendController
{
    public function _initialize()
    {
        parent::_initialize();
        $secretKey = C('qscms_weixinapp_secretkey');
        $secret    = I('request.secretKey', "", "trim");
        if ($secret != $secretKey) {
            $this->ajaxReturn(2, '秘钥错误！', $list);
        }
    }
    public function login()
    {
        $expire = I('get.expire', 1, 'intval');
        $mobile = I('get.mobile', '', 'trim');
        !$mobile && $this->ajaxReturn(0, '请输入手机号');
        !fieldRegex($mobile, 'mobile') && $this->ajaxReturn(0, '手机号格式错误！');
        $gender   = I('get.gender');
        $nickName = I('get.nickName');
        $nickName = preg_replace_callback('/./u', function (array $match) {
            return strlen($match[0]) >= 4 ? '' : $match[0];
        }, $nickName);
        $avatarUrl = I('get.avatarUrl');
        $smsVerify     = I('get.md5Code', '', 'trim');
        $verfiy_mobile = I('get.verfiy_mobile', '', 'trim');
        $verfiy_time   = I('get.verfiy_time', '', 'trim');
        !$smsVerify && $this->ajaxReturn(0, '验证码错误！'); //验证码错误！
        if ($mobile != $verfiy_mobile) {
            $this->ajaxReturn(0, '手机号不一致！');
        }
//手机号不一致
        if (time() > $verfiy_time + 600) {
            $this->ajaxReturn(0, '验证码过期！');
        }
//验证码过期
        $vcode_sms   = I('get.mobile_vcode');
        $mobile_rand = substr(md5($vcode_sms), 8, 16);
        if ($mobile_rand != $smsVerify) {
            $this->ajaxReturn(0, '验证码匹配错误！');
        }
//验证码错误！
        $openid   = I('get.openid', '', 'trim');
        $passport = $this->_user_server();
        $user     = M('Members')->where(array('mobile' => $mobile))->find();
        if ($user) {
            M('Members')->where(array('uid' => $user['uid']))->setField('utype',2);
            $user['utype'] = 2;
            if ($user['utype'] == 1) {
                // $this->ajaxReturn(2, '检测到你的当前角色是企业，确定切换到求职者吗？');
                if (null === D('MembersSetmeal')->get_user_setmeal($user['uid'])) {
                    D('Members')->user_register($user); //积分、套餐、分配客服等初始化操作
                }
            } elseif ($user['utype'] == 2) {
                $already_bind = M("MembersBind")->where(array('keyid' => $openid, 'type' => 'wxapp'))->find();
                if (!$already_bind) {
                    $user_bind_info['uid']         = $user['uid'];
                    $user_bind_info['type']        = 'wxapp';
                    $user_bind_info['keyid']       = $openid;
                    $user_bind_info['info']        = serialize(array('keyid' => $openid, 'keyname' => $nickName,'avatarUrl'=>$avatarUrl));
                    $user_bind_info['bindingtime'] = time();
                    M("MembersBind")->add($user_bind_info);
                }
            }
            $uid = $user['uid'];
            if ($user['utype'] == 1 && !$user['sitegroup_uid']) {
                $company = M('CompanyProfile')->field('companyname,contact,landline_tel')->where(array('uid' => $user['uid']))->find();
                $user    = array_merge($user, $company);
            }
            if (!$user['sitegroup_uid'] && $passport->is_sitegroup()) {
                $temp = $passport->uc('sitegroup')->register($user);
                $temp && M('Members')->where(array('uid' => $user['uid']))->setfield('sitegroup_uid', $temp['sitegroup_uid']);
            }
        } elseif ($passport->is_sitegroup() && false !== $sitegroup_user = $passport->uc('sitegroup')->get($verfiy_mobile, 'mobile')) {
            if ($sitegroup_user['utype'] == 1) {
                if (null === D('MembersSetmeal')->get_user_setmeal($user['uid'])) {
                    D('Members')->user_register($user); //积分、套餐、分配客服等初始化操作
                }
            } else {
                if ($user = $this->_sitegroup_register($sitegroup_user, 'mobile')) {
                    if ($user['utype'] == 2) {
                        $already_bind = M("MembersBind")->where(array('keyid' => $openid, 'type' => 'wxapp'))->find();
                        if (!$already_bind) {
                            $user_bind_info['uid']         = $user['uid'];
                            $user_bind_info['type']        = 'wxapp';
                            $user_bind_info['keyid']       = $openid;
                            $user_bind_info['info']        = serialize(array('keyid' => $openid, 'keyname' => $nickName));
                            $user_bind_info['bindingtime'] = time();
                            M("MembersBind")->add($user_bind_info);
                        }
                    }
                }
            }
        } else {
            if (C('qscms_mobile_closereg')) {
                $this->ajaxReturn(0, '网站暂停会员注册，请稍后再试！');
            }

            $data['mobile'] = $mobile;
            $data['utype']  = I('utype', 2, 'intval');
            if (false === $data = $passport->register($data)) {
                $this->ajaxReturn(0, $passport->get_error());
            }
            $uid = $data['uid'];

            $sendSms['tpl']    = 'set_register_resume';
            $sendSms['data']   = array('username' => $data['username'] . '', 'password' => $data['password']);
            $sendSms['mobile'] = $data['mobile'];
            if (true !== $reg = D('Sms')->sendSms('captcha', $sendSms)) {
                $this->ajaxReturn(0, $reg);
            }

            D('Members')->user_register($data); //积分、套餐、分配客服等初始化操作
            $already_bind = M("MembersBind")->where(array('keyid' => $openid, 'type' => 'wxapp'))->find();
            if (!$already_bind) {
                $user_bind_info['uid']         = $uid;
                $user_bind_info['type']        = 'wxapp';
                $user_bind_info['keyid']       = $openid;
                $user_bind_info['info']        = serialize(array('keyid' => $openid, 'keyname' => $nickName));
                $user_bind_info['bindingtime'] = time();
                M("MembersBind")->add($user_bind_info);
            }
            $points_rule = D('Task')->get_task_cache($data['utype'], 'reg');
        }
        if ($uid) {
            if (false === $this->visitor->login($uid, $expire)) {
                $this->ajaxReturn(0, $this->visitor->getError());
            }

            //同步登陆
            $passport->synlogin($uid, $expire);
            $this->ajaxReturn(1, '登录成功！', $result['openid']);
        }
    }
    /**
     * [_sitegroup_register 站群用户注册本地]
     */
    protected function _sitegroup_register($sitegroup_user, $reg_type)
    {
        $passport = $this->_user_server();
        if ($sitegroup_user['reg_type'] == 1 && !$sitegroup_user['email']) {
            unset($sitegroup_user['email']);
        }

        if (false === $sitegroup_user = $passport->uc('default')->register($sitegroup_user)) {
            if ($user = $passport->get_status()) {
                $this->ajaxReturn(2, '检测到您的手机已绑定用户，需要解绑当前手机么');
            }

            $this->ajaxReturn(0, $passport->get_error());
        }
        if ($audit && $passport->is_sitegroup()) {
            $passport->uc('sitegroup')->edit($sitegroup_user['uid'], array('_status' => 1));
        }
        D('Members')->user_register($sitegroup_user);
        $points_rule = D('Task')->get_task_cache($sitegroup_user['utype'], 'reg');
        return $sitegroup_user;
    }

    // 注册发送短信/找回密码 短信
    public function reg_send_sms()
    {
        // if(C('qscms_mobile_captcha_open') && C('qscms_wap_captcha_config.varify_mobile') && true !== $reg = \Common\qscmslib\captcha::verify('mobile')) $this->ajaxReturn(0,$reg.'1');
        if ($uid = I('get.uid', 0, 'intval')) {
            $mobile = M('Members')->where(array('uid' => $uid))->getfield('mobile');
            !$mobile && $this->ajaxReturn(0, '用户不存在！');
        } else {
            $mobile = I('get.mobile', '', 'trim');
            !$mobile && $this->ajaxReturn(0, '请填手机号码！');
        }
        if (!fieldRegex($mobile, 'mobile')) {
            $this->ajaxReturn(0, '手机号错误！');
        }

        $rand            = getmobilecode();
        $sendSms['tpl']  = 'set_register';
        $sendSms['data'] = array('rand' => $rand . '', 'sitename' => C('qscms_site_name'));
        $smsVerify       = session('reg_smsVerify');
        if ($smsVerify && $smsVerify['mobile'] == $mobile && time() < $smsVerify['time'] + 60) {
            $this->ajaxReturn(0, '60秒内仅能获取一次短信验证码,请稍后重试');
        }

        $sendSms['mobile'] = $mobile;
        if (true === $reg = D('Sms')->sendSms('captcha', $sendSms)) {
            session('reg_smsVerify', array('rand' => substr(md5($rand), 8, 16), 'time' => time(), 'mobile' => $mobile));
            $this->ajaxReturn(1, '手机验证码发送成功！', session('reg_smsVerify'));
        } else {
            $this->ajaxReturn(0, $reg);
        }
    }

    /*
    获取网站配置
     */
    public function web_cfg()
    {
        $list = C();
        foreach ($list as $key => $val) {
            if (strpos($key, 'QSCMS_') !== false) {
                $arr[strtolower($key)] = $val;
            }
        }
        $arr['backgroundColor'] = C('qscms_weixinapp_top_color') ? C('qscms_weixinapp_top_color') : "#0180CF";
        $arr['fontColor']       = "#ffffff";
        $arr['recommend']       = C('qscms_weixinapp_index_login_recommend'); //千人千面
        $arr['index_jobstype']  = C('qscms_weixinapp_index_jobs_type'); //new,nearby,allowance
        $arr['logo_home_url'] = attach(C('qscms_logo_home'), 'resource');
        $this->ajaxReturn(1, '获取数据成功', $arr);
    }

    /**
     * 获取导航
     */
    public function get_index_nav()
    {
        $list = D('NavigationWeixinapp')->get_nav();
        foreach ($list as $key => $value) {
            $list[$key]['nav_img'] = rtrim(C('qscms_site_dir'), '/') . ($value['nav_img'] ? attach($value['nav_img'], 'images') : attach($value['alias'] . '.png', 'resource/weixinapp_nav'));
        }
        $this->ajaxReturn(1, 'success', $list);
    }

    /**
     * 首页广告
     */
    public function get_index_ads()
    {
        $index_focus_where['starttime']  = array(array('elt', time()), array('eq', 0), 'or');
        $index_focus_where['deadline']   = array(array('gt', time()), array('eq', 0), 'or');
        $index_focus_where['is_display'] = 1;
        $index_focus_where['alias']      = 'QS_weixinapp_indexfocus';
        $list                            = D('AdWeixinapp')->get_ad_list($index_focus_where);
        foreach ($list as $key => $value) {
            $list[$key]['content'] = attach($value['content'], 'attach_img');
        }
        $this->ajaxReturn(1, 'success', $list);
    }

    /*
    首页热门职位
     */
    public function index_hotword()
    {
        $where['显示数目'] = 12;
        $class                 = new \Common\qscmstag\hotwordTag($where);
        $list                  = $class->run();
        $this->ajaxReturn(1, '获取数据成功', $list);
    }

    /*
    首页公告
     */
    public function index_notice()
    {
        $where['显示数目'] = 10;
        $where['分类']       = 1;
        $where['排序']       = 'addtime:desc';
        $class                 = new \Common\qscmstag\notice_listTag(array('显示数目' => 10, '分类' => 1, '排序' => 'addtime:desc'));
        $list                  = $class->run();
        $this->ajaxReturn(1, '获取数据成功', $list);
    }

    /*
    首页职位推荐
     */
    public function index_jobslist()
    {
        $type                  = I('get.jobstype', 'new', 'trim'); // new：最新职位 ；//nearby：附近的职位；//allowance：红包职位
        $where['显示数目'] = 4;
        $where['分页显示'] = 1;
        if ($type == 'new') {
            $where['排序'] = "rtime";
            $title           = '最新职位';
        } elseif ($type == 'nearby') {
            $title                 = '附近的职位';
            $longitude       = I('get.longitude', '', 'trim');
            $latitude      = I('get.latitude', '', 'trim');
            $location_data = change_coords(array($longitude.','.$latitude),3,5);
            $where['经度']       = $location_data['data'][0]['x'];
            $where['纬度']       = $location_data['data'][0]['y'];
            $where['搜索范围'] = 3;
            //经度="$_GET['lng']" 纬度="$_GET['lat']"
        } elseif ($type == 'allowance') {
            $where['排序']       = "rtime";
            $where['搜索内容'] = "allowance";
            $title                 = '红包职位';
        }
        $class         = new \Common\qscmstag\jobs_listTag($where);
        $list          = $class->run();
        $list['title'] = $title;
        $this->ajaxReturn(1, '获取数据成功', $list);
    }

    public function index_jobscategory()
    {
        $class = new \Common\qscmstag\classifyTag(array('类型' => 'QS_jobs'));
        $list  = $class->run();
        $this->ajaxReturn(1, '获取数据成功', $list);
    }

    public function index_major()
    {
        $major_arr = M('category_major')->where(' parentid > 0 ')->select();
        $this->ajaxReturn(1, '获取数据成功', $major_arr);
    }

    public function index_jobcategory_info()
    {
        $class = new \Common\qscmstag\classifyTag(array('类型' => 'QS_jobs_info'));
        $list  = $class->run();
        $this->ajaxReturn(1, '获取数据成功', $list);

    }

    public function other_category()
    {
        $type  = I('get.type', '', 'trim');
        $class = new \Common\qscmstag\classifyTag(array('类型' => $type));
        $list  = $class->run();
        $this->ajaxReturn(1, '获取数据成功', $list);
    }

    public function city_category()
    {
        $district  = I('get.district', '', 'trim');
        $sdistrict = I('get.sdistrict', '', 'trim');
        if (intval($district) > 0) {
            $citys = M('category_district')->where(' parentid = ' . $district)->select();
        } elseif (intval($sdistrict) > 0) {
            $citys = M('category_district')->where(' parentid = ' . $sdistrict)->select();
        } else {
            $citys = M('category_district')->where(' parentid = 0 ')->select();
        }
        $this->ajaxReturn(1, '获取数据成功', $citys);

    }

    public function jobslist()
    {
        $keyword       = I('get.key', '', 'trim');
        $range     = I('get.range', 0, 'intval');
        $allowance = I('get.allowance', 0, 'intval');
        if (!empty($keyword)) {
            $where['关键字'] = $keyword;
        }
        if ($range > 0) {
            $where['搜索范围'] = $range;
            $longitude       = I('get.longitude', '', 'trim');
            $latitude      = I('get.latitude', '', 'trim');
            // $where['经度']       = I('get.longitude', '', 'trim');
            // $where['纬度']       = I('get.latitude', '', 'trim');
            $location_data = change_coords(array($longitude.','.$latitude),3,5);
            $where['经度']       = $location_data['data'][0]['x'];
            $where['纬度']       = $location_data['data'][0]['y'];
        }
        if ($allowance == 1) {
            $where['排序']       = "rtime";
            $where['搜索内容'] = "allowance";
        }
        $where['描述长度'] = '100';
        $where['显示数目'] = 4;
        $where['分页显示'] = 1;
        $class                 = new \Common\qscmstag\jobs_listTag($where);
        $list                  = $class->run();
        foreach ($list['list'] as $key => $val) {
            $list['list'][$key]['refreshtime']    = date('Y-m-d', $val['refreshtime']);
            $list['list'][$key]['refreshtime_cn'] = strip_tags($val['refreshtime_cn']);
        }
        if (!empty($list)) {
            $this->ajaxReturn(1, '获取数据成功', $list);
        } else {
            $this->ajaxReturn(0, '获取数据失败', $list);
        }

    }

    public function jobsshow($id, $uid = 0)
    {
        $info = D('Jobs')->get_jobs_one(array('id' => $id));
        if ($info) {
            if ($uid > 0) {
                $favorites = D('personal_favorites')->where(array('jobs_id' => $id, 'personal_uid' => $uid))->find();
                if (!empty($favorites)) {
                    $info['is_favorites'] = 1;
                }
                $apply_record = D('PersonalJobsApply')->where(array('jobs_id'=>$id,'personal_uid'=>$uid))->find();
                if (!empty($apply_record)) {
                    $info['is_apply'] = 1;
                }
            }
            $profile                         = M('CompanyProfile')->where(array('id' => $info['company_id']))->find();
            $info['company']                 = $profile;
            $setmeal                         = D('MembersSetmeal')->get_user_setmeal($info['uid']);
            $info['show_contact_direct']     = $setmeal['show_contact_direct'];
            $info['company']['setmeal_id']   = $setmeal['setmeal_id'];
            $info['company']['setmeal_name'] = $setmeal['setmeal_name'];
            $info['contact']                 = M('JobsContact')->where(array('pid' => $info['id']))->find();
            $info['expire']                  = sub_day($info['deadline'], time());
            $info['contents']                = htmlspecialchars_decode($info['contents'], ENT_QUOTES);
            $info['refreshtime_cn']          = daterange(time(), $info['refreshtime'], 'Y-m-d', "#FF3300");
            if ($info['company']['logo']) {
                $info['company']['logo'] = attach($info['company']['logo'], 'company_logo');
            } else {
                $info['company']['logo'] = attach('no_logo.png', 'resource');
            }

            if ($info['negotiable'] == 0) {
                if (C('qscms_wage_unit') == 1) {
                    $info['minwage'] = $info['minwage'] % 1000 == 0 ? (($info['minwage'] / 1000) . 'K') : (round($info['minwage'] / 1000, 1) . 'K');
                    $info['maxwage'] = $info['maxwage'] ? ($info['maxwage'] % 1000 == 0 ? (($info['maxwage'] / 1000) . 'K') : (round($info['maxwage'] / 1000, 1) . 'K')) : 0;
                } elseif (C('qscms_wage_unit') == 2) {
                    if ($info['minwage'] >= 10000) {
                        if ($info['minwage'] % 10000 == 0) {
                            $info['minwage'] = ($info['minwage'] / 10000) . '万';
                        } else {
                            $info['minwage'] = round($info['minwage'] / 10000, 1);
                            $info['minwage'] = strpos($info['minwage'], '.') ? str_replace('.', '万', $info['minwage']) : $info['minwage'] . '万';
                        }
                    } else {
                        if ($info['minwage'] % 1000 == 0) {
                            $info['minwage'] = ($info['minwage'] / 1000) . '千';
                        } else {
                            $info['minwage'] = round($info['minwage'] / 1000, 1);
                            $info['minwage'] = strpos($info['minwage'], '.') ? str_replace('.', '千', $info['minwage']) : $info['minwage'] . '千';
                        }
                    }
                    if ($info['maxwage'] >= 10000) {
                        if ($info['maxwage'] % 10000 == 0) {
                            $info['maxwage'] = ($info['maxwage'] / 10000) . '万';
                        } else {
                            $info['maxwage'] = round($info['maxwage'] / 10000, 1);
                            $info['maxwage'] = strpos($info['maxwage'], '.') ? str_replace('.', '万', $info['maxwage']) : $info['maxwage'] . '万';
                        }
                    } elseif ($info['maxwage']) {
                        if ($info['maxwage'] % 1000 == 0) {
                            $info['maxwage'] = ($info['maxwage'] / 1000) . '千';
                        } else {
                            $info['maxwage'] = round($info['maxwage'] / 1000, 1);
                            $info['maxwage'] = strpos($info['maxwage'], '.') ? str_replace('.', '千', $info['maxwage']) : $info['maxwage'] . '千';
                        }
                    } else {
                        $info['maxwage'] = 0;
                    }
                }
                if ($info['maxwage'] == 0) {
                    $info['wage_cn'] = '面议';
                } else {
                    if ($info['minwage'] == $info['maxwage']) {
                        $info['wage_cn'] = $info['minwage'] . '/月';
                    } else {
                        $info['wage_cn'] = $info['minwage'] . '-' . $info['maxwage'] . '/月';
                    }
                }
            } else {
                $info['wage_cn'] = '面议';
            }
            $age = explode('-', $info['age']);
            if (!$age[0] && !$age[1]) {
                $info['age_cn'] = '不限';
            } else {
                $age[0] && $info['age_cn'] = $age[0] . '岁以上';
                $age[1] && $info['age_cn'] = $age[1] . '岁以下';
            }
            if ($info['tag_cn']) {
                $tag_cn         = explode(',', $info['tag_cn']);
                $info['tag_cn'] = $tag_cn;
            } else {
                $info['tag_cn'] = array();
            }
            //简历处理率
            $apply = M('PersonalJobsApply')->where(array('company_uid' => $info['uid'], 'apply_addtime' => array('gt', strtotime("-14day"))))->select();
            foreach ($apply as $key => $v) {
                if ($v['is_reply']) {
                    $reply++;
                    $v['reply_time'] && $reply_time += $v['reply_time'] - $v['apply_addtime'];
                }
            }
            $info['company']['reply_ratio']     = !$apply ? 100 : intval($reply / count($apply) * 100);
            $info['company']['reply_time']      = !$reply_time ? '0天' : sub_day(intval($reply_time / count($apply)), 0);
            $last_login_time                    = M('Members')->where(array('uid' => $info['uid']))->getfield('last_login_time');
            $info['company']['last_login_time'] = $last_login_time ? fdate($last_login_time) : '未登录';
            $info['contact']                    = M('JobsContact')->where(array('pid' => $info['id']))->find();
            $info['refreshtime_cn']             = strip_tags($info['refreshtime_cn']);
            $info['company']['logo']            = $info['company']['logo'];

            /**
             * 5.0新增
             */
            if(C('qscms_apply_jobs_contact')){
                    $apply = D('PersonalJobsApply')->where(array('personal_uid'=>$uid,'company_id'=>$info['company_id']))->find();
                    if($apply){
                        if($info['contact']['telephone']){
                            $info['contact']['telephone'] = $info['contact']['telephone'];
                        }else{
                                $info['contact']['telephone'] = trim($info['contact']['landline_tel'],'-');
                        } 
                    }else{
                        if($info['contact']['telephone']){
                            $info['contact']['telephone'] = contact_hide($info['contact']['telephone']);
                        }else{
                            $info['contact']['telephone'] = contact_hide(trim($info['contact']['landline_tel'],'-'),1);
                        }
                    } 
            }else{
                $hide = true;
                    $showjobcontact = C('qscms_showjobcontact_wap');
                    if($showjobcontact == 0)
                    {
                        $hide = false;
                    }
                    else
                    {
                        if($uid){
                            $resume = D('Resume')->where(array('uid'=>$uid))->find();
                            if($showjobcontact == 1){
                                $hide = false;
                            }elseif($resume && $showjobcontact == 2){
                                $hide = false;
                            }
                        }
                    }
                    if($hide){
                        if($info['contact']['telephone']){
                            $info['contact']['telephone'] = contact_hide($info['contact']['telephone']);
                        }else{
                            $info['contact']['telephone'] = contact_hide(trim($info['contact']['landline_tel'],'-'),1);
                        }
                    }else{
                        if($info['contact']['telephone_show']==0 && $info['contact']['landline_tel_show']==0){
                            $info['contact']['telephone_show'] = 0;
                        }
                        elseif($info['contact']['telephone_show']==1 && $info['contact']['telephone'])
                        {
                            $info['contact']['telephone_show'] = $info['contact']['telephone_show'];
                        }
                        elseif($info['contact']['telephone'] && !trim($info['contact']['landline_tel'],'-') && $info['contact']['telephone_show']==0)
                        {
                            $info['contact']['telephone_show'] = $info['contact']['telephone_show'];
                        }
                        else
                        {
                            $info['contact']['telephone'] = trim($info['contact']['landline_tel'],'-');
                            $info['contact']['telephone_show'] = $info['contact']['landline_tel_show'];
                        }
                    }
            }   
            $info['has_resume'] = $resume?1:0; 
            $info['hide'] = $hide; 
            $info['personal_uid'] = $uid;
            /**
             * 5.0新增end
             */



            if (C('apply.Allowance')) {
                $info['allowance_open'] = 1;
                if ($info['allowance_id'] > 0) {
                    $allowance            = D('Allowance/AllowanceInfo')->find($info['allowance_id']);
                    $allowance['type_cn'] = D('Allowance/AllowanceInfo')->get_alias_cn($allowance['type_alias']);
                    if ($uid && C('visitor.utype') == 2) {
                        $allowance_record = D('Allowance/AllowanceRecord')->where(array('personal_uid' => $uid, 'info_id' => $info['allowance_id']))->find();
                    }
                } else {
                    $allowance = array();
                }
            } else {
                $info['allowance_open'] = 0;
                $info['allowance_id']   = 0;
            }
            $info['allowance']        = $allowance;
            $info['allowance_record'] = $allowance_record;
            $list_class               = new \Common\qscmstag\jobs_listTag(array('显示数目' => 6, '去除id' => $info['id'], '职位分类' => $info['jobcategory']));
            $info['jobslist']         = $list_class->run();
            foreach ($info['jobslist']['list'] as $k => $v) {
                $info['jobslist']['list'][$k]['refreshtime_cn'] = strip_tags($v['refreshtime_cn']);
            }
        }
        $this->ajaxReturn(1, '获取数据成功', $info);
    }
    public function get_jobs_contact($id, $uid = 0){
        if(!$uid){
            $this->ajaxReturn(0,'请先登录');
        }
        $info = D('Jobs')->get_jobs_one(array('id' => $id));
        if(!$info){
            $this->ajaxReturn(0,'没有找到职位信息');
        }
        $info['contact'] = M('JobsContact')->where(array('pid' => $info['id']))->find();
        if(C('qscms_apply_jobs_contact')){
                $apply = D('PersonalJobsApply')->where(array('personal_uid'=>$uid,'company_id'=>$info['company_id']))->find();
                if($apply){
                    if($info['contact']['telephone']){
                        $info['contact']['telephone'] = $info['contact']['telephone'];
                    }else{
                        $info['contact']['telephone'] = trim($info['contact']['landline_tel'],'-');
                    } 
                    $this->ajaxReturn(1,'获取联系电话成功',$info['contact']['telephone']);
                }else{
                    $this->ajaxReturn(0,'请投递简历后再拨打电话');
                } 
        }else{
            $hide = true;
            $showjobcontact = C('qscms_showjobcontact_wap');
            if($showjobcontact == 0)
            {
                $hide = false;
            }
            else
            {
                if($uid){
                    $resume = D('Resume')->where(array('uid'=>$uid))->find();
                    if($showjobcontact == 1){
                        $hide = false;
                    }elseif($resume && $showjobcontact == 2){
                        $hide = false;
                    }
                }
            }
            if($hide){
                $this->ajaxReturn(0,'请先创建一份简历');
            }else{
                if($info['contact']['telephone_show']==0 && $info['contact']['landline_tel_show']==0){
                    $info['contact']['telephone_show'] = 0;
                }
                elseif($info['contact']['telephone_show']==1 && $info['contact']['telephone'])
                {
                    $info['contact']['telephone_show'] = $info['contact']['telephone_show'];
                }
                elseif($info['contact']['telephone'] && !trim($info['contact']['landline_tel'],'-') && $info['contact']['telephone_show']==0)
                {
                    $info['contact']['telephone_show'] = $info['contact']['telephone_show'];
                }
                else
                {
                    $info['contact']['telephone'] = trim($info['contact']['landline_tel'],'-');
                    $info['contact']['telephone_show'] = $info['contact']['landline_tel_show'];
                }
                if($info['contact']['telephone_show']==0){
                    $this->ajaxReturn(0,'企业设置不公开联系方式');
                }else{
                    $this->ajaxReturn(1,'获取联系电话成功',$info['contact']['telephone']);
                }
            }
        }   
    }
    public function jobscontact($id)
    {
        //$id = I('get.id','','intval');
        $jobscontact = D('jobs_contact')->where(array("id" => $id))->find();
        $this->ajaxReturn(1, '获取数据成功', $jobscontact['telephone']);

    }

    public function resumeshow($id)
    {
        $class = new \Common\qscmstag\resume_showTag(array('简历id' => $id));
        $info  = $class->run();
        if ($info) {
            $info['refreshtime_cn'] = strip_tags($info['refreshtime_cn']);
            $list_class             = new \Common\qscmstag\jobs_listTag(array('显示数目' => 6, '去除id' => $info['id'], '职位分类' => $info['jobcategory']));
            $info['jobslist']       = $list_class->run();
            foreach ($info['jobslist']['list'] as $k => $v) {
                $info['jobslist']['list'][$k]['refreshtime_cn'] = strip_tags($v['refreshtime_cn']);
            }
        }
        $this->ajaxReturn(1, '获取数据成功', $info);
    }

    public function companylist()
    {
        $key       = I('get.key', '', 'trim');
        if (!empty($key)) {
            $where['关键字'] = $key;
        }
        $where['显示数目'] = 8;
        $where['分页显示'] = 1;
        $class                 = new \Common\qscmstag\company_listTag($where);
        $list                  = $class->run();
        
        if (!empty($list['list'])) {
            $this->ajaxReturn(1, '获取数据成功', $list);
        } else {
            $this->ajaxReturn(0, '获取数据失败', $list);
        }

    }
    public function companyshow($id,$uid=0)
    {
        //        $class = new \Common\qscmstag\company_showTag(array('企业id'=>$id));
        //        $info = $class->run();

        $info = M('company_profile')->find($id);
        if ($info) {
            if ($info['map_x'] > 0 && $info['map_y'] > 0) {
                $info['map_x'] = $info['map_x'] - 0.0064;
                $info['map_y'] = $info['map_y'] - 0.0064;
            }
            if ($info['tag']) {
                $arr = explode(",", $info['tag']);
                foreach ($arr as $k => $v) {
                    $temp  = explode("|", $v);
                    $tag[] = $temp[1];
                }
                $info['tag_arr'] = $tag;
            }

            $info['landline_tel'] = $info['landline_tel'] == '-' ? "" : $info['landline_tel'];
            if ($info['logo']) {
                $info['logo'] = C('qscms_site_domain') . C('qscms_site_dir') . C('qscms_attach_path') . 'company_logo/' . $info['logo'];
            } else {
                $info['logo'] = C('qscms_site_domain') . C('qscms_site_dir') . C('qscms_attach_path') . 'resource/no_logo.png';
            }
            $list_class       = new \Common\qscmstag\jobs_listTag(array('显示数目' => 6, '会员uid' => $info['uid']));
            $info['jobslist'] = $list_class->run();
            foreach ($info['jobslist']['list'] as $k => $v) {
                $info['jobslist']['list'][$k]['refreshtime_cn'] = strip_tags($v['refreshtime_cn']);
            }
            /**
             * 5.0新增
             */
            if(C('qscms_apply_jobs_contact')){
                $apply = D('PersonalJobsApply')->where(array('personal_uid'=>$uid,'company_id'=>$info['id']))->find();
                if($apply){
                    $info['telephone'] = $info['telephone'];
                    $info['landline_tel'] = $info['landline_tel'];
                    $info['email'] = $info['email'];
                }else{
                    $info['telephone'] = contact_hide($info['telephone'],2);
                    $info['landline_tel'] = contact_hide($info['landline_tel'],1);
                    $info['email'] = contact_hide($info['email'],3);  
                }
            }else{
                $hide = true;
                $showjobcontact = C('qscms_showjobcontact_wap');
                if($showjobcontact == 0)
                {
                    $hide = false;
                }
                else
                {
                    if($uid){
                        $resume = D('Resume')->where(array('uid'=>$uid))->find();
                        if($showjobcontact == 1){
                            $hide = false;
                        }elseif($resume && $showjobcontact == 2){
                            $hide = false;
                        }
                    }
                }  
                $info['landline_tel'] = trim($info['landline_tel'],'-');
                $hide && $info['telephone'] = contact_hide($info['telephone'],2);
                $hide && $info['landline_tel'] = contact_hide($info['landline_tel'],1);
                $hide && $info['email'] = contact_hide($info['email'],3);
            }
            $info['has_resume'] = $resume?1:0; 
            $info['hide'] = $hide; 
            $info['personal_uid'] = $uid;
            /**
             * 5.0新增 end
             */
        }
        $this->ajaxReturn(1, '获取数据成功', $info);
    }

    public function get_openid($code)
    {
        $url      = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . C('qscms_weixinapp_appid') . '&secret=' . C('qscms_weixinapp_appsecret') . '&js_code=' . $code . '&grant_type=authorization_code';
        $result   = https_request($url);
        $jsoninfo = json_decode($result, true);
        $openid   = $jsoninfo["openid"];
        if ($openid) {
            $this->ajaxReturn(1, '获取数据成功', $openid);
        } else {
            $this->ajaxReturn(0, '获取数据失败');
        }
        //$this->ajaxReturn(1,'获取数据成功',$openid);
    }

    public function favoritejobs()
    {
        $jid = I('request.id', '', 'intval');
        !$jid && $this->ajaxReturn(0, '请选择职位！');
        $uid  = I("get.uid", "intval");
        $has  = D('PersonalFavorites')->where(array('jobs_id' => $jid, 'personal_uid' => $uid))->find();
        $user = D("members")->where(array("uid" => $uid))->find();
        if ($has) {
            D('PersonalFavorites')->where(array('jobs_id' => $jid, 'personal_uid' => $uid))->delete();
            $this->ajaxReturn(1, '取消收藏成功！', 'cancel');
        } else {
            $reg = D('PersonalFavorites')->add_favorites($jid, $user);
            !$reg['state'] && $this->ajaxReturn(0, $reg['error']);
            $this->ajaxReturn(1, '收藏成功！');
        }

    }

    public function applyjobs()
    {
        $jobsid = I("get.id", "intval");
        $uid    = I("get.uid", "intval");
        $user   = D("members")->where(array("uid" => $uid))->find();
        $resume = D('resume')->where(array("uid" => $uid))->select();
        !$resume && $this->ajaxReturn(0, "请先填写一份简历");
        $reg = D('PersonalJobsApply')->jobs_apply_add($jobsid, $user);
        !$reg['state'] && $this->ajaxReturn(0, "您已经申请过这个职位了", $reg['create']);
        $reg['data']['failure'] && $this->ajaxReturn(0, $reg['data']['list'][$jid]['tip']);
        $this->ajaxReturn(1, '投递成功！');

    }

    /**
     * 关注企业/取消关注
     */
    public function favoritecompany($id)
    {
        $id  = I("get.id", "intval");
        $uid = I("get.uid", "intval");
        if (!$id) {
            $this->ajaxReturn(0, '请选择企业！');
        }
        $user = D("members")->where(array("uid" => $uid))->find();
        $r    = D('PersonalFocusCompany')->add_focus($id, $user);
        $this->ajaxReturn($r['state'], $r['msg'], $r['data']);
    }
    /**
     * 验证码
     */
    public function get_vcode()
    {
        import("Library.Org.Verify.Verify", dirname(__FILE__) . '/../');
        $config = array(
            'fontSize' => 30, // 验证码字体大小
            'length'   => 4, // 验证码位数
            'useNoise' => true, // 关闭验证码杂点
        );

        $Verify = new \Verify($config);
        $res    = $Verify->entry();
        $this->ajaxReturn(1, '获取成功', $res);

    }
}
