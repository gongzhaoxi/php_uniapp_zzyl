<?php
namespace app\admin\validate;
use think\Validate;

/**
 * 出库单验证
 * Class ErpMaterialCheckValidate
 * @package app\admin\validate
 */
class ErpMaterialCheckValidate extends Validate{

    protected $rule = [
        'id' 					=> 'require',
        'order_id|订单id' 		=> 'number',
		'remark|备注'			=> 'max:255',
        'type|类型' 			=> 'require',
		'material_type|物料类型'=> 'require|in:1,2',
		'material|物料' 		=> 'require|array',
		'stock_date|盘点日期' 	=> 'require|date',
		'ids' 					=> 'require|array',
    ];

    protected $message = [
        'id.require' 			=> '参数缺失',
		'ids.require'			=> '请选择数据',
		'ids.array'				=> '请选择数据',
    ];

    public function sceneAdd(){
		return $this->only(['order_id','type','material_type','material','remark','stock_date']);
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