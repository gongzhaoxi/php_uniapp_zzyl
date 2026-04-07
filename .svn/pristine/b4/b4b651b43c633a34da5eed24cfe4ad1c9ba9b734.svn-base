<?php
namespace app\admin\validate;
use think\Validate;

/**
 * 生产流程验证
 * Class CustomerValidate
 * @package app\admin\validate
 */
class ErpProcessValidate extends Validate{

    protected $rule = [
        'id' 					=> 'require',
		'sn|工艺组编码' 		=> 'require|max:50|unique:app\common\model\ErpProcess',
        'name|工艺组名称'		=> 'require|max:50|unique:app\common\model\ErpProcess',
		'user_id|岗位员工' 		=> 'require',
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
		return $this->only(['sn','name','user_id','status']);
    }

    public function sceneEdit(){
        return $this->only(['id','sn','name','user_id','status']);
    }

    public function sceneRemove(){
		return $this->only(['ids']);
    }
	
    public function sceneRecycle(){
		return $this->only(['ids']);
    }

}