<?php
namespace app\common\model;
use app\common\model\BaseModel;

class ErpSupplierProcessBom extends BaseModel
{

    public function searchQueryAttr($query, $value, $data)
    {
		$alias 		= '';
		$m_alias	= '';
		$p_alias	= '';
		$rm_alias	= '';
		if (!empty($value['_alias'])) {
			$alias 	= $value['_alias'].'.';
        }
		if (!empty($value['_material_alias'])) {
			$m_alias= $value['_material_alias'].'.';
        }	
		if (!empty($value['_process_alias'])) {
			$p_alias= $value['_process_alias'].'.';
        }		
		if (!empty($value['_related_material_alias'])) {
			$rm_alias= $value['_related_material_alias'].'.';
        }		
        if ($m_alias && !empty($value['material_keyword'])) {
			$query->where($m_alias.'sn|'.$m_alias.'name', 'like', '%' . $value['material_keyword'] . '%');
        }
        if ($rm_alias && !empty($value['related_material_keyword'])) {
			$query->where($rm_alias.'sn|'.$rm_alias.'name', 'like', '%' . $value['related_material_keyword'] . '%');
        }
        if ($p_alias && !empty($value['supplier_id'])) {
            $query->where($p_alias.'supplier_id', '=', $value['supplier_id']);
        }		
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($value['sort'],$value['order']);
		}
    }

    public function getStatusDescAttr($value, $data)
    {
        return $data['status'] ? '正常' : '停用';
    }	

	public function process(){
		return $this->belongsTo('app\common\model\ErpSupplierProcess','process_id','id');
	}	
	
	public function material(){
		return $this->belongsTo('app\common\model\ErpMaterial','material_id','id');
	}

	public function relatedMaterial(){
		return $this->belongsTo('app\common\model\ErpMaterial','related_material_id','id');
	}	
	
}