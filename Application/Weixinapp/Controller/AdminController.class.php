<?php
namespace Weixinapp\Controller;
use Common\Controller\ConfigbaseController;
class AdminController extends ConfigbaseController{
	public function _initialize() {
        parent::_initialize();
    }
    public function edit(){
        if(IS_POST){
            foreach (I('post.') as $key => $val) {
                $val = is_array($val) ? serialize(htmlspecialchars_decode($val,ENT_QUOTES)) : htmlspecialchars_decode($val,ENT_QUOTES);
                D('Config')->where(array('name' => $key))->save(array('value' => $val));
            }
        }
        $this->_edit();
        $this->display();
    }
    /**
     * 导航列表
     */
    public function nav_list(){
        $list = D('NavigationWeixinapp')->order('ordid desc,id asc')->select();
        $this->assign('list',$list);
        $this->display();
    }
    
    /**
     * 全部保存
     */
    public function nav_save_all(){
        $id = I('post.id');
        $show_name = I('post.show_name');
        $display = I('post.display');
        $ordid = I('post.ordid');
        if (is_array($id) && count($id)>0)
        {
            $model = D('NavigationWeixinapp');
            foreach($id as $k=>$v)
            {
                $setsqlarr['show_name']=trim($show_name[$k]);
                $setsqlarr['display']=intval($display[$k]);
                $setsqlarr['ordid']=intval($ordid[$k]);
                $model->where(array('id'=>array('eq',intval($id[$k]))))->save($setsqlarr);
            }
        }
        $this->returnMsg(1,'保存成功！');
    }
    /**
     * 修改导航
     */
    public function nav_edit(){
        $this->_name = 'NavigationWeixinapp';
        parent::edit();
    }
}
?>