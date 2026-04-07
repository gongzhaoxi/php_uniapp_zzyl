<?php
namespace app\common\model;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;
use app\common\model\ErpUser;

/**
 * 生产流程模型
 * Class ErpSupplierProcess
 * @package app\common\model
 */
class ErpSupplierProcess extends BaseModel
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';

    public function searchQueryAttr($query, $value, $data)
    {
        if (!empty($value['keyword'])) {
            $query->where('name|sn', 'like', '%' . $value['keyword'] . '%');
        }
        if (!empty($value['name'])) {
            $query->where('name', 'like', '%' . $value['name'] . '%');
        }	
        if (!empty($value['sn'])) {
            $query->where('sn', 'like', '%' . $value['sn'] . '%');
        }	
        if (!empty($value['supplier_id'])) {
            $query->where('supplier_id', '=', $value['supplier_id']);
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

	
	public function supplier(){
		return $this->belongsTo('app\common\model\ErpSupplier','supplier_id','id');
	}	
	

}