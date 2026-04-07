<?php
namespace app\admin\validate;
use think\Validate;

/**
 * 随工单验证
 * Class CustomerValidate
 * @package app\admin\validate
 */
class ErpFollowValidate extends Validate{

    protected $rule = [
        'id' 					=> 'require',
        'name|随工单名称'		=> 'require|max:255|unique:app\common\model\ErpFollow',
		'code|随工单编码'		=> 'require|max:255|unique:app\common\model\ErpFollow',
		'iso|ISO文件档案号' 	=> 'require',
		'address|生产地点' 		=> 'require',
        'status' 				=> 'require|in:0,1',
		'product|产品' 			=> 'array',
		'process|工序' 			=> 'array',
    ];

    protected $message = [
        'id.require' 			=> '参数缺失',
        'status.require' 		=> '请选择状态',
        'status.in' 			=> '状态参数错误',
		'ids.require'			=> '请选择数据',
		'ids.array'				=> '请选择数据',
    ];

    public function sceneAdd(){
		return $this->only(['name','status','code','iso','address','product','process']);
    }

    public function sceneEdit(){
        return $this->only(['id','name','status','code','iso','address','product','process']);
    }

    public function sceneRemove(){
		return $this->only(['ids']);
    }
	
    public function sceneRecycle(){
		return $this->only(['ids']);
    }

}