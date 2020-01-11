<?php

/**
 * 租房安装程序
 */
class CareerSetup {
    /**
     * [init 安装程序初始化程序]
     */
    public function setup_init() {
        if (false === $apply = F('apply_info_list')) $apply = D('Apply')->apply_info_cache();
        if($apply['Career']){
            $this->_error = '安装前请先卸载已有事业单位招考插件！';
            return false;
        }
        if ($apply['Home']['version']) {
            $version = explode('.', $apply['Home']['version']);
            $v = $version[0] * 1000000 + $version[1] * 10000 + $version[2];
            if ($v < 5000001) {
                $this->_error = '请将基础版程序升级至5.0.1版本以上（含）！';
                return false;
            }
        }
        if ($apply['Mobile']['version']) {
            $version = explode('.', $apply['Mobile']['version']);
            $v = $version[0] * 1000000 + $version[1] * 10000 + $version[2];
            if ($v < 5000001) {
                $this->_error = '请将触屏版程序升级至5.0.1版本以上（含）！';
                return false;
            }
        }
        return true;
    }

    /**
     * [setup 安装程序]
     */
    public function setup() {
    }

    /**
     * [init 安装程序初始化程序]
     */
    public function unload_init() {
        return true;
    }

    /**
     * [unload 卸载程序]
     */
    public function unload() {
    }

    /**
     * [getError 返回错误]
     */
    public function getError() {
        return $this->_error;
    }
}

?>