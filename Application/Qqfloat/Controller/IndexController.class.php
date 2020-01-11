<?php
namespace Qqfloat\Controller;
use Common\Controller\FrontendController;
class IndexController extends FrontendController {

    public function _initialize() {
        parent::_initialize();
    }

    public function index() {
        $qq_list = D('QqFloat')->where(array('display' => 1))->order('sort DESC,id')->select();
        $this->assign('qq_list', $qq_list);
        $cookie_show_qq_float = cookie('show_qq_float');
        $is_show = $cookie_show_qq_float ? 1 : 0;
        $this->assign('is_show', $is_show);
        $tpl = C('qscms_qq_float_theme') ? C('qscms_qq_float_theme') : 'default';
        $html = $this->fetch($tpl);
        $this->ajaxReturn(1, '获取数据成功！', $html);
    }
}