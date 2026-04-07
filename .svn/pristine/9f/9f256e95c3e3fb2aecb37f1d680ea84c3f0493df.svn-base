<?php
namespace app\admin\validate;
use think\Validate;

/**
 * 订单验证
 * Class CustomerValidate
 * @package app\admin\validate
 */
class ErpOrderValidate extends Validate{

    protected $rule = [
        'id' 								=> 'require',
        'order_sn|合同号' 					=> 'require|max:20|unique:app\common\model\ErpOrder',
        'customer_id' 						=> 'require|number',
		'delivery_time|交货日期' 			=> 'require|date',
		'delivery_remark|交货备注' 			=> 'max:255',
		'address|收货地址/国家' 			=> 'require|max:255',
		'region_type' 						=> 'require|in:1,2',
		'contacts|联系人' 					=> 'require|max:100',
		'phone|联系电话' 					=> 'require|max:100',
        'cabinet_num|装柜号' 				=> 'max:255',
		'technical_parameter|技术参数' 		=> 'max:255',
		'customer_remark|客户要求' 			=> 'max:255',
		'motor_code|电机编码' 				=> 'max:255',
		'is_special' 						=> 'in:0,1',
		'order_remark|订单备注' 			=> 'max:255',
		'ids' 								=> 'require|array',
    ];

    protected $message = [
        'id.require' 			=> '参数缺失',
        'customer_id.require' 	=> '请选择客户',
        'customer_id.number' 	=> '客户不存在',
		'region_type.require' 	=> '请选择域属',
        'region_type.in' 		=> '域属参数错误',
		'is_special.require' 	=> '请选择是否特殊订单',
        'is_special.in' 		=> '是否特殊订单错误',
		'ids.require'			=> '请选择数据',
		'ids.array'				=> '请选择数据',
    ];

    public function sceneAdd(){
		return $this->only(['customer_id','customer_name','delivery_time','delivery_remark','address','region_type','contacts','phone','cabinet_num','technical_parameter','customer_remark','shipping_type','motor_code','is_special','order_remark']);
    }

    public function sceneEdit(){
        return $this->only(['id','order_sn','customer_id','customer_name','delivery_time','delivery_remark','address','region_type','contacts','phone','cabinet_num','technical_parameter','customer_remark','shipping_type','motor_code','is_special','order_remark']);
    }

    public function sceneRemove(){
		return $this->only(['ids']);
    }
	
    public function sceneRecycle(){
		return $this->only(['ids']);
    }

}