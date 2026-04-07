<?php
namespace app\admin\validate;
use think\Validate;

/**
 * 客户验证
 * Class CustomerValidate
 * @package app\admin\validate
 */
class ErpCustomerValidate extends Validate{

    protected $rule = [
        'id' 			=> 'require',
        'sn' 			=> 'require|max:100|unique:app\common\model\ErpCustomer',
        'name' 			=> 'require|max:100',
		'contacts' 		=> 'max:100',
		'phone' 		=> 'max:100',
		'address' 		=> 'max:255',
		'address_en'	=> 'max:255',
        'status' 		=> 'require|in:0,1',
		'region_type' 	=> 'require|in:1,2',
		'ids' 			=> 'require|array',
    ];

    protected $message = [
        'id.require' 			=> '参数缺失',
        'sn.require' 			=> '客户编码不能为空',
        'sn.max' 				=> '客户编码最多100位字符',
        'name.require' 			=> '客户名称/订货单位不能为空',
        'name.max' 				=> '客户名称/订货单位最多100位字符',
		'contacts.max' 			=> '联系人最多100个字符',
		'phone.max' 			=> '联系电话最多100个字符',
		'address.max' 			=> '收货地址/国家最多255个字符',
		'address_en.max' 		=> '收货地址/国家(英)最多255个字符',
        'status.require' 		=> '请选择客户状态',
        'status.in' 			=> '客户状态参数错误',
		'region_type.require' 	=> '请选择域属',
        'region_type.in' 		=> '域属参数错误',
		'ids.require'			=> '请选择数据',
		'ids.array'				=> '请选择数据',
    ];

    public function sceneAdd(){
		return $this->only(['sn','name','contacts','phone','address','address_en','status','region_type']);
    }

    public function sceneEdit(){
        return $this->only(['id','sn','name','contacts','phone','address','address_en','status','region_type']);
    }

    public function sceneRemove(){
		return $this->only(['ids']);
    }
	
    public function sceneRecycle(){
		return $this->only(['ids']);
    }

}