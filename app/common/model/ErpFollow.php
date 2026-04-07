<?php
namespace app\common\model;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

/**
 * 随工单模型
 * Class ErpFollow
 * @package app\common\model
 */
class ErpFollow extends BaseModel
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';

    public function product(){
		return $this->hasMany('app\common\model\ErpFollowItem', 'follow_id', 'id')->where('type',1);
    }

    public function process(){
		return $this->hasMany('app\common\model\ErpFollowItem', 'follow_id', 'id')->where('type',2);
    }

	public function category(){
		return $this->belongsTo('app\common\model\DictData','cid','id');
	}
	
	public function getCategoryNameAttr($value, $data){
		$category = get_dict_data('follow_type');
		return $category&&!empty($category[$data['cid']])?$category[$data['cid']]['name']:'';
    } 

    public function searchQueryAttr($query, $value, $data)
    {
        if (!empty($value['name'])) {
            $query->where('name', 'like', '%' . $value['name'] . '%');
        }
		if (!empty($value['create_time'])) {
			$time = is_array($value['create_time'])?$value['create_time']:explode('至',$value['create_time']);
            $query->whereBetweenTime('create_time', trim($time[0]), trim($time[1]));
		}
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($value['sort'],$value['order']);
		}
    }

    public function getStatusDescAttr($value, $data)
    {
        return $data['status'] ? '正常' : '停用';
    }

}