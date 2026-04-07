<?php
namespace app\common\model;
use app\common\model\BaseModel;

class ErpOrderProduceFollow extends BaseModel
{
	protected $json = ['component','partn'];
	protected $jsonAssoc = true;
	
  	public function user(){
		return $this->belongsTo('app\common\model\ErpUser','user_id','id');
	}

  	public function process(){
		return $this->belongsTo('app\common\model\ErpProcess','process_id','id');
	}	
	
  	public function orderProduce(){
		return $this->belongsTo('app\common\model\ErpOrderProduce','order_produce_id','id');
	}
	
	
  	public function followItem(){
		return $this->belongsTo('app\common\model\ErpFollowItem','follow_item_id','id');
	}	

}