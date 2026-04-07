<?php
namespace app\common\model;
use app\common\model\BaseModel;

class ErpProductStat extends BaseModel
{
  	public function product(){
		return $this->belongsTo('app\common\model\ErpProduct','product_id','id');
	}
	
   

}