<?php
namespace app\admin\validate;
use think\Validate;

/**
 * 订单商品验证
 * Class ErpOrderProductValidate
 * @package app\admin\validate
 */
class ErpOrderProductValidate extends Validate{

    protected $rule = [
        'id' 								=> 'require',
        'order_id|订单' 					=> 'require|number',
		'product_id|产品' 					=> 'require|number',
		'product_sn|产品编码' 				=> 'require|max:255',
		'product_name|产品名称' 			=> 'require|max:255',
		'product_model|型号' 				=> 'require|max:255',
		'product_specs|款式' 				=> 'require|max:255',
		'product_num|产品数量' 				=> 'require|number',
		'currency|币种' 					=> 'require|max:50',
		'exchange_rates|汇率' 				=> 'require|max:50',
		'tax_rate|税率' 					=> 'require|max:50',
		'product_price|单价' 				=> 'require|float',
		'total_price|总价' 					=> 'require|float',
		'ids' 								=> 'require|array',
    ];

    protected $message = [
        'id.require' 			=> '参数缺失',
		'ids.require'			=> '请选择数据',
		'ids.array'				=> '请选择数据',
    ];

    public function sceneAdd(){
		return $this->only(['order_id','product_id','product_sn','product_name','product_model','product_num','currency','exchange_rates','tax_rate','product_price','total_price']);
    }

    public function sceneEdit(){
        return $this->only(['id','product_model','product_num','currency','exchange_rates','tax_rate','product_price','total_price']);
    }

    public function sceneRemove(){
		return $this->only(['ids']);
    }
	
    public function sceneRecycle(){
		return $this->only(['ids']);
    }

}