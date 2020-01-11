<?php
namespace Mobile\Controller;
use Mobile\Controller\MobileController;

class NewsController extends MobileController
{
    // 初始化函数
    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        if(C('PLATFORM') != 'mobile'){
            redirect(U('Home/News/index','',true,C('qscms_site_domain')));
        }
        $this->display();
    }

    /**
     * 资讯详情
     */
    public function show()
    {
        if(C('PLATFORM') != 'mobile'){
            redirect(U('Home/News/news_show',array('id'=>intval($_GET['id'])),true,C('qscms_site_domain')));
        }
        $this->wx_share();
        $config = D('Config')->config_cache();
        
        if($config['qscms_m_template_dir'] == 'qichen'){
            $this->display('show1'); 
        }else{
            $this->display();
        }
    }
}

?>