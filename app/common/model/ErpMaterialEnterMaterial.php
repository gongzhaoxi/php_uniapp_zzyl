<?php
namespace app\common\model;
use app\common\model\BaseModel;
use app\common\enum\ErpMaterialEnterMaterialEnum;
/**
 * 物料入库详情模型
 * Class ErpMaterialEnterMaterial
 * @package app\common\model;
 */
class ErpMaterialEnterMaterial extends BaseModel
{
	protected $json = ['inspection'];
	protected $jsonAssoc = true;
	
    public function code(){
		return $this->hasMany('app\common\model\ErpMaterialCode', 'data_id', 'id')->where('data_type','erp_material_enter_material');
    }	
	
	public function enter(){
		return $this->belongsTo('app\common\model\ErpMaterialEnter','material_stock_id','id');
	}
	
	public function report(){
		return $this->hasOne('app\common\model\ErpMaterialEnterMaterialReport', 'material_enter_material_id', 'id');
    }	
	
	public function warehouse(){
		return $this->belongsTo('app\common\model\ErpWarehouse','warehouse_id','id');
	}	

	public function purchaseOrder(){
		return $this->belongsTo('app\common\model\ErpPurchaseOrder', 'purchase_order_id', 'id');
    }

	
	public function material(){
		return $this->belongsTo('app\common\model\ErpMaterial','material_id','id');
	}	

    public function getStatusDescAttr($value, $data)
    {
        return ErpMaterialEnterMaterialEnum::getStatusDesc($data['status']);
    }
	
    public function getCheckStatusDescAttr($value, $data)
    {
        return ErpMaterialEnterMaterialEnum::getCheckStatusDesc($data['check_status']);
    }	
	
	
    public function getTestNumAttr($value, $data)
    {
        return 0;
    }	
	
	public function getCanEnterAttr($value, $data)
    {
		return $data['quality_num']>0 && ($data['status']==ErpMaterialEnterMaterialEnum::STATUS_HANDLE || $data['status'] == ErpMaterialEnterMaterialEnum::STATUS_PART)?true:false;
    }
	
	public function getCanCancelAttr($value, $data)
    {
		return $data['status']==ErpMaterialEnterMaterialEnum::STATUS_HANDLE || $data['status'] == ErpMaterialEnterMaterialEnum::STATUS_PART?true:false;
    }	
	
	public function getCanCheckAttr($value, $data)
    {
        return $data['stock_num']-$data['qualities_num']-$data['defective_num']>0 && ($data['status'] == ErpMaterialEnterMaterialEnum::STATUS_HANDLE || $data['status'] == ErpMaterialEnterMaterialEnum::STATUS_PART || $data['status'] == ErpMaterialEnterMaterialEnum::STATUS_RETURN)?true:false;
    }	
	
	public function getCanReturnAttr($value, $data)
    {
		return $data['status'] != ErpMaterialEnterMaterialEnum::STATUS_RETURN && $data['defective_num'] > 0?true:false;
    }		
	
	
	public function getCanNoticeAttr($value, $data)
    {
        return $data['check_status'] == ErpMaterialEnterMaterialEnum::CHECK_STATUS_HANDLE;
    }	
	
	public function searchQueryAttr($query, $value, $data)
    {
		$alias 		= '';
		$m_alias	= '';
		$s_alias	= '';
		$r_alias	= '';
		if (!empty($value['_alias'])) {
			$alias 	= $value['_alias'].'.';
        }
		if (!empty($value['_material_alias'])) {
			$m_alias= $value['_material_alias'].'.';
        }	
		if (!empty($value['_stock_alias'])) {
			$s_alias= $value['_stock_alias'].'.';
        }	
		if (!empty($value['_report_alias'])) {
			$r_alias= $value['_report_alias'].'.';
        }		
		if (!empty($value['material_stock_id'])) {
			$query->where($alias.'material_stock_id', '=', $value['material_stock_id']);
        }
        if ($m_alias && !empty($value['keyword'])) {
			$query->where($m_alias.'sn|'.$m_alias.'name', 'like', '%' . $value['keyword'] . '%');
        }
        if ($m_alias && !empty($value['sn'])) {
			$query->where($m_alias.'sn', 'like', '%' . $value['sn'] . '%');
        }		
        if ($m_alias && !empty($value['name'])) {
			$query->where($m_alias.'name', 'like', '%' . $value['name'] . '%');
        }		
        if ($m_alias && !empty($value['cid'])) {
			$query->where($m_alias.'cid', '=', $value['cid']);
        }	
        if ($m_alias && !empty($value['quality_testing_area'])) {
			$query->where($m_alias.'quality_testing_area', '=', $value['quality_testing_area']);
        }		
        if ($s_alias && !empty($value['order_sn'])) {
			$query->where($s_alias.'order_sn', 'like', '%' . $value['order_sn'] . '%');
        }		
        if (!empty($value['status'])) {
			$query->where($alias.'status', 'in', $value['status']);
        }	
		if ($r_alias && !empty($value['finish_status'])) {
			if($value['finish_status'] == 1){
				$query->whereRaw($r_alias.'id is null or '.$r_alias.'status = 1');
			}else if($value['finish_status'] == 2){
				$query->whereRaw($r_alias.'status = 2');
			}			
        }
		if ($r_alias && !empty($value['report_status'])) {
			if($value['report_status'] == 1){
				$query->whereRaw($r_alias.'id is null or '.$r_alias.'inspector_sign = "" or '.$alias.'status = 2 and '.$r_alias.'status = 2 ');
			}else if($value['report_status'] == 2){
				$query->whereRaw($r_alias.'inspector_sign <> "" and '.$alias.'status <> 2 and '.$r_alias.'status <> 2 ');
			}			
        }		
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($alias.$value['sort'],$value['order']);
		}
    }
}