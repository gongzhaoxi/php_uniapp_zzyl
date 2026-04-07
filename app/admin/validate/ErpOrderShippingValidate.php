<?php
namespace app\admin\validate;
use think\Validate;

/**
 * 客户验证
 * Class CustomerValidate
 * @package app\admin\validate
 */
class ErpOrderShippingValidate extends Validate{

    protected $rule = [
		'shipping_sn' 		=> 'require',
		'shipping_num' 		=> 'require',
		'shipping_photo' 	=> 'require',
		'ids' 				=> 'require|array',
    ];

    protected $message = [
		'ids.require'				=> '请选择数据',
		'ids.array'					=> '请选择数据',
		'shipping_num.require'		=> '物流单号不能为空',
		'shipping_photo.require'	=> '请上传物流单据图片',
    ];


    public function scenePrint(){
		return $this->only(['ids']);
    }
	
    public function sceneCancel(){
		return $this->only(['ids']);
    }

    public function sceneConfirm(){
		return $this->only(['shipping_sn','shipping_num','shipping_photo']);
    }

}