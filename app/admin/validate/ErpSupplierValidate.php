<?php
namespace app\admin\validate;
use think\Validate;

/**
 * 供应商验证
 * Class CustomerValidate
 * @package app\admin\validate
 */
class ErpSupplierValidate extends Validate{

    protected $rule = [
        'id' 					=> 'require',
        'name|名称'				=> 'require|max:50|unique:app\common\model\ErpSupplier',
        'status' 				=> 'require|in:0,1',
		'code|供应链组织码'		=> 'require|max:50|unique:app\common\model\ErpSupplier',
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
		return $this->only(['name','status','code']);
    }

    public function sceneEdit(){
        return $this->only(['id','name','status','code']);
    }

    public function sceneRemove(){
		return $this->only(['ids']);
    }
	
    public function sceneRecycle(){
		return $this->only(['ids']);
    }

}