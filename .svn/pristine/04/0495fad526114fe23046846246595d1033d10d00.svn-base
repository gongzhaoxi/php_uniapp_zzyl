<?php
namespace app\admin\validate;
use think\Validate;

/**
 * 订单商品验证
 * Class ErpOrderAftersaleValidate
 * @package app\admin\validate
 */
class ErpOrderAftersaleValidate extends Validate{

    protected $rule = [
        'id' 								=> 'require',
        'order_id|订单' 					=> 'require|number',
		'material_id|物料' 					=> 'require|number',
		'material_sn|物料编码' 				=> 'require|max:255',
		'material_name|物料名称' 			=> 'require|max:255',
		'material_num|物料数量' 			=> 'require|number',
		'ids' 								=> 'require|array',
    ];

    protected $message = [
        'id.require' 			=> '参数缺失',
		'ids.require'			=> '请选择数据',
		'ids.array'				=> '请选择数据',
    ];

    public function sceneAdd(){
		return $this->only(['order_id','material_id','material_sn','material_name','material_num']);
    }

    public function sceneEdit(){
        return $this->only(['id','material_num']);
    }

    public function sceneRemove(){
		return $this->only(['ids']);
    }
	
    public function sceneRecycle(){
		return $this->only(['ids']);
    }

}