<?php
namespace app\common\model;
use app\common\model\BaseModel;

class ErpMaterialPlanError extends BaseModel
{
  	public function user(){
		return $this->belongsTo('app\common\model\ErpUser','user_id','id');
	}

  	public function process(){
		return $this->belongsTo('app\common\model\ErpProcess','process_id','id');
	}	
	
  	public function plan(){
		return $this->belongsTo('app\common\model\ErpMaterialPlan','plan_id','id');
	}	
	
  	public function material(){
		return $this->belongsTo('app\common\model\ErpMaterial','material_id','id');
	}		
	
	public function getPhotoAttr($value, $data){
		return $value?explode(',',$value):[];
    }  
	
	public function setPhotoAttr($value, $data){
		return is_array($value)?implode(',',$value):'';
    } 	
	

}