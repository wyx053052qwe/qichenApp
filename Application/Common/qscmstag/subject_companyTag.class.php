<?php
/**
 * 合并加载JS和CSS文件
 *
 * @author brivio
 */
namespace Common\qscmstag;
defined('THINK_PATH') or exit();
class subject_companyTag {
    protected $params = array();
    protected $map = array();
    public function __construct($options) {
        $array = array(
            '列表名'           =>  'listname',
            '显示数目'          =>  'row',
            '专题公司id'          =>  'id'
        );
        foreach ($options as $key => $value) {
            $this->params[$array[$key]] = $value;
        }
        $this->map['subject_id'] = array('eq',intval($this->params['id']));
        $this->limit = isset($this->params['row'])?intval($this->params['row']):5;
        $this->limit>20 && $this->limit=20;
        $this->params['listname']=isset($this->params['listname'])?$this->params['listname']:"list";
    }
    public function run(){
        if($this->params['paged']){
            $total = M('SubjectCompany')->where($this->map)->count();
            $pager = pager($total, $this->limit);
            $page = $pager->fshow();
            $this->limit = $pager->firstRow.','.$pager->listRows;
            $page_params = $pager->get_page_params();
        }
        $subject_list = M('SubjectCompany')->where($this->map)->select();
        $uids =array();
        foreach ($subject_list as $key => $value) {
            $uids[] = $value['company_uid'];
        }
        if($uids){
            $company = M('CompanyProfile')->where(array('uid'=>array('in',$uids)))->order('refreshtime desc')->getfield('id,uid,audit,companyname,addtime,refreshtime,logo,short_name,contact,address');
        }
        $cids = array();
        foreach ($company as $key=>$val) {
            //$company[$key] = M('CompanyProfile')->where(array('uid'=>$val['company_uid']))->find();
            $company[$key]['companyname'] = $val['companyname'];
            $company[$key]['contact'] = $val['contact'];
            $company[$key]['address'] = $val['address'];
            if ($val['logo'])
            {
                $company[$key]['logo']=attach($val['logo'],'company_logo');
            }
            else
            {
                $company[$key]['logo']=attach('no_logo.png','resource');
            }
            $company[$key]['company_url']=url_rewrite('QS_companyshow',array('id'=>$val['id']));
            $jobs_num = M('Jobs')->where(array('company_id'=>$val['id']))->count();
            $company[$key]['jobs_num'] = $jobs_num;
            $cids[] = $val['id'];
        }
        if($cids){
            $list_map['company_id'] = array('in',$cids);
            if(C('qscms_jobs_display')==1){
                    $list_map['audit'] = 1;
                }
            $jobs_list = M('Jobs')->field('id,jobs_name,minwage,maxwage,company_id,negotiable')->where($list_map)->order('refreshtime desc')->select();
            foreach ($jobs_list as $k => $val) {
                    if(count($company[$val['company_id']]['jobs']) >= 4) continue;
                    $val['jobs_url'] = url_rewrite('QS_jobsshow',array('id'=>$val['id']));
                    if($val['negotiable']==0){
                        if(C('qscms_wage_unit') == 1){
                            $val['minwage'] = $val['minwage']%1000==0?(($val['minwage']/1000).'K'):(round($val['minwage']/1000,1).'K');
                            $val['maxwage'] = $val['maxwage']?($val['maxwage']%1000==0?(($val['maxwage']/1000).'K'):(round($val['maxwage']/1000,1).'K')):0;
                        }elseif(C('qscms_wage_unit') == 2){
                            if($val['minwage']>=10000){
                                if($val['minwage']%10000==0){
                                   $val['minwage'] = ($val['minwage']/10000).'万';
                                }else{
                                    $val['minwage'] = round($val['minwage']/10000,1);
                                    $val['minwage'] = strpos($val['minwage'],'.') ? str_replace('.','万',$val['minwage']) : $val['minwage'].'万';
                                }
                            }else{
                                if($val['minwage']%1000==0){
                                    $val['minwage'] = ($val['minwage']/1000).'千';
                                }else{
                                    $val['minwage'] = round($val['minwage']/1000,1);
                                    $val['minwage'] = strpos($val['minwage'],'.') ? str_replace('.','千',$val['minwage']) : $val['minwage'].'千';
                                }
                            }
                            if($val['maxwage']>=10000){
                                if($val['maxwage']%10000==0){
                                   $val['maxwage'] = ($val['maxwage']/10000).'万';
                                }else{
                                    $val['maxwage'] = round($val['maxwage']/10000,1);
                                    $val['maxwage'] = strpos($val['maxwage'],'.') ? str_replace('.','万',$val['maxwage']) : $val['maxwage'].'万';
                                }
                            }elseif($val['maxwage']){
                                if($val['maxwage']%1000==0){
                                   $val['maxwage'] = ($val['maxwage']/1000).'千';
                                }else{
                                    $val['maxwage'] = round($val['maxwage']/1000,1);
                                    $val['maxwage'] = strpos($val['maxwage'],'.') ? str_replace('.','千',$val['maxwage']) : $val['maxwage'].'千';
                                }
                            }else{
                                $val['maxwage'] = 0;
                            }
                        }
                        if($val['maxwage']==0){
                            $val['wage_cn'] = '面议';
                        }else{
                            if($val['minwage']==$val['maxwage']){
                                $val['wage_cn'] = $val['minwage'].'/月';
                            }else{
                                $val['wage_cn'] = $val['minwage'].'-'.$val['maxwage'].'/月';
                            }
                        }
                    }else{
                        $val['wage_cn'] = '面议';
                    }
                    $company[$val['company_id']]['jobs'][] = $val;
            }
        }   
        $return['list'] = $company;
        return $return;
    }
}