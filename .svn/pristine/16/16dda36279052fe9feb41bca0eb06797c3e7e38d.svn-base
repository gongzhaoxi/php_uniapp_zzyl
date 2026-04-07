<?php
namespace app\common\model;
use app\common\model\ErpMaterialStock;
/**
 * 物料调拨模型
 * Class ErpMaterialAllocate
 * @package app\common\model;
 */
class ErpMaterialAllocate extends ErpMaterialStock
{
	protected $name='erp_material_stock'; 

    public function materials(){
		return $this->hasMany('app\common\model\ErpMaterialAllocateMaterial', 'material_stock_id', 'id');
    }
	
	public function fromWarehouse(){
		return $this->belongsTo('app\common\model\ErpWarehouse','from_warehouse_id','id');
	}
	
	public function toWarehouse(){
		return $this->belongsTo('app\common\model\ErpWarehouse','to_warehouse_id','id');
	}	

}