<?php
namespace app\common\model;
use app\common\model\BaseModel;

class ErpOrderAccessory extends BaseModel
{

	public function order(){
		return $this->belongsTo('app\common\model\ErpOrder','order_id','id');
	}

	public function searchQueryAttr($query, $value, $data){
        if (!empty($value['order_id'])) {
			$query->where('order_id', '=', $value['order_id']);
        }			
        if (!empty($value['keyword'])) {
            $query->where('product_name|product_model', 'like', '%' . $value['keyword'] . '%');
        }	
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($value['sort'],$value['order']);
		}
    }
	
}