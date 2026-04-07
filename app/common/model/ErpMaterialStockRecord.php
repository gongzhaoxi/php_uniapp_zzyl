<?php
namespace app\common\model;
use app\common\model\BaseModel;
use app\common\enum\ErpMaterialOutMaterialEnum;
/**
 * 物料出库详情模型
 * Class ErpMaterialStockRecord
 * @package app\common\model;
 */
class ErpMaterialStockRecord extends BaseModel
{

	public function stock(){
		return $this->belongsTo('app\common\model\ErpMaterialStock','material_stock_id','id');
	}
	
	public function material(){
		return $this->belongsTo('app\common\model\ErpMaterial','material_id','id');
	}	

	public function searchQueryAttr($query, $value, $data)
    {
        if (!empty($value['material_stock_id'])) {
			$query->where('material_stock_id', '=', $value['material_stock_id']);
        }
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($value['sort'],$value['order']);
		}
    }
}