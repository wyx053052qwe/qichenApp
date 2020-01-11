<?php
namespace Mobile\Controller;
use Mobile\Controller\MobileController;
class ImController extends MobileController{
	// 初始化函数
	public function _initialize(){
		parent::_initialize();
		if(!$this->visitor->is_login) {
            IS_AJAX && $this->ajaxReturn(0, L('login_please'),'',1);
            //非ajax的跳转页面
            if(!$this->openid){
                $this->redirect('members/login');
            }else{
                $this->redirect('members/apilogin_binding');
            }
        }
	}
	/**
	 * [add_message 新增消息]
	 */
	public function add_message(){
		if(IS_AJAX && IS_POST){
			$reg = D('ImMessage')->add_message();
			$this->ajaxReturn($reg['state'],$reg['error'],$reg['message']);
		}
	}
	/**
	 * [user_list 历史会话列表]
	 */
	public function user_list(){
		$this->assign('userList',D('ImUser')->get_user_list());
		$this->_config_seo(array('title'=>'在线咨询','header_title'=>'在线咨询'));
		$this->display();
	}
	/**
	 * [read_message 标记当剪卡会话为已读消息]
	 */
	public function read_message(){
		if(IS_AJAX && IS_POST){
			$uid = I('post.uid',0,'intval');
			$reg = D('ImUser')->read_message($uid);
			$this->ajaxReturn($reg['state'],$reg['error']);
		}
	}
	/**
	 * [unread_message 读取未读消息数]
	 */
	public function unread_message(){
		if(IS_AJAX && IS_GET){
			$reg = D('ImUser')->unread_message();
			$this->ajaxReturn(1,'',$reg);
		}
	}
	/**
	 * [del_dialog 删除会话]
	 */
	public function del_dialog(){
		if(IS_AJAX && IS_GET){
			$uid = I('get.uid',0,'intval');
			$reg = D('ImUser')->del_dialog($uid);
			$this->ajaxReturn($reg['state'],$reg['error']);
		}
	}
	/**
	 * [message 会话]
	 */
	public function message(){
        $uid = I('get.uid',0,'intval');
		if(!$uid)$this->error('请选择用户！');
		//if($uid == C('visitor.uid')) $this->redirect('Im/user_list');
		$where['uid'] = $uid;
		$sendUser = M('Members')->field('uid,username,avatars,utype')->where($where)->find();
		if(!$sendUser) $this->error('用户不存在或已被管理员删除！');
		if($sendUser['utype'] == 1){
			$company = M('CompanyProfile')->where($where)->find();
        	$company['companyname'] && $sendUser['username'] = $company['companyname'];
        	$company['logo'] && $sendUser['avatars'] = '';
		}
		if($sendUser['avatars']){
            $sendUser['avatars'] = attach($sendUser['avatars'],'avatar');
        }elseif($sendUser['utype'] == 1){
        	$sendUser['avatars'] = $company['logo'] ? attach($company['logo'],'company_logo') : attach('no_logo.png','resource');
        }elseif($sendUser['utype'] == 2){
        	$where['def'] = 1;
            $sex = M('Resume')->where($where)->limit(1)->getfield('sex');
            $avatar_default = $sex==1?'no_photo_male.png':'no_photo_female.png';
            $sendUser['avatars'] = attach($avatar_default,'resource');
        }else{
        	$sendUser['avatars'] = attach('no_photo_male.png','resource');
        }
		$reg = D('ImUser')->get_user_info($uid);
		if(!$reg['state']) $this->error($reg['error']);
        $this->assign('ronguser',$reg['user']);
		$this->assign('sendUser',$sendUser);
		$message = D('ImMessage')->get_message($uid);
        if($message['state']){
			$this->assign('message',$message['data']);
        }
		$this->_config_seo(array('title'=>$sendUser['username'],'header_title'=>$sendUser['username']));
		$this->display();
	}
	/**
	 * [get_message AJAX获取聊天内容]
	 */
	public function get_message(){
		if(IS_AJAX && IS_GET){
			$uid = I('get.uid',0,'intval');
			if(!$uid) $this->ajaxReturn(0,'请选择用户！');
			//if($uid == C('visitor.uid')) $this->ajaxReturn(0,'不能与自已聊天！');
			$where['uid'] = $uid;
			$sendUser = M('Members')->field('uid,username,avatars')->where($where)->find();
			if($sendUser['utype'] == 1){
				$company = M('CompanyProfile')->where($where)->find('logo');
	        	$company['companyname'] && $sendUser['username'] = $company['companyname'];
	        	$company['logo'] && $sendUser['avatars'] = '';
			}
			if($sendUser['avatars']){
	            $sendUser['avatars'] = attach($sendUser['avatars'],'avatar');
	        }elseif($sendUser['utype'] == 1){
	        	$sendUser['avatars'] = $company['logo'] ? attach($company['logo'],'company_logo') : attach('no_logo.png','resource');
	        }elseif($sendUser['utype'] == 2){
	        	$where['def'] = 1;
	            $sex = M('Resume')->where($where)->limit(1)->getfield('sex');
	            $avatar_default = $sex==1?'no_photo_male.png':'no_photo_female.png';
	            $sendUser['avatars'] = attach($avatar_default,'resource');
	        }else{
	        	$sendUser['avatars'] = attach('no_photo_male.png','resource');
	        }
			$message = D('ImMessage')->get_message($uid);
			if($message['state']){
				$userInfo = C('visitor');
				if($userInfo['utype'] == 1){
		            $avatars = M('CompanyProfile')->where(array('uid'=>$userInfo['uid']))->getfield('logo');
		        	$userInfo['avatars'] = $avatars ? attach($avatars,'company_logo') : attach('no_logo.png','resource');
		        }
		        $this->assign('ronguser',$userInfo);
				$this->assign('sendUser',$sendUser);
				$this->assign('message',$message['data']);
				$message['data']['html']=$this->fetch('ajax_message');
	        }
	        unset($message['data']['list']);
			$this->ajaxReturn($message['state'],$message['error'],$message['data']);
		}
	}
}
?>