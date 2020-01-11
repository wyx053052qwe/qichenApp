<?php
namespace Mobile\Controller;

use Mobile\Controller\MobileController;

class ResumeController extends MobileController
{
    // 初始化函数
    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        if(C('PLATFORM') != 'mobile'){
            redirect(U('Home/Resume/resume_list','',true,C('qscms_site_domain')));
        }
        $citycategory = I('get.citycategory','','trim');
        $where = array(
            '类型' => 'QS_citycategory',
            '地区分类' => $citycategory
        );
        $classify = new \Common\qscmstag\classifyTag($where);
        $city = $classify->run();
        $this->show_index_ads();
        $this->wx_share();
        $this->display(); 
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
    public function ajax_resume_list(){
        if(IS_AJAX){
            $where = array(
                '搜索类型' => I('get.search_type'),
                '显示数目' => 3,
                '分页显示' => 1,
                '关键字' => I('get.key'),
                '职位分类' => I('get.jobcategory'),
                '地区分类' => I('get.citycategory'),
                '日期范围' => I('get.settr'),
                '学历' => I('get.education'),
                '工作经验' => I('get.experience'),
                '工资' => I('get.wage'),
                '工作性质' => I('get.nature'),
                '标签' => I('get.resumetag'),
                '照片' => I('get.photo'),
                '所学专业' => I('get.major'),
                '行业' => I('get.trade'),
                '年龄' => I('get.age'),
                '性别' => I('get.sex'),
                '特长描述长度' => 100,
                '排序' => I('get.sort'),
                '检测收藏'=>1,
                '检测点赞'=>1
            );
            $resume_mod = new \Common\qscmstag\resume_listTag($where);
            $resume_list = $resume_mod->run();
            $isfull = $resume_list['page_params']['nowPage'] >= $resume_list['page_params']['totalPages'];
            if($resume_list['page_params']['nowPage'] <= $resume_list['page_params']['totalPages']){
                $this->assign('resumelist',$resume_list['list']);
                $data['html'] = $this->fetch('ajax_resume_list');
            }
            $data['isfull'] = $isfull;
            if(I('get.page' > 1)){
                //当前用户的企业信息 
                    $this->company_profile = M('CompanyProfile')->where(array('uid' => C('visitor.uid')))->find();
                    if ($this->company_profile) {
                        // 判断是否需要完善信息
                        $this->cominfo_flge = true;
                        $array = array("companyname", "nature", "trade", "district", "scale", "address", "contact", "contents");
                        foreach ($this->company_profile as $key => $value) {
                            if (in_array($key, $array) && empty($value)) {
                                $this->cominfo_flge = false;
                                break;
                            }
                        }
                    } else {
                        $this->cominfo_flge = false;
                    }
                    if($this->cominfo_flge === false){
                        $data['url'] = U('company/com_info');
                        $msg = '为了达到更好的招聘效果，请先完善您的企业资料！';
                    }else{
                        $jobs = M('Jobs')->where(array('uid' => C('visitor.uid')))->select();
                        if(!$jobs){
                            $data['url'] = U('company/jobs_add');
                            $msg = '您还没发布职位，请先发布招聘信息!';
                        }
                    }
            }
            $this->ajaxReturn(1,$msg,$data);
        }
    }
    /**
     * 简历详情
     */
    public function show()
    {   
        if(C('PLATFORM') != 'mobile'){
            redirect(U('Home/Resume/resume_show',array('id'=>intval($_GET['id'])),true,C('qscms_site_domain')));
        }
        $this->wx_share();
        $this->display();
    }
    /**
     * [like 点赞/取消点赞]
     */
    public function like(){
        $model = D('LikeRecord');
        $data['pid'] = I('get.id',0,'intval');
        $data['uid'] = C('visitor.uid');
        $data['ptype'] = 4;
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