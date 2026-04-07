<?php
namespace app\common\model;
use app\common\model\BaseModel;
use app\common\enum\ErpMaterialScrapEnum;

/**
 * 报废出库详情模型
 * Class ErpMaterialScrap
 * @package app\common\model;
 */
class ErpMaterialScrap extends BaseModel
{

	public function enterMaterial(){
		return $this->belongsTo('app\common\model\ErpMaterialEnterMaterial','enter_material_id','id');
	}
	
	public function material(){
		return $this->belongsTo('app\common\model\ErpMaterial','material_id','id');
	}	

    public function getTypeAttr($value, $data)
    {
		if($data['cid'] == 0){
			return '质检检验';
		}
		$category = get_dict_data('material_scrap_type');
		return $category&&!empty($category[$data['cid']])?$category[$data['cid']]['name']:'';
    }
	
	public function getPhotoLinkAttr($value,$data)
    {
        return get_browse_url($data['photo']);
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
        if ($m_alias && !empty($value['keyword'])) {
			$query->where($m_alias.'sn|'.$m_alias.'name', 'like', '%' . $value['keyword'] . '%');
        }
        if ($alias && isset($value['cid']) && $value['cid'] !== '') {
			$query->where($alias.'cid', '=', $value['cid']);
        }	
        if ($alias && !empty($value['ids'])) {
			$query->where($alias.'id', 'in', $value['ids']);
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
        if ($m_alias && !empty($value['supplier_id'])) {
			$query->where($m_alias.'supplier_id', 'find in set',  $value['supplier_id'] );
        }
        if (!empty($value['stock_date'])) {
			$stock_date = is_array($value['stock_date'])?$value['stock_date']:explode('至',$value['stock_date']);
			if(!empty($stock_date[0])){
				$query->where($alias.'stock_date', '>=', (trim($stock_date[0])));
			}
			if(!empty($stock_date[1])){
				$query->where($alias.'stock_date', '<=', (trim($stock_date[1])));
			}
        }
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($alias.$value['sort'],$value['order']);
		}
    }
}