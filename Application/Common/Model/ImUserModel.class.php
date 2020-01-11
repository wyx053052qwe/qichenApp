<?php 
namespace Common\Model;
use Think\Model;
class ImUserModel extends Model{
	protected $_auto = array (
		array('addtime','time',1,'function'),
		array('updatetime','time',3,'function')
	);
	public function add_dialogue($formuid,$touid){
		$user = $this->where("(formuid={$formuid} and touid={$touid}) or (formuid={$touid} and touid={$formuid})")->getfield('formuid,id,sendtime');
		if(!$user[$formuid]){
			if(false === $this->create(array('formuid'=>$formuid,'touid'=>$touid))) return array('state'=>0,'error'=>$this->getError());
			if(!$this->add()) return array('state'=>0,'error'=>'用户会话信息新增失败！');
			$user[$formuid]['sendtime']=time();
		}
		if(!$user[$touid]){
			if(false === $this->create(array('formuid'=>$touid,'touid'=>$formuid))) return array('state'=>0,'error'=>$this->getError());
			if(!$this->add()) return array('state'=>0,'error'=>'用户会话信息新增失败！');
			$user[$touid]['sendtime']=time();
		}
		return array('state'=>1,'data'=>$user);
	}
	public function get_user_info($uid){
		if(!C('visitor.uid')) return array('state'=>0,'error'=>'请登录帐号！');
		$userInfo = C('visitor');
		if($uid) $this->where(array('formuid'=>C('visitor.uid'),'touid'=>$uid))->setfield('unread',0);
		if($userInfo['utype'] == 1){
            $avatars = M('CompanyProfile')->where(array('uid'=>$userInfo['uid']))->getfield('logo');
        	$userInfo['avatars'] = $avatars ? attach($avatars,'company_logo') : attach('no_logo.png','resource');
        }
        if(!fieldRegex($userInfo['avatars'],'url')) $userInfo['avatars'] = C('qscms_site_domain') . $userInfo['avatars'];
        $im = new \Common\qscmslib\im();
		$userInfo['rong_token'] = $im->token($userInfo);
		$userInfo['rong_key'] = C('qscms_rongyun_appkey');
		$userInfo['sendUid'] = $uid;
		return $userInfo ? array('state'=>1,'user'=>$userInfo) : array('state'=>0,'error'=>'用户信息获取失败！');
	}
	/**
	 * [get_user_list 读取会话列表]
	 */
	public function get_user_list(){
		$user = $this->where(array('formuid'=>C('visitor.uid')))->order('sendtime desc,id asc')->select();
		if($user){
			foreach($user as $val){
				$uids[] = $val['touid'];
			}
			$userInfo = M('Members')->where(array('uid'=>array('in',$uids)))->getfield('uid,username,avatars,utype');
			foreach($user as $key=>$val){
				if(!$userInfo[$val['touid']]){
					unset($user[$key]);
					continue;
				}
				$where['uid'] = $val['touid'];
				if($userInfo[$val['touid']]['utype'] == 1){
					$company = M('CompanyProfile')->where($where)->find();
		        	$company['companyname'] && $userInfo[$val['touid']]['username'] = $company['companyname'];
		        	$company['logo'] && $userInfo[$val['touid']]['avatars'] = '';
				}
				if($userInfo[$val['touid']]['avatars']){
		            $userInfo[$val['touid']]['avatars'] = attach($userInfo[$val['touid']]['avatars'],'avatar');
		        }elseif($userInfo[$val['touid']]['utype'] == 1){
		        	$userInfo[$val['touid']]['avatars'] = $company['logo'] ? attach($company['logo'],'company_logo') : attach('no_logo.png','resource');
		        }elseif($userInfo[$val['touid']]['utype'] == 2){
		        	$where['def'] = 1;
		            $sex = M('Resume')->where($where)->limit(1)->getfield('sex');
		            $avatar_default = $sex==1?'no_photo_male.png':'no_photo_female.png';
		            $userInfo[$val['touid']]['avatars'] = attach($avatar_default,'resource');
		        }else{
		        	$userInfo[$val['touid']]['avatars'] = attach('no_photo_male.png','resource');
		        }
		        $user[$key] = array_merge($user[$key],$userInfo[$val['touid']]);
			}
			return $user;
		}
		return '';
	}
	/**
	 * [read_message 标记已读消息]
	 */
	public function read_message($uid){
		if(!$uid) return array('state'=>0,'error'=>'请选择用户uid！');
		$s = $this->where(array('formuid'=>C('visitor.uid'),'touid'=>$uid))->save(array('unread'=>array('exp','unread-1')));
		if(!$s) return array('state'=>0,'error'=>'未读消息标记失败！');
		return array('state'=>1);
	}
	/**
	 * [unread_message 未读消息数量]
	 */
	public function unread_message(){
		$num = $this->where(array('formuid'=>C('visitor.uid'),'unread'=>array('neq',0)))->sum('unread');
		return $num;
	}
	/**
	 * [del_dialog description]
	 * @return [type] [description]
	 */
	public function del_dialog($uid){
		if(!$uid) return array('state'=>0,'error'=>'请选择要删除的会话！');
		$result = $this->where(array('formuid'=>C('visitor.uid'),'touid'=>$uid))->delete();
        if($result){
        	$reg = $this->where(array('formuid'=>$uid,'touid'=>C('visitor.uid')))->find();
        	if(!$reg){
        		$ftid = intval(C('visitor.uid')) + intval($uid);
        		M('ImMessage')->where(array('ftid'=>$ftid,'formuid|touid'=>$uid))->delete();
        	}
        	return array('state'=>1,'error'=>'删除成功！');
        }else{
        	return array('state'=>0,'error'=>'删除失败！');
        }
	}
}
?>