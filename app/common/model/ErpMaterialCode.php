<?php
namespace app\common\model;
use app\common\model\BaseModel;

class ErpMaterialCode extends BaseModel
{
    public function detail(){
		return $this->morphTo(['data_type','data_id'],[
        	'erp_material_enter_material'	=>	'app\common\model\ErpMaterialEnterMaterial',
            'erp_material_plan'				=>	'app\common\model\ErpMaterialPlan',
        ]);
	}
	
	public function material(){
		return $this->belongsTo('app\common\model\ErpMaterial','material_id','id');
	}

  	public function purchaseOrder(){
		return $this->belongsTo('app\common\model\ErppPurchaseOrder','purchase_order_id','id');
	}
	
  	public function purchaseOrderData(){
		return $this->belongsTo('app\common\model\ErppPurchaseOrderData','purchase_order_data_id','id');
	}	
   
	public function searchQueryAttr($query, $value, $data){
        if (!empty($value['material_id'])) {
			$query->where('material_id', '=', $value['material_id']);
        }
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($value['sort'],$value['order']);
		}
    }

}