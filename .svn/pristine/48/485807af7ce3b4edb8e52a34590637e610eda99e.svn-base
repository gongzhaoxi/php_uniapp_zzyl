<?php
namespace app\common\model;
use app\common\model\BaseModel;

class ErpMaterialWarehouseReturn extends BaseModel
{
	public function material(){
		return $this->belongsTo('app\common\model\ErpMaterial','material_id','id');
	}	
	
	public function warehouse(){
		return $this->belongsTo('app\common\model\ErpWarehouse','warehouse_id','id');
	}	
	
	public function materialWarehouse(){
		return $this->belongsTo('app\common\model\ErpMaterialWarehouse','material_warehouse_id','id');
	}	

	public function searchQueryAttr($query, $value, $data)
    {
		$alias 		= '';
		$m_alias	= '';
		$w_alias	= '';
		if (!empty($value['_alias'])) {
			$alias 	= $value['_alias'].'.';
        }
		if (!empty($value['_material_alias'])) {
			$m_alias= $value['_material_alias'].'.';
        }		
		if (!empty($value['_warehouse_alias'])) {
			$w_alias= $value['_warehouse_alias'].'.';
        }		
        if ($m_alias && !empty($value['keyword'])) {
			$query->where($m_alias.'sn|'.$m_alias.'name', 'like', '%' . $value['keyword'] . '%');
        }
        if (!empty($value['warehouse_id'])) {
			$query->where($alias.'warehouse_id', '=', $value['warehouse_id']);
        }	
        if (!empty($value['material_id'])) {
			$query->where($alias.'material_id', '=', $value['material_id']);
        }		
        if (!empty($value['material_warehouse_id'])) {
			$query->where($alias.'material_warehouse_id', '=', $value['material_warehouse_id']);
        }		
		if (!empty($value['ids'])) {
			$query->where($alias.'id', 'in', $value['ids']);
        }	
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($alias.$value['sort'],$value['order']);
		}
    }	
	

}