<?php
namespace Weixinapp\Controller;

use Common\Controller\FrontendController;

class WxMapsearchController extends FrontendController
{
    public function _initialize()
    {
        parent::_initialize();
        $secretKey = C('qscms_weixinapp_secretkey');
        $secret    = I('request.secretKey', "", "trim");
        if ($secret != $secretKey) {
            $this->ajaxReturn(2, '秘钥错误！', $list);
        }
    }
    
    public function get_company_markers($ne_lng,$ne_lat,$sw_lng,$sw_lat)
    {
        if(C('qscms_jobs_display')==1){
            $where['j.audit'] = 1;
        }
        $location_data = change_coords(array($ne_lng.','.$ne_lat,$sw_lng.','.$sw_lat),3,5);
        if($location_data['status']==1){
            $ne_lng = $location_data['data'][0]['x'];
            $ne_lat = $location_data['data'][0]['y'];
            $sw_lng = $location_data['data'][1]['x'];
            $sw_lat = $location_data['data'][1]['y'];
        }else{
            $this->ajaxReturn(2, '获取地理位置数据失败');
        }
        // var_dump($location_data);die;
        $where['j.id'] = array('gt',0);
        $where['c.map_x'] = array(array('lt',$ne_lng),array('gt',$sw_lng),'and');
        $where['c.map_y'] = array(array('lt',$ne_lat),array('gt',$sw_lat),'and');
        $list = D('CompanyProfile c')->distinct(true)->field('c.*')->join('left join '.C('DB_PREFIX').'jobs as j on j.uid=c.uid')->where($where)->select();
        $markers = array();
        $coords = array();
        $i = 0;
        foreach ($list as $key => $value) {
            $arr['id'] = $value['id'];
            $arr['companyname'] = $value['companyname'];
            $arr['map_x'] = $value['map_x'];
            $arr['map_y'] = $value['map_y'];
            $count_map['uid'] = $value['uid'];
            if(C('qscms_jobs_display')==1){
                $count_map['audit'] = 1;
            }
            $arr['jobs_count'] = D('Jobs')->where($count_map)->count();
            $markers[] = $arr;
            if($value['map_x'] && $value['map_y']){
                if(isset($coords[$i]) && count($coords[$i])>=100){
                    ++$i;
                }
                $coords[$i][] = $value['map_x'].','.$value['map_y'];
            }
        }
        $coords_changed = array();
        foreach ($coords as $key => $value) {
            $r = change_coords($value,5,3);
            if($r['status']==1){
                $coords_changed = array_merge($coords_changed,$r['data']);
            }
        }
        $j = 0;
        foreach ($markers as $key => $value) {
            if($value['map_x'] && $value['map_y']){
                $markers[$key]['lng'] = $coords_changed[$j]['x'];
                $markers[$key]['lat'] = $coords_changed[$j]['y'];
                $j++;
            }
        }
        if (!empty($markers)) {
            $this->ajaxReturn(1, '获取数据成功', $markers);
        } else {
            $this->ajaxReturn(0, '视野范围内没有找到职位信息', $markers);
        }

    }
    public function get_company_joblist($companyid){
        $companyname = D('CompanyProfile')->where(array('id'=>$companyid))->getField('companyname');
        if(C('qscms_jobs_display')==1){
            $where['audit'] = 1;
        }
        $where['company_id'] = $companyid;
        $joblist = D('Jobs')->where($where)->select();
        foreach ($joblist as $key => $val) {
            $val['refreshtime_cn'] = daterange(time(),$val['refreshtime']);
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
            $joblist[$key] = $val;
        }
        if (!empty($joblist)) {
            $result['joblist'] = $joblist;
            $result['companyname'] = $companyname;
            $this->ajaxReturn(1, '获取数据成功', $result);
        } else {
            $this->ajaxReturn(0, '获取数据失败');
        }
    }
}
