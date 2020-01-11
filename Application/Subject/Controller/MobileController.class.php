<?php
namespace Subject\Controller;

class MobileController extends \Mobile\Controller\MobileController
{
    // 初始化函数
    public function _initialize()
    {
        parent::_initialize();
        
        $tpl_dir = C('TPL_PUBLIC_DIR');
        $tpl_dir = str_replace("Mobile", "Subject", $tpl_dir);
        $tpl_dir = str_replace(C('DEFAULT_THEME'), "mobile", $tpl_dir);
        $this->assign('tpl_public_dir',$tpl_dir);
    }

    public function index()
    {
        /*if(C('PLATFORM') != 'mobile'){
            redirect(U('Home/News/index','',true,C('qscms_site_domain')));
        }*/
        $this->theme('mobile')->display('Subject@./index');die;
    }

    /**
     * 资讯详情
     */
    public function show()
    {
        /*if(C('PLATFORM') != 'mobile'){
            redirect(U('Home/News/news_show',array('id'=>intval($_GET['id'])),true,C('qscms_site_domain')));
        }
        $this->wx_share();*/
        $this->theme('mobile')->display('Subject@./show');die;
    }
}

?>