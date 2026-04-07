<?php
namespace app\common\model;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;
use app\common\model\ErpUser;


class ErpMaterialPrice extends BaseModel
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';

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
        if ($m_alias && !empty($value['keyword'])) {
			$query->where($m_alias.'sn|'.$m_alias.'name', 'like', '%' . $value['keyword'] . '%');
        }
        if ($m_alias && !empty($value['name'])) {
			$query->where($m_alias.'name', 'like', '%' . $value['name'] . '%');
        }	
        if ($m_alias && !empty($value['sn'])) {
			$query->where($m_alias.'sn', 'like', '%' . $value['sn'] . '%');
        }
		if ($m_alias && !empty($value['tree_id'])) {
			$query->where($m_alias.'tree_id', '=', $value['tree_id']);
        }
        if (!empty($value['supplier_id'])) {
            $query->where($alias.'supplier_id', '=', $value['supplier_id']);
        }		
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($value['sort'],$value['order']);
		}
    }

    public function getStatusDescAttr($value, $data)
    {
        return $data['status'] ? '正常' : '停用';
    }	

	
	public function supplier(){
		return $this->belongsTo('app\common\model\ErpSupplier','supplier_id','id');
	}	
	
	public function material(){
		return $this->belongsTo('app\common\model\ErpMaterial','material_id','id');
	}	
	
}