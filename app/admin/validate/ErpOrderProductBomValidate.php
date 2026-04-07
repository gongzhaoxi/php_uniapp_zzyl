<?php
namespace app\admin\validate;
use think\Validate;

/**
 * 订单商品验证
 * Class ErpOrderProductBomValidate
 * @package app\admin\validate
 */
class ErpOrderProductBomValidate extends Validate{

    protected $rule = [
        'id' 								=> 'require|number',
		'type|类型' 						=> 'require|in:2,3',
        'product_bom_id|标配物料' 			=> 'require|number',
		'order_id|标配' 					=> 'require|number',
		'order_product_id|订单产品' 		=> 'require|number',
		'replace_product_bom_id|替换物料' 	=> 'requireIf:type,2|number',
		'bill_type|类型' 					=> 'require|number',
		'num|数量' 							=> 'requireIf:type,3|number',
		'ids' 								=> 'require|array',
    ];

    protected $message = [
        'id.require' 			=> '参数缺失',
		'ids.require'			=> '请选择数据',
		'ids.array'				=> '请选择数据',
    ];

    public function sceneAdd(){
		return $this->only(['product_bom_id','order_product_id','replace_product_bom_id','bill_type','num']);
    }

    public function sceneEdit(){
        return $this->only(['id','product_bom_id','order_product_id','replace_product_bom_id','bill_type','num']);
    }

    public function sceneRemove(){
		return $this->only(['id']);
    }
	
    public function sceneRecycle(){
		return $this->only(['id']);
    }

}