<?php
namespace app\common\model;
use app\common\model\ErpMaterialStock;
/**
 * 报废出库模型
 * Class ErpMaterialStock
 * @package app\common\model;
 */
class ErpMaterialDiscard extends ErpMaterialStock
{
	protected $name='erp_material_stock'; 

    public function materials(){
		return $this->hasMany('app\common\model\ErpMaterialDiscardMaterial', 'material_stock_id', 'id');
    }
	
		
}