<?php
namespace app\admin\validate;
use think\Validate;

/**
 * 产品验证
 * Class CustomerValidate
 * @package app\admin\validate
 */
class ErpProductProjectValidate extends Validate{

    protected $rule = [
        'id' 						=> 'require',
        'code|方案编号' 			=> 'require|max:100|unique:app\common\model\ErpProductProject',
        'name|方案名称' 			=> 'require|max:100',
		'product_id|产品' 			=> 'require|number',
		'cid|方案分类' 				=> 'require|number',
		'type|方案功能' 			=> 'require|number',
        'status' 					=> 'require|in:0,1',	
		'ids' 						=> 'require|array',
		'erp_project_id|方案' 		=> 'require',
    ];

    protected $message = [
        'id.require' 			=> '参数缺失',
        'status.require' 		=> '请选择状态',
        'status.in' 			=> '状态参数错误',
		'cid.require' 			=> '请选择方案分类',
        'cid.number' 			=> '方案分类错误',
		'ids.require'			=> '请选择数据',
		'ids.array'				=> '请选择数据',
    ];

    public function sceneAdd(){
		return $this->only(['code','name','cid','product_id','type','status']);
    }

    public function sceneAddFromProject(){
		return $this->only(['erp_project_id','product_id']);
    }

    public function sceneEdit(){
		return $this->only(['id','code','name','cid','status']);
    }

    public function sceneRemove(){
		return $this->only(['ids']);
    }
	
    public function sceneRecycle(){
		return $this->only(['ids']);
    }

}