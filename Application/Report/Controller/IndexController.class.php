<?php
namespace Report\Controller;
use Common\Controller\FrontendController;
class IndexController extends FrontendController {

    public function _initialize() {
        parent::_initialize();
    }

    public function index() {
        $id = I('get.id',0,'intval');
        !$id && $this->_empty();
        if (!I('get.org', '', 'trim') && C('PLATFORM') == 'mobile' && $this->apply['Mobile']) {
            redirect(build_mobile_url(array('c' => 'Jobs', 'a' => 'com_report', 'params' => 'id=' . intval($_GET['id']))));
        }
        $where['com_id'] = $id;
        $where['status'] = 1;
        $report = M('CompanyReport')->where($where)->find();
        !$report && $this->_empty();
        $report['img'] && $report['img_arr'] = array_slice(explode('#',$report['img']),0,5);
        $this->assign('info',$report);
        $page_seo = D('Page')->get_page();
        $this->_config_seo($page_seo[strtolower(MODULE_NAME).'_'.strtolower(CONTROLLER_NAME).'_'.strtolower(ACTION_NAME)],$report);
        $this->display();
    }
}