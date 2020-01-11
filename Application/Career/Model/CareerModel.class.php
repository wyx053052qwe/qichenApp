<?php
namespace Career\Model;
use Think\Model;
class CareerModel extends Model{
	protected $_validate = array(
		array('title','require','请填写标题！',1),
        array('small_img','require','请上传招考图片！',1),
        array('content','require','请填写招考内容！',1),
		array('title','2,50','招考标题应在1~25个字内！',0,'length'),
	);

	protected $_auto = array (
        array('is_display',1),
        array('article_order',255),
        array('click',1),
    );

}
?>