<?php
namespace app\common\model;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

/**
 * 车间员工模型
 * Class ErpUser
 * @package app\common\model
 */
class ErpUser extends BaseModel
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';

    public function userAuth()
    {
        return $this->hasOne(ErpUserAuth::class, 'user_id');
    }

    public function searchQueryAttr($query, $value, $data)
    {
        if (!empty($value['keyword'])) {
            $query->where('name|title|mobile', 'like', '%' . $value['keyword'] . '%');
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

    public function getLoginTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }

    public static function createUserSn($prefix = '', $length = 8)
    {
        $rand_str = '';
        for ($i = 0; $i < $length; $i++) {
            $rand_str .= mt_rand(0, 9);
        }
        $sn = $prefix . $rand_str;
        if (ErpUser::where(['sn' => $sn])->count()) {
            return self::createUserSn($prefix, $length);
        }
        return $sn;
    }
	
    public function getPermissionAttr($value, $data)
    {
        return $value ? explode(',',$value) : [];
    }	
	
    public function getWarehouseIdAttr($value, $data)
    {
        return $value ? explode(',',$value) : [];
    }
}