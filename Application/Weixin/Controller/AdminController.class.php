<?php
namespace Weixin\Controller;
use Common\Controller\ConfigbaseController;
class AdminController extends ConfigbaseController{
	public function _initialize() {
        parent::_initialize();
    }
	/**
	 * [menu 微信自定义菜单]
	 */
	public function menu(){
		$parentid = I('get.parentid',0,'intval');
        $menu_list = M('WeixinMenu')->field('id,title,key,type,url,menu_order,status')->where(array('parentid'=>$parentid))->order('menu_order desc,id')->select();
        if(IS_AJAX) $this->ajaxReturn(1,'菜单获取成功！',$menu_list);
        $this->assign('list', $menu_list);
        $this->display();
	}
	/**
	 * [menu_add 添加菜单]
	 */
	public function menu_add(){
		if(IS_POST){
			$this->_name = 'WeixinMenu';
            $this->add();
        }else{
        	if(false === $menus = F('weixin_menu_list')){
                $menus = D('WeixinMenu')->menu_chche();
            }
            $this->assign('menus',$menus);
            $this->assign('parentid',I('get.parentid',0,'intval'));
            $this->display();
        }
	}
	/**
	 * [menu_edit 修改菜单]
	 */
	public function menu_edit(){
		if(!IS_POST){
			if(false === $menus = F('weixin_menu_list')){
                $menus = D('WeixinMenu')->menu_chche();
            }
            $this->assign('menus',$menus);
        }
		$this->_name = 'WeixinMenu';
        $this->edit();
	}
	/**
	 * [menu_del 删除微信菜单]
	 */
	public function menu_del(){
		$this->_name = 'WeixinMenu';//数据表
		parent::delete();//调用父类delete方法
	}
	/**
	 * [menu_sync 同步微信菜单]
	 */
	public function menu_sync(){
		if(true === $reg = \Common\qscmslib\weixin::menu_sync()){
			$this->success('微信菜单同步成功！',U('Admin/menu'));
		}else{
			$this->error($reg,U('Admin/menu'));
		}
	}
	public function bind_list_per(){
		$this->bind_list(2);
	}
	public function bind_list_com(){
		$this->bind_list(1);
	}
	/**
	 * [bind_list 微信用户绑定列表]
	 */
	public function bind_list($utype = false){
        $db_pre = C('DB_PREFIX');
        $this_t = $db_pre.'members_bind';
		$this->_name = 'MembersBind';//数据表
		$where['is_focus'] = 1;
		$where['type'] = 'weixin';
		if($utype){
			$where['m.utype'] = array(array('eq',$utype),array('is',NULL),'or');
		}
		$this->where = $where;
		$this->custom_fun = '_bind_list_after_search';//查询结果后的回调方法
		$this->join = 'left join '.$db_pre."members as m on ".$this_t.".uid=m.uid";
		$this->order = 'is_bind desc';
		$this->_tpl = 'bind_list';
		parent::index();//调用父类index方法
	}
	/**
	 * [bind_list_after_search 绑定用户查询结果回调]
	 */
	public function _bind_list_after_search($list){
		foreach ($list as $key => $val) {
			$list[$key]['info'] = unserialize($val['info']);
			$list[$key]['info']['keyname'] = unicode_emoji($list[$key]['info']['keyname']);
		}
		return $list;
	}
	/**
	 * [msg_list 微信消息列表]
	 */
	public function msg_list(){
		$this->_name = 'WeixinMsgList';
		$where['type'] = 'weixin';
		$this->where = $where;
		$this->pagesize = 10;//列表单页显示多少条数据
		parent::index();
	}
	/**
	 * [msg_send 发送微信消息]
	 */
	public function msg_send(){
		if(IS_POST){
			$openid = I('post.weixin_openid','','trim');
			$content = I('post.content','','trim,badword');

			if(true === $reg = \Common\qscmslib\weixin::send_msg($openid,$content)){
				$this->_name = 'WeixinMsgList'; 
				$this->add();
				$this->returnMsg(1,'发送成功');
			}else{
				$this->returnMsg(0,$reg);
			}
		}else{
			$id = I('get.id',0,'intval');
			$uid = I('get.uid',0,'intval');
			!$id && !$uid && $this->ajaxReturn(0,'请选择用户！');
			$id ? $where['id'] = $id : $where['uid'] = $uid;
			$where['type'] = 'weixin';
			$user = D('MembersBind')->where($where)->find();
			if(!$user || !$user['openid']) $this->ajaxReturn(0,'该用户还没有绑定微信公众号！');
			if($user['uid']){
				$user_info = M('Members')->field('username,utype')->where(array('uid'=>$user['uid']))->find();
				$user = array_merge($user,$user_info);
			}else{
				$user['info'] = unserialize($user['info']);
				$user['username'] = $user['info']['keyname'];
			} 
			$this->assign('userinfo',$user);
            $html = $this->fetch();
            $this->ajaxReturn(1,'获取数据成功！',$html);
		}
	}
	/**
	 * [msg_queue_send 批量发送微信消息]
	 */
	public function msg_queue_send(){
		$ids = I('request.ids');
		if(IS_POST){
			$content = I('post.content','','trim,badword');
			!$content && $this->error('请填写消息内容！');
			if(!fieldRegex($ids,'in')) $this->error('请正确选择微信用户！');
			$data = array('ids'=>explode(',',$ids),'content'=>$content,'url'=>$_SERVER['HTTP_REFERER']);
			F('weixin_queue_cache',$data);
			redirect(U('Weixin/Admin/msg_queue_send',array('queue'=>1),'','',true));
		}else{
            if(!IS_AJAX && I('request.queue',0,'intval')){
            	$data = F('weixin_queue_cache');
            	if(false === $data || !$data['ids']){
            		F('weixin_queue_cache',null);
            		$this->success('任务执行完毕！',$data['url']);
            	}else{
            		$id = array_shift($data['ids']);
            		$count = count($data['ids']);
            		F('weixin_queue_cache',$data);
            		$user = M('MembersBind')->where(array('id'=>$id))->find();
            		$utype = M('Members')->where(array('uid'=>$user['uid']))->getfield('utype');
            		if(true === $reg = \Common\qscmslib\weixin::send_msg($user['openid'],$data['content'])){
						$user['info'] = unserialize($user['info']);
						D('WeixinMsgList')->add(array('uid'=>$user['uid'],'utype'=>$utype?:0,'username'=>$user['info']['keyname'],'content'=>$data['content'],'sendtime'=>time()));
		                $this->success('发送成功，准备发送下一封，剩余任务总数：'.$count,U('msg_queue_send',array('queue'=>1),'','',true));
					}else{
		                $this->error('发生错误，准备发送下一封，剩余任务总数：'.$count,U('msg_queue_send',array('queue'=>1),'','',true));
		            }
            	}
            }else{
	            $this->assign('ids',$ids);
	            $html = $this->fetch();
	            $this->ajaxReturn(1,'获取数据成功！',$html);
            }
		}
	}
	/**
	 * [msg_del 删除微信消息]
	 */
	public function msg_del(){
		$this->_name = 'WeixinMsgList';//数据表
		parent::delete();//调用父类delete方法
	}
	/**
	 * [rule 微信通知规则]
	 */
	public function rule(){
		if(IS_POST){
			$post = I('post.');
			foreach ($post['alias'] as $key => $val) {
				unset($data);
				$data['value'] = $post[$val.'_value'];
				$data['template_id'] = $post['template_id'][$key];
	        	D('WeixinConfig')->where(array('alias' => $val))->save($data);
	        }
	        $this->returnMsg(1,L('operation_success'));
		}else{
			$weixin_config_list = D('WeixinConfig')->select();
			$this->assign('weixin_config_list',$weixin_config_list);
			$this->display();
		}
	}
	/**
	 * [queue 任务列表]
	 */
	public function queue(){
		$this->_name = 'WeixinMsgQueueList';
		$this->pagesize = 10;//列表单页显示多少条数据
		parent::index();
	}
	/**
     * 添加发送任务
     */
    public function queue_add(){
        if(IS_POST){
        	$this->_name = 'WeixinMsgQueueList';
            $content=I('post.content','','trim');
            !$content && $this->error('请填写微信内容');
            mb_strlen($content,'utf-8')>600 && $this->error('微信内容超过600个字，请重新输入！');
            $utype=I('post.utype',0,'intval');
            $where['is_focus'] = 1;
            $where['type'] = 'weixin';
            $where['openid'] = array('neq','');
            if($utype) $where['m.utype'] = $utype;
            $num = 0;
            $result = M('MembersBind')->field('info,openid,m.uid,m.utype')->where($where)->join('left join '. C('DB_PREFIX') .'members m on m.uid='. C('DB_PREFIX').'members_bind.uid')->select();
            foreach ($result as $key => $value) {
            	$info = unserialize($value['info']);
                $smssqlarr['uid']=$value['uid'];
                $smssqlarr['utype']=$value['utype'];
                $smssqlarr['username']=$info['keyname'];
                $smssqlarr['openid']=$value['openid'];
                $smssqlarr['content']=$content;
                $smssqlarr['addtime']=time();
                $smssqlarr['type']=1;
                if(M($this->_name)->add($smssqlarr)) $num++;
            }
            $this->success("成功添加{$num}条任务！");
            exit;
        }else{
            $this->display();
        }
    }
    public function queue_edit(){
    	$this->_name = 'WeixinMsgQueueList';
    	if(IS_POST){
            $content=I('post.content','','trim');
            !$content && $this->error('请填写微信内容');
            mb_strlen($content,'utf-8')>600 && $this->error('微信内容超过600个字，请重新输入！');
            $_POST['type']=1;
            $_POST['content']=$content;
            $_POST['sendtime']=time();
        }
        parent::edit();
    }
    public function send_weixin_queue(){
    	$this->_name = 'WeixinMsgQueueList';
    	if(!I('request.tpl')){
    		$sendtype=I('post.sendtype',1,'intval');
	        $intervaltime=I('post.intervaltime',3,'intval');
	        $sendmax=I('post.sendmax',0,'intval');
	        $senderr=I('post.senderr',0,'intval');
	        if ($sendmax>0)
	        {
	            $limit = $sendmax;
	        }else{
	            $limit = false;
	        }
	        if ($sendtype==1)
	        {
	            $id=I('post.id');
	            if (empty($id))
	            {
	                $this->error("请选择项目！",1);
	            }
	            if(!is_array($id)) $id=array($id);
	            $sqlin=implode(",",$id);
	            if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	            {
	                $select = M($this->_name)->where(array('id'=>array('in',$sqlin)));
	                if($limit){
	                    $select = $select->limit($limit);
	                }
	                $result = $select->select();
	                $idarr = array();
	                foreach ($result as $key => $value) {
	                    $idarr[] = $value['id'];
	                }
	                if (empty($idarr))
	                {
	                    $this->error("没有可发送的微信消息");
	                }
	                file_put_contents(RUNTIME_PATH."Temp/sendweixin.txt", serialize($idarr));
	                redirect(U('Weixin/Admin/send',array('senderr'=>$senderr,'intervaltime'=>$intervaltime),'','',true));
	            }
	            
	        }
	        elseif ($sendtype==2)
	        {
	            $select = M($this->_name)->where(array('type'=>1));
	            if($limit){
	                $select = $select->limit($limit);
	            }
	            $result = $select->select();
	            $idarr = array();
	            foreach ($result as $key => $value) {
	                $idarr[] = $value['id'];
	            }
	            if (empty($idarr))
	            {
	                $this->error("没有可发送的微信消息");
	            }
	            @file_put_contents(RUNTIME_PATH."Temp/sendweixin.txt", serialize($idarr));
	            redirect(U('send',array('senderr'=>$senderr,'intervaltime'=>$intervaltime),'','',true));
	        }
	        elseif ($sendtype==3)
	        {
	            $select = M($this->_name)->where(array('type'=>3));
	            if($limit){
	                $select = $select->limit($limit);
	            }
	            $result = $select->select();
	            $idarr = array();
	            foreach ($result as $key => $value) {
	                $idarr[] = $value['id'];
	            }
	            if (empty($idarr))
	            {
	                $this->error("没有可发送的微信消息");
	            }
	            @file_put_contents(RUNTIME_PATH."Temp/sendweixin.txt", serialize($idarr));
	            redirect(U('send',array('senderr'=>$senderr,'intervaltime'=>$intervaltime),'','',true));
	        }
    	}else{
    		$ids = I('request.id');
	        if(!$ids) $this->ajaxReturn(0,'请选择项目！');
	        $this->assign('ids',$ids);
	        $html = $this->fetch();
	        $this->ajaxReturn(1,'获取数据成功！',$html);
    	}
    }
    /**
     * 执行发送
     */
    public function send(){
    	$this->_name = 'WeixinMsgQueueList';
        $sendtype=I('get.sendtype',1,'intval');
        $intervaltime=I('get.intervaltime',3,'intval');
        $senderr = I('get.senderr',0,'intval');
        $tempdir=RUNTIME_PATH."Temp/sendweixin.txt";
        $content = file_get_contents($tempdir);
        $idarr = unserialize($content);
        $totalid=count($idarr);
        if (empty($idarr))
        {
            $this->success('任务执行完毕！',U('queue'));
            exit;
        }
        else
        {
            $m_id=array_shift($idarr);
            @file_put_contents($tempdir,serialize($idarr));
            $weixin = M($this->_name)->where(array('id'=>array('eq',intval($m_id))))->find();
            if(true === $reg = \Common\qscmslib\weixin::send_msg($weixin['openid'],$weixin['content'])){
				M($this->_name)->where(array('id'=>array('eq',intval($m_id))))->save(array('type'=>2,'sendtime'=>time()));
                $this->success('发送成功，准备发送下一封，剩余任务总数：'.($totalid-1),U('send',array('senderr'=>$senderr,'intervaltime'=>$intervaltime),'','',true),$intervaltime);
			}else{
				if ($senderr=="2")
                {
                    $this->error('邮件发送发生错误！',U('queue'));
                }
                else
                {
                    M($this->_name)->where(array('id'=>array('eq',intval($m_id))))->setField('type',3);
                    $this->error('发生错误，准备发送下一封，剩余任务总数：'.($totalid-1),U('send',array('senderr'=>$senderr,'intervaltime'=>$intervaltime),'','',true),$intervaltime);
                } 
			}
        }   
    }
    public function delete_weixin_queue(){
    	if(!I('request.tpl')){
    		$n=0;
	        $deltype=I('post.deltype',0,'intval');
	        $map = false;
	        if ($deltype==1)
	        {
	            $id=I('post.id');
	            if (empty($id))
	            {
	                $this->error("请选择项目！");
	            }
	            if(!is_array($id)) $id=array($id);
	            $sqlin=implode(",",$id);
	            if (preg_match("/^(\d{1,10},)*(\d{1,10})$/",$sqlin))
	            {
	                $map['id'] = array('in',$sqlin); 
	            }
	        }
	        elseif ($deltype==2)
	        {
	            $map['type'] = array('eq',1); 
	        }
	        elseif ($deltype==3)
	        {
	            $map['type'] = array('eq',2); 
	        }
	        elseif ($deltype==4)
	        {
	            $map['type'] = array('eq',3); 
	        }
	        elseif ($deltype==5)
	        {
	            $map['id'] = array('gt',0);
	        }
	        $this->_del($map);
	        $this->success('删除成功！');
    	}else{
    		$ids = I('request.id');
	        if(!$ids) $this->ajaxReturn(0,'请选择项目！');
	        $this->assign('ids',$ids);
	        $html = $this->fetch();
	        $this->ajaxReturn(1,'获取数据成功！',$html);
    	}
    }
    /**
     * 删除公用方法
     */
    protected function _del($map=false){
    	$this->_name = 'WeixinMsgQueueList';
        $model = D($this->_name);
        if($map){
            $model = $model->where($map);
        }
        $model->delete();
    }
    public function red_package(){
        $this->_mod = D('Config');
		if(!IS_POST){
			$count = M('WxpayLog')->count();
			$page = pager($count,10);
			$limit = $page->firstRow.','.$page->listRows;
            $page_html   = $page->fshow(true);
            $list = M('WxpayLog')->order('id desc')->limit($limit)->select();
            $this->assign('list',$list);
            $this->assign('page_html',$page_html);
            $weixin_red_package_amount_str = C('qscms_weixin_red_package_amount');
            if(stripos($weixin_red_package_amount_str,'-')===false){
            	$min = intval($weixin_red_package_amount_str);
            	$max = $min;
            }else{
            	$weixin_red_package_amount_arr = explode("-", $weixin_red_package_amount_str);
            	$min = $weixin_red_package_amount_arr[0];
            	$max = $weixin_red_package_amount_arr[1];
            }
            $this->assign('min',$min);
            $this->assign('max',$max);
		}else{
			$min = I('post.min','','trim');
			$max = I('post.max','','trim');
			if(!$min || !$max){
				$this->returnMsg(0,'请输入发放金额');
			}
			$_POST['weixin_red_package_amount'] = $min.'-'.$max;
		}
		$this->_edit();//调用父类_edit方法
        $this->display();
    }
}
?>