<?php
/**
 * 微信小程序接口安装程序
 */
class WeixinappSetup{
	/**
	 * [setup_init 安装程序初始化程序]
	 */
	public function setup_init(){
        if(false === $apply = F('apply_info_list')) $apply = D('Apply')->apply_info_cache();
        if($apply['Weixinapp']){
            $this->_error = '安装前请先卸载已有微信小程序插件！';
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
        D('Config')->where(array('type'=>'Weixinapp'))->delete();
		return true;
	}
    /**
     * [setup 安装程序]
     */
    public function setup(){
        copy(APP_PATH.'Weixinapp/Install/images/1048.png', APP_PATH.'Admin/View/default/public/images/menu/1048.png');
        if(!is_dir(QSCMS_DATA_PATH.'upload/resource/weixinapp_nav')){
            mkdir(QSCMS_DATA_PATH.'upload/resource/weixinapp_nav',0755,true);
        }
        copy(APP_PATH.'Weixinapp/Install/images/allowance.png', QSCMS_DATA_PATH.'upload/resource/weixinapp_nav/allowance.png');
        copy(APP_PATH.'Weixinapp/Install/images/joblist.png', QSCMS_DATA_PATH.'upload/resource/weixinapp_nav/joblist.png');
        copy(APP_PATH.'Weixinapp/Install/images/members_center.png', QSCMS_DATA_PATH.'upload/resource/weixinapp_nav/members_center.png');
        copy(APP_PATH.'Weixinapp/Install/images/myresume.png', QSCMS_DATA_PATH.'upload/resource/weixinapp_nav/myresume.png');
        copy(APP_PATH.'Weixinapp/Install/images/nearbyjobs.png', QSCMS_DATA_PATH.'upload/resource/weixinapp_nav/nearbyjobs.png');
        return true;
    }
    /**
     * [init 卸载程序初始化程序]
     */
    public function unload_init(){
        return true;
    }
    /**
     * [unload 卸载程序]
     */
    public function unload(){
        @unlink(APP_PATH.'Admin/View/default/public/images/menu/1048.png');
        @unlink(QSCMS_DATA_PATH.'upload/resource/weixinapp_nav/allowance.png');
        @unlink(QSCMS_DATA_PATH.'upload/resource/weixinapp_nav/joblist.png');
        @unlink(QSCMS_DATA_PATH.'upload/resource/weixinapp_nav/members_center.png');
        @unlink(QSCMS_DATA_PATH.'upload/resource/weixinapp_nav/myresume.png');
        @unlink(QSCMS_DATA_PATH.'upload/resource/weixinapp_nav/nearbyjobs.png');
        return true;
    }
    /**
     * [getError 返回错误]
     */
    public function getError(){
        return $this->_error;
    }
}
?>