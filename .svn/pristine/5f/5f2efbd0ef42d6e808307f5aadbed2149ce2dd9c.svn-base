<?php
namespace app\common\model;
use app\common\model\BaseModel;
use app\common\enum\ErpMaterialEnum;
class ErpOrderProduceBom extends BaseModel
{
  	public function order(){
		return $this->belongsTo('app\common\model\ErpOrder','order_id','id');
	}
	
  	public function orderProduct(){
		return $this->belongsTo('app\common\model\ErpOrderProduct','order_product_id','id');
	}	
	
  	public function orderProduce(){
		return $this->belongsTo('app\common\model\ErpOrderProduce','order_produce_id','id');
	}	
	
  	public function material(){
		return $this->belongsTo('app\common\model\ErpMaterial','material_id','id');
	}	
	
    public function getMaterialTypeDescAttr($value, $data){
        return ErpMaterialEnum::getTypeDesc($data['material_type']);
    }   
	
}