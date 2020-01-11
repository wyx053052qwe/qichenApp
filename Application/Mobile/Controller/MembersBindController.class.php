<?php 
namespace Mobile\Controller;
use Mobile\Controller\MobileController;
class MembersBindController extends MobileController{
	private $bind;
	public function _initialize() {
        
		parent::_initialize();
		if (!$this->visitor->is_login) {
            if (in_array(ACTION_NAME, array('index'))) {
                IS_AJAX && $this->ajaxReturn(0, L('login_please'), '', 1);
                //非ajax的跳转页面
                if(!$this->openid){
                    $this->redirect('members/login');
                }else{
                    $this->redirect('members/apilogin_binding');
                }
            }
        }
        $member = $this->visitor->info;
        //dump($member);die;
        $this->bind = M('members_zaizhi')->where(['uid'=>$member['uid'],'is_bind'=>1])->find();
        if($member['utype'] == '1'){
        	$this->error('您是企业用户，此功能只针对在职人员开放');
        }	

	}

	public function gold_log1(){
		$where['uid']=$this->uid;
		$operate = I('get.operate',1,'intval');
		$where['operate']=$operate;
		$list = D('MembersHandsel')->get_handsel_list($where);
		$get_num = D('MembersHandsel')->where(array('uid'=>$this->uid,'operate'=>1))->sum('points');
		$use_num = D('MembersHandsel')->where(array('uid'=>$this->uid,'operate'=>2))->sum('points');

		$this->assign('operate',$operate);
		$this->assign('list',$list);
		$this->assign('get_num',intval($get_num));
		$this->assign('use_num',intval($use_num));
		$this->_config_seo(array('title'=>C('qscms_points_byname').'收支明细','header_title'=>C('qscms_points_byname').'收支明细'));
		$this->display('Personal/service/gold_log1');
	}

    public function index(){
		session('error_login_count', 0);
		$uid = C('visitor.uid');
        $member = $this->visitor->info;
        if(empty($this->bind)){
            $url = U('Bind/member_binds');
            $msg = "未注册在职人员，请<a href='".$url."'>点击</a>注册";
            $this->error($msg);
        }
		$resume_list = D('Resume')->get_resume_list(array('where' => array('uid' => $uid), 'limit' => 1, 'order' => 'def desc', 'countinterview' => true, 'countdown' => true, 'countapply' => true, 'views' => true, 'stick' => true, 'feedback' => true));
		$this->assign('points', D('MembersPoints')->get_user_points($uid));//当前用户积分数
		$msg = D('Pms')->update_pms_read(C('visitor'), 10,$map);
		$this->assign('msg_count',$msg['count']);
        $list = $this->get_huodong_list();
        $this->assign('list',$list['jiesu']);
        $this->display('index');
    }


	public function gongzi(){
		$member = $this->visitor->info;
		$bind = M('members_zaizhi')->where(['uid'=>$member['uid']])->find();
		if(empty($bind)){
			$this->redirect('Bind/member_binds');
		}
		$com_id = $bind['com_id'];
		if(empty($bind['is_bind'])){
			if(empty($bind['lizhi_time'])){
				$this->success('请先注册在职人员信息',U('Bind/member_binds'));
			}else{
				$year = date("Y",$bind['lizhi_time']);
				$mon = date("m",$bind['lizhi_time']);
				$mon = $mon + 2;
				$d = $year . "-" . $mon . "-" .'1';
				$t = strtotime($d);
				if($t<time()){
					
					$this->error('您已离职，此功能只针对在职人员开放');
				}else{
					$com_id = $bind['agin_com_id'];
				}
			}	
		}

		$config = $this->get_gongzi_config($com_id);
		$list = $this->get_gongzi_list($member['uid'],$bind['id']);

		$this->assign('config',$config);
		$this->assign('list',$list);
		$this->display('gongzi');
		
	}

	public function see_gongzi($id){
        $members = $this->visitor->info;
        //dump($member);die;
        $member = M('members_zaizhi')->where(['uid'=>$members['uid']])->find();
		$data = M("members_gongzi")->where(['id'=>$id])->find();
		$data['data'] = json_decode($data['data'],true);
		$config = $this->get_gongzi_config($data['com_id']);
		
		foreach ($data['data'] as $key => &$value) {
			$value['en'] = $config['config'][$key]['cn'];
			if(empty($value['value'])){
				$value['value'] = '未填写';
			}
		}
        $this->assign('member',$member);
		$this->assign('config',$config);
		$this->assign('data',$data);
		//dump($data);die;
		$this->display('see_gongzi1');
	}

	private function get_gongzi_list($uid,$zaizhi_id){
		$list = M('members_gongzi')->where(['uid'=>$uid,'zaizhi_id'=>$zaizhi_id])->select();
		return $list;
	}
	private function get_gongzi_config($com_id){
        $data = M('company_gongzi')->where(['com_id'=>$com_id])->find();
        $company = M('company_profile')->where(['id'=>$com_id])->field('id,companyname')->find();
        $data['companyname'] = $company['companyname'];
        $data['config'] = json_decode($data['config'],true);
        return $data;
    }

    public function zhengming(){
    	$member = $this->visitor->info;
    	if(empty($this->bind)){
    		$url = U('Bind/member_binds');
    		$msg = "未注册在职人员，请<a href='".$url."'>点击</a>注册";
    		$this->error($msg);
    	}
    	if(IS_POST){
//            dump($_FILES);die;
    		$file = $this->uploadForm();
//    		var_dump($file);die;
			$img = [];
			$files = [];
			foreach($file as $k=>$v){
				$str=strtolower(pathinfo($v['src'], PATHINFO_EXTENSION));
				 if($str == 'jpg' || $str == 'png' || $str == 'jpeg'){
				 	$img[]=['src'=>$v['src']];
				 }else{
				 	$files[] = ['src'=>$v['src']];
				 }
			}
    		
    		if(empty($_POST['baodan'])){
    			$this->error('请选择证明类型');
    		}
    		if(empty($_POST['name'])){
    			$this->error('请填写姓名');
    		}
    		if(empty($_POST['id_card'])){
    			$this->error('请填写身份证号码');
    		}
    		if(empty($_POST['address'])){
    			$this->error('请选择收获地址');
    		}
            if(empty($_POST['xiangxidizhi'])){
                $this->error('请输入详情地址');
            }
    		if(empty($_POST['mobile'])){
    			$this->error('请输入手机号');
    		}
            $_POST['address'] = $_POST['address'] . " " . $_POST['xiangxidizhi'];
            $_POST['leixing'] = $_POST['baodan'];
            unset($_POST['xiangxidizhi']);
            unset($_POST['baodan']);
    		$_POST['add_time'] = time();
    		$_POST['img'] = json_encode($img);
    		$_POST['files'] = json_encode($files);
//    		var_dump($_POST['files']);die;
    		$_POST['uid'] = $member['uid'];
//			var_dump($files);die;
    		$is = M('members_zaizhi_zhengming')->add($_POST);
    		if($is){
    			$this->success('申请成功');
    		}else{
    			$this->error('申请失败');
    		}

    	}else{

    		$this->display('zhengming1');
    	}
    }

    private function uploadForm(){

    	$upload = new \Common\ORG\UploadFile();

	    // 上传文件
	    $info   =   $upload->upload("data/upload/zhengming/");
//	    var_dump($info);die;
	    if(!$info){
	    	$this->error('请上传证明');
	    }
	    $arr = $upload->getUploadFileInfo();
	    $new_arr = [];
	    foreach ($arr as $key => $value) {
	    	$new_arr[$key]['src'] = "/".$value['savepath'].$value['savename'];
	    }
//	    var_dump($new_arr);die;
	    return $new_arr;
    }

    public function huodong_list(){
    	$member = $this->visitor->info;
    	if(empty($this->bind)){
    		$url = U('Bind/member_binds');
    		$msg = "未注册在职人员，请<a href='".$url."'>点击</a>注册";
    		$this->error($msg);
    	}
    	$zaizhi_id = $this->bind['id'];
    	$data = $this->get_huodong_list();
    	
    	$this->assign('list',$data['jinxing']);
    	
    	$this->display('huodong_list1');
    }

    public function get_huodong_list(){
        $huodong = M('members_zaizhi_huodong');
        
        $list = $huodong->order('add_time DESC')->field('id,name,img,add_time,start_time,end_time,descript')->select();
        
        $arr['jiesu'] = [];
        $arr['jinxing'] = [];
        foreach ($list as $key => &$value) {
            $value['url'] = U('huodong_show',array('id'=>$value['id']));
            $value['count'] = $this->get_huodong_renshu($value['id']);
            if(time()>$value['start_time']){
                if(count($arr['jiesu'])<3){
                    array_push($arr['jiesu'],$value);
                }
                
            }else{
                array_push($arr['jinxing'],$value);
            }
        }
        return $arr;
    }

    public function get_huodong_renshu($huodong_id){
    	$count = M('members_zaizhi_huodong_baoming')->where(['huodong_id'=>$huodong_id])->count();
    	return $count;
    }

    public function huodong_show(){

    	$id = I('get.id');
    	$data = M('members_zaizhi_huodong')->where(['id'=>$id])->find();
    	$data['count'] = $this->get_huodong_renshu($id);
    	$data['header_title'] = $data['name']."报名啦！！";
    	$data['seo_keywords'] = $data['name'];
    	$data['seo_description'] = $data['descript'];
    	$data['img'] = "/data/upload/images/".$data['img'];
    	
    	
    	if(time()>$data['start_time']){
    		$this->assign('is_bao',0);
    	}else{
    		$this->assign('is_bao',1);
    	}
    	
    	$this->assign('info',$data);
    	$this->wx_share();
    	$this->display('huodong_show1');
    }

    public function baoming(){
    	$data['huodong_id'] = I("get.id");
    	$zaizhi = $this->bind;
    	$member = $this->visitor->info;
    	$data['zaizhi_id'] = $zaizhi['id']; 
    	$data['uid'] = $member['uid'];
    	$data['add_time'] = time();
    	$baoming = M('members_zaizhi_huodong_baoming');
    	$is_bao = $baoming->where(['huodong_id'=>$data['huodong_id'],'uid'=>$member['uid']])->find();
    	if(!empty($is_bao)){
    		$this->ajaxReturn(0,'您已报过了');
    	}
    	//dump($data);die;
    	$is = $baoming->add($data);
    	if($is){
    		$this->ajaxReturn(1,'报名成功');
    	}else{
    		$this->ajaxReturn(0,'服务器失败');
    	}
    }

    public function lizhi(){
    	$member = $this->visitor->info;
    	if(empty($this->bind)){
    		$url = U('Bind/member_binds');
    		$msg = "未注册在职人员，请<a href='".$url."'>点击</a>注册";
    		$this->error($msg);
    	}

    	if(IS_POST){
    		if(empty($_POST['descript'])){
    			$this->error('填写离职原因');
    		}
            if(empty($_POST['name'])){
                $this->error('请填写姓名');
            }
            if(empty($_POST['mobile'])){
                $this->error('请填写手机号');
            }
            if(empty($_POST['id_card'])){
                $this->error('请填写身份证号码');
            }
    		$file = $this->uploadForm();
    		$data['img'] = json_encode($file);
    		$data['descript'] = $_POST['descript'];
    		$data['zaizhi_id'] = $this->bind['id'];
    		$data['add_time'] = time();
    		//dump($data);die;
    		M('members_zaizhi_lizhi')->add($data);
    		$this->success('提交成功');
    	}else{
    		$this->assign('zaizhi',$this->bind);
    		$this->display('lizhi1');
    	}
    }

    public function hetong(){
        $member = $this->visitor->info;
        if(empty($this->bind)){
            $url = U('Bind/member_binds');
            $msg = "未注册在职人员，请<a href='".$url."'>点击</a>注册";
            $this->error($msg);
        }
        $com = M('company_profile')->field('companyname,id')->where(['id'=>$this->bind['com_id']])->find();
        $this->assign('name',$this->bind['name']);
        $this->assign('companyname',$com['companyname']);
        $this->assign('ht_time',date("Y-m-d",$this->bind['ht_time']));
        $this->assign('zaizhi',$this->bind);
        $this->display('hetong');
    }

    public function tongzhi_true(){
        $id = I('get.id');
        M('members_zaizhi')->where(['id'=>$id])->save(['is_send'=>1]);
        $this->success('开启通知成功');
    }

    public function tongzhi_false(){
        $id = I('get.id');
        M('members_zaizhi')->where(['id'=>$id])->save(['is_send'=>0]);
        $this->success('关闭通知成功');
    }
  
  	public function xuexi(){
    	$member = $this->visitor->info;
        if(empty($this->bind)){
            $url = U('Bind/member_binds');
            $msg = "未注册在职人员，请<a href='".$url."'>点击</a>注册";
            $this->error($msg);
        }
      $url = "http://kaoshi.qichenhr.xyz/app/index.php?i=2&c=entry&eid=25";
      	Header("Location:$url"); 
      	
    }
}

 ?>
