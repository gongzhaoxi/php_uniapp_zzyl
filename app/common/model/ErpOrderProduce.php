<?php
namespace app\common\model;
use app\common\model\BaseModel;
use app\common\enum\{ErpOrderProduceEnum,ErpOrderEnum};
use think\model\concern\SoftDelete;


class ErpOrderProduce extends BaseModel
{
	use SoftDelete;
	use \app\common\traits\OrderProductProject;
    protected $deleteTime = 'delete_time';
	protected $json = ['report'];
	protected $jsonAssoc = true;

  	public function order(){
		return $this->belongsTo('app\common\model\ErpOrder','order_id','id');
	}
	
  	public function orderProduct(){
		return $this->belongsTo('app\common\model\ErpOrderProduct','order_product_id','id');
	}	
	
  	public function product(){
		return $this->belongsTo('app\common\model\ErpProduct','product_id','id');
	}	
	
	public function bom(){
		return $this->hasMany('app\common\model\ErpOrderProduceBom', 'order_produce_id', 'id');
    }	
	
	public function process(){
		return $this->hasMany('app\common\model\ErpOrderProduceProcess', 'order_produce_id', 'id');
    }		
	
	public function rework(){
		return $this->hasMany('app\common\model\ErpOrderProduceRework', 'order_produce_id', 'id');
    }	
	
  	public function shipping(){
		return $this->belongsTo('app\common\model\ErpOrderShipping','order_shipping_id','id');
	}	
	
	public function getCheckStatusDescAttr($value, $data){
        return ErpOrderProduceEnum::getCheckStatusDesc($data['check_status']);
    }	
	
	public function getProduceStatusDescAttr($value, $data){
        return ErpOrderProduceEnum::getProduceStatusDesc($data['produce_status']);
    }
	
	public function getProduceTypeDescAttr($value, $data){
        return ErpOrderProduceEnum::getProduceTypeDesc($data['produce_type']);
    }	
	
	public function getApproveStatusDescAttr($value, $data){
        return ErpOrderProduceEnum::getApproveStatusDesc($data['approve_status']);
    }		
	
	public function getCanPurchaseAttr($value, $data){
        return $data['produce_type'] == ErpOrderProduceEnum::PRODUCE_TYPE_1 && $data['produce_status'] == ErpOrderProduceEnum::PRODUCE_STATUS_NO;
    }		
	
	public function getCanApproveAttr($value, $data){
        return $data['approve_status'] == ErpOrderProduceEnum::APPROVE_STATUS_NO;
    }	
	
	public function getDeliveryTimeAttr($value,$data){
		return $value?date('Y-m-d',$value):'';
	}	

	public function getCanNoticeShippingAttr($value, $data){
        return $data['order_shipping_id'] == 0 && $data['produce_status'] == ErpOrderProduceEnum::PRODUCE_STATUS_FINISH;
    }
	
    public function getOrderTypeDescAttr($value, $data){
        return ErpOrderEnum::getOrderTypeDesc($data['order_type']);
    }	
	
	public function getQcFileLinkAttr($value,$data)
    {
		$data 						= $data['qc_file'];
		if(!empty($data['file'])){
			foreach($data['file'] as $key=>$vo){
				$data['file'][$key]	= get_browse_url($vo);
			}
		}
        return $data;
    }
	
	public function getProduceFileLinkAttr($value,$data)
    {
		$data 						= $data['produce_file'];
		if(!empty($data['file'])){
			foreach($data['file'] as $key=>$vo){
				$data['file'][$key]	= get_browse_url($vo);
			}
		}
        return $data;
    }		
	
}