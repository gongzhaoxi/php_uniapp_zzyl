<?php
namespace app\common\model;
use app\common\model\BaseModel;
use app\common\enum\YesNoEnum;
/**
 * 指导书模型
 * Class ErpGuideBook
 * @package app\common\model;
 */
class ErpGuideBook extends BaseModel
{

	public function getAttachmentLinkAttr($value,$data)
    {
        return get_browse_url($data['attachment']);
    }
	
    public function getStatusDescAttr($value, $data)
    {
        return YesNoEnum::getIsOpenDesc($data['status']);
    }	
	
    public function setDataIdAttr($value, $data)
    {
		$value = !is_array($data['data_id'])?explode(',',$data['data_id']):$data['data_id'];
		sort($value);
        return implode(',',$value);
    }	
	
    public function getDataIdAttr($value, $data)
    {
        return isset($data['data_id'])?explode(',',$data['data_id']):[];
    }		
		
		
    public function getIsImageAttr($value, $data)
    {
		if(empty($data['attachment'])){
			return false;
		}
        $arr = explode('.',$data['attachment']);
		return in_array(strtolower($arr[count($arr)-1]),['png','jpg','jpeg','gif','ico','bmp']);
    }		
		
		
	public function searchQueryAttr($query, $value, $data)
    {
        if (!empty($value['name'])) {
            $query->where('name', 'like', '%' . $value['name'] . '%');
        }
        if (isset($value['status']) && $value['status'] !== '') {
            $query->where('status', '=', $value['status']);
        }
        if (!empty($value['code'])) {
            $query->where('code', 'like', '%' . $value['code'] . '%');
        }
        if (!empty($value['keyword'])) {
            $query->where('code|name', 'like', '%' . $value['keyword'] . '%');
        }
        if (isset($value['data_type']) && $value['data_type'] !== '') {
            $query->where('data_type', '=', $value['data_type']);
        }
        if (!empty($value['data_id'])) {
            $query->where('data_id = 0 or data_id = '. $value['data_id']);
        }		
        if (!empty($value['material_category'])) {
            $query->where('material_category', '=', $value['material_category']);
        }		
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($value['sort'],$value['order']);
		}
    }

}