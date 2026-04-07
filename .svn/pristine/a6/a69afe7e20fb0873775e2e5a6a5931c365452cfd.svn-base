<?php
namespace app\admin\validate;
use think\Validate;


class ErpPurchaseApplyValidate extends Validate{

    protected $rule = [
        'id' 					=> 'require',
		'remark|备注'			=> 'max:255',
		'material|物料' 		=> 'require|array',
		'apply_date|申请日期' 	=> 'require|date',
		'supplier_id|供应商' 	=> 'require',
		'ids' 					=> 'require|array',
    ];

    protected $message = [
        'id.require' 			=> '参数缺失',
		'ids.require'			=> '请选择数据',
		'ids.array'				=> '请选择数据',
    ];

    public function sceneMaterialAdd(){
		return $this->only(['material','remark','apply_date']);
    }

    public function sceneEdit(){
        return $this->only(['id','material','remark','apply_date']);
    }

    public function sceneRemove(){
		return $this->only(['ids']);
    }

}