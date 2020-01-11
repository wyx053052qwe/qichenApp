<?php
/**
 * 商城安装程序
 */
class MallSetup{
	/**
	 * [init 安装程序初始化程序]
	 */
	public function setup_init(){
        if(false === $apply = F('apply_info_list')) $apply = D('Apply')->apply_info_cache();
        if($apply['Mall']){
            $this->_error = '安装前请先卸载已有商城插件！';
            return false;
        }
        if($apply['Home']['version']){
            $version =  explode('.', $apply['Home']['version']);
            $v = $version[0] * 1000000 + $version[1] * 10000 + $version[2];
            if($v >= 5000001) return true;
            $this->_error = '请将基础版程序升级至5.0.1版本以上（含）！';
            return false;
        }
		return true;
	}
	/**
	 * [setup 安装程序]
	 */
    public function setup(){
    	$this->setup_ad();
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
    	D('AdCategory')->where(array('org'=>'Mall'))->delete();
        D('Page')->where(array('module'=>'Mall'))->delete();
    }
    /**
     * [setup_ad 安装商城广告位]
     */
    protected function setup_ad(){
    	$path = APP_PATH.C('DEFAULT_MODULE').'/View/'.C('DEFAULT_THEME').'/Config/';
		$adcate = F('config_ads','',$path)?:array();
		$ads = F('Mall/Install/config_ads','',APP_PATH);
		$adcate = array_merge($adcate,$ads);
		F('config_ads',$adcate,$path);
    }
    /**
     * [getError 返回错误]
     */
    public function getError(){
    	return $this->_error;
    }
}
?>