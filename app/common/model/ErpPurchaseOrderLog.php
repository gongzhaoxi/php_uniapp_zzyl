<?php
namespace app\common\model;
use app\common\model\BaseModel;
use app\common\enum\ErpPurchaseOrderLogEnum;

class ErpPurchaseOrderLog extends BaseModel
{

	
	public function order(){
		return $this->belongsTo('app\common\model\ErpPurchaseOrder','order_id','id');
	}
	
	public function orderData(){
		return $this->belongsTo('app\common\model\ErpPurchaseOrderData','order_data_id','id');
	}	
	
	public function getDataTypeDescAttr($value,$data){
		return ErpPurchaseOrderLogEnum::getDataTypeDesc($data['data_type']);
	}

	public function searchQueryAttr($query, $value, $data){
        if (!empty($value['data_type'])) {
            $query->where('data_type', '=', $value['data_type']);
        }
        if (!empty($value['order_id'])) {
			$query->where('order_id', '=', $value['order_id']);
        }	
        if (isset($value['order_data_id']) && $value['order_data_id'] !== '') {
			$query->where('order_data_id', '=', $value['order_data_id']);
        }			
        if (!empty($value['log'])) {
            $query->where('log', 'like', '%' . $value['log'] . '%');
        }	
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($value['sort'],$value['order']);
		}
    }
	
}