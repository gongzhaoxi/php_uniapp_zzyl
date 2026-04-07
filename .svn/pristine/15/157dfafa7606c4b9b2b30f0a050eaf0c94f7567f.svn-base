<?php
namespace app\common\model;
use app\common\model\BaseModel;
use app\common\enum\ErpOrderShippingEnum;
use think\model\concern\SoftDelete;

class ErpOrderShipping extends BaseModel
{
	use SoftDelete;
    protected $deleteTime = 'delete_time';

  	public function order(){
		return $this->belongsTo('app\common\model\ErpOrder','order_id','id');
	}
	
  	public function orderProduct(){
		return $this->belongsTo('app\common\model\ErpOrderProduct','order_product_id','id');
	}	
	
  	public function product(){
		return $this->belongsTo('app\common\model\ErpProduct','product_id','id');
	}	
	
	public function orderProduce(){
		return $this->hasMany('app\common\model\ErpOrderProduce', 'order_shipping_id', 'id');
    }	
	
	public function getShippingStatusDescAttr($value, $data){
        return ErpOrderShippingEnum::getShippingStatusDesc($data['shipping_status']);
    }
	
	public function getCanCancelAttr($value, $data){
        return $data['shipping_status'] != ErpOrderShippingEnum::SHIPPING_STATUS_FINISH;
    }
	
	public function getCanConfirmAttr($value, $data){
		return $data['shipping_status'] == ErpOrderShippingEnum::SHIPPING_STATUS_PRINTED;
    }	
	
	
	public function getApproveStatusDescAttr($value, $data){
		return ErpOrderShippingEnum::getApproveStatusDesc($data['approve_status']);
    }		
	
	public function getShippingPhotoAttr($value, $data){
		return $value?explode(',',$value):[];
    }  
	
	public function setShippingPhotoAttr($value, $data){
		return is_array($value)?implode(',',$value):'';
    } 
	
}