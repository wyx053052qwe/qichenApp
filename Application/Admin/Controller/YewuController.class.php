<?php 
namespace Admin\Controller;
use Common\Controller\BackendController;
class YewuController extends BackendController{
	public function list_yewu(){
		$list = M('yewu')->order('add_time DESC')->select();
		$this->assign('list',$list);
		$this->display('list_yewu');
	}

	public function add_yewu(){
		if(IS_POST){
			if(empty($_POST['name'])){
				$this->ajaxReturn(0,'请填写名字');
			}

			if(empty($_POST['content'])){
				$this->ajaxReturn(0,'请填写内容');
			}

			$_POST['add_time'] = time();
			M('yewu')->add($_POST);
			$this->ajaxReturn(1,'添加成功');
		}else{
			$this->display('add_yewu');
		}
	}

	public function edit_yewu(){
		if(IS_POST){
			if(empty($_POST['name'])){
				$this->ajaxReturn(0,'请填写名字');
			}

			if(empty($_POST['content'])){
				$this->ajaxReturn(0,'请填写内容');
			}

			if(empty($_POST['id'])){
				$this->ajaxReturn(0,'参数错误！');
			}

			
			M('yewu')->save($_POST);
			$this->ajaxReturn(1,'保存成功！');
		}else{
			$id = I('get.id');
			$yewu = M('yewu')->where(['id'=>$id])->find();
			$this->assign('data',$yewu);
			$this->display('edit_yewu');
		}
	}

	public function del_yewu(){
		$id = I('get.id');
		M('yewu')->where(['id'=>$id])->delete();
		$this->success('删除成功');
	}
}
 ?>
