<?php
namespace Mobile\Controller;
use Mobile\Controller\MobileController;
class AjaxPersonalController extends MobileController{
	public function _initialize() {
        parent::_initialize();
        //访问者控制
        if(!$this->visitor->is_login && !in_array(ACTION_NAME,array('resume_add_dig','resume_add'))){
            IS_AJAX && $this->ajaxReturn(0, L('login_please'),'',1);
            $this->error(L('login_please'));
        }
        if($this->visitor->is_login && C('visitor.utype') != 2){
            IS_AJAX && $this->ajaxReturn(0,'请登录个人帐号！');
            $this->redirect('members/index');
        }
    }
	/**
	 * 关注企业/取消关注
	 */
	public function company_focus($company_id){
		if(!$company_id){
			$this->ajaxReturn(0,'请选择企业！');
		}
		$r = D('PersonalFocusCompany')->add_focus($company_id,C('visitor.uid'));
		$this->ajaxReturn($r['state'],$r['msg'],$r['data']);
	}
	/**
	 * [jobs_favorites 收藏职位]
	 */
	public function jobs_favorites(){
		$jid = I('request.jid','','trim');
		!$jid && $this->ajaxReturn(0,'请选择职位！');
		$has = $this->_check_favor($jid,C('visitor.uid'));
		if($has){
			D('PersonalFavorites')->where(array('jobs_id'=>$jid,'personal_uid'=>C('visitor.uid')))->delete();
			$this->ajaxReturn(1,'取消收藏成功！','cancel');
		}else{
			$reg = D('PersonalFavorites')->add_favorites($jid,C('visitor'));
	        !$reg['state'] && $this->ajaxReturn(0,$reg['error']);
			$this->ajaxReturn(1,'收藏成功！');
		}
	}
	//检测是否已收藏
    protected function _check_favor($jobs_id,$uid){
        $r = D('PersonalFavorites')->where(array('jobs_id'=>$jobs_id,'personal_uid'=>$uid))->find();
        if($r){
            return 1;
        }else{
            return 0;
        }
    }
    public function resume_add_dig(){
        if($this->visitor->is_login) $this->redirect('personal/index',array('uid'=>$this->visitor->info['uid']));
    	$category = D('Category')->get_category_cache();
        $birthdate_arr = range(date('Y')-16,date('Y')-65);
        $this->assign('education',$category['QS_education']);//最高学历
        $this->assign('experience',$category['QS_experience']);//工作经验
        $this->assign('birthdate_arr',$birthdate_arr);
        $this->assign('wage',$category['QS_wage']);//期望薪资
    	$this->assign('jid',I('request.jid',0,'intval'));
    	$this->_config_seo(array('title'=>'快速注册','header_title'=>'快速注册'));
    	$this->display('AjaxCommon/resume_add_dig');
    }
    /**
     * [resume_add 快速创简历，并登录帐号]
     */
    public function resume_add(){
    	$jid = I('request.jid','','trim');
		!$jid && $this->ajaxReturn(0,'请选择要投递的职位！');
    	if (C('qscms_closereg')) $this->ajaxReturn(0,'网站暂停会员注册，请稍后再次尝试！');
        $mobile = I('post.telephone',0,'trim');
        $smsVerify = session('gsou_reg_smsVerify');
        if(true !== $tip = verify_mobile($mobile,$smsVerify,I('post.mobilevcode', 0, 'intval'))) $this->ajaxReturn(0, $tip);
        if(!$data = M('Members')->field('uid,mobile,utype')->where(array('mobile'=>$smsVerify['mobile']))->find()){
            $data['utype'] = 2;
            $data['mobile'] = $smsVerify['mobile'];
            $passport = $this->_user_server();
            if(false === $data = $passport->register($data)) $this->ajaxReturn(0,$passport->get_error());

            $sendSms['tpl']='set_register_resume';
            $sendSms['data']=array('username'=>$data['username'].'','password'=>$data['password']);
            $sendSms['mobile']=$data['mobile'];
            if(true !== $reg = D('Sms')->sendSms('captcha',$sendSms)) $this->ajaxReturn(0,$reg);

            session('gsou_reg_smsVerify',null);
            D('Members')->user_register($data);
        }
        if(false === $this->visitor->login($data['uid'])) $this->ajaxReturn(0,$this->visitor->getError());
        if($data['utype'] != 2){
            if (null === D('MembersSetmeal')->get_user_setmeal($this->visitor->info['uid'])) {
                D('Members')->user_register($this->visitor->info);//积分、套餐、分配客服等初始化操作
            }
            if (false !== D('Members')->where(array('uid' => $this->visitor->info['uid']))->setField('utype', 2)) {
                $this->visitor->update();
            }
        }
        $ints = array('sex','birthdate','education','experience','wage');
        $trims = array('telephone','fullname','intention_jobs_id','district');
        foreach ($ints as $val) {
            $setsqlarr[$val] = I('post.'.$val,0,'intval');
        }
        foreach ($trims as $val) {
            $setsqlarr[$val] = I('post.'.$val,'','trim,badword');
        }
        if($data['password']){
            $setsqlarr['nature'] = 62;
            $setsqlarr['def'] = 1;
            $setsqlarr['display_name'] = C('qscms_default_display_name');
            $setsqlarr['is_quick'] = 1;
            $rst = D('Resume')->add_resume($setsqlarr,$this->visitor->info);
        }else{
            $resume = D('Resume')->where(array('uid'=>$data['uid']))->order('def desc')->find();
            if($resume){
                $rst = D('Resume')->save_resume($setsqlarr,$resume['id'],$this->visitor->info);
            }else{
                $setsqlarr['nature'] = 62;
                $setsqlarr['def'] = 1;
                $setsqlarr['display_name'] = C('qscms_default_display_name');
                $setsqlarr['is_quick'] = 1;
                $rst = D('Resume')->add_resume($setsqlarr,$this->visitor->info);
            }
        }
        if(!$rst['state']) $this->ajaxReturn(0,$rst['error']);
		$this->_resume_apply_exe($jid,$rst['id']);
        $this->ajaxReturn(1,'简历创建成功！');
    }
	/**
     * [ resume_apply 简历投递]
     */
    public function resume_apply($jid){
		$jid = I('request.jid');
		!$jid && $this->ajaxReturn(0,'请选择要投递的职位！');
		$this->_resume_apply_exe($jid);
    }
    protected function _resume_apply_exe($jid,$rid){
    	$reg = D('PersonalJobsApply')->jobs_apply_add($jid,$this->visitor->info,$rid);
        if(!$reg['state'] && $reg['complete']){// 完整度不够
            $this->ajaxReturn(1,$reg['error'],array('complete'=>$reg['complete']));
        }
        !$reg['state'] && $this->ajaxReturn(1,$reg['error'],array('create'=>$reg['create']));
        $reg['data']['failure'] && $this->ajaxReturn(0,$reg['data']['list'][$jid]['tip']);
		$this->ajaxReturn(1,'投递成功！');
    }
}
?>