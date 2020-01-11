<?php
namespace Subject\Controller;

use Common\Controller\FrontendController;

class IndexController extends FrontendController {

   public function _initialize() {
        parent::_initialize();
        $url = __ROOT__.'/'.APP_NAME.'/'.MODULE_NAME.'/View/public';
        $this->assign('url',$url);
    }
    /**
     * 出租列表
     */
    public function index(){
       
        $this->display();
    }
    /**
     * 求租列表
     */
    public function subject_show(){
    	$id = I('get.id',0,'intval');
        $subject = M('Subject')->where(array('id'=>$id))->field('s_tpl_id')->find();
        $tpl = M('Tpl')->where(array('tpl_id'=>$subject['s_tpl_id']))->field('tpl_dir')->find();
        $this->theme($tpl['tpl_dir'])->display();
    }
   
}

?>