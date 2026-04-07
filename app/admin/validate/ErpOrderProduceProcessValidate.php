<?php
namespace app\admin\validate;
use think\Validate;


class ErpOrderProduceProcessValidate extends Validate{

    protected $rule = [
        'id' 							=> 'require',
        'confirm_date|随工单完成时间' 	=> 'require|date',
		'price|报工单价' 				=> 'require|float',
        'username|完成人' 				=> 'require',
		'ids' 					=> 'require|array',
    ];

    protected $message = [
        'id.require' 			=> '参数缺失',
		'ids.require'			=> '请选择数据',
		'ids.array'				=> '请选择数据',
    ];

    public function sceneEdit(){
        return $this->only(['id','confirm_date','price','username']);
    }

    public function sceneRemove(){
		return $this->only(['ids']);
    }

}