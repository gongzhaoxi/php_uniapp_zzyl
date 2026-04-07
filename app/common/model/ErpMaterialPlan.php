<?php
namespace app\common\model;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;
use app\common\enum\ErpMaterialPlanEnum;

class ErpMaterialPlan extends BaseModel
{
	use SoftDelete;
    protected $deleteTime = 'delete_time';

	public function material(){
		return $this->belongsTo('app\common\model\ErpMaterial','material_id','id');
	}
	
	public function getStatusDescAttr($value,$data){
		return ErpMaterialPlanEnum::getStatusDesc($data['status']);
	}

	public function getTypeDescAttr($value,$data){
		return ErpMaterialPlanEnum::getTypeDesc($data['type']);
	}	
	
    public function code(){
		return $this->hasMany('app\common\model\ErpMaterialCode', 'data_id', 'id')->where('data_type','erp_material_plan');
    }			
	
	public function process(){
		return $this->hasMany('app\common\model\ErpMaterialPlanProcess', 'plan_id', 'id');
    }	
	
	public function searchQueryAttr($query, $value, $data)
    {
        if (!empty($value['plan_sn'])) {
            $query->where('plan_sn', 'like', '%' . $value['plan_sn'] . '%');
        }
        if (!empty($value['status'])) {
            $query->where('status', 'in', $value['status']);
        }
        if (!empty($value['type'])) {
            $query->where('type', '=', $value['type']);
        }		
        if (!empty($value['create_time'])) {
			$create_time = is_array($value['create_time'])?$value['create_time']:explode('至',$value['create_time']);
			if(!empty($create_time[0])){
				$query->where('create_time', '>', strtotime(trim($create_time[0])));
			}
			if(!empty($create_time[1])){
				$query->where('create_time', '<', strtotime(trim($create_time[1]))+24*3600);
			}
        }
        if (!empty($value['start_date'])) {
			$start_date = is_array($value['start_date'])?$value['start_date']:explode('至',$value['start_date']);
			if(!empty($start_date[0])){
				$query->where('start_date', '>=', (trim($start_date[0])));
			}
			if(!empty($start_date[1])){
				$query->where('start_date', '<=', (trim($start_date[1])));
			}
        }
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($value['sort'],$value['order']);
		}
    }	
	
}