<?php
namespace app\admin\validate;
use think\Validate;
use app\common\model\DictType;

/**
 * 字典数据验证
 * Class DictDataValidate
 * @package app\admin\validate
 */
class DictDataValidate extends Validate{

    protected $rule = [
        'id' 		=> 'require',
        'name' 		=> 'require|length:1,255',
        //'value' 	=> 'require',
        'type_id' 	=> 'require|checkDictType',
        'status' 	=> 'require|in:0,1',
    ];

    protected $message = [
        'id.require' 		=> '参数缺失',
        'name.require' 		=> '请填写字典数据名称',
        'name.length' 		=> '字典数据名称长度须在1-255位字符',
        'value.require' 	=> '请填写字典数据值',
        'type_id.require'	=> '字典类型缺失',
        'status.require' 	=> '请选择字典数据状态',
        'status.in' 		=> '字典数据状态参数错误',
    ];

    public function sceneAdd(){
		return $this->only(['type_id','name','value','status']);
    }

    public function sceneEdit(){
        return $this->only(['id','name','value','status']);
    }

    protected function checkDictType($value){
        if(DictType::where('id',$value)->count() == 0) {
			return '字典类型不存在';
        }
		return true;
    }

}