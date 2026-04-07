<?php
namespace app\common\model;
use app\common\model\BaseModel;
use app\common\enum\{ErpProductStockEnum,RegionTypeEnum,ErpOrderEnum};

class ErpProductStock extends BaseModel
{
	use \app\common\traits\OrderProductProject;
	
  	public function order(){
		return $this->belongsTo('app\common\model\ErpOrder','order_id','id');
	}
	
  	public function orderProduct(){
		return $this->belongsTo('app\common\model\ErpOrderProduct','order_product_id','id');
	}	
	
  	public function orderProduce(){
		return $this->belongsTo('app\common\model\ErpOrderProduce','order_produce_id','id');
	}	
	
  	public function supplier(){
		return $this->belongsTo('app\common\model\ErpSupplier','supplier_id','id');
	}	
	
  	public function warehouse(){
		return $this->belongsTo('app\common\model\ErpWarehouse','warehouse_id','id');
	}		
	
    public function getTypeDescAttr($value, $data){
        return ErpProductStockEnum::getTypeDesc($data['type']);
    }   
	
    public function getStatusDescAttr($value, $data){
        return ErpProductStockEnum::getStatusDesc($data['status']);
    } 	
	
    public function getRegionTypeAttr($value, $data){
        return RegionTypeEnum::getDesc($value);
    }		
	
    public function getShippingTypeAttr($value, $data){
		return ErpOrderEnum::getShippingTypeDesc($value);
    }			
		
	public function salesman(){
		return $this->belongsTo('app\common\model\AdminAdmin','salesman_id','id')->field('id,username,nickname');
	}			
		
		
	public function searchQueryAttr($query, $value, $data)
    {
		$alias 			= '';
		$produce_alias	= '';
		$order_alias	= '';
		$product_alias	= '';
		if (!empty($value['_alias'])) {
			$alias 	= $value['_alias'].'.';
        }
		if (!empty($value['_order_alias'])) {
			$order_alias= $value['_order_alias'].'.';
        }	
		if (!empty($value['_produce_alias'])) {
			$produce_alias= $value['_produce_alias'].'.';
        }
		if (!empty($value['_product_alias'])) {
			$product_alias= $value['_product_alias'].'.';
        }	
		if (isset($value['status']) && $value['status'] !== '') {
			$query->where($alias.'status', 'in', $value['status']);
        }		
		if (isset($value['is_re_produce']) && $value['is_re_produce'] !== '') {
			$query->where($alias.'is_re_produce', 'in', $value['is_re_produce']);
        }			
		if (isset($value['type']) && $value['type'] !== '') {
			$query->where($alias.'type', 'in', $value['type']);
        }		
		
		if (!empty($value['order_produce_ids'])) {
			$query->where($alias.'order_produce_id', 'in', $value['order_produce_ids']);
        }
		
		if (isset($value['is_out_warehouse']) && $value['is_out_warehouse'] !== '') {
			$query->where($alias.'is_out_warehouse', 'in', $value['is_out_warehouse']);
        }
		if (isset($value['is_returned']) && $value['is_returned'] !== '') {
			$query->where($alias.'is_returned', 'in', $value['is_returned']);
        }		
        if (!empty($value['stock_date'])) {
			$stock_date = is_array($value['stock_date'])?$value['stock_date']:explode('至',$value['stock_date']);
			if(!empty($stock_date[0])){
				$query->where($alias.'stock_date', '>=', trim($stock_date[0]));
			}
			if(!empty($stock_date[1])){
				$query->where($alias.'stock_date', '<=', trim($stock_date[1]));
			}
        }		
        if ($order_alias && !empty($value['customer_name'])) {
			$query->where($order_alias.'customer_name', 'like', '%' . $value['customer_name'] . '%');
        }		
        if ($order_alias && !empty($value['order_sn'])) {
			$query->where($order_alias.'order_sn', 'like', '%' . $value['order_sn'] . '%');
        }		
        if ($produce_alias && !empty($value['produce_sn'])) {
			$query->where($produce_alias.'produce_sn', 'like', '%' . $value['produce_sn'] . '%');
        }
        if ($produce_alias && !empty($value['produce_finish_sn'])) {
			$query->where($produce_alias.'produce_finish_sn', 'like', '%' . $value['produce_finish_sn'] . '%');
        }		
		if (!empty($value['keyword'])) {
			$key = [];
			if($product_alias){
				$key[] = $product_alias.'product_name';
				$key[] = $product_alias.'product_model';
			}
			if($product_alias){
				$key[] = $produce_alias.'produce_sn';
			}
			if($key){
				$query->where(implode('|',$key), 'like', '%' . $value['keyword'] . '%');
			}	
		}
		
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($alias.$value['sort'],$value['order']);
		}
    }
	
}