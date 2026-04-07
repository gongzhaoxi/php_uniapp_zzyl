<?php
namespace app\admin\validate;
use think\Validate;

class ErpNoticeValidate extends Validate{

    protected $rule = [
        'id' 						=> 'require',
		'auditing_admin_id|审批人' 	=> 'require',
		'notice_admin_id|接收人' 	=> 'require',
        'content|通知内容' 			=> 'require',
		'ids' 						=> 'require|array',
    ];

    protected $message = [
        'id.require' 			=> '参数缺失',
		'ids.require'			=> '请选择数据',
		'ids.array'				=> '请选择数据',
    ];

    public function sceneAdd(){
		return $this->only(['auditing_admin_id','notice_admin_id','content']);
    }

    public function sceneEdit(){
        return $this->only(['id','auditing_admin_id','notice_admin_id','content']);
    }

    public function sceneRemove(){
		return $this->only(['ids']);
    }

}