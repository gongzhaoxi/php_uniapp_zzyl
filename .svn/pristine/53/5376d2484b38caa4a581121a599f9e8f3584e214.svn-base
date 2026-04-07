<?php
namespace app\common\model;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;
use app\common\enum\ErpOrderEnum;
use app\common\enum\RegionTypeEnum;
use app\common\enum\YesNoEnum;

class ErpOrder extends BaseModel
{
	use SoftDelete;
    protected $deleteTime = 'delete_time';


    public function setCreateTimeAttr($value, $data)
    {
		return $value?strtotime($value):time();
    }	

    public function setRegionAttr($value, $data)
    {
		return is_array($value)?implode(',',$value):$value;
    }	

    public function getRegionAttr($value, $data)
    {
		return $value?explode(',',$value):[];
    }	

    public function getRegionNameAttr($value, $data)
    {
		return $value?$value:($data['region']?implode(',',Region::where('id','in',$data['region'])->order('id asc')->column('name')):'');
    }
	
    public function setRegionNameAttr($value, $data)
    {
		return $value?$value:($data['region']?implode(',',array_slice(Region::where('id','in',$data['region'])->order('id asc')->column('name'), -2)):'');
    }	

	public function setDeliveryTimeAttr($value,$data){
		return $value?strtotime($value):0;
	}

	public function getDeliveryTimeAttr($value,$data){
		return $value?date('Y-m-d',$value):'';
	}

	public function getCreateDateAttr($value,$data){
		return date('Y-m-d',$this->getData('create_time'));
	}

	public function customer(){
		return $this->belongsTo('app\common\model\ErpCustomer','customer_id','id');
	}
	
	public function salesman(){
		return $this->belongsTo('app\common\model\AdminAdmin','salesman_id','id')->field('id,username,nickname');
	}	
	
	public function saleOrder(){
		return $this->belongsTo('app\common\model\ErpOrder','sale_order_id','id');
	}		

	public function getCanCancelAttr($value,$data){
		return $data['produce_status'] == ErpOrderEnum::PRODUCE_STATUS_NO;
	}	
	
	public function getCanRemoveAttr($value,$data){
		return $data['order_status'] == ErpOrderEnum::ORDER_STATUS_WAIT_HANDLE||$data['order_status'] == ErpOrderEnum::ORDER_STATUS_CLOSE;
	}	
	
	public function getCanEditAttr($value,$data){
		return $data['salesman_approve'] == 0;
	}
	
	public function getCanSaveProductAttr($value,$data){
		if(($data['is_special'] == 1 && $data['technician_approve'] == 1) || $data['salesman_approve'] == 1){
			return false;
		}
		return true;
	}
	
	public function getTechnicianApproveDescAttr($value,$data){
		return $data['technician_approve'] == 1?'已审核':'未审核';
	}	
	
	public function getSalesmanApproveDescAttr($value,$data){
		return $data['salesman_approve'] == 1?'已审核':'未审核';
	}	
	
	public function getOrderStatusDescAttr($value,$data){
		return ErpOrderEnum::getOrderStatusDesc($data['order_status']);
	}
	
	public function getShippingStatusDescAttr($value,$data){
		return ErpOrderEnum::getShippingStatusDesc($data['shipping_status']);
	}

	public function getProduceStatusDescAttr($value,$data){
		return ErpOrderEnum::getProduceStatusDesc($data['produce_status']);
	}	
	
    public function orderProduct(){
		return $this->hasMany('app\common\model\ErpOrderProduct', 'order_id', 'id');
    }		
	
    public function orderProductBom(){
		return $this->hasMany('app\common\model\ErpOrderProductBom', 'order_id', 'id');
    }	
	
    public function orderAccessory(){
		return $this->hasMany('app\common\model\ErpOrderAccessory', 'order_id', 'id');
    }	
	
	public function orderRemarks(){
		return $this->hasMany('app\common\model\ErpOrderRemark', 'order_id', 'id');
    }
	
    public function getRegionTypeDescAttr($value, $data){
        return RegionTypeEnum::getDesc($data['region_type']);
    }		

    public function getIsSpecialDescAttr($value, $data){
        return YesNoEnum::getBooleanDesc($data['is_special']);
    }	
	
    public function getShippingTypeDescAttr($value, $data){
        return ErpOrderEnum::getShippingTypeDesc($data['shipping_type']);
    }
	
    public function getOrderTypeDescAttr($value, $data){
        return ErpOrderEnum::getOrderTypeDesc($data['order_type']);
    }	
	
	public function searchQueryAttr($query, $value, $data)
    {
        if (!empty($value['customer_name'])) {
            $query->where('customer_name', 'like', '%' . $value['customer_name'] . '%');
        }
        if (!empty($value['order_status'])) {
            $query->where('order_status', '=', $value['order_status']);
        }
        if (isset($value['technician_approve']) && $value['technician_approve'] !== '') {
            $query->where('technician_approve', '=', $value['technician_approve']);
        }		
        if (isset($value['salesman_approve']) && $value['salesman_approve'] !== '') {
			$query->where('salesman_approve', '=', $value['salesman_approve']);
        }	
        if (!empty($value['shipping_status'])) {
            $query->where('shipping_status', '=', $value['shipping_status']);
        }
        if (!empty($value['produce_status'])) {
            $query->where('produce_status', '=', $value['produce_status']);
        }		
        if (!empty($value['region_type'])) {
            $query->where('region_type', '=', $value['region_type']);
        }	
        if (isset($value['is_special']) && $value['is_special'] !== '') {
            $query->where('is_special', '=', $value['is_special']);
        }			
        if (!empty($value['order_sn'])) {
            $query->where('order_sn', 'like', '%' . $value['order_sn'] . '%');
        }
        if (!empty($value['keyword'])) {
            $query->where('order_sn|customer_name', 'like', '%' . $value['keyword'] . '%');
        }
        if (!empty($value['create_time'])) {
			$create_time = is_array($value['create_time'])?$value['create_time']:explode('至',$value['create_time']);
			if(!empty($create_time[0])){
				$query->where('create_time', '>', strtotime(trim($create_time[0])));
			}
			if(!empty($create_time[1])){
				$query->where('create_time', '<', strtotime(trim($create_time[1]))+24*3600);
			}
        }
		if (!empty($value['create_start'])) {
			$query->where('create_time', '>', strtotime(trim($value['create_start'])));
		}
		if (!empty($value['create_end'])) {
			$query->where('create_time', '<', strtotime(trim($value['create_end']))+24*3600);
		}		
        if (!empty($value['delivery_time'])) {
			$delivery_time = is_array($value['delivery_time'])?$value['delivery_time']:explode('至',$value['delivery_time']);
			if(!empty($delivery_time[0])){
				$query->where('delivery_time', '>', strtotime(trim($delivery_time[0])));
			}
			if(!empty($delivery_time[1])){
				$query->where('delivery_time', '<', strtotime(trim($delivery_time[1]))+24*3600);
			}
        }
        if (!empty($value['data_type'])) {
            $query->where('data_type', '=', $value['data_type']);
        }	
        if (!empty($value['type'])) {
			$query->where('type', '=', $value['type']);
        }	
        if (!empty($value['salesman_id'])) {
			$query->where('salesman_id', '=', $value['salesman_id']);
        }		
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($value['sort'],$value['order']);
		}
    }	
	
	public function getCanSaveAftersaleAttr($value,$data){
		if($data['salesman_approve'] == 1){
			return false;
		}
		return true;
	}
	
	public function orderAftersale(){
		return $this->hasMany('app\common\model\ErpOrderAftersale', 'order_id', 'id');
    }
	
	public function getSaleOrderSnAttr($value,$data){
		return !empty($data['sale_order_id']) && $this->sale_order?$this->sale_order['order_sn']:'';
	}
	
	
	
	
}