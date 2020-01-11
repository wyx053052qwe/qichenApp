<?php
namespace Mobile\Controller;
use Mobile\Controller\MobileController;
class IndexController extends MobileController{
	// 初始化函数
	public function _initialize(){
		parent::_initialize();
        if (!$this->visitor->is_login) {
            if (in_array(ACTION_NAME, array('index'))) {
                IS_AJAX && $this->ajaxReturn(0, L('login_please'), '', 1);

                if(!$this->openid){
                    $this->redirect('members/login');
                }else{
                    $this->redirect('members/apilogin_binding_select'); 
                }
            }
        }
	}
	public function index(){
		$uid = I('request.uid', '', 'trim');
        //dump($this->visitor->info);die;
		$user = $this->visitor->info;
		$this->show_index_jobs();
		$this->show_index_total();
		$this->show_index_ads();
        $this->wx_share();
        $this->recommend_jobs_index('recommend_jobs'); 
        if($this->visitor->is_login){
            $uid = $this->visitor->info['uid'];
        	$url = build_mobile_url(array('c'=>'Index','a'=>'index','params'=>"uid=".$uid));
        	
        }else{
        	$url = build_mobile_url(array('c'=>'Index','a'=>'index'));
        }
        
        $this->assign('url',$url);
        $config = D('Config')->config_cache();
        //$this->display();die;
        $jineng = M('jineng')->where(['is_show'=>1])->limit(3)->select();
        foreach ($jineng as $key => &$value) {
            $value['count'] = $this->get_jineng_count($value['id']);
        }
        $this->assign('jineng',$jineng);
        if($config['qscms_m_template_dir'] == 'qichen'){
            if($user['utype'] == 1){
                $this->display('index_1');
            }else{
                $this->display('index_2');
            }
        }else{
            $this->display();
        }
        

		
	}

    public function sign(){
        $user = $this->visitor->info;
        $mon_time = strtotime(date("Y-m-01"));
        $where['addtime'] = ['EGT',$mon_time];
        $where['uid'] = $user['uid'];
        $where['taskid'] = 3;
        //dump($where);die;
        $sign_list = M('task_log')->where(['uid'=>$user['uid'],'taskid'=>3])->where($where)->order('addtime DESC')->select();
        $arr = [];
        $arr['day'] = [];
        $arr['is_sign'] = 'false';
        $if_day = 100;
        $count = 0;
        foreach ($sign_list as $key => $value) {
            $day = date("d",$value['addtime']);
            $arr['day'][$key] = ['signDay'=>$day];
            if(intval($day)-intval($if_day) == -1){
                $count++;
            }
            $if_day = $day;
            if($day == date("d",time())){
                $arr['is_sign'] = 'true';
            }
        }
        $arr['count'] = $count + 1;

        $arr['json_day'] = json_encode($arr['day']);
        
        $lianxu[0] = [
            'count'=>5,
            'points'=>10,
        ];
        $lianxu[1] = [
            'count'=>10,
            'points'=>20,
        ];
        $lianxu[2] = [
            'count'=>15,
            'points'=>30,
        ];
        $lianxu[3] = [
            'count'=>20,
            'points'=>40,
        ];
        $lianxu[4] = [
            'count'=>25,
            'points'=>50,
        ];
        $lianxu[5] = [
            'count'=>30,
            'points'=>60,
        ];

        foreach ($lianxu as $key => &$value) {
            if($value['count']<=$arr['count']){
                $value['cn'] = "领取";
                $value['class'] = "btn-lingqu";
                $value['disabled'] = "";
            }else{
                $value['cn'] = "领取";
                $value['class'] = "btn-disable";
                $value['disabled'] = "disabled";   
            }
            $year_mon = date("Y-m");
            $lianxu_arr = M('sign_lianxu')->where(['time'=>$year_mon,'uid'=>$user['uid'],'count'=>$value['count']])->find();
            if(!empty($lianxu_arr)){
                $value['cn'] = "已领取";
                $value['class'] = "btn-disable";
                $value['disabled'] = "disabled";
            }
        }
        $this->assign('lianxu',$lianxu);
        $this->assign('arr',$arr);
        $this->display();
    }

    public function sign_lianxu(){
        $count = I('get.count');
        $points = I('get.points');
        $user = $this->visitor->info;
        if(empty($count)){
            $this->ajaxReturn(0,"参数传递错误");
        }

        if(empty($points)){
            $this->ajaxReturn(0,"参数传递错误");
        }

        $lianxu['count'] = $count;
        $lianxu['time'] = date("Y-m",time());
        $lianxu['uid'] = $user['uid'];

        $jifen_num = intval($points);
        $uid = intval($user['uid']);
        $jifen = D("MembersPoints");
        $jifen->report_deal($uid,1,$jifen_num);
        $cn = "连续签到".$count."天礼包，"."获得".$jifen_num."积分";
        $is = M('members_handsel')->add(['uid'=>$uid,'htype'=>'sign','htype_cn'=>$cn,'operate'=>1,'points'=>$jifen_num,'addtime'=>time()]);
        $msg = "领取礼包成功，获得".$jifen_num."积分";
        if($is){
            M('sign_lianxu')->add($lianxu);
            $this->ajaxReturn(1,$msg,'','');
        }else{
            $this->ajaxReturn(0,'领取失败','','');
        }
    }

    public function shuoming(){
        $this->display();
    }

    public function get_jineng_count($id){
        $list = M('jineng_baoming')->where(['jineng_id'=>$id])->field('id')->select();
        return count($list);
    }

    public function jineng(){
        $cate_id = I('get.cate_id');
        $keywords = I('get.keywords');
       
        $is_show = I('get.is_show');
        $is_display = I('get.is_display');
        if(!empty($keywords)){
            $where['name'] = array('like',"%".$keywords."%");
            $this->assign('keywords',$keywords);
        }
        if(!empty($cate_id)){
            $where['cate_id'] = $cate_id;
        }
        if(!empty($is_show)){
            $where['is_show'] = $is_show;
        }

        if(!empty($is_display)){
            $where['is_show'] = $is_display;
        }
        $category = M('jineng_cate')->order('add_time DESC')->select();
        $this->assign('category',$category);

        $list = M('jineng')->where($where)->order('add_time DESC')->select();
        foreach ($list as $key => &$value) {
            $value['count'] = $this->get_jineng_count($value['id']);
        }
        //dump($list);die;
        $this->assign('list',$list);
        $this->display();
    }

    public function baoming(){
        $user = $this->visitor->info;
        $id = I('get.id');
        if(empty($id)){
            $this->error('参数错误！');
        }
        $is = M('jineng_baoming')->where(['uid'=>$user['uid'],'jineng_id'=>$id])->find();
        if(!empty($is)){
            $this->error('您已报名');
        }
        $jineng = M('jineng')->where(['id'=>$id])->find();
        if($jineng['is_display'] == 0){
            $this->error('报名已截至');
        }

        if(!empty($jineng['money'])){
            $this->baoming_order($jineng);
        }

        $data['uid'] = $user['uid'];
        $data['jineng_id'] = $jineng['id'];
        $data['add_time'] = time();
        $data['descript'] = "免费报名";
        M('jineng_baoming')->add($data);
        $this->success('报名成功');

    }

    private function baoming_order($jineng){

    }

    public function jineng_show(){
        $id = I('get.id');
        if(empty($id)){
            $this->error('此技能学习已经删除');
        }
        
        $model = M('jineng');
        $data = $model->where(['id'=>$id])->find();
        $data['count'] = $this->get_jineng_count($data['id']);
        $model->where(['id'=>$id])->save(['click'=>$data['click']+1]);
        $this->assign('data',$data);
        $this->display();
    }

   
	/**
	 * 首页统计
	 */
	public function show_index_total(){
		$total['company'] = D('CompanyProfile')->count();
		$total['job'] = D('Jobs')->count();
		$total['resume'] = D('Resume')->count();
		$this->assign('index_total',$total);
	}
  	public function tuijian(){
		if (!$this->visitor->is_login) {
            if (in_array(ACTION_NAME, array('index'))) {
                IS_AJAX && $this->ajaxReturn(0, L('login_please'), '', 1);

                if(!$this->openid){
                    $this->redirect('members/login');
                }else{
                    $this->redirect('members/apilogin_binding');
                }
            }
        }
        
        if($this->visitor->info['utype'] == '1'){
        	$this->redirect('company/tuijian');
        }else{
        	$this->redirect('PersonalService/tuijian');
        }
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
		$index_focus = D('AdMobile')->get_ad_list($index_focus_where);
		$this->assign('index_focus',$index_focus);
        //dump($index_focus);die;
		$this->assign('index_block',$index_block);
	}
	/**
	 * 首页显示推荐职位
	 */
	protected function show_index_jobs(){
		//已登录，千人千面是否开启
		if(C('qscms_mobile_index_login_recommend')==1 && C('visitor.uid')){
			$type = 'recommend_jobs';
		}else{
			$type = C('qscms_mobile_index_jobs_type').'_jobs';
		}
		if($type=='nearby_jobs'){
			$recommend_jobs_nearby = 1;
		}else{
			$recommend_jobs_nearby = 0;
		}
		$this->assign('recommend_jobs_nearby',$recommend_jobs_nearby);
        
		$this->assign('recommend_jobs_type',$type);
	}
    public function recommend_jobs_index($type=''){
        $type = $type ? $type : I('get.type','','trim,badword');
        !$type && IS_AJAX && $this->ajaxReturn(0,'数据类型错误！');
        if(!in_array($type, array('recommend_jobs','nearby_jobs','new_jobs','allowance_jobs'))) $this->ajaxReturn(0,'数据类型错误！');
        $where = array(
            '显示数目' => '3'
        );
        if($type=='recommend_jobs'){
            $jobcategory = M('Resume')->where(array('uid'=>C('visitor.uid')))->getField('intention_jobs_id');
            $where['职位分类'] = $jobcategory;
            $where['排序'] = 'stickrtime';
            $title = "推荐职位";
            $more_url = url_rewrite('QS_jobslist');
        }elseif($type=='nearby_jobs'){
        	$lng = I('get.lng','0','trim,badword');
        	$lat = I('get.lat','0','trim,badword');
            $where['经度'] = $lng;//112.732929
            $where['纬度'] = $lat;//37.714684
            $where['搜索范围'] = 20;
            $where['分页显示'] = 1;
            $title = "附近职位";
            $more_url = url_rewrite('QS_jobslist',array('lng'=>$lng,'lat'=>$lat,'range'=>20));
        }
        elseif($type=='allowance_jobs'){
        	$where['搜索内容'] = 'allowance';
            $title = "红包职位";
            $more_url = url_rewrite('QS_jobslist');
        }else{
            $where['排序'] = 'rtime';
            $title = "最新职位";
            $more_url = url_rewrite('QS_jobslist');
        }
        $jobs_mod = new \Common\qscmstag\jobs_listTag($where);
        $jobs_list = $jobs_mod->run();
        if(empty($jobs_list['list'])){
        	unset($where['职位分类']);
        	$where['排序'] = 'rtime';
            $title = "最新职位";
            $more_url = url_rewrite('QS_jobslist');
            $jobs_mod = new \Common\qscmstag\jobs_listTag($where);
        	$jobs_list = $jobs_mod->run();
        }
        $this->assign('title',$title);
        $this->assign('more_url',$more_url);
        $this->assign('jobs_list',$jobs_list['list']);
        //dump($jobs_list);die;
        if(IS_AJAX){
            $data['html'] = $this->fetch('Ajax_tpl/recommend_jobs_index');
            $this->ajaxReturn(1,'职位信息获取成功！',$data);
        }
    }
	public function compatibility(){
		$this->display();
	}
	public function app_download(){
		$page_seo['title'] = 'APP下载 - '.C('qscms_site_name');
		$this->assign('page_seo',$page_seo);
		$this->display();
	}
	public function ajax_jump_nearby($url,$lat,$lng){
		$range = strstr($url,'range');
		$range = ltrim($range,'range=');
		$range = ltrim($range,'range/');
		$this->ajaxReturn(1,'success',U('Mobile/Jobs/index',array('range'=>$range,'lat'=>$lat,'lng'=>$lng)));
	}
}
?>