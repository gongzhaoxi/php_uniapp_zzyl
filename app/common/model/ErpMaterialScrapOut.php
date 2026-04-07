<?php
namespace app\common\model;
use app\common\model\BaseModel;

/**
 * 报废出库详情模型
 * Class ErpMaterialScrapOut
 * @package app\common\model;
 */
class ErpMaterialScrapOut extends BaseModel
{

	public function scrap(){
		return $this->belongsTo('app\common\model\ErpMaterialScrap','scrap_id','id');
	}
	
	public function material(){
		return $this->belongsTo('app\common\model\ErpMaterial','material_id','id');
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
        if ($alias && !empty($value['order_sn'])) {
			$query->where($alias.'order_sn', '=',  $value['order_sn'] );
        }	
        if ($m_alias && !empty($value['sn'])) {
			$query->where($m_alias.'sn', '=',  $value['sn'] );
        }
        if ($m_alias && !empty($value['name'])) {
			$query->where($m_alias.'name', 'like', '%' . $value['name'] . '%');
        }		
        if ($alias && !empty($value['supplier_id'])) {
			$query->where($alias.'supplier_id', '=',  $value['supplier_id'] );
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