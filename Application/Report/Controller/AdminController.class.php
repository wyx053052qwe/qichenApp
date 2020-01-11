<?php
namespace Report\Controller;
use Common\Controller\BackendController;
class AdminController extends BackendController {
    public function _initialize() {
        parent::_initialize();
    }
    public function index(){
        $this->_mod = D('CompanyProfile');
        $this->_name = 'CompanyProfile';
        $db_pre = C('DB_PREFIX');
        $this_t = $db_pre.'company_profile';
        $overtime = I('request.overtime',0,'intval');
        $key_type = I('request.key_type',0,'intval');
        $key = I('request.key','','trim');
        $join = array();
        $join[] = 'left join '.$db_pre."members as m on ".$this_t.".uid=m.uid";
        if ($key && $key_type>0){
            switch($key_type){
                case 1:
                    $where[$this_t.'.companyname']=array('like','%'.$key.'%');break;
                case 2:
                    $where[$this_t.'.id']=array('eq',$key);break;
                case 3:
                    $where['m.username']=array('like','%'.$key.'%');break;
                case 4:
                    $where[$this_t.'.uid']=array('eq',$key);break;
                case 5:
                    $where[$this_t.'.address']=array('like','%'.$key.'%');break;
                case 6:
                    $where[$this_t.'.telephone']=array('like','%'.$key.'%');break;
            }
        }else{
            if($settr=I('get.settr',0,'intval')){
                $where['addtime']=array('gt',strtotime("-".$settr." day"));
            }
            if($overtime>0){
                $join[] = 'left join '.$db_pre."members_setmeal as s on ".$this_t.".uid=s.uid";
                $where['s.endtime']=array(array('neq',0),array('elt',strtotime("+".$overtime." day")),'and');
            }else if($overtime==-1){
                $join[] = 'left join '.$db_pre."members_setmeal as s on ".$this_t.".uid=s.uid";
                $where['s.expire']=array('eq',1);
            }
        }
        $this->where = $where;
        $this->field = $this_t.'.*,m.username,m.mobile,m.email as memail';
        $this->order = 'field('. $this_t.'.audit,2) desc ,id '.'desc';
        $this->join = $join;
        $this->assign('count',parent::_pending('CompanyProfile',array('audit'=>2)));
        $setmeal = D('Setmeal')->get_setmeal_cache();
        $this->assign('setmeal',$setmeal);
        parent::index();
    }
    /**
     * 格式化列表
     */
    public function _custom_fun($list){
        $this->_mod = D('CompanyProfile');
        return $this->_mod->admin_format_company_list($list);
    }
    /**
     * 实地认证报告
     */
    public function set() {
        if (!IS_POST) {
            $id = I('request.id', 0, 'intval');
            !$id && $this->returnMsg(0,'你没有选择企业！');
            $com_report = M('CompanyReport')->where(array('com_id' => $id))->find();
            if (!$com_report) {// 添加
                $com_profile = M('CompanyProfile')->field('id,uid,companyname,registered,address,scale_cn')->find($id);
                $com_report['status'] = 1;
                $com_report['com_id'] = $id;
                $com_report['uid'] = $com_profile['uid'];
                $com_report['com_name'] = $com_profile['companyname'];
                $com_report['reg_capital'] = $com_profile['registered'];
                $com_report['office_address'] = $com_profile['address'];
                $com_report['office_env'] = 2;
                $com_report['number'] = $com_profile['scale_cn'];
                $com_report['addtime'] = time();
            }
            $com_report['img'] && $com_report['img_arr'] = explode('#',$com_report['img']);
            $this->assign('info', $com_report);
            $this->display();
        } else { // 保存认证报告
            $report = D('CompanyReport');
            $img = I('post.img_item');
            $_POST['img'] = $img ? implode('#',$img) : '';
            if (false === $report->update()) {
                $error = $report->getError();
                $this->returnMsg(0,$error ? $error : '保存失败！');
            }
            $this->returnMsg(1,'保存成功！');
        }
    }
    /**
     * 实地认证
     */
    public function report(){
        $uid = I('get.uid',0,'intval');
        $info = D('CompanyReport')->where(array('uid'=>$uid))->find();
        $img = $info['img']?explode("#", $info['img']):array();
        $this->assign('img',$img);
        $this->assign('info',$info);
        $html = $this->fetch('report');
        $this->ajaxReturn(1,'获取数据成功！',$html);
    }
}
?>