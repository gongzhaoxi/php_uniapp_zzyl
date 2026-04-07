<?php
namespace app\admin\validate;
use think\Validate;

/**
 * 出库单验证
 * Class ErpMaterialOutValidate
 * @package app\admin\validate
 */
class ErpMaterialAllocateValidate extends Validate{

    protected $rule = [
        'id' 					=> 'require',
        'order_id|订单id' 		=> 'number',
		'remark|备注'			=> 'max:255',
        'type|类型' 			=> 'require',
		'material|物料' 		=> 'require|array',
		'stock_date|调拨日期' 	=> 'require|date',
		'ids' 					=> 'require|array',
    ];

    protected $message = [
        'id.require' 			=> '参数缺失',
		'ids.require'			=> '请选择数据',
		'ids.array'				=> '请选择数据',
    ];

    public function sceneAdd(){
		return $this->only(['order_id','type','material','remark','stock_date']);
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