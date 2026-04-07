<?php
namespace app\common\model;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;


class ErpProcessWage extends BaseModel
{
    //use SoftDelete;
    //protected $deleteTime = 'delete_time';


	public function process(){
		return $this->belongsTo('app\common\model\ErpProcess','process_id','id');
	}
	
	public function product(){
		return $this->belongsTo('app\common\model\ErpProduct','product_id','id');
	}
}