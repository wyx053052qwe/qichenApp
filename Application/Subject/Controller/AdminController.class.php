<?php
namespace Subject\Controller;

use Common\Controller\ConfigbaseController;

class AdminController extends ConfigbaseController {

    public function _initialize() {
        parent::_initialize();
        $this->_mod = D('Subject');
    }
    public function index(){
        $this->_name = 'Subject';
        $this->order = 'article_order desc, addtime desc';
        parent::index();
    }
    public function add(){
        $this->_name = 'Subject';
        parent::add();
    }
    public function edit(){
        $this->_name = 'Subject';
        parent::edit();
    }
    public function delete(){
        $this->_name = 'Subject';
        parent::delete();
    }
    public function subject_company(){
        $subject_id = I('get.id',0,'intval');
        $subject_title = D('Subject')->where(array('id'=>$subject_id))->field('title')->find();
        if($settr=I('get.settr',0,'intval')){
                $where['addtime']=array('gt',strtotime("-".$settr." day"));
        }
        $where['subject_id']=$subject_id;
        $company_uid = D('SubjectCompany')->where($where)->select();
        $info =array();
        foreach ($company_uid as $key => $value) {
            $companyname =D('CompanyProfile')->where(array('uid'=>$value['company_uid']))->field('companyname')->find();
            $row['companyname'] = $companyname['companyname'];
            $row['addtime'] =$value['addtime'];
            $row['title'] = $subject_title['title'];
            $row['id'] = $value['id'];
            $info[]= $row;
        }
        $this->assign('info',$info);
        $this->display();
    }
    public function subject_company_add(){
        if (IS_POST) {
            $post_data['company_uid'] = I('post.uid',0,'intval');
            $post_data['subject_id'] = I('post.subject_id',0,'intval');
            $company = D('SubjectCompany')->where(array('company_uid'=>$post_data['company_uid'],'subject_id'=>$post_data['subject_id']))->select();
            if($company){
                $this->error('此企业已添加过此网络招聘会专题，请重新添加！');
            }
            if (false === $reg = D('SubjectCompany')->create($post_data)) {
                $this->error(D('SubjectCompany')->getError());
            } else {
                if (false === $insert_id = D('SubjectCompany')->add($reg)) {
                    $this->error(D('SubjectCompany')->getError());
                } else {
                    $this->success('添加成功！');
                }
            }
        } else {
            $this->assign('subject_id',I('get.subject_id',0,'intval'));
            $this->display();
        }
    }
    public function subject_company_delete(){
        $this->_name = 'SubjectCompany';
        parent::delete();
    }
    /**
     * [_before_search 查询条件]
     */
    public function _before_search($data){
        $this->_name = 'Subject';
        $key_type = I('request.key_type',0,'intval');
        $key = I('request.key','','trim');
        if($key_type && $key){
            switch ($key_type){
                case 1:
                    $data['title'] = array('like','%'.$key.'%');
                    break;
                case 2:
                    $data['id'] = intval($key);
                    break;
            }
        }else{
            if($settr=I('get.settr',0,'intval')){
                $data['addtime']=array('gt',strtotime("-".$settr." day"));
            }
        }
        return $data;
    }
    /**
     * [_before_add 添加专题招聘会]
     */
    public function _before_add(){
        if(IS_POST){
             $_POST['addtime'] = time();
            
        }else{
            $result = D('Tpl')->where(array('tpl_type'=>3))->select();
            $list = array();
            foreach ($result as $key => $value) {
                $list[] =$value;
            }
            $this->assign('list',$list);
        }

    }
      /**
     * [_before_edit 修改专题信息]
     */
    public function _before_edit(){
        $this->_before_add();
    }
    /**
     * [_before_update 加粗是否有值]
     */
    public function _before_update($data){
        $data['tit_b'] = $data['tit_b']?1:0;
        return $data;
    }
    /**
     * [del_img 删除缩略图]
     */
    public function del_img(){
        $id = I('get.id',0,'intval');
        $small_img = D('Article')->where(array('id'=>$id))->getfield('small_img');
        false === $small_img && $this->error('新闻不存在或已经删除！');
        if($small_img){
            $reg = D('Article')->where(array('id'=>$id))->setfield('small_img','');
            if(false !== $reg){
                @unlink(C('qscms_attach_path')."images/".$small_img);
            }else{
                $this->error('缩略图删除失败，请重新操作！');
            }
        }
        $this->success('缩略图删除成功！');
    }

    /**
     * ajax获取职位
     */
    public function ajax_get_jobs(){
        $type=I('get.type','','trim');
        $key=I('get.key','','trim');
        switch($type){
            case 'get_comname':
                $where = array('companyname'=>array('like','%'.$key.'%'));
                $limit = 30;
                break;
            case 'get_uid':
                $uid=intval($key);
                $where = array('uid'=>array('eq',$uid));
                $limit = 30;
                break;
        }
        if($this->apply['Subsite']){
            $field = D('Jobs')->getDbFields();
            if(in_array('subsite_id',$field) && C('visitor.subsite')){
                $where['subsite_id'] = array('in',C('visitor.subsite'));
            }
        }
        $result = D('Jobs')->where($where)->limit($limit)->select();
        $info = array();
        foreach ($result as $key => $value) {
            $value['addtime']=date("Y-m-d",$value['addtime']);
            $value['deadline']=$value['deadline']==0?'无限期':date("Y-m-d",$value['deadline']);
            $value['refreshtime']=date("Y-m-d",$value['refreshtime']);
            $value['company_url']=url_rewrite('QS_companyshow',array('id'=>$value['company_id']));
            $value['jobs_url']=url_rewrite('QS_jobsshow',array('id'=>$value['id']));
            $info[]=$value['uid']."%%%".$value['jobs_name']."%%%".$value['jobs_url']."%%%".$value['companyname']."%%%".$value['company_url']."%%%".$value['addtime']."%%%".$value['deadline']."%%%".$value['refreshtime']."%%%".$subsite_id;
        }
        if (!empty($info))
        {
        exit(implode('@@@',$info));
        }
        else
        {
        exit();
        }
    }
    public function subject_tpl(){
        $this->tpl_dir = APP_PATH.'/Subject/View/';
        $result = D('Tpl')->where(array('tpl_type'=>3))->select();
        $list = array();
        foreach ($result as $key => $value) {
            //$value['info']=$this->_get_templates_info($this->tpl_dir.$tpl_file_dir.'/'.$value['tpl_dir']."/info.txt");
            $value['thumb_dir'] = $this->tpl_dir.'/'.$value['tpl_dir'];
            $list[] =$value;
        }
        $this->assign('list',$list);
        $this->display();
    }
}

?>