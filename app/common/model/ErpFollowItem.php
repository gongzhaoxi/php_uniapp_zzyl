<?php
namespace app\common\model;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

/**
 * 随工单模型
 * Class ErpFollowItem
 * @package app\common\model
 */
class ErpFollowItem extends BaseModel
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';


	public function follow(){
		return $this->belongsTo('app\common\model\ErpFollow','follow_id','id');
	}

}