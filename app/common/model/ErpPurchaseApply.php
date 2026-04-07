<?php
namespace app\common\model;
use app\common\model\BaseModel;
use app\common\enum\ErpPurchaseApplyEnum;


class ErpPurchaseApply extends BaseModel
{

	public function supplier(){
		return $this->belongsTo('app\common\model\ErpSupplier','supplier_id','id');
	}	

	public function material(){
		return $this->belongsTo('app\common\model\ErpMaterial','data_id','id');
	}	

  	public function orderProduce(){
		return $this->belongsTo('app\common\model\ErpOrderProduce','data_id','id');
	}	

  	public function orderProduct(){
		return $this->belongsTo('app\common\model\ErpOrderProduct','order_product_id','id');
	}	

    public function getStatusDescAttr($value, $data)
    {
        return ErpPurchaseApplyEnum::getStatusDesc($data['status']);
    }
	
    public function getDataTypeDescAttr($value, $data)
    {
        return ErpPurchaseApplyEnum::getDataTypeDesc($data['data_type']);
    }	

	public function searchQueryAttr($query, $value, $data)
    {
		$alias 		= '';
		$m_alias	= '';
		$o_alias	= '';
		$pc_alias	= '';
		$pt_alias	= '';
		if (!empty($value['_alias'])) {
			$alias 		= $value['_alias'].'.';
        }
		if (!empty($value['_material_alias'])) {
			$m_alias	= $value['_material_alias'].'.';
        }		
		if (!empty($value['_produce_alias'])) {
			$pc_alias	= $value['_produce_alias'].'.';
        }		
		if (!empty($value['_product_alias'])) {
			$pt_alias	= $value['_product_alias'].'.';
        }		
		if (!empty($value['_order_alias'])) {
			$o_alias	= $value['_order_alias'].'.';
        }		
		if ($m_alias && !empty($value['supplier_id'])) {
			$query->where($m_alias.'supplier_id', 'find in set', $value['supplier_id']);
        }
		if (!empty($value['ids'])) {
			$query->where($alias.'id', 'in', $value['ids']);
        }		
		if (!empty($value['data_type'])) {
			$query->where($alias.'data_type', '=', $value['data_type']);
        }
		if (!empty($value['type'])) {
			$query->where($alias.'type', '=', $value['type']);
        }		
		if (!empty($value['remark'])) {
			$query->where($alias.'remark', 'like', '%' . $value['remark'] . '%');
        }			
        if(!empty($value['username'])) {
			$query->where($alias.'username', 'like', '%' . $value['username'] . '%');
        }
        if ($m_alias && !empty($value['material'])) {
			$query->where($m_alias.'sn|'.$m_alias.'name', 'like', '%' . $value['material'] . '%');
        }
        if ($m_alias && !empty($value['cid'])) {
			$query->where($m_alias.'cid', '=', $value['cid']);
        }	
        if (!empty($value['status'])) {
			$query->where($alias.'status', 'in', $value['status']);
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
        if (!empty($value['apply_date'])) {
			$apply_date = is_array($value['apply_date'])?$value['apply_date']:explode('至',$value['apply_date']);
			if(!empty($apply_date[0])){
				$query->where($alias.'apply_date', '>=', trim($apply_date[0]));
			}
			if(!empty($apply_date[1])){
				$query->where($alias.'apply_date', '<=', trim($apply_date[1]));
			}
        }
        if ($pc_alias && !empty($value['produce_sn'])) {
			$query->where($pc_alias.'produce_sn', 'like', '%' . $value['produce_sn'] . '%');
        }		
        if ($pt_alias && !empty($value['product'])) {
			$query->where($pt_alias.'product_model|'.$pt_alias.'product_specs', 'like', '%' . $value['product'] . '%');
        }	
        if ($o_alias && !empty($value['customer_name'])) {
			$query->where($o_alias.'customer_name', 'like', '%' . $value['customer_name'] . '%');
        }
		if ($o_alias && !empty($value['salesman_id'])) {
			$query->where($o_alias.'salesman_id', '=', $value['salesman_id']);
        }		
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($alias.$value['sort'],$value['order']);
		}
    }
}