<?php
namespace app\admin\validate;
use think\Validate;

/**
 * 生产流程验证
 * Class CustomerValidate
 * @package app\admin\validate
 */
class ErpSupplierProcessValidate extends Validate{

    protected $rule = [
        'id' 						=> 'require',
		'supplier_id|供应商'		=> 'require',
		'sn|工艺编码' 				=> 'require|max:50|unique:app\common\model\ErpSupplierProcess',
        'name|工艺名称'				=> 'require|max:50',
		'last_price|上次单价' 		=> 'require|float',
		'price|本次单价' 			=> 'require|float',
		'effective_date|生效日期'	=> 'require|date',
        'status' 					=> 'require|in:0,1',
		'ids' 						=> 'require|array',
    ];

    protected $message = [
        'id.require' 			=> '参数缺失',
        'status.require' 		=> '请选择状态',
        'status.in' 			=> '状态参数错误',
		'ids.require'			=> '请选择数据',
		'ids.array'				=> '请选择数据',
    ];

    public function sceneAdd(){
		return $this->only(['sn','name','last_price','price','effective_date','status']);
    }

    public function sceneEdit(){
        return $this->only(['id','sn','name','last_price','price','effective_date','status']);
    }

    public function sceneRemove(){
		return $this->only(['ids']);
    }

}