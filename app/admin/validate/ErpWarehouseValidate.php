<?php
namespace app\admin\validate;
use think\Validate;

/**
 * 仓库验证
 * Class CustomerValidate
 * @package app\admin\validate
 */
class ErpWarehouseValidate extends Validate{

    protected $rule = [
        'id' 			=> 'require',
        'sn' 			=> 'require|max:100|unique:app\common\model\ErpWarehouse',
        'name' 			=> 'require|max:100',
        'status' 		=> 'require|in:0,1',
		'type' 			=> 'require|in:1,2,3,4',
		'ids' 			=> 'require|array',
    ];

    protected $message = [
        'id.require' 			=> '参数缺失',
        'sn.require' 			=> '仓库编码不能为空',
        'sn.max' 				=> '仓库编码最多100位字符',
		'sn.unique' 			=> '仓库编码已存在',
        'name.require' 			=> '仓库名称不能为空',
        'name.max' 				=> '仓库名称最多100位字符',
		'type.require' 			=> '请选择仓库类型',
        'type.in' 				=> '仓库类型错误',
		'ids.require'			=> '请选择数据',
		'ids.array'				=> '请选择数据',
    ];

    public function sceneAdd(){
		return $this->only(['sn','name','status','type']);
    }

    public function sceneEdit(){
        return $this->only(['id','name','sn','status','type']);
    }

    public function sceneRemove(){
		return $this->only(['ids']);
    }
	
    public function sceneRecycle(){
		return $this->only(['ids']);
    }

}