<?php
namespace Report\Model;
use Think\Model;
class CompanyReportModel extends Model
{
	protected $_validate = array(
		array('uid,com_id,com_name,corporate,com_type,reg_time,reg_capital,reg_address,office_address,registrar,scope,office_area,office_env,workplace,number,sex_ratio,average_age,route,evaluation,certifier','identicalNull','',0,'callback'),
		array('uid,com_id,reg_capital,average_age,office_area','identicalEnum','',0,'callback'),
		array('scope','1,240','{%company_report_scope_length_error}',2,'length'),
		array('evaluation','1,480','{%company_report_evaluation_length_error}',2,'length')
	);

	protected $_auto = array (
		array('status',1),
		array('office_env',2)
	);

    /**
     * 添加或更新记录
     * @param array $data 添加或更新内容
     * @return boolean 添加或更新状态
     */
    public function update($data=array()) {
        $data = $this->create($data);
        if (!$data) { //数据对象创建错误
            return false;
        }
        $data['reg_time'] = strtotime($data['reg_time']);
        $data['addtime'] = strtotime($data['addtime']);
        $pk = $this->getPk();
        /* 添加或更新数据 */
        if (empty($data[$pk])) {
            $res = $this->add($data);
        } else {
            $res = $this->save($data);
        }
        return $res;
    }
}
?>