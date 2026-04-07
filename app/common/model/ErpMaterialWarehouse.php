<?php
namespace app\common\model;
use app\common\model\BaseModel;
use app\common\enum\ErpMaterialEnum;
class ErpMaterialWarehouse extends BaseModel
{
	public function material(){
		return $this->belongsTo('app\common\model\ErpMaterial','material_id','id');
	}	
	
	public function warehouse(){
		return $this->belongsTo('app\common\model\ErpWarehouse','warehouse_id','id');
	}	
	
 	public function getTypeAttr($value, $data){
		return ErpMaterialEnum::getTypeDesc($data['type']);
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
        if (isset($value['is_show']) && $value['is_show'] !== '') {
			$query->where($alias.'is_show', '=', $value['is_show']);
        }			
		if (!empty($value['ids'])) {
			$query->where($alias.'id', 'in', $value['ids']);
        }	
        if ($w_alias&&!empty($value['warehouse_type'])) {
			$query->where($w_alias.'type', 'in', $value['warehouse_type']);
        }
		if (!empty($value['material_ids'])) {
			$query->where($alias.'material_id', 'in', $value['material_ids']);
        }		
		if (!empty($value['stock_search'])) {
			if($value['stock_search'] == 1){
				$query->whereRaw($alias.'stock < '.$alias.'safety_stock');
			}else if($value['stock_search'] == 2){
				$query->whereRaw($alias.'stock < '.$alias.'min_stock');
			}else if($value['stock_search'] == 3){
				$query->where($alias.'stock', '<=', 0);
			}
        }
		if ($m_alias && !empty($value['type'])) {
			$query->where($m_alias.'type', '=',  $value['type']);
        }
		if ($m_alias && !empty($value['material_status'])) {
			$query->where($m_alias.'status', '=',  $value['material_status']);
        }		
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($alias.$value['sort'],$value['order']);
		}
    }	
	

}