<?php
/**
 * 触屏安装程序
 */
class MobileSetup{
	/**
	 * [setup_init 安装程序初始化程序]
	 */
	public function setup_init(){
        if(false === $apply = F('apply_info_list')) $apply = D('Apply')->apply_info_cache();
        if($apply['Mobile']){
            $this->_error = '安装前请先卸载已有触屏插件！';
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
        $mod = D('Page');
        $field = $mod->getDbFields();
        if(!in_array('type',$field)){
            $mod->execute("ALTER TABLE `qs_page` ADD `type` varchar(30) NOT NULL");
        }
        $mod->where(array('module'=>'Mobile'))->delete();
        $mod->where(array('type'=>''))->setfield(array('type'=>'Home'));
		return true;
	}
	/**
	 * [setup 安装程序]
	 */
    public function setup(){
        return true;
    }
    /**
     * [init 安装程序初始化程序]
     */
    public function unload_init(){
        if(false === $apply = F('apply_list')) $apply = D('Apply')->apply_cache();
        if($apply['Weixin']){
            $this->_error = '微信正在运行，请先卸载微信应用！';
            return false;
        }
        return true;
    }
    /**
     * [unload 卸载程序]
     */
    public function unload(){
        M('Page')->where(array('module|type'=>'Mobile'))->delete();
    }
    /**
     * [getError 返回错误]
     */
    public function getError(){
    	return $this->_error;
    }
}
?>