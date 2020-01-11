<?php
namespace Career\Controller;

use Common\Controller\ConfigbaseController;

class AdminController extends ConfigbaseController {

   public function _initialize() {
        parent::_initialize();
        $this->_mod = D('Career');
    }
    public function index(){
        $this->_name = 'Career';
        $this->order = 'article_order desc, addtime desc';
        parent::index();
    }
    public function add(){
        $this->_name = 'Career';
        parent::add();
    }
    public function edit(){
        $this->_name = 'Career';
        parent::edit();
    }
    public function delete(){
        $this->_name = 'Career';
        parent::delete();
    }
    /**
     * [_before_search 查询条件]
     */
    public function _before_search($data){
        $this->_name = 'Career';
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
     * [_before_add 添加事业单位招考]
     */
    public function _before_add(){
        if(IS_POST){
                $_POST['addtime'] = time();
        }
    }
    /**
     * [_before_edit 修改资讯信息]
     */
    //public function _before_edit(){
       // $this->_before_add();
    //}
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
}


?>