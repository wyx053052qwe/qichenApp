<?php
namespace Career\Controller;

use Common\Controller\FrontendController;

class IndexController extends FrontendController {

   public function _initialize() {
        parent::_initialize();
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
    public function career_show(){
    	
        $this->display();
    }
   
}

?>