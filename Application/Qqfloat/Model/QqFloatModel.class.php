<?php
namespace Qqfloat\Model;
use Think\Model;
class QqFloatModel extends Model{
	protected $_validate = array(
		array('qq','require','QQ号不能为空！',self::EXISTS_VALIDATE,'regex'),
		array('qq','qq','QQ号不合法！',self::EXISTS_VALIDATE,'regex'),
		array('name','require','所属客服不能为空！',self::EXISTS_VALIDATE,'regex'),
        array('name','1,15','所属客服应在1-15个字内！',self::EXISTS_VALIDATE,'length'),
	);
	protected $_auto = array ( 

	);
}
?>