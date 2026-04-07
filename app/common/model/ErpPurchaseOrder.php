<?php
namespace app\common\model;
use app\common\model\BaseModel;
use app\common\enum\ErpPurchaseOrderEnum;
use think\model\concern\SoftDelete;
class ErpPurchaseOrder extends BaseModel
{
	use SoftDelete;
    protected $deleteTime = 'delete_time';
	
	public function supplier(){
		return $this->belongsTo('app\common\model\ErpSupplier','supplier_id','id');
	}

	public function followAdmin(){
		return $this->belongsTo('app\common\model\AdminAdmin','follow_admin_id','id');
	}

    public function orderData(){
		return $this->hasMany('app\common\model\ErpPurchaseOrderData', 'order_id', 'id');
    }


    public function setApplyIdsAttr($value, $data)
    {
        return is_array($value)?implode(',',$value):$value;
    }

    public function getStatusDescAttr($value, $data)
    {
        return ErpPurchaseOrderEnum::getStatusDesc($data['status']);
    }	
	
    public function getCanEditAttr($value, $data)
    {
		return $data['status'] == ErpPurchaseOrderEnum::STATUS_NO;
        //return ($data['status']!=ErpPurchaseOrderEnum::STATUS_YES) && ($data['status']===ErpPurchaseOrderEnum::STATUS_NO||($data['supplier_status']===ErpPurchaseOrderEnum::SUPPLIER_STATUS_WAIT_SEND||$data['supplier_status']===ErpPurchaseOrderEnum::SUPPLIER_STATUS_WAIT_CONFIRM));
    }
	
    public function getCanRecheckAttr($value, $data)
    {
        return ($data['supplier_status']===ErpPurchaseOrderEnum::SUPPLIER_STATUS_WAIT_SEND||$data['supplier_status']===ErpPurchaseOrderEnum::SUPPLIER_STATUS_WAIT_CONFIRM)&&$data['status']===ErpPurchaseOrderEnum::STATUS_YES;
    }	
	
    public function getCanCheckAttr($value, $data)
    {
        return $data['status']===ErpPurchaseOrderEnum::STATUS_NO;
    }	
	
    public function getCanSendAttr($value, $data)
    {
        return $data['status']===ErpPurchaseOrderEnum::STATUS_YES&&$data['supplier_status']===ErpPurchaseOrderEnum::SUPPLIER_STATUS_WAIT_SEND;
    }		
	
	public function getCanCancelAttr($value, $data)
    {
		return  ($data['status'] == ErpPurchaseOrderEnum::STATUS_NO || $data['status'] == ErpPurchaseOrderEnum::STATUS_YES) && ($data['supplier_status']==ErpPurchaseOrderEnum::SUPPLIER_STATUS_CONFIRMED || $data['supplier_status']==ErpPurchaseOrderEnum::SUPPLIER_STATUS_WAIT_SEND);
    }		
	
	public function getCanConfirmAttr($value, $data)
    {
		return $data['supplier_status']==ErpPurchaseOrderEnum::SUPPLIER_STATUS_WAIT_CONFIRM||$data['supplier_status']==ErpPurchaseOrderEnum::SUPPLIER_STATUS_CANCEL;
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
	
	public function searchQueryAttr($query, $value, $data)
    {
		$alias 		= '';
		if (!empty($value['_alias'])) {
			$alias 	= $value['_alias'].'.';
        }
        if (!empty($value['type'])) {
			$query->where($alias.'type', '=', $value['type']);
        }
        if (!empty($value['supplier_id'])) {
			$query->where($alias.'supplier_id', '=', $value['supplier_id']);
        }
        if (!empty($value['follow_admin_id'])) {
			$query->where($alias.'follow_admin_id', '=', $value['follow_admin_id']);
        }		
        if (isset($value['status']) && $value['status'] !== '') {
            $query->where($alias.'status', '=', $value['status']);
        }
        if (!empty($value['order_sn'])) {
			$query->where($alias.'order_sn', 'like', '%' . $value['order_sn'] . '%');
        }		
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($alias.$value['sort'],$value['order']);
		}
        if (!empty($value['material'])) {
			$query->where($alias.'id', 'in', function ($que) use ($value) {
				$que->name('erp_purchase_order_data')->alias('a')->join('erp_material b','a.data_id = b.id','LEFT')->where('a.type',1)->where('b.sn|b.name', 'like', '%' . $value['material'] . '%')->group('a.order_id')->field(['a.order_id'])->select();
			});
        }		
    }
	
	public function getLastFeedbackAttr($value,$data){
		return ErpPurchaseOrderFeedback::where('order_id',$data['id'])->order('id desc')->value('content');
	}

}