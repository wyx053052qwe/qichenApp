<?php
namespace Export\Controller;
use Common\Controller\BackendController;
class AdminController extends BackendController{
    public function _initialize() {
        parent::_initialize();
    }
    /**
     * 职位导出
     */
    public function ajax_export_jobs() {
        $this->assign('education_arr',D('Category')->get_category_cache('QS_education'));
        $this->assign('experience_arr',D('Category')->get_category_cache('QS_experience'));
        $html = $this->fetch("ajax_export_jobs");
        $this->ajaxReturn(1, '获取数据成功！', $html);
    }
    /**
     * 职位导出
     */
    public function export_jobs(){
        $limit = 500;
        $id = I('request.id');
        if(!$id){
            $this->error('请选择职位');
        }
        $sqlin=implode(",",$id);
        $where = array('j.id'=>array('in',$sqlin));
        $title=array('职位ID','职位名称','企业名称','招聘类型','性别','学历要求','工作经验要求','招聘人数','年龄','职位亮点','所属部门','职位类别','公司性质','公司人数','工作地区','最低薪资','最高薪资','职位介绍','联系人','QQ','联系电话','座机','联系地址','联系邮箱');
        $file_name = '职位导出'.date("Y-m-d");

        $count = M('Jobs j')->where($where)->count();
        if($count==0){
            $this->error('没有符合条件的数据');
        }

        $join = 'left join '.C('DB_PREFIX').'jobs_contact as c on c.pid=j.id';
        for ($i=0; $i < ceil($count/$limit); $i++) { //分段查询
            $offset = $i * $limit;
            $data = M('Jobs j')->join($join)->where($where)->order('id desc')->limit($offset.','.$limit)->getField('j.id,j.jobs_name,j.companyname,j.nature_cn,j.sex_cn,j.education_cn,j.experience_cn,j.amount,j.age,j.tag_cn,j.department,j.category_cn,j.trade_cn,j.scale_cn,j.district_cn,j.minwage,j.maxwage,j.contents,c.contact,c.qq,c.telephone,c.landline_tel,c.address,c.email');
            $this->exportexcel($data,$title,$file_name,$i+1);
        }
        $this->fileload($file_name);
    }
    public function export_jobs_s(){
        $where = array();
        $sex = I('post.sex', 0, 'intval');
        $sex && $where['j.sex'] = $sex;
        $settr = I('post.settr', '', 'trim'); 
        if($settr){
            $daterange = explode("-", $settr);
            $min = trim($daterange[0]);
            $max = trim($daterange[1]);
            $where['j.addtime'] = array(array('egt',strtotime($min)),array('lt',strtotime($max)),'and');
        }
        $education = I('post.education', 0, 'intval');
        $education && $where['j.education'] = $education;
        $experience = I('post.experience', 0, 'intval');
        $experience && $where['j.experience'] = $experience;


        $join = 'left join '.C('DB_PREFIX').'jobs_contact as c on c.pid=j.id';
        $limit = 500;
        $title=array('职位ID','职位名称','企业名称','招聘类型','性别','学历要求','工作经验要求','招聘人数','年龄','职位亮点','所属部门','职位类别','公司性质','公司人数','工作地区','最低薪资','最高薪资','职位介绍','联系人','QQ','联系电话','座机','联系地址','联系邮箱');
        $file_name = '职位导出'.date("Y-m-d");
        $count = M('Jobs j')->where($where)->join($join)->count();//获得数据表查询结果的总条数
        if($count==0){
            $this->error('没有符合条件的数据');
        }
        for ($i=0; $i < ceil($count/$limit); $i++) { //分段查询
            $offset = $i * $limit;
            $data = M('Jobs j')->join($join)->field('j.id,j.jobs_name,j.companyname,j.nature_cn,j.sex_cn,j.education_cn,j.experience_cn,j.amount,j.age,j.tag_cn,j.department,j.category_cn,j.trade_cn,j.scale_cn,j.district_cn,j.minwage,j.maxwage,j.contents,c.contact,c.qq,c.telephone,c.landline_tel,c.address,c.email')->where($where)->order('j.id desc')->limit($offset.','.$limit)->select();
            $this->exportexcel($data,$title,$file_name,$i+1);
        }
        $this->fileload($file_name);
    }
    /**
     * 企业导出
     */
    public function ajax_export_company() {
        $setmeal = D('Setmeal')->get_setmeal_cache();
        $this->assign('setmeal',$setmeal);
        $this->assign('trade_arr',D('Category')->get_category_cache('QS_trade'));
        $html = $this->fetch("ajax_export_company");
        $this->ajaxReturn(1, '获取数据成功！', $html);
    }
    /**
     * 企业导出
     */
    public function export_company(){
        $limit = 500;
        $id = I('request.y_id');
        if(!$id){
            $this->error('请选择企业');
        }
        $sqlin=implode(",",$id);
        $where = array('uid'=>array('in',$sqlin));
        $title=array('企业名称','企业性质','所属行业','企业规模','所在地区','注册资金','币种','企业网址','企业福利','企业简介','联系人','联系电话','座机','联系邮箱','QQ','联系地址');
        $file_name = '企业导出'.date("Y-m-d");
        $count = M('CompanyProfile')->where($where)->count();
        if($count==0){
            $this->error('没有符合条件的数据');
        }
        for ($i=0; $i < ceil($count/$limit); $i++) { //分段查询
            $offset = $i * $limit;
            $data = M('CompanyProfile')->where($where)->order('id desc')->limit($offset.','.$limit)->getField('companyname,nature_cn,trade_cn,scale_cn,district_cn,registered,currency,website,tag,contents,contact,telephone,landline_tel,email,qq,address');
            $this->exportexcel($data,$title,$file_name,$i+1);
        }
        $this->fileload($file_name);
    }
    public function export_company_s(){
        $join = '';
        $where = array();
        if($settr=I('post.settr',0,'intval')){
            $where['c.addtime']=array('gt',strtotime("-".$settr." day"));
        }
        $overtime = I('post.overtime',0,'intval');
        if($overtime>0){
            $join = 'left join '.C('DB_PREFIX')."members_setmeal as s on c.uid=s.uid";
            $where['s.endtime']=array(array('neq',0),array('elt',strtotime("+".$overtime." day")),'and');
        }else if($overtime==-1){
            $join = 'left join '.C('DB_PREFIX')."members_setmeal as s on c.uid=s.uid";
            $where['s.expire']=array('eq',1);
        }
        $setmeal_id = I('post.setmeal_id',0,'intval');
        if($setmeal_id){
            $where['c.setmeal_id'] = $setmeal_id;
        }
        $audit = I('post.audit','','trim');
        if($audit != ''){
            $where['c.audit'] = $audit;
        }
        $trade = I('post.trade',0,'intval');
        if($trade){
            $where['c.trade'] = $trade;
        }
        $limit = 500;
        $title=array('企业名称','企业性质','所属行业','企业规模','所在地区','注册资金','币种','企业网址','企业福利','企业简介','联系人','联系电话','座机','联系邮箱','QQ','联系地址');
        $file_name = '企业导出'.date("Y-m-d");
        $count = M('CompanyProfile c')->where($where)->join($join)->count();
        if($count==0){
            $this->error('没有符合条件的数据');
        }
        for ($i=0; $i < ceil($count/$limit); $i++) { //分段查询
            $offset = $i * $limit;
            $data = M('CompanyProfile c')->where($where)->join($join)->order('c.id desc')->limit($offset.','.$limit)->getField('c.companyname,c.nature_cn,c.trade_cn,c.scale_cn,c.district_cn,c.registered,c.currency,c.website,c.tag,c.contents,c.contact,c.telephone,c.landline_tel,c.email,c.qq,c.address');
            $this->exportexcel($data,$title,$file_name,$i+1);
        }
        $this->fileload($file_name);
    }
    /**
     * 简历导出
     */
    public function ajax_export_personal() {
        $this->assign('education_arr',D('Category')->get_category_cache('QS_education'));
        $this->assign('experience_arr',D('Category')->get_category_cache('QS_experience'));
        $html = $this->fetch("ajax_export_personal");
        $this->ajaxReturn(1, '获取数据成功！', $html);
    }
    /**
     * 简历导出
     */
    public function export_personal(){
        $limit = 500;
        $id = I('request.id');
        if(!$id) $this->error('请选择在职人员');
        $sqlin=implode(",",$id);
        $where = array('id'=>array('in',$sqlin));
        
        $title=array('简历ID','标题','姓名','性别','出生年份','现居住地','最高学历','工作经验','手机','邮箱','所学专业','身高','籍贯','婚姻状况','QQ','微信号','目前状态','工作性质','期望行业','期望职位','工作地区','期望薪资','自我描述','教育经历','工作经历','培训经历','获得证书','语言能力');
        $file_name = '简历导出'.date("Y-m-d");
        $count = M('Resume')->where($where)->count();
        if($count==0){
            $this->error('没有符合条件的数据');
        }
        for ($i=0; $i < ceil($count/$limit); $i++) { //分段查询
            $offset = $i * $limit;
            $data = M('Resume')->where($where)->order('id desc')->limit($offset.','.$limit)->getField('id,title,fullname,sex_cn,birthdate,residence,education_cn,experience_cn,telephone,email,major_cn,height,householdaddress,marriage_cn,qq,weixin,current_cn,nature_cn,trade_cn,intention_jobs,district_cn,wage_cn,specialty');
            foreach ($data as $key => $value) {
                $education=M('ResumeEducation')->where(array('pid'=>$value['id']))->select();
                $education_s = '';
                foreach ($education as $key_education => $value_education) {
                    $education_s.=$value_education['startyear'].'/'.$value_education['startmonth'].'-';
                    if($value_education['todate']==1){
                        $education_s.='至今';
                    }else{
                        $education_s.=$value_education['endyear'].'/'.$value_education['endmonth'];
                    }
                    $education_s.=',学校名称：'.$value_education['school'].',专业名称：'.$value_education['speciality'].',获取学历：'.$value_education['education_cn'];
                    $education_s.=' | ';
                }
                $data[$key]['education']=$education_s;
                
                $work=M('ResumeWork')->where(array('pid'=>$value['id']))->select();
                $work_s = '';
                foreach ($work as $key_work => $value_work) {
                    $work_s.=$value_work['startyear'].'/'.$value_work['startmonth'].'-';
                    if($value_work['todate']==1){
                        $work_s.='至今';
                    }else{
                        $work_s.=$value_work['endyear'].'/'.$value_work['endmonth'];
                    }
                    $work_s.=',公司名称：'.$value_work['companyname'].',职位名称：'.$value_work['jobs'].',工作职责：'.$value_work['achievements'];
                    $work_s.=' | ';
                }
                $data[$key]['work']=$work_s;

                $training=M('ResumeTraining')->where(array('pid'=>$value['id']))->select();
                $training_s = '';
                foreach ($training as $key_training => $value_training) {
                    $training_s.=$value_training['startyear'].'/'.$value_training['startmonth'].'-';
                    if($value_training['todate']==1){
                        $training_s.='至今';
                    }else{
                        $training_s.=$value_training['endyear'].'/'.$value_training['endmonth'];
                    }
                    $training_s.=',培训机构：'.$value_training['agency'].',培训课程：'.$value_training['course'].',培训内容：'.$value_training['description'];
                    $training_s.=' | ';
                }
                $data[$key]['training']=$training_s;

                $credent=M('ResumeCredent')->where(array('pid'=>$value['id']))->select();
                $credent_s = '';
                foreach ($credent as $key_credent => $value_credent) {
                    $credent_s.=$value_credent['year'].'/'.$value_credent['month'].'获取证书：'.$value_credent['name'];
                    $credent_s.=' | ';
                }
                $data[$key]['credent']=$credent_s;

                $language=M('ResumeLanguage')->where(array('pid'=>$value['id']))->select();
                $language_s = '';
                foreach ($language as $key_language => $value_language) {
                    $language_s.=$value_language['language_cn'].'/'.$value_language['level_cn'];
                    $language_s.=' | ';
                }
                $data[$key]['language']=$language_s;
            }
            $this->exportexcel($data,$title,$file_name,$i+1);
        }
        $this->fileload($file_name);
    }


    /**
     * 在职员工导出
     */
    public function export_personal1(){
        $limit = 500;
        $id = I('request.tuid');
        if(!$id) $this->error('请选择在职人员');
        $sqlin=implode(",",$id);
        $where = array('qs_members_zaizhi.id'=>array('in',$sqlin));

        $title=array('在职人员ID','姓名','身份证','绑定用户','绑定时间','合同到期时间','在职企业','工资卡号','联系方式');
        $file_name = '在职人员导出'.date("Y-m-d");
        $count = M('members_zaizhi')->where($where)->count();
        if($count==0){
            $this->error('没有符合条件的数据');
        }
        for ($i=0; $i < ceil($count/$limit); $i++) { //分段查询
            $offset = $i * $limit;
            $join = "left join qs_members as b ON b.uid=qs_members_zaizhi.uid";
            $join1 = "left join qs_company_profile as c ON c.id=qs_members_zaizhi.com_id";
            $data = M('members_zaizhi')->join($join)->join($join1)->where($where)->order('dengji_time DESC')->limit($offset.','.$limit)->getField("qs_members_zaizhi.id,qs_members_zaizhi.name,qs_members_zaizhi.id_card,b.username as uid_name,qs_members_zaizhi.dengji_time,qs_members_zaizhi.ht_time,c.companyname,qs_members_zaizhi.gongzi_card,qs_members_zaizhi.mobile");
           $datas = [];
            foreach($data as $k=>$v){
                $ht_time = $v['ht_time'];
                $ht_time = date('Y-m-d H:i:s',$ht_time);
                $v['ht_time'] = $ht_time;
                $dengji_time = $v['dengji_time'];
                $dengji_time = date('Y-m-d H:i:s',$dengji_time);
                $v['dengji_time'] =  $dengji_time;
                $datas[$v['id']]=$v;
            }
            $this->exportexcel($datas,$title,$file_name,$i+1);
        }
        $this->fileload($file_name);
    }



    public function export_personal_s(){
        $where = array();
        $sex = I('post.sex', 0, 'intval');
        $sex && $where['sex'] = $sex;
        $settr = I('post.settr', '', 'trim'); 
        if($settr){
            $daterange = explode("-", $settr);
            $min = trim($daterange[0]);
            $max = trim($daterange[1]);
            $where['addtime'] = array(array('egt',strtotime($min)),array('lt',strtotime($max)),'and');
        }
        $education = I('post.education', 0, 'intval');
        $education && $where['education'] = $education;
        $experience = I('post.experience', 0, 'intval');
        $experience && $where['experience'] = $experience;

        $limit = 500;
        $title=array('简历ID','标题','姓名','性别','出生年份','现居住地','最高学历','工作经验','手机','邮箱','所学专业','身高','籍贯','婚姻状况','QQ','微信号','目前状态','工作性质','期望行业','期望职位','工作地区','期望薪资','自我描述','教育经历','工作经历','培训经历','获得证书','语言能力');
        $file_name = '简历导出'.date("Y-m-d");
        $count = M('Resume')->where($where)->count();
        if($count==0){
            $this->error('没有符合条件的数据');
        }
        for ($i=0; $i < ceil($count/$limit); $i++) { //分段查询
            $offset = $i * $limit;
            $data = M('Resume')->where($where)->order('id desc')->limit($offset.','.$limit)->getField('id,title,fullname,sex_cn,birthdate,residence,education_cn,experience_cn,telephone,email,major_cn,height,householdaddress,marriage_cn,qq,weixin,current_cn,nature_cn,trade_cn,intention_jobs,district_cn,wage_cn,specialty');
            foreach ($data as $key => $value) {
                $education=M('ResumeEducation')->where(array('pid'=>$value['id']))->select();
                $education_s = '';
                foreach ($education as $key_education => $value_education) {
                    $education_s.=$value_education['startyear'].'/'.$value_education['startmonth'].'-';
                    if($value_education['todate']==1){
                        $education_s.='至今';
                    }else{
                        $education_s.=$value_education['endyear'].'/'.$value_education['endmonth'];
                    }
                    $education_s.=',学校名称：'.$value_education['school'].',专业名称：'.$value_education['speciality'].',获取学历：'.$value_education['education_cn'];
                    $education_s.=' | ';
                }
                $data[$key]['education']=$education_s;
                
                $work=M('ResumeWork')->where(array('pid'=>$value['id']))->select();
                $work_s = '';
                foreach ($work as $key_work => $value_work) {
                    $work_s.=$value_work['startyear'].'/'.$value_work['startmonth'].'-';
                    if($value_work['todate']==1){
                        $work_s.='至今';
                    }else{
                        $work_s.=$value_work['endyear'].'/'.$value_work['endmonth'];
                    }
                    $work_s.=',公司名称：'.$value_work['companyname'].',职位名称：'.$value_work['jobs'].',工作职责：'.$value_work['achievements'];
                    $work_s.=' | ';
                }
                $data[$key]['work']=$work_s;

                $training=M('ResumeTraining')->where(array('pid'=>$value['id']))->select();
                $training_s = '';
                foreach ($training as $key_training => $value_training) {
                    $training_s.=$value_training['startyear'].'/'.$value_training['startmonth'].'-';
                    if($value_training['todate']==1){
                        $training_s.='至今';
                    }else{
                        $training_s.=$value_training['endyear'].'/'.$value_training['endmonth'];
                    }
                    $training_s.=',培训机构：'.$value_training['agency'].',培训课程：'.$value_training['course'].',培训内容：'.$value_training['description'];
                    $training_s.=' | ';
                }
                $data[$key]['training']=$training_s;

                $credent=M('ResumeCredent')->where(array('pid'=>$value['id']))->select();
                $credent_s = '';
                foreach ($credent as $key_credent => $value_credent) {
                    $credent_s.=$value_credent['year'].'/'.$value_credent['month'].'获取证书：'.$value_credent['name'];
                    $credent_s.=' | ';
                }
                $data[$key]['credent']=$credent_s;

                $language=M('ResumeLanguage')->where(array('pid'=>$value['id']))->select();
                $language_s = '';
                foreach ($language as $key_language => $value_language) {
                    $language_s.=$value_language['language_cn'].'/'.$value_language['level_cn'];
                    $language_s.=' | ';
                }
                $data[$key]['language']=$language_s;
            }
            $this->exportexcel($data,$title,$file_name,$i+1);
        }
        $this->fileload($file_name);
    }
    function exportexcel($data=array(),$title=array(),$filename,$file_index=1){
        set_time_limit(0);
        header("Content-type: text/html; charset=utf-8");
        $dir = QSCMS_DATA_PATH.'csv/';
        if(!is_dir($dir))
        {
            mkdir($dir,0777,true);
        }
        $filePath = $dir . iconv("UTF-8", "GB2312", $filename) . "_".$file_index . '.csv';
        $fp = fopen($filePath, 'a');
        fwrite($fp, chr(0xEF).chr(0xBB).chr(0xBF));//转码 防止乱码(比如微信昵称(乱七八糟的))
        fputcsv($fp,$title);
        $index = 0;
        foreach ($data as $item) {
            if($index==1000){
                $index=0;
                ob_flush();
                flush();
            }
            $index++;
            fputcsv($fp,$item);
        }
        fclose($fp);
        unset($data);
        return true;
    }
    private function fileload($filename)
    {
        $filename = iconv("UTF-8", "GB2312", $filename);
        $zipname =  QSCMS_DATA_PATH . $filename. '.zip';
        $zipObj = new \ZipArchive();
        if($zipObj->open($zipname, \ZipArchive::CREATE) === true){
            $res = false;
            foreach(glob(QSCMS_DATA_PATH.'csv/' . "*") as $file){ 
                $res = $zipObj->addFile($file);
            }
            $zipObj->close();
            if($res){
                header("Cache-Control: max-age=0");
                header("Content-Description: File Transfer");
                header("Content-Disposition: attachment;filename =" . $filename.'.zip');
                header('Content-Type: application/zip');
                header('Content-Transfer-Encoding: binary');
                header('Content-Length: ' . filesize($zipname));
                @readfile($zipname);//输出文件;
                //清理临时目录和文件
                rmdirs(QSCMS_DATA_PATH.'csv/'); 
                @unlink($zipname);
                ob_flush();
                flush();
            }else{
                rmdirs(QSCMS_DATA_PATH.'csv/'); 
                ob_flush();
                flush();
                $this->error('暂无文件可下载！');
            }
        }else{
            rmdirs(QSCMS_DATA_PATH.'csv/'); 
            ob_flush();
            flush();
            $this->error('文件压缩失败！');
        }
    }
}
?>