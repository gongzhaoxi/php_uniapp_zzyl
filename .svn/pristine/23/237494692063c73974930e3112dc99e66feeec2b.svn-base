<?php
namespace app\common\model;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;
use app\common\enum\{ErpMaterialEnum,ErpOrderProduceEnum,ErpOrderEnum};
class ErpOrderAftersale extends BaseModel
{
	
	public function order(){
		return $this->belongsTo('app\common\model\ErpOrder','order_id','id');
	}
	
	public function material(){
		return $this->belongsTo('app\common\model\ErpMaterial','material_id','id');
	}		
	
	public function getMaterialTypeDescAttr($value, $data){
		return ErpMaterialEnum::getTypeDesc($data['material_type']);
    }
	
	public function getCanNoticeShippingAttr($value, $data){
        return $data['order_shipping_id'] == 0 && $data['produce_status'] == ErpOrderProduceEnum::PRODUCE_STATUS_FINISH;
    }
	
  	public function shipping(){
		return $this->belongsTo('app\common\model\ErpOrderShipping','order_shipping_id','id');
	}	
	
	public function getCreateDateAttr($value,$data){
		return date('Y-m-d',$this->getData('create_time'));
	}
	
    public function getOrderTypeDescAttr($value, $data){
        return ErpOrderEnum::getOrderTypeDesc($data['order_type']);
    }


	public function searchQueryAttr($query, $value, $data)
    {
		$alias 		= '';
		$o_alias 	= '';
		if (!empty($value['_alias'])) {
			$alias 	= $value['_alias'].'.';
        }
		if (!empty($value['_order_alias'])) {
			$o_alias= $value['_order_alias'].'.';
        }	
        if (!empty($value['create_time'])) {
			$create_time = is_array($value['create_time'])?$value['create_time']:explode('至',$value['create_time']);
			if(!empty($create_time[0])){
				$query->where($alias.'create_time', '>', strtotime(trim($create_time[0])));
			}
			if(!empty($create_time[1])){
				$query->where($alias.'create_time', '<', strtotime(trim($create_time[1]))+24*3600);
			}
        }
		if ($o_alias && !empty($value['region_type'])) {
			$query->where($o_alias.'region_type', '=', $value['region_type']);
		}
		
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($alias.$value['sort'],$value['order']);
		}
    }
}