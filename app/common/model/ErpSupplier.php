<?php
namespace app\common\model;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

/**
 * 供应商模型
 * Class ErpSupplier
 * @package app\common\model
 */
class ErpSupplier extends BaseModel
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
	protected $json = ['file'];
	protected $jsonAssoc = true;

    public function searchQueryAttr($query, $value, $data)
    {
        if (!empty($value['name'])) {
            $query->where('name', 'like', '%' . $value['name'] . '%');
        }
		if (!empty($value['create_time'])) {
			$time = is_array($value['create_time'])?$value['create_time']:explode('至',$value['create_time']);
            $query->whereBetweenTime('create_time', trim($time[0]), trim($time[1]));
		}
        if (!empty($value['ids'])) {
            $query->where('id', 'in', is_array($value['ids'])?implode(',',$value['ids']):$value['ids']);
        }		
		
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($value['sort'],$value['order']);
		}
    }

    public function getStatusDescAttr($value, $data)
    {
        return $data['status'] ? '正常' : '停用';
    }


	public function getFileLinkAttr($value,$data)
    {
		$data 						= $data['file'];
		if(!empty($data['file'])){
			foreach($data['file'] as $key=>$vo){
				$data['file'][$key]	= get_browse_url($vo);
			}
		}
        return $data;
    }

}