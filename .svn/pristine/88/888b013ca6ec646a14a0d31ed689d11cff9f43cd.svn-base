<?php
namespace app\common\model;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;
use app\common\enum\YesNoEnum;
use app\common\enum\RegionTypeEnum;
/**
 * 客户模型
 * Class ErpCustomer
 * @package app\common\model;
 */
class ErpCustomer extends BaseModel
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';

	public function order(){
		return $this->hasMany('app\common\model\ErpOrder','customer_id','id');
	}

    public function setRegionAttr($value, $data)
    {
		return is_array($value)?implode(',',$value):$value;
    }	

    public function getRegionAttr($value, $data)
    {
		return $value?explode(',',$value):[];
    }	

    public function getRegionNameAttr($value, $data)
    {
		return $data['region']?implode('',Region::where('id','in',$data['region'])->order('id asc')->column('name')):'';
    }


    public function getStatusDescAttr($value, $data)
    {
        return YesNoEnum::getIsOpenDesc($data['status']);
    }	
	
    public function getRegionTypeDescAttr($value, $data)
    {
        return RegionTypeEnum::getDesc($data['region_type']);
    }		
	
	public function searchQueryAttr($query, $value, $data)
    {
        if (!empty($value['name'])) {
            $query->where('name', 'like', '%' . $value['name'] . '%');
        }
        if (isset($value['status']) && $value['status'] !== '') {
            $query->where('status', '=', $value['status']);
        }
        if (!empty($value['sn'])) {
            $query->where('sn', 'like', '%' . $value['sn'] . '%');
        }
        if (!empty($value['keyword'])) {
            $query->where('sn|name|contacts|phone', 'like', '%' . $value['keyword'] . '%');
        }
        if (isset($value['region_type']) && $value['region_type'] !== '') {
            $query->where('region_type', '=', $value['region_type']);
        }
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($value['sort'],$value['order']);
		}
    }

}