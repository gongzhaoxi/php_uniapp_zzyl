<?php
namespace app\admin\validate;
use think\Validate;


class ErpProductStockValidate extends Validate{

    protected $rule = [
        'id' 					=> 'require',
		'remark|备注'			=> 'max:255',
        'supplier_id|供应商' 	=> 'require|number',
		'purchase_date|采购日期'=> 'require|date',
		'material|物料' 		=> 'require|array',
		'stock_date|入库日期' 	=> 'require|date',
		'ids' 					=> 'require|array',
    ];

    protected $message = [
        'id.require' 			=> '参数缺失',
		'ids.require'			=> '请选择数据',
		'ids.array'				=> '请选择数据',
    ];

    public function sceneAdd(){
		return $this->only(['supplier_id','purchase_date','material','remark','stock_date']);
    }

    public function sceneEdit(){
        return $this->only(['id','supplier_id','remark','stock_date']);
    }

    public function sceneConfirm(){
		return $this->only(['ids']);
    }
	
    public function sceneReturned(){
		return $this->only(['ids']);
    }

}