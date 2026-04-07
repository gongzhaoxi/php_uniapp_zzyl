<?php
namespace app\common\model;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;
use app\common\enum\ErpOrderLogEnum;

class ErpOrderLog extends BaseModel
{

	
	public function order(){
		return $this->belongsTo('app\common\model\ErpOrder','order_id','id');
	}
	
	public function orderProduct(){
		return $this->belongsTo('app\common\model\ErpOrderProduct','order_product_id','id');
	}	
	
	public function getDataTypeDescAttr($value,$data){
		return ErpOrderLogEnum::getDataTypeDesc($data['data_type']);
	}

	public function searchQueryAttr($query, $value, $data){
        if (!empty($value['data_type'])) {
            $query->where('data_type', '=', $value['data_type']);
        }
        if (!empty($value['order_id'])) {
			$query->where('order_id', '=', $value['order_id']);
        }	
        if (isset($value['order_product_id']) && $value['order_product_id'] !== '') {
			$query->where('order_product_id', '=', $value['order_product_id']);
        }			
        if (!empty($value['log'])) {
            $query->where('log', 'like', '%' . $value['log'] . '%');
        }	
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($value['sort'],$value['order']);
		}
    }
	
}