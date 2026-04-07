<?php
namespace app\common\model;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;
use app\common\model\ErpUser;

/**
 * 生产流程模型
 * Class ErpProcess
 * @package app\common\model
 */
class ErpProcess extends BaseModel
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';

    public function searchQueryAttr($query, $value, $data)
    {
        if (!empty($value['keyword'])) {
            $query->where('name|sn', 'like', '%' . $value['keyword'] . '%');
        }
		if (!empty($value['create_time'])) {
			$time = is_array($value['create_time'])?$value['create_time']:explode('至',$value['create_time']);
            $query->whereBetweenTime('create_time', trim($time[0]), trim($time[1]));
		}
        if (!empty($value['type'])) {
            $query->where('type', '=', $value['type']);
        }		
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($value['sort'],$value['order']);
		}
    }

    public function getStatusDescAttr($value, $data)
    {
        return $data['status'] ? '正常' : '停用';
    }	

    public function getUserIdAttr($value, $data)
    {
        return $value ? explode(',',$value) : [];
    }	

    public function getUserNameAttr($value, $data)
    {
        return !empty($data['user_id']) ? implode(',',ErpUser::where('id','in',$data['user_id'])->column('name')) : '';
    }

    public function getUserAttr($value, $data)
    {
        return ErpUser::field('id,sn,name')->where('id','in',$data['user_id'])->select();
    }


    public function getMonitorAttr($value, $data)
    {
        return $value ? explode(',',$value) : [];
    }	

    public function getMonitorNameAttr($value, $data)
    {
        return !empty($data['monitor']) ? implode(',',ErpUser::where('id','in',$data['monitor'])->column('name')) : '';
    }



    public static function createSn($prefix = '', $length = 8)
    {
        $rand_str = '';
        for ($i = 0; $i < $length; $i++) {
            $rand_str .= mt_rand(0, 9);
        }
        $sn = $prefix . $rand_str;
        if (ErpProcess::where(['sn' => $sn])->count()) {
            return self::createSn($prefix, $length);
        }
        return $sn;
    }
	
	public function follow(){
		return $this->belongsTo('app\common\model\ErpFollow','follow_id','id');
	}	
	
	
	public function wage(){
		return $this->hasMany('app\common\model\ErpProcessWage', 'process_id', 'id');
    }

    public function getTypeDescAttr($value, $data)
    {
        return $data['type'] == 1 ? '产品工序' : '组件工序';
    }

	public function material(){
		return $this->hasMany('app\common\model\ErpProcessMaterial', 'process_id', 'id');
    }


}