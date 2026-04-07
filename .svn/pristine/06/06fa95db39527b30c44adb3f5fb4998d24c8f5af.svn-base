<?php
namespace app\common\model;
use app\common\model\BaseModel;
use app\common\enum\ErpMaterialOutMaterialEnum;

/**
 * 物料出库详情模型
 * Class ErpMaterialOutMaterial
 * @package app\common\model;
 */
class ErpMaterialOutMaterial extends BaseModel
{

	public function out(){
		return $this->belongsTo('app\common\model\ErpMaterialOut','material_stock_id','id');
	}
	
	public function material(){
		return $this->belongsTo('app\common\model\ErpMaterial','material_id','id');
	}	

	public function detail(){
		return $this->belongsTo('app\common\model\ErpOrderProduceBom','data_id','id');
	}


    public function getStatusDescAttr($value, $data)
    {
        return ErpMaterialOutMaterialEnum::getStatusDesc($data['status']);
    }
	
	public function getPhotoLinkAttr($value,$data)
    {
        return get_browse_url($data['photo']);
    }	
	
	public function getCanOutAttr($value, $data)
    {
         return $data['stock_num'] - $data['stocked_num']>0&&$data['status']!=ErpMaterialOutMaterialEnum::STATUS_CANCEL&&$data['status']!=ErpMaterialOutMaterialEnum::STATUS_FINISH?true:false;
    }
	
	public function getCanCancelAttr($value, $data)
    {
		return $data['status']==ErpMaterialOutMaterialEnum::STATUS_CANCEL||$data['status']==ErpMaterialOutMaterialEnum::STATUS_FINISH?false:true;
    }	
	
	public function getCanFinishAttr($value, $data)
    {
		return $data['status']==ErpMaterialOutMaterialEnum::STATUS_CANCEL||$data['status']==ErpMaterialOutMaterialEnum::STATUS_FINISH?false:true;
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
        if ($m_alias && !empty($value['produce_type'])) {
			$query->where($m_alias.'produce_type', '=', $value['produce_type']);
        }
        if (!empty($value['status'])) {
			$query->where($alias.'status', 'in', $value['status']);
        }		
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($alias.$value['sort'],$value['order']);
		}
    }
}