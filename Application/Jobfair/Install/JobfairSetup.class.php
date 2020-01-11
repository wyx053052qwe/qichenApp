<?php
/**
 * 招聘会安装程序
 */
class JobfairSetup{
	/**
     * [init 安装程序初始化程序]
     */
    public function setup_init(){
        if(false === $apply = F('apply_info_list')) $apply = D('Apply')->apply_info_cache();
        if($apply['Seniorjobfair']){
            $this->_error = '安装前请先卸载已有招聘会插件！';
            return false;
        }
        if($apply['Home']['version']){
            $version =  explode('.', $apply['Home']['version']);
            $v = $version[0] * 1000000 + $version[1] * 10000 + $version[2];
            if($v < 5000001){
                $this->_error = '请将基础版程序升级至5.0.1版本以上（含）！';
                return false;
            }
        }
        if($apply['Mobile']['version']){
            $version =  explode('.', $apply['Mobile']['version']);
            $v = $version[0] * 1000000 + $version[1] * 10000 + $version[2];
            if($v < 5000001){
                $this->_error = '请将触屏版程序升级至5.0.1版本以上（含）！';
                return false;
            }
        }
        return true;
    }
    /**
	 * [setup 安装程序]
	 */
    public function setup(){
        //初始化附件添加
        $imgs = array('57e887cf02d37.jpg','57c96d5b37541.jpg','57e888401f3c0.png');
        $path = C('qscms_attach_path').'jobfair/16/09/26/';
        $org = APP_PATH.'Jobfair/Install/attach/';
        foreach ($imgs as $key => $val) {
            if (!is_dir($path)) mkdir($path,0755,true);
            copy($org.$val,$path.$val);
        }
        return true;
    }
    /**
     * [init 安装程序初始化程序]
     */
    public function unload_init(){
        return true;
    }
    /**
     * [unload 卸载程序]
     */
    public function unload(){
    	D('Navigation')->where(array('pagealias'=>'QS_jobfairlist'))->delete();
    	D('Page')->where(array('module'=>'Jobfair'))->delete();
        D('Page')->where(array('module'=>'Mobile','controller'=>'Jobfair'))->delete();
    }
    /**
     * [getError 返回错误]
     */
    public function getError(){
        return $this->_error;
    }
}
?>