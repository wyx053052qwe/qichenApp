<?php 
namespace Mobile\Controller;
use Mobile\Controller\MobileController;
class YewuController extends MobileController{
	public function index(){
		$id = I('get.id');
		$data = M('yewu')->where(['id'=>$id])->find();
		$this->wx_share();
		$this->assign('data',$data);
		$this->display('News/yewu1');
	}
}
 ?>
