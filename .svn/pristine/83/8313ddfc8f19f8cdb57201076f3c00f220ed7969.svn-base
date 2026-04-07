<?php
namespace app\common\model;
use app\common\model\BaseModel;
use app\common\enum\ErpMaterialDiscardMaterialEnum;

/**
 * 报废出库详情模型
 * Class ErpMaterialDiscardMaterial
 * @package app\common\model;
 */
class ErpMaterialDiscardMaterial extends BaseModel
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
        return ErpMaterialDiscardMaterialEnum::getStatusDesc($data['status']);
    }
	
	public function getPhotoLinkAttr($value,$data)
    {
        return get_browse_url($data['photo']);
    }	
	
	public function getCanOutAttr($value, $data)
    {
         return $data['status']!=ErpMaterialDiscardMaterialEnum::STATUS_CANCEL&&$data['status']!=ErpMaterialDiscardMaterialEnum::STATUS_FINISH?true:false;
    }
	
	public function getCanCancelAttr($value, $data)
    {
		return $data['status']==ErpMaterialDiscardMaterialEnum::STATUS_CANCEL?false:true;
    }	
	
	public function getCanFinishAttr($value, $data)
    {
		return $data['status']==ErpMaterialDiscardMaterialEnum::STATUS_CANCEL||$data['status']==ErpMaterialDiscardMaterialEnum::STATUS_FINISH?false:true;
    }	

	
	public function searchQueryAttr($query, $value, $data)
    {
		$alias 		= '';
		$m_alias	= '';
		$s_alias	= '';
		if (!empty($value['_alias'])) {
			$alias 	= $value['_alias'].'.';
        }
		if (!empty($value['_material_alias'])) {
			$m_alias= $value['_material_alias'].'.';
        }	
		if (!empty($value['_stock_alias'])) {
			$s_alias= $value['_stock_alias'].'.';
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
        if ($m_alias && !empty($value['sn'])) {
			$query->where($m_alias.'sn', '=',  $value['sn'] );
        }
        if ($m_alias && !empty($value['name'])) {
			$query->where($m_alias.'name', 'like', '%' . $value['name'] . '%');
        }		
        if ($s_alias && !empty($value['order_sn'])) {
			$query->where($s_alias.'order_sn', '=',  $value['order_sn'] );
        }
        if ($s_alias && !empty($value['supplier_id'])) {
			$query->where($s_alias.'supplier_id', '=',  $value['supplier_id'] );
        }
        if (!empty($value['stock_date'])) {
			$stock_date = is_array($value['stock_date'])?$value['stock_date']:explode('至',$value['stock_date']);
			if(!empty($stock_date[0])){
				$query->where('stock_date', '>=', (trim($stock_date[0])));
			}
			if(!empty($stock_date[1])){
				$query->where('stock_date', '<=', (trim($stock_date[1])));
			}
        }
		
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($alias.$value['sort'],$value['order']);
		}
    }
}