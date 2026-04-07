<?php
namespace app\common\model;
use app\common\model\BaseModel;
use app\common\enum\ErpOrderProduceProcessEnum;
class ErpOrderProduceProcess extends BaseModel
{

  	public function user(){
		return $this->belongsTo('app\common\model\ErpUser','user_id','id');
	}

  	public function process(){
		return $this->belongsTo('app\common\model\ErpProcess','process_id','id');
	}	
	
  	public function orderProduce(){
		return $this->belongsTo('app\common\model\ErpOrderProduce','order_produce_id','id');
	}	
	
  	public function orderProduct(){
		return $this->belongsTo('app\common\model\ErpOrderProduct','order_product_id','id');
	}		
	
  	public function product(){
		return $this->belongsTo('app\common\model\ErpProduct','product_id','id');
	}		
	

}