<?php
namespace app\admin\validate;
use think\Validate;

/**
 * 车间员工验证
 * Class CustomerValidate
 * @package app\admin\validate
 */
class ErpUserValidate extends Validate{

    protected $rule = [
        'id' 					=> 'require',
        'name|姓名' 			=> 'require|max:50',
		'title|职称' 			=> 'require|max:50',
		'mobile|手机号码' 		=> 'require|mobile|unique:app\common\model\ErpUser',
		'sn|手机端身份识别码' 	=> 'require|max:50',
        'status' 				=> 'require|in:0,1',
		'ids' 					=> 'require|array',
    ];

    protected $message = [
        'id.require' 			=> '参数缺失',
        'status.require' 		=> '请选择状态',
        'status.in' 			=> '状态参数错误',
		'ids.require'			=> '请选择数据',
		'ids.array'				=> '请选择数据',
    ];

    public function sceneAdd(){
		return $this->only(['name','title','mobile','sn','status']);
    }

    public function sceneEdit(){
        return $this->only(['id','name','title','mobile','sn','status']);
    }

    public function sceneRemove(){
		return $this->only(['ids']);
    }
	
    public function sceneRecycle(){
		return $this->only(['ids']);
    }

}