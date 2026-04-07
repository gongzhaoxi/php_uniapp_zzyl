<?php
namespace app\common\model;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;


class ErpProcessMaterial extends BaseModel
{
    //use SoftDelete;
    //protected $deleteTime = 'delete_time';


	public function process(){
		return $this->belongsTo('app\common\model\ErpProcess','process_id','id');
	}
	
	public function material(){
		return $this->belongsTo('app\common\model\ErpMaterial','material_id','id');
	}
}