<?php
namespace app\admin\validate;
use app\common\model\DictType;
use think\Validate;

/**
 * 字典类型验证
 * Class DictTypeValidate
 * @package app\admin\validate
 */
class DictTypeValidate extends Validate{
    
    protected $rule = [
        'id' 		=> 'require',
        'name' 		=> 'require|length:1,255',
        'type' 		=> 'require|unique:' . DictType::class,
        'status' 	=> 'require|in:0,1',
		'remark' 	=> 'max:200',
    ];

    protected $message = [
        'id.require' 	=> '参数缺失',
        'name.require' 	=> '请填写字典名称',
        'name.length' 	=> '字典名称长度须在1~255位字符',
        'type.require' 	=> '请填写字典类型',
        'type.unique' 	=> '字典类型已存在',
        'status.require'=> '请选择状态',
        'remark.max' 	=> '备注长度不能超过200',
    ];

    public function sceneAdd(){
        return $this->only(['name','type','status','remark']);
    }
    
    public function sceneEdit(){
		return $this->only(['id','name','type','status','remark']);
    }

}