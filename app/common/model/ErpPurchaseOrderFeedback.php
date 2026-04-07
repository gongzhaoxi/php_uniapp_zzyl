<?php
namespace app\common\model;
use app\common\model\BaseModel;

class ErpPurchaseOrderFeedback extends BaseModel
{

	public function order(){
		return $this->belongsTo('app\common\model\ErpPurchaseOrder','order_id','id');
	}
	
	public function searchQueryAttr($query, $value, $data){
        if (!empty($value['order_id'])) {
			$query->where('order_id', '=', $value['order_id']);
        }			
        if (!empty($value['content'])) {
            $query->where('content', 'like', '%' . $value['content'] . '%');
        }	
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($value['sort'],$value['order']);
		}
    }
	
}