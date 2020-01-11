<?php
namespace Weixinapp\Controller;

use Common\Controller\FrontendController;

class WxSubjectController extends FrontendController
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
    public function index()
    {
        $where['显示数目'] = 10;
        $where['分页显示'] = 1;
        $where['填补字符'] = '…';
        $where['摘要长度'] = 160;
        $class                 = new \Common\qscmstag\subjectTag($where);
        $list                  = $class->run();
        foreach ($list['list'] as $key => $value) {
            $list['list'][$key]['addtime'] = date('Y-m-d',$value['addtime']);
        }
        $this->ajaxReturn(1, '获取数据成功', $list);
    }

    public function show($id)
    {
        $class = new \Common\qscmstag\subject_showTag(array('专题id' => $id));
        $info  = $class->run();
        $info['content'] = strip_tags($info['content']);
        if ($info) {
            $list_class             = new \Common\qscmstag\subject_companyTag(array('专题公司id' => $info['id']));
            $info['comlist']       = $list_class->run();
        }
        $this->ajaxReturn(1, '获取数据成功', $info);
    }
}
