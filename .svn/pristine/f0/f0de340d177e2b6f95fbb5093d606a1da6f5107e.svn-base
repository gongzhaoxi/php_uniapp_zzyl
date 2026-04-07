<?php
namespace app\admin\validate;
use think\Validate;

/**
 * 报废单验证
 * Class ErpMaterialDiscardValidate
 * @package app\admin\validate
 */
class ErpMaterialDiscardValidate extends Validate{

    protected $rule = [
        'id' 					=> 'require',
        'order_id|订单id' 		=> 'number',
		'remark|备注'			=> 'max:255',
        'type|类型' 			=> 'require',
		'material_type|物料类型'=> 'require|in:1,2',
		'material|物料' 		=> 'require|array',
		'stock_date|出库日期' 	=> 'require|date',
		'ids' 					=> 'require|array',
		'supplier_id|供应商' 	=> 'require|number',
    ];

    protected $message = [
        'id.require' 			=> '参数缺失',
		'ids.require'			=> '请选择数据',
		'ids.array'				=> '请选择数据',
    ];

    public function sceneAdd(){
		return $this->only(['order_id','type','material_type','material','remark','stock_date','supplier_id']);
    }

    public function sceneEdit(){
        return $this->only(['id','order_id','type','remark','stock_date']);
    }

    public function sceneRemove(){
		return $this->only(['ids']);
    }
	
    public function sceneRecycle(){
		return $this->only(['ids']);
    }

}