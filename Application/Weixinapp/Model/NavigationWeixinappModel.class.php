<?php
namespace Weixinapp\Model;
use Think\Model;
class NavigationWeixinappModel extends Model{
	/**
	 * [nav_cache 读取页导航写入缓存]
	 * @return [array] [description]
	 */
	public function nav_cache(){
		$nav_list = $this->order('ordid desc,id asc')->select();
		F('nav_weixinapp_list',$nav_list);
		return $nav_list;
	}
    public function get_nav(){
        if(false === $list = F('nav_weixinapp_list')){
            $list = $this->nav_cache();
        }
        return $list;
    }
	/**
     * 后台有更新则删除缓存
     */
    protected function _before_write($data, $options) {
        F('nav_weixinapp_list', NULL);
    }
    /**
     * 后台有删除也删除缓存
     */
    protected function _after_delete($data,$options){
        F('nav_weixinapp_list', NULL);
    }
}
?>