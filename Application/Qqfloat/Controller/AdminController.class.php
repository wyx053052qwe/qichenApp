<?php
namespace Qqfloat\Controller;
use Common\Controller\ConfigbaseController;
class AdminController extends ConfigbaseController {
    public function _initialize() {
        parent::_initialize();
        $this->_name = 'QqFloat';
    }

    /**
     * 基本配置+QQ列表
     */
    public function index() {
        if (IS_POST) {
            $id = I('post.id');
            $sort = I('post.sort');
            foreach ($id as $key => $value) {
                D('QqFloat')->where(array('id' => intval($value)))->setField('sort', $sort[$key]);
            }
            $this->success('保存成功！');
        } else {
            $list = D('QqFloat')->order('display DESC,sort DESC,id')->select();
            $this->assign('list', $list);
            $this->display();
        }
    }

    /**
     * 保存配置项
     */
    public function save_config(){
        parent::_edit();
    }

    /**
     * 添加客服QQ
     */
    public function add() {
        if (IS_POST) {
            parent::add();
        } else {
            $info['display'] = 1;
            $this->assign('info', $info);
            $this->display('edit');
        }
    }

    /**
     * 修改客服QQ
     */
    public function edit() {
        $this->_tpl = 'edit';
        parent::edit();
    }

    /**
     * 删除客服QQ
     */
    public function del() {
        parent::delete();
    }
}
?>