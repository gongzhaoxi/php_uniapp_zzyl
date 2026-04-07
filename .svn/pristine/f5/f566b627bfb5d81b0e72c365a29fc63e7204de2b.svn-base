<?php
namespace app\common\model;
use app\common\model\BaseModel;
use app\common\enum\ErpMaterialAllocateMaterialEnum;

/**
 * 物料调拨详情模型
 * Class ErpMaterialAllocateMaterial
 * @package app\common\model;
 */
class ErpMaterialAllocateMaterial extends BaseModel
{

	public function allocate(){
		return $this->belongsTo('app\common\model\ErpMaterialAllocate','material_stock_id','id');
	}
	
	public function material(){
		return $this->belongsTo('app\common\model\ErpMaterial','material_id','id');
	}	

    public function getStatusDescAttr($value, $data)
    {
        return ErpMaterialAllocateMaterialEnum::getStatusDesc($data['status']);
    }
	
    public function getReturnedStatusDescAttr($value, $data)
    {
        return ErpMaterialAllocateMaterialEnum::getReturnedStatusDesc($data['returned_status']);
    }	

	public function getCanSignAttr($value, $data)
    {
		return $data['status']==ErpMaterialAllocateMaterialEnum::STATUS_HANDLE;
    }
	
	public function getCanCancelAttr($value, $data)
    {
		return $data['status']==ErpMaterialAllocateMaterialEnum::STATUS_HANDLE;
    }	
	
	public function getCanReturnedAttr($value, $data)
    {
		return $data['status']==ErpMaterialAllocateMaterialEnum::STATUS_SIGNED&&$data['returned_status']==ErpMaterialAllocateMaterialEnum::RETURNED_STATUS_NO&&$data['stock_num']>$data['signed_num'];
    }	
	
    public function getSendStatusDescAttr($value, $data)
    {
        return ErpMaterialAllocateMaterialEnum::getSendStatusDesc($data['send_status']);
    }	
	
	public function fromWarehouse(){
		return $this->belongsTo('app\common\model\ErpWarehouse','from_warehouse_id','id');
	}
	
	public function toWarehouse(){
		return $this->belongsTo('app\common\model\ErpWarehouse','to_warehouse_id','id');
	}	
	
	public function searchQueryAttr($query, $value, $data)
    {
		$alias 		= '';
		$m_alias	= '';
		if (!empty($value['_alias'])) {
			$alias 	= $value['_alias'].'.';
        }
		if (!empty($value['_material_alias'])) {
			$m_alias= $value['_material_alias'].'.';
        }		
		if (!empty($value['material_stock_id'])) {
			$query->where($alias.'material_stock_id', '=', $value['material_stock_id']);
        }
        if ($m_alias && !empty($value['keyword'])) {
			$query->where($m_alias.'sn|'.$m_alias.'name', 'like', '%' . $value['keyword'] . '%');
        }
        if (!empty($value['status'])) {
			$query->where($alias.'status', 'in', $value['status']);
        }	
        if (!empty($value['returned_status'])) {
			$query->where($alias.'returned_status', 'in', $value['returned_status']);
        }	
        if (!empty($value['from_warehouse_id'])) {
			$query->where('erp_material_stock.from_warehouse_id', '=', $value['from_warehouse_id']);
        }
        if (!empty($value['to_warehouse_id'])) {
			$query->where('erp_material_stock.to_warehouse_id', '=', $value['to_warehouse_id']);
        }	
        if (!empty($value['create_admin'])) {
			$query->where('erp_material_stock.create_admin', '=', $value['create_admin']);
        }
        if (isset($value['send_status']) && $value['send_status'] !== '') {
			$query->where($alias.'send_status', '=', $value['send_status']);
        }		
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($alias.$value['sort'],$value['order']);
		}
    }
}