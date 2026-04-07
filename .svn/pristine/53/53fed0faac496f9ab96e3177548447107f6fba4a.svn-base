<?php
namespace app\common\model;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;
use app\common\enum\YesNoEnum;
use app\common\enum\ErpWarehouseEnum;
/**
 * 仓库模型
 * Class ErpWarehouse
 * @package app\common\model;
 */
class ErpWarehouse extends BaseModel
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';

    public function getStatusDescAttr($value, $data)
    {
        return YesNoEnum::getIsOpenDesc($data['status']);
    }	
	
    public function getTypeDescAttr($value, $data)
    {
        return ErpWarehouseEnum::getTypeDesc($data['type']);
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
            $query->where('sn|name', 'like', '%' . $value['keyword'] . '%');
        }
        if (isset($value['type']) && $value['type'] !== '') {
            $query->where('type', 'in', $value['type']);
        }		
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($value['sort'],$value['order']);
		}
    }

}