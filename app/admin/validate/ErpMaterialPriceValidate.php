<?php
namespace app\admin\validate;
use think\Validate;

class ErpMaterialPriceValidate extends Validate{

    protected $rule = [
        'id' 						=> 'require',
		'supplier_id|供应商'		=> 'require',
		'material_id|物料' 			=> 'require|unique:app\common\model\ErpMaterialPrice,material_id^supplier_id',
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
		return $this->only(['material_id','last_price','price','effective_date','status']);
    }

    public function sceneEdit(){
        return $this->only(['id','material_id','last_price','price','effective_date','status']);
    }

    public function sceneRemove(){
		return $this->only(['ids']);
    }

}