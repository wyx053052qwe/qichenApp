<?php 
namespace Mobile\Controller;
use Mobile\Controller\MobileController;
class BindController extends MobileController{
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

        $bind = M('members_zaizhi')->where(['uid'=>$member['uid'],'is_bind'=>1])->find();
        if(!empty($bind)){
        	$this->redirect('index/index');
        }
	}

	public function get_com(){
        $companyname = I("post.companyname");
        $com = M("company_profile");
        $data = $com->where(['companyname'=>$companyname])->field("id,companyname")->find();

        if(empty($data)){
            $this->ajaxReturn(0,'请输入正确的企业名称', '', 'add');
        }else{
            $this->ajaxReturn(1,$data['id'], $data, 'add');
        }
    }

	public function member_binds(){
		if(IS_POST){
			$zaizhi = M('members_zaizhi')->where(['name'=>$_POST['name'],'id_card'=>$_POST['id_card']])->find();
			if(empty($zaizhi)){
				$this->ajaxReturn(0,'检查身份证信息，如果无误，请提交入职申请', '', '');
			}
			if(!empty($zaizhi['lizhi_time'])){
				$this->ajaxReturn(0,'您已离职，请重新申请入职', '', U('shenqing'));
			}
			if($zaizhi['agin_com_id'] == $_POST['com_id']){
				$this->ajaxReturn(0,'您已经离职，请填写其他企业', '', '');
			}
			$zaizhi['com_id'] = $_POST['com_id'];
			$zaizhi['dengji_time'] = time();
			$zaizhi['is_bind'] = 1;
			$zaizhi['lizhi_time'] = 0;
			$zaizhi['gongzi_card'] = $_POST['gongzi_card'];
			$zaizhi['mobile'] = $_POST['mobile'];
			//dump($zaizhi);die;
			$is = M('members_zaizhi')->save($zaizhi);
			if($is){
				$this->ajaxReturn(1,'注册成功', '', U('index/index'));
			}else{
				$this->ajaxReturn(0,'失败', '','');
			}
		}else{

			$this->display('member_binds');
		}
	}

	public function shenqing(){
		$member = $this->visitor->info;
		if(IS_POST){
			
			$_POST['uid'] = $member['uid'];
			$_POST['add_time'] = time();
			$_POST['status'] = 0;
			M('members_zaizhi_shenqing')->add($_POST);
			$this->ajaxReturn(1,'信息已提交', '', U('index/index'));
		}else{
			
			$shenqing = M('members_zaizhi_shenqing')->where(['uid'=>$member['uid']])->find();
			if(!empty($shenqing)){
				$com = M('company_profile')->where(['id'=>$shenqing['com_id']])->field('companyname,id')->find();
				if(!empty($com)){
					$shenqing['companyname'] = $com['companyname'];
				}else{
					$shenqing['companyname'] = '未填写任职企业';
				}
				if($shenqing['status'] == 0){
					$this->error('您已提交过申请了');
				}
			}
			$this->assign('shenqing',$shenqing);
			$this->display('shenqing');
		}
	}
}	
 ?>
