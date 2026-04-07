<?php
namespace app\common\model;
use app\common\model\BaseModel;
use app\common\enum\ErpMaterialCheckMaterialEnum;

/**
 * 物料出库详情模型
 * Class ErpMaterialCheckMaterial
 * @package app\common\model;
 */
class ErpMaterialCheckMaterial extends BaseModel
{

	public function check(){
		return $this->belongsTo('app\common\model\ErpMaterialCheck','material_stock_id','id');
	}
	
	public function material(){
		return $this->belongsTo('app\common\model\ErpMaterial','material_id','id');
	}	

	public function warehouse(){
		return $this->belongsTo('app\common\model\ErpWarehouse','warehouse_id','id');
	}	

    public function getStatusDescAttr($value, $data)
    {
        return ErpMaterialCheckMaterialEnum::getStatusDesc($data['status']);
    }
	
	public function getCanCheckAttr($value, $data)
    {
         return $data['status']!=ErpMaterialCheckMaterialEnum::STATUS_CANCEL&&$data['status']!=ErpMaterialCheckMaterialEnum::STATUS_FINISH?true:false;
    }
	
	public function getCanCancelAttr($value, $data)
    {
		return $data['status']==ErpMaterialCheckMaterialEnum::STATUS_CANCEL?false:true;
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
        if ($m_alias && !empty($value['cid'])) {
			$query->where($m_alias.'cid', '=', $value['cid']);
        }	
        if (!empty($value['status'])) {
			$query->where($alias.'status', 'in', $value['status']);
        }		
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($alias.$value['sort'],$value['order']);
		}
    }
}