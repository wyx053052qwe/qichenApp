<?php
namespace Parttime\Controller;

class MobileController extends \Mobile\Controller\MobileController {
    public $authentication_user;
    public $cache_apply_info;
    // 初始化函数
    public function _initialize() {
        parent::_initialize();
        $this->authentication_user = C('visitor');
        $this->cache_apply_info = session('cache_apply_info')?session('cache_apply_info'):cookie('cache_apply_info');
        $tpl_dir = C('TPL_PUBLIC_DIR');
        $tpl_dir = str_replace("Mobile", "Parttime", $tpl_dir);
        $tpl_dir = str_replace(C('DEFAULT_THEME'), "mobile", $tpl_dir);
        $this->assign('tpl_public_dir',$tpl_dir);
    }

    /**
     * 兼职首页
     */
    public function index() {
        $category_jobs = D('Parttime/ParttimeCategory')->get_category_cache('QS_category_jobs');
        $category_settlement = D('Parttime/ParttimeCategory')->get_category_cache('QS_settlement');
        $this->assign('category_jobs',$category_jobs);
        $this->assign('category_settlement',$category_settlement);
        $this->show_index_ads();
        $this->theme('mobile')->display('Parttime@./index');die;
    }
    /**
     * 首页广告
     */
    protected function show_index_ads(){
        $time_now = time();
        $index_focus_where['is_display'] = 1;
        $index_focus_where['alias'] = 'QS_mobile_indexfocus';
        $index_focus_where['starttime'] = array('elt', $time_now);
        $index_focus_where['deadline'] = array(array('egt',$time_now),array('eq',0), 'or');
        $index_block_where['is_display'] = 1;
        $index_block_where['alias'] = 'QS_mobile_centerblock';
        $index_block_where['starttime'] = array('elt', $time_now);
        $index_block_where['deadline'] = array(array('egt',$time_now),array('eq',0), 'or');
        $index_focus = D('AdMobile')->get_ad_list($index_focus_where);
        $index_block = D('AdMobile')->get_ad_list($index_block_where,2);
        $this->assign('index_focus',$index_focus);
        $this->assign('index_block',$index_block);
    }
    public function ajax_jobs_list(){
        if(IS_AJAX){
            $where = array(
                '显示数目' => 10,
                '兼职类型' => I('get.category'),
                '结算方式' => I('get.settlement'),
                '地区分类' => I('get.citycategory'),
                '关键字' => I('get.key'),
                '分页显示' => 1
            );
            $jobs_mod = new \Common\qscmstag\parttime_jobs_listTag($where);
            $jobs_list = $jobs_mod->run();
            !$jobs_list['list'] && $this->ajaxReturn(0,'没有更多职位信息！');
            $isfull = $jobs_list['page_params']['nowPage'] >= $jobs_list['page_params']['totalPages'];
            if($jobs_list['page_params']['nowPage'] <= $jobs_list['page_params']['totalPages']){
                $this->assign('jobslist',$jobs_list['list']);
                $data['html'] = $this->theme('mobile')->fetch('Parttime@./ajax_jobs_list');
            }
            $data['isfull'] = $isfull;
            $this->ajaxReturn(1,'职位列表信息获取成功！',$data);
        }
    }
    private function get_current_url() {
        $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
        $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
        return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
    }
    private function check_auth(){
        if(!$this->authentication_user){
            if(IS_AJAX){
                $this->ajaxReturn(0,'请先验证身份！');
            }else{
                session('auth_url_referrer',$this->get_current_url());
                $this->redirect('authenticate');
            }
        }else{
            $auth_info = D('Members')->where(array('mobile'=>$this->authentication_user['mobile'],'password'=>$this->authentication_user['password']))->find();
            if(!$auth_info){
                if(IS_AJAX){
                    $this->ajaxReturn(0,'请先验证身份！');
                }else{
                    session('auth_url_referrer',$this->get_current_url());
                    $this->redirect('authenticate');
                }
            }
        }
    }
    // 发送短信验证码
    public function send_sms(){
        if(C('qscms_mobile_captcha_open') && C('qscms_wap_captcha_config.varify_mobile') && true !== $reg = \Common\qscmslib\captcha::verify('mobile')) $this->ajaxReturn(0,$reg);
        $mobile = I('post.mobile','','trim');
        !$mobile && $this->ajaxReturn(0,'请填手机号码！');
        if(!fieldRegex($mobile,'mobile')) $this->ajaxReturn(0,'手机号错误！');
        $rand=getmobilecode();
        $sendSms['tpl']='set_login';
        $sendSms['data']=array('rand'=>$rand.'','sitename'=>C('qscms_site_name'));
        $smsVerify = session('login_smsVerify');
        if($smsVerify && $smsVerify['mobile']==$mobile && time()<$smsVerify['time']+60) $this->ajaxReturn(0,'60秒内仅能获取一次短信验证码,请稍后重试');
        $sendSms['mobile']=$mobile;
        if(true === $reg = D('Sms')->sendSms('captcha',$sendSms)){
            session('login_smsVerify',array('rand'=>substr(md5($rand), 8,16),'time'=>time(),'mobile'=>$mobile));
            session('_verify_num_check',null);
            $this->ajaxReturn(1,'手机验证码发送成功！');
        }else{
            $this->ajaxReturn(0,$reg);
        }
    }
    private function auth_mobile_code(){
        $expire = I('post.expire',1,'intval');
        if($mobile = I('post.mobile','','trim')){
            if(!fieldRegex($mobile,'mobile')) $this->ajaxReturn(0,'手机号格式错误！');
            $smsVerify = session('login_smsVerify');
            if(true !== $tip = verify_mobile($mobile,$smsVerify,I('post.mobile_vcode', 0, 'intval'))) $this->ajaxReturn(0, $tip);
            $user = D('Members')->where(array('mobile'=>$smsVerify['mobile']))->find();
            if(!$user){
                $user = D('Members')->add_auth_info($smsVerify['mobile']);
            }elseif(!$user['secretkey']){
                $user['secretkey'] = D('Members')->randstr(16,true);
                M('Members')->where(array('uid'=>$user['uid']))->setfield('secretkey',$user['secretkey']);
            }
            $user['contact'] = I('post.contact','','trim');
            session('login_smsVerify',null);
        }
        if($user) $this->apply_login($smsVerify['mobile'],$expire);
    }
    public function do_auth(){
        $this->auth_mobile_code();
        $this->ajaxReturn(1,'验证通过');
    }
    public function authenticate(){
        $this->assign('auth_url_referrer',session('auth_url_referrer'));
        $this->_config_seo(array('title' => '身份验证', 'header_title' => '身份验证'));
        $this->theme('mobile')->display('Parttime@./authenticate');die;
    }
    /**
     * 兼职职位管理
     */
    public function manage() {
        $this->check_auth();
        $model = D('Parttime/ParttimeJobs');
        $map['uid'] = $this->authentication_user['uid'];
        $type = I('get.type',0,'intval');
        if($type==1){
            $map['audit'] = $model::AUDIT_PASS;
        }else if($type==2){
            $map['audit'] = array('neq',$model::AUDIT_PASS);
        }
        $total = $model->where($map)->count();
        $pager = pager($total, 10);
        $page = $pager->fshow();
        $limit = $pager->firstRow.','.$pager->listRows;
        $joblist = $model->where($map)->limit($limit)->order('refreshtime desc,id desc')->select();
        $audit_status_cn = $model->audit_status;
        $this->assign('joblist',$joblist);
        $this->assign('page',$page);
        $this->assign('audit_status_cn',$audit_status_cn);
        $this->_config_seo(array('title' => '兼职管理', 'header_title' => '兼职管理'));
        $this->theme('mobile')->display('Parttime@./manage');die;
    }

    /**
     * 收到的报名
     */
    public function receive() {
        $map = array();
        $pid = I('get.pid',0,'intval');
        $settr = I('get.settr',0,'intval');
        $pid && $map['pid'] = $pid;
        $settr && $map['addtime'] = array('egt',strtotime('-'.$settr.'day'));
        $model = D('Parttime/ParttimeJobsApply');
        $total = $model->where($map)->count();
        $pager = pager($total, 10);
        $page = $pager->fshow();
        $limit = $pager->firstRow.','.$pager->listRows;
        $applylist = $model->join($join)->where($map)->limit($limit)->order('id desc')->select();
        $this->assign('jobslist',D('Parttime/ParttimeJobs')->where(array('uid'=>$this->authentication_user['uid']))->getField('id,jobs_name'));
        $this->assign('applylist',$applylist);
        $this->assign('pid',$pid);
        $this->assign('page',$page);
        $this->_config_seo(array('title' => '收到的报名', 'header_title' => '收到的报名'));
        $this->theme('mobile')->display('Parttime@./receive');die;
    }

    /**
     * 发布兼职职位
     */
    public function add() {
        if(IS_POST){
            $post_data = I('post.');
            if(!$this->authentication_user){
                $this->auth_mobile_code();
                $post_data['uid'] = $this->authentication_user['uid'];
            }else{
                $auth_info = D('Members')->where(array('mobile'=>$this->authentication_user['mobile'],'password'=>$this->authentication_user['password']))->find();
                if(!$auth_info){
                    $this->ajaxReturn(0,'请先验证身份！');
                }else{
                    $post_data['uid'] = $this->authentication_user['uid'];
                }
            }
            if($post_data['long_period']==1){
                $post_data['cycle_starttime'] = $post_data['cycle_endtime'] = 0;
            }else{
                if($post_data['cycle_starttime']){
                    $post_data['cycle_starttime'] = strtotime($post_data['cycle_starttime']);
                }
                if($post_data['cycle_endtime']){
                    $post_data['cycle_endtime'] = strtotime($post_data['cycle_endtime']);
                }
            }
            if(false === $reg = D('Parttime/ParttimeJobs')->create($post_data)){
                $this->ajaxReturn(0,D('Parttime/ParttimeJobs')->getError());
            }else{
                if(false === D('Parttime/ParttimeJobs')->add($reg)){
                    $this->ajaxReturn(0,D('Parttime/ParttimeJobs')->getError());
                }else{
                    $this->ajaxReturn(1,'发布成功！',array('url'=>U('Parttime/manage')));
                }
            }
        }else{
            $count_jobs = D('Parttime/ParttimeJobs')->where(array('uid'=>$this->authentication_user['uid']))->count();
            if($count_jobs>=C('qscms_parttime_max')){
                exit('你已发布的职位数已超出限制！');
            }
            if($this->authentication_user && $count_jobs && !$this->authentication_user['contact']){
                $jobs = D('Parttime/ParttimeJobs')->where(array('uid'=>$this->authentication_user['uid']))->order('id desc')->find();
                $this->authentication_user['contact'] = $jobs['contact'];
            }
            $category_jobs = D('Parttime/ParttimeCategory')->get_category_cache('QS_category_jobs');
            $category_wage = D('Parttime/ParttimeCategory')->get_category_cache('QS_wage_type');
            $category_settlement = D('Parttime/ParttimeCategory')->get_category_cache('QS_settlement');
            $this->assign('need_mobile_audit',$this->authentication_user?0:1);
            $this->assign('contact',$this->authentication_user['contact']);
            $this->assign('mobile',$this->authentication_user['mobile']);
            $this->assign('new_record',1);
            $this->assign('category_jobs',$category_jobs);
            $this->assign('category_wage',$category_wage);
            $this->assign('category_settlement',$category_settlement);
            $this->assign('leave_jobs_num',C('qscms_parttime_max')-$count_jobs);
            $this->_config_seo(array('title' => '发布兼职', 'header_title' => '发布兼职'));
            $this->theme('mobile')->display('Parttime@./add');die;
        }
    }

    /**
     * 修改兼职职位
     */
    public function edit() {
        $id = I('request.id',0,'intval');
        if(IS_POST){
            $post_data = I('post.');
            if(!$this->authentication_user){
                $this->ajaxReturn(0,'请先验证身份！');
            }else{
                $auth_info = D('Members')->where(array('mobile'=>$this->authentication_user['mobile'],'password'=>$this->authentication_user['password']))->find();
                if(!$auth_info){
                    $this->ajaxReturn(0,'请先验证身份！');
                }
            }
            if($post_data['long_period']==1){
                $post_data['cycle_starttime'] = $post_data['cycle_endtime'] = 0;
            }else{
                if($post_data['cycle_starttime']){
                    $post_data['cycle_starttime'] = strtotime($post_data['cycle_starttime']);
                }
                if($post_data['cycle_endtime']){
                    $post_data['cycle_endtime'] = strtotime($post_data['cycle_endtime']);
                }
            }
            if(false === $reg = D('Parttime/ParttimeJobs')->create($post_data)){
                $this->ajaxReturn(0,D('Parttime/ParttimeJobs')->getError());
            }else{
                if(false === D('Parttime/ParttimeJobs')->save($reg)){
                    $this->ajaxReturn(0,D('Parttime/ParttimeJobs')->getError());
                }else{
                    D('Parttime/ParttimeJobs')->update_search($reg['id']);
                    $this->ajaxReturn(1,'修改成功！',array('url'=>U('Parttime/manage')));
                }
            }
        }else{
            $this->check_auth();
            $category_jobs = D('Parttime/ParttimeCategory')->get_category_cache('QS_category_jobs');
            $category_wage = D('Parttime/ParttimeCategory')->get_category_cache('QS_wage_type');
            $category_settlement = D('Parttime/ParttimeCategory')->get_category_cache('QS_settlement');
            $jobs = D('Parttime/ParttimeJobs')->where(array('id'=>$id,'uid'=>$this->authentication_user['uid']))->find();
            $jobs['cycle_starttime'] = $jobs['cycle_starttime']?date('Y-m-d',$jobs['cycle_starttime']):'';
            $jobs['cycle_endtime'] = $jobs['cycle_endtime']?date('Y-m-d',$jobs['cycle_endtime']):'';
            $jobs['worktime'] = $jobs['worktime']?unserialize($jobs['worktime']):'';
            $this->assign('mobile',$this->authentication_user['mobile']);
            $this->assign('new_record',0);
            $this->assign('jobs',$jobs);
            $this->assign('category_jobs',$category_jobs);
            $this->assign('category_wage',$category_wage);
            $this->assign('category_settlement',$category_settlement);
            $this->_config_seo(array('title' => '修改兼职职位', 'header_title' => '修改兼职职位'));
            $this->theme('mobile')->display('Parttime@./add');die;
        }
    }
    public function check_apply_cache(){
        $pid = I('request.pid',0,'intval');
        if($this->authentication_user){
            $apply = D('Parttime/ParttimeJobsApply')->where(array('uid'=>$this->authentication_user['uid'],'pid'=>$pid))->find();
            if($apply){
                $this->ajaxReturn(1,'你已经报名过该职位了！');
            }
            if($this->cache_apply_info){
                $addsql = $this->cache_apply_info;
                $addsql['pid'] = $pid;
                $addsql['addtime'] = time();
                D('Parttime/ParttimeJobsApply')->add($addsql);
                $this->ajaxReturn(1,'报名成功！');
            }else{
                $this->ajaxReturn(0,'',U('Parttime/apply',array('id'=>$pid)));
            }
        }else{
            $this->ajaxReturn(0,'',U('Parttime/apply',array('id'=>$pid)));
        }
    }
    /**
     * 报名兼职
     */
    public function apply() {
        if(IS_POST){
            $post_data = I('post.');
            if(!$this->authentication_user){
                $this->auth_mobile_code();
                $post_data['uid'] = $this->authentication_user['uid'];
            }else{
                $auth_info = D('Members')->where(array('mobile'=>$this->authentication_user['mobile'],'password'=>$this->authentication_user['password']))->find();
                if(!$auth_info){
                    $this->ajaxReturn(0,'请先验证身份！');
                }else{
                    $post_data['uid'] = $this->authentication_user['uid'];
                }
            }
            if(false === $reg = D('Parttime/ParttimeJobsApply')->create($post_data)){
                $this->ajaxReturn(0,D('Parttime/ParttimeJobsApply')->getError());
            }else{
                if(false === D('Parttime/ParttimeJobsApply')->add($reg)){
                    $this->ajaxReturn(0,D('Parttime/ParttimeJobsApply')->getError());
                }else{
                    unset($reg['pid'],$reg['addtime']);
                    session('cache_apply_info',$reg);
                    cookie('cache_apply_info',$reg);
                    $this->cache_apply_info = $reg;
                    $this->ajaxReturn(1,'报名成功！',array('url'=>U('Parttime/index')));
                }
            }
        }else{
            $id = I('get.id',0,'intval');
            $info = D('Parttime/ParttimeJobs')->find($id);
            !$info && exit('参数错误！');
            $birthdate_arr[] = date('Y')-18;
            for ($i=1; $i <= 42; $i++) { 
                $birthdate_arr[] = $birthdate_arr[0]-$i;
            }
            $identity_arr = D('Parttime/ParttimeCategory')->get_category_cache('QS_identity_apply');
            $this->assign('authentication_user',$this->authentication_user);
            $this->assign('need_audit',$this->authentication_user?0:1);
            $this->assign('birthdate_arr',$birthdate_arr);
            $this->assign('identity_arr',$identity_arr);
            $this->_config_seo(array('title' => '兼职报名', 'header_title' => '兼职报名'));
            $this->theme('mobile')->display('Parttime@./apply');die;
        }
    }

    /**
     * 兼职详情
     */
    public function show() {
        $id = I('request.id',0,'intval');
        $model = D('Parttime/ParttimeJobs');
        $info = $model->find($id);
        if(!$info){
            $controller = new \Common\Controller\BaseController;
            $controller->_empty();
        }
        $model->where(array('id'=>$id))->setInc('click',1);
        $info['worktime'] = $info['worktime']?unserialize($info['worktime']):array();
        if(C('qscms_contact_img_parttime')==2){
            $info['show_mobile'] = '<img src="'.C('qscms_site_domain').U('Home/Qrcode/get_font_img',array('str'=>encrypt($info['mobile'],C('PWDHASH'))),'','',true).'"/>';
        }else{
            $info['show_mobile'] = $info['mobile'];
        }
        $recommend_map['id'] = array('neq',$id);
        $recommend_map['category'] = array('eq',$info['category']);
        $recommend_map['audit'] = $model::AUDIT_PASS;
        $recommend = $model->where($recommend_map)->order('refreshtime desc,id desc')->limit(3)->select();
        $this->assign('recommend',$recommend);
        $this->assign('info',$info);
        $this->wx_share();
        $page_seo = D('Page')->get_page();
        $this->_config_seo($page_seo[strtolower(MODULE_NAME).'_'.strtolower(CONTROLLER_NAME).'_'.strtolower(ACTION_NAME)],$info);
        $this->theme('mobile')->display('Parttime@./show');die;
    }
    /**
     * 刷新职位
     */
    public function refresh(){
        if(!$this->authentication_user){
            $this->ajaxReturn(0,'请先验证身份！');
        }
        $yid = I('get.yid',0,'intval');
        if(!$yid){
            $this->ajaxReturn(0,'请选择职位！');
        }else{
            D('Parttime/ParttimeJobs')->refresh_jobs($yid,$this->authentication_user['uid']);
            $this->ajaxReturn(1,'刷新成功！');
        }
    }
    /**
     * 删除职位
     */
    public function delete(){
        if(!$this->authentication_user){
            $this->ajaxReturn(0,'请先验证身份！');
        }
        $id = I('request.id',0,'intval');
        if(!$id){
            $this->ajaxReturn(0,'请选择职位！');
        }else{
            D('Parttime/ParttimeJobs')->where(array('id'=>$id,'uid'=>$this->authentication_user['uid']))->delete();
            $this->ajaxReturn(1,'删除成功！');
        }
    }
    /**
     * 检测已发布职位数
     */
    public function check_jobs_num(){
        if($this->authentication_user){
            $count_jobs = D('Parttime/ParttimeJobs')->where(array('uid'=>$this->authentication_user['uid']))->count();
            if($count_jobs>=C('qscms_parttime_max')){
                $this->ajaxReturn(0,'你发布的职位数已达最大限制！');
            }else{
                $this->ajaxReturn(1,'可以发布');
            }
        }else{
            $this->ajaxReturn(1,'可以发布');
        }
    }
    /**
     * [like 点赞/取消点赞]
     */
    public function like(){
        $model = D('LikeRecord');
        $data['pid'] = I('get.id',0,'intval');
        $data['uid'] = C('visitor.uid');
        $data['ptype'] = 2;
        if ($like = $model->where($data)->find()) {
            $msg = '您已经点过赞了！';
            $data['is_like'] = 1;
        } else {
            $rst = $model->add_like($data);
            !$rst && $this->ajaxReturn(0, '点赞失败！');
            $msg = '点赞成功！';
        }
        $like_num = M($model->type[$data['ptype']])->getFieldById($data['pid'], 'like_num');
        $data['like_num'] = $like_num>99 ? '99+' : $like_num;
        $this->ajaxReturn(1, $msg, $data);
    }
}

?>