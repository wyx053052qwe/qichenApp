<?php
namespace Jobfair\Controller;
use Common\Controller\FrontendController;
class IndexController extends FrontendController{
	public function _initialize() {
        parent::_initialize();
    }
	public function index(){
		if(!I('get.org','','trim') && C('PLATFORM') == 'mobile' && $this->apply['Mobile']){
			redirect(build_mobile_url(array('c'=>'Jobfair','a'=>'index')));
		}
		$this->display();
	}
	public function jobfair_com(){
		if(!I('get.org','','trim') && C('PLATFORM') == 'mobile' && $this->apply['Mobile']){
			redirect(build_mobile_url(array('c'=>'Jobfair','a'=>'comlist','params'=>'id='.intval($_GET['id']))));
		}
		$this->display();
	}
	public function jobfair_reserve(){
		if(!I('get.org','','trim') && C('PLATFORM') == 'mobile' && $this->apply['Mobile']){
			redirect(build_mobile_url(array('c'=>'Jobfair','a'=>'show','params'=>'id='.intval($_GET['id']))));
		}
		$this->display();
	}
	public function jobfair_retrospect(){
		if(!I('get.org','','trim') && C('PLATFORM') == 'mobile' && $this->apply['Mobile']){
			redirect(build_mobile_url(array('c'=>'Jobfair','a'=>'show','params'=>'id='.intval($_GET['id']))));
		}
		$this->display();
	}
	public function jobfair_show(){
		if(!I('get.org','','trim') && C('PLATFORM') == 'mobile' && $this->apply['Mobile']){
			redirect(build_mobile_url(array('c'=>'Jobfair','a'=>'show','params'=>'id='.intval($_GET['id']))));
		}
		$this->display();
	}
	public function jobfair_traffic(){
		if(!I('get.org','','trim') && C('PLATFORM') == 'mobile' && $this->apply['Mobile']){
			redirect(build_mobile_url(array('c'=>'Jobfair','a'=>'show','params'=>'id='.intval($_GET['id']))));
		}
		$this->display();
	}
	/**
	 * 参会企业页面中ajax加载更多参会企业
	 */
	public function ajax_jobfair_com(){
		$page = I('get.page',0,'intval');
		$start = $page*10;
		$this->assign('start',$start);
		$html = $this->fetch('ajax_jobfair_com');
		if($html){
			$this->ajaxReturn(1,'',$html);
		}else{
			$this->ajaxReturn(0);
		}
	}
	/**
	 * 招聘会首页中ajax加载更多招聘会
	 */
	public function ajax_jobfair_list(){
		$html = $this->fetch('ajax_jobfair_list');
		if($html){
			$this->ajaxReturn(1,'',$html);
		}else{
			$this->ajaxReturn(0);
		}
	}
    /**
     * ajax预定展位弹框
     */
    public function ajax_reserve(){
        $jobfair_id = I('request.jobfair_id',0,'intval');
        !$jobfair_id && $this->ajaxReturn(0,'招聘会id错误！');
        $position_id = I('request.position_id',0,'intval');
        !$position_id && $this->ajaxReturn(0,'展位号id错误！');
        if(!C('visitor')){
            $this->ajaxReturn(0,'请先登录企业会员！','',1);
        } elseif(C('visitor.utype') != 1) {
            $this->ajaxReturn(0,'只有企业会员可以预定！');
        }
        if(IS_POST){
            $setsqlarr = array('contact'=>I('request.contact','','trim'),'mobile'=>I('request.mobile','','trim'));
            $r = D('Jobfair')->jobfair_booth(C('visitor'),$jobfair_id,$position_id,$setsqlarr);
            $this->ajaxReturn($r['state'],$r['msg']);
        }else{
            $position = M('JobfairPosition')->where(array('id'=>$position_id))->getField('position');
            $contact = M('CompanyProfile')->field('contact,telephone,landline_tel')->where(array('uid'=>C('visitor.uid')))->find();
            $contact['mobile'] = $contact['telephone'] ? : trim($contact['landline_tel'],'-');
            $this->assign('jobfair_id',$jobfair_id);
            $this->assign('position_id',$position_id);
            $this->assign('position',$position);
            $this->assign('contact',$contact);
            $html = $this->fetch('ajax_reserve');
            $this->ajaxReturn(1,'获取数据成功',$html);
        }
    }
}
?>