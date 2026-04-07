<?php
namespace app\admin\validate;
use think\Validate;

/**
 * 入库单验证
 * Class ErpMaterialEnterValidate
 * @package app\admin\validate
 */
class ErpMaterialEnterValidate extends Validate{

    protected $rule = [
        'id' 						=> 'require',
		'batch_number|入库批次号'	=> 'require|max:50',
		'supplier_id|供应商' 	=> 'require|number',
        'order_id|订单id' 		=> 'number',
		'remark|备注'			=> 'max:255',
        'type|类型' 			=> 'require',
		'material_type|物料类型'=> 'require|in:1,2',
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
		return $this->only(['order_id','supplier_id','batch_number','type','material_type','material','remark','stock_date']);
    }

    public function sceneEdit(){
        return $this->only(['id','order_id','type','material','remark','stock_date']);
    }

    public function sceneRemove(){
		return $this->only(['ids']);
    }
	
    public function sceneRecycle(){
		return $this->only(['ids']);
    }

}