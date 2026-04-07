<?php
namespace app\common\model;
use app\common\model\ErpMaterialStock;
/**
 * 物料出库模型
 * Class ErpMaterialStock
 * @package app\common\model;
 */
class ErpMaterialOut extends ErpMaterialStock
{
	protected $name='erp_material_stock'; 

    public function materials(){
		return $this->hasMany('app\common\model\ErpMaterialOutMaterial', 'material_stock_id', 'id');
    }
	
		
}