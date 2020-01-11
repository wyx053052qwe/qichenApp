<?php
namespace Subject\Model;
use Think\Model;
class SubjectCompanyModel extends Model{
    protected $_validate = array(
        array('company_uid','require','请填写企业UID！',1),
    );

    protected $_auto = array (
        array('addtime','time',1,'function'),//添加时间
    );

}
?>