<?php
namespace Mobile\Controller;
use Mobile\Controller\MobileController;
class jobsController extends MobileController
{
    // 初始化函数
    public function _initialize()
    {
        parent::_initialize();
    }
    public function index()
    {
        if(C('PLATFORM') != 'mobile'){
            redirect(U('Home/Jobs/jobs_list','',true,C('qscms_site_domain')));
        }
        $citycategory = I('get.citycategory','','trim');
        $where = array(
            '类型' => 'QS_citycategory',
            '地区分类' => $citycategory
        );
        $classify = new \Common\qscmstag\classifyTag($where);
        $city = $classify->run();
//        var_dump($city);die;
        $this->assign('search_type','jobs'); 
        $this->wx_share();
        $this->show_index_ads();
        $this->display();
    }
    public function ajax_jobs_list(){
        if(IS_AJAX){
            $where = array(
                '搜索类型' => I('get.search_type'),
                '搜索内容' => I('get.search_cont'),
                '显示数目' => 3,
                '分页显示' => 1,
                '关键字' => I('get.key'),
                '职位分类' => I('get.jobcategory'),
                '地区分类' => I('get.citycategory'),
                '行业' => I('get.trade'),
                '日期范围' => I('get.settr'),
                '学历' => I('get.education'),
                '工作经验' => I('get.experience'),
                '工资' => I('get.wage'),
                '职位性质' => I('get.nature'),
                '标签' => I('get.jobtag'),
                '公司规模' => I('get.scale'),
                '营业执照' => I('get.license'),
                '过滤已投递' => I('get.deliver'),
                '排序' => I('get.sort'),
                '合并' => $_COOKIE['com_list'],
                '描述长度' => 100,
                '搜索范围' => I('get.range'),
                '经度' => I('get.lng'),
                '纬度' => I('get.lat'),
                '检测收藏'=>1,
                '检测点赞'=>1
            );
            $jobs_mod = new \Common\qscmstag\jobs_listTag($where);
            $jobs_list = $jobs_mod->run();
//            var_dump($jobs_list);die;
            !$jobs_list['list'] && $this->ajaxReturn(0,'没有更多职位信息！');
            $isfull = $jobs_list['page_params']['nowPage'] >= $jobs_list['page_params']['totalPages'];
            if($jobs_list['page_params']['nowPage'] <= $jobs_list['page_params']['totalPages']){
                $this->assign('jobslist',$jobs_list['list']);
                $data['html'] = $this->fetch('ajax_jobs_list');
            }
            $data['isfull'] = $isfull;
            if(I('get.page') > 1){
                //判断登录会员是否创建简历
                $resume = M('Resume')->where(array('uid'=>C('visitor.uid')))->find();
                if(!$resume){
                    $data['resume'] = true;
                    $msg = '亲爱的会员，填写简历可以让企业快速了解你从而提升你的职场身价。';
                }else{
                     // 后台设置了申请职位要求的简历完整度 且 默认简历完整度不达标
                    if(C('qscms_apply_job_min_percent') && $resume['complete_percent']<C('qscms_apply_job_min_percent')){
                        $data['pid'] = $resume['id'];
                        //$msg = '您的简历完整度只有 '. $resume['complete_percent'] .'%，要求达到 '. C('qscms_apply_job_min_percent').'% 才可以申请，请继续完善吧~';
                        $msg = '您的简历完整度较低，90%的企业更愿意联系完整的简历，完善后可提高8倍的求职效果！';
                    }
                }
            }
            $this->ajaxReturn(1,$msg,$data);
        }
    }

    /**
     * 公司详情
     */
    public function comshow()
    {
        if(C('PLATFORM') != 'mobile'){
            redirect(U('Home/Jobs/com_show',array('id'=>intval($_GET['id'])),true,C('qscms_site_domain')));
        }

        // 获取访问者简历
        $visitor_resume_id = (int)M('Resume')->where(array('uid' => C('visitor.uid')))->order('def desc')->getField('id');
        $this->assign('visitor_resume_id', $visitor_resume_id);

        $this->wx_share();
        $config = D('Config')->config_cache();
        if($config['qscms_m_template_dir'] == 'qichen'){
            $this->display('comshow1');
        }else{
            $this->display();
        }
        
    }

    /**
     * 职位详情
     */
    public function show()
    {   
	    if(C('PLATFORM') != 'mobile'){
            redirect(U('Home/Jobs/jobs_show',array('id'=>intval($_GET['id'])),true,C('qscms_site_domain')));
		}
        $this->wx_share();
        $config = D('Config')->config_cache();
        //dump($config);die;
        if($config['qscms_m_template_dir'] == 'qichen'){
            $this->display('show1');
        }else{
            $this->display();
        }
        
    }

    /**
     * 企业实地报告
     */
    public function com_report() {
        $id = I('get.id',0,'intval');
        if(C('PLATFORM') != 'mobile'){
            redirect(U('Report/Index/index',array('id'=>$id),true,C('qscms_site_domain')));
        }
        !$id && $this->_empty();
        $where['com_id'] = $id;
        $where['status'] = 1;
        $report = M('CompanyReport')->where($where)->find();
        !$report && $this->_empty();
        $report['img'] && $report['img_arr'] = array_slice(explode('#',$report['img']),0,5);
        $this->assign('info',$report);
        $this->display();
    }
    /**
     * [like 点赞/取消点赞]
     */
    public function like(){
        $model = D('LikeRecord');
        $data['pid'] = I('get.id',0,'intval');
        $data['uid'] = C('visitor.uid');
        $data['ptype'] = 1;
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
}

?>