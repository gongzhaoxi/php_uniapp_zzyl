<?php
namespace app\common\model;
use app\common\model\BaseModel;


class ErpPurchaseOrderOut extends BaseModel
{

	public function order(){
		return $this->belongsTo('app\common\model\ErpPurchaseOrder','order_id','id');
	}
	
	public function orderData(){
		return $this->belongsTo('app\common\model\ErpPurchaseOrderData','order_data_id','id');
	}	
	
	public function searchQueryAttr($query, $value, $data)
    {
		$alias 		= '';
		$m_alias	= '';
		$o_alias	= '';
		if (!empty($value['_alias'])) {
			$alias 	= $value['_alias'].'.';
        }
		if (!empty($value['_material_alias'])) {
			$m_alias= $value['_material_alias'].'.';
        }	
		if (!empty($value['_order_alias'])) {
			$o_alias= $value['_order_alias'].'.';
        }	
		if (!empty($value['order_id'])) {
			$query->where($alias.'order_id', '=', $value['order_id']);
        }		

        if ($o_alias && !empty($value['order_date'])) {
			$order_date = is_array($value['order_date'])?$value['order_date']:explode('至',$value['order_date']);
			if(!empty($order_date[0])){
				$query->where($o_alias.'order_date', '>=', trim($order_date[0]));
			}
			if(!empty($order_date[1])){
				$query->where($o_alias.'order_date', '<=', trim($order_date[1]));
			}
        }		
		if ($o_alias && !empty($value['follow_admin_id'])) {
			$query->where($o_alias.'follow_admin_id', '=', $value['follow_admin_id']);
        }	
		if ($o_alias && !empty($value['supplier_id'])) {
			$query->where($o_alias.'supplier_id', '=', $value['supplier_id']);
        }		
        if ($o_alias && !empty($value['order_sn'])) {
			$query->where($o_alias.'order_sn', 'like', '%' . $value['order_sn'] . '%');
        }	
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($alias.$value['sort'],$value['order']);
		}
    }
	
}