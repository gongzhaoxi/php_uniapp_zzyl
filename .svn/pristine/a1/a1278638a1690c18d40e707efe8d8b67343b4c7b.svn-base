<?php
namespace app\common\model;
use app\common\model\ErpMaterialStock;
/**
 * 物料入库模型
 * Class ErpMaterialEnter
 * @package app\common\model;
 */
class ErpMaterialEnter extends ErpMaterialStock
{
	protected $name='erp_material_stock'; 

    public function materials(){
		return $this->hasMany('app\common\model\ErpMaterialEnterMaterial', 'material_stock_id', 'id');
    }

}