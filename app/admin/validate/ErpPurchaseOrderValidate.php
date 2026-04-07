<?php
namespace app\admin\validate;
use think\Validate;


class ErpPurchaseOrderValidate extends Validate{

    protected $rule = [
        'id' 					=> 'require',
		'remark|备注'			=> 'max:255',
		'material|物料' 		=> 'require|array',
		'order_date|申请日期' 	=> 'require|date',
		'supplier_id|供应商' 	=> 'require',
		'ids' 					=> 'require|array',
		'delivery_date|要求交货日期'=> 'require|date',
		'apply_ids' 			=> 'array',
    ];

    protected $message = [
        'id.require' 			=> '参数缺失',
		'ids.require'			=> '请选择数据',
		'ids.array'				=> '请选择数据',
    ];

    public function sceneAdd(){
		return $this->only(['supplier_id','material','remark','order_date','apply_ids','delivery_date']);
    }

    public function sceneEdit(){
        return $this->only(['id','supplier_id','material','remark','order_date','delivery_date']);
    }

    public function sceneRemove(){
		return $this->only(['ids']);
    }

}