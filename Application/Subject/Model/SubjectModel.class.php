<?php
namespace Subject\Model;
use Think\Model;
class SubjectModel extends Model{
	protected $_validate = array(
		array('title','require','请填写标题！',1),
        array('s_tpl_name','require','请选择专题模板！',1),
        array('small_img','require','请上传专题图片！',1),
        array('content','require','请填写专题内容！',1),
		array('title','2,50','专题标题应在1~25个字内！',0,'length'),
	);

	protected $_auto = array (
        array('is_display',1),
        array('article_order',255),
        array('click',1),
    );

}
?>