<?php
namespace app\common\model;
use app\common\model\BaseModel;

class ErpMaterialPlanFollow extends BaseModel
{
	protected $json = ['follow_item'];
	protected $jsonAssoc = true;
	
  	public function user(){
		return $this->belongsTo('app\common\model\ErpUser','user_id','id');
	}

  	public function process(){
		return $this->belongsTo('app\common\model\ErpProcess','process_id','id');
	}	
	
  	public function plan(){
		return $this->belongsTo('app\common\model\ErpMaterialPlan','plan_id','id');
	}

}