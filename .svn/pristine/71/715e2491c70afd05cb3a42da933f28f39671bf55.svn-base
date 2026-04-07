<?php
namespace app\common\model;
use app\common\model\BaseModel;
use app\common\enum\{ErpPurchaseOrderDataEnum,ErpOrderEnum};

class ErpPurchaseOrderData extends BaseModel
{

	public function supplier(){
		return $this->belongsTo('app\common\model\ErpSupplier','supplier_id','id');
	}	

	public function material(){
		return $this->belongsTo('app\common\model\ErpMaterial','data_id','id');
	}	
	
	public function order(){
		return $this->belongsTo('app\common\model\ErpPurchaseOrder','order_id','id');
	}	

  	public function orderProduce(){
		return $this->belongsTo('app\common\model\ErpOrderProduce','data_id','id');
	}	

  	public function orderProduct(){
		return $this->belongsTo('app\common\model\ErpOrderProduct','order_product_id','id');
	}

	public function follower(){
		return $this->belongsTo('app\common\model\AdminAdmin','follow_admin_id','id');
	}

	public function salesman(){
		return $this->belongsTo('app\common\model\AdminAdmin','salesman_id','id');
	}

    public function getStatusDescAttr($value, $data)
    {
        return ErpPurchaseOrderDataEnum::getStatusDesc($data['status']);
    }

	public function getOverDayAttr($value, $data)
    {
		$timestamp1 		= strtotime($data['delivery_date']);
		$timestamp2 		= strtotime(date('Y-m-d'));
		if($timestamp2 < $timestamp1){
			return 0;
		}else{
			$diff_seconds 	= $timestamp2 - $timestamp1;
			$diff_days 		= floor($diff_seconds / (60 * 60 * 24));
			return $diff_days;
		}
    }
	
    public function getNoWarehousNumAttr($value, $data)
    {
		return $data['apply_num'] - $data['warehous_num']- $data['re_apply_num'];
    }	
	
	public function getCanWarehousAttr($value, $data)
    {
		return $data['apply_num']>$data['warehous_num']&&$data['status']!=ErpPurchaseOrderDataEnum::STATUS_CANCEL;
    }	
	
	public function getCanRemoveAttr($value, $data)
    {
		return $data['apply_num']>$data['warehous_num']&&$data['status']!=ErpPurchaseOrderDataEnum::STATUS_CANCEL;
    }	
	
    public function getOrderTypeDescAttr($value, $data){
        return ErpOrderEnum::getOrderTypeDesc($data['order_type']);
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
		
		if (!empty($value['supplier_id'])) {
			$query->where($alias.'supplier_id', '=', $value['supplier_id']);
        }
		if (!empty($value['ids'])) {
			$query->where($alias.'id', 'in', $value['ids']);
        }		
		if (!empty($value['order_id'])) {
			$query->where($alias.'order_id', '=', $value['order_id']);
        }		
		if (!empty($value['type'])) {
			$query->where($alias.'type', '=', $value['type']);
        }
		if (isset($value['status']) && $value['status'] !== '') {
			$query->where($alias.'status', 'in', $value['status']);
        }		
        if ($m_alias && !empty($value['material'])) {
			$query->where($m_alias.'sn|'.$m_alias.'name', 'like', '%' . $value['material'] . '%');
        }
        if ($m_alias && !empty($value['cid'])) {
			$query->where($m_alias.'cid', '=', $value['cid']);
        }		
        if (!empty($value['delivery_date'])) {
			$delivery_date = is_array($value['delivery_date'])?$value['delivery_date']:explode('至',$value['delivery_date']);
			if(!empty($delivery_date[0])){
				$query->where($alias.'delivery_date', '>=', trim($delivery_date[0]));
			}
			if(!empty($delivery_date[1])){
				$query->where($alias.'delivery_date', '<=', trim($delivery_date[1]));
			}
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
		if ($o_alias && isset($value['order_status']) && $value['order_status'] !== '') {
			$query->where($alias.'status', '=', $value['order_status']);
        }	
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($alias.$value['sort'],$value['order']);
		}
    }
}