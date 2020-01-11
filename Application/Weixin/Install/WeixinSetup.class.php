<?php
/**
 * 微信安装程序
 */
class WeixinSetup{
	/**
	 * [init 安装程序初始化程序]
	 */
	public function setup_init(){
		if(false === $apply = F('apply_list')) $apply = D('Apply')->apply_cache();
        if($apply['Weixin']){
            $this->_error = '安装前请先卸载已有微信插件！';
            return false;
        }
		if(!$apply['Mobile']){
			$this->_error = '请先安装触屏端应用！';
			return false;
		}
        if(false === $apply = F('apply_info_list')) $apply = D('Apply')->apply_info_cache();
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
        $urls = M('WeixinMenu')->where(array('type'=>'view'))->getfield('id,url');
        foreach ($urls as $key => $val) {
            if(C('qscms_weixin_appid')){
                $val = str_replace('|appid|',C('qscms_weixin_appid'),$val);
            }
            $val = str_replace('|domain|',C('qscms_site_domain'),$val);
            M('WeixinMenu')->where(array('id'=>$key))->setfield('url',$val);
        }
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
    public function unload(){}
    /**
     * [getError 返回错误]
     */
    public function getError(){
    	return $this->_error;
    }
}
?>