<?php
namespace app\admin\validate;
use think\Validate;


class ErpProductionSettingValidate extends Validate{

    protected $rule = [
        'id' 								=> 'require',
        'produce_date|日期' 				=> 'require|date',
        'produce_num|日产值' 				=> 'require|number',
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

    public function sceneSave(){
		return $this->only(['produce_date','produce_num']);
    }

    public function sceneRemove(){
		return $this->only(['ids']);
    }
	
    public function sceneRecycle(){
		return $this->only(['ids']);
    }

}