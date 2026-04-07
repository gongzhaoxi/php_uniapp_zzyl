<?php
namespace app\common\model;
use app\common\model\BaseModel;
use app\common\enum\YesNoEnum;
/**
 * 图纸集模型
 * Class ErpDrawing
 * @package app\common\model;
 */
class ErpDrawing extends BaseModel
{

	public function getPicLinkAttr($value,$data)
    {
        return get_browse_url($data['pic']);
    }
	
	public function getFinalPicLinkAttr($value,$data)
    {
        return get_browse_url($data['final_pic']);
    }	
	
    public function getStatusDescAttr($value, $data)
    {
        return YesNoEnum::getIsOpenDesc($data['status']);
    }	
	
	public function getFinalCheckAttr($value,$data)
    {
		if($data['final_check_time']){
			return $data['final_check_name'].date('Y/m/d',$data['final_check_time']);
		}else{
			return '';
		}
    }	

	public function getFirstCheckAttr($value,$data)
    {
		if($data['first_check_time']){
			return $data['first_check_name'].date('Y/m/d',$data['first_check_time']);
		}else{
			return '';
		}
    }		
	
		
	public function searchQueryAttr($query, $value, $data)
    {
		$alias 		= '';
		$m_alias	= '';
		if (!empty($value['_alias'])) {
			$alias 	= $value['_alias'].'.';
        }
		if (!empty($value['_material_alias'])) {
			$m_alias= $value['_material_alias'].'.';
        }		
        if ($m_alias && !empty($value['name'])) {
            $query->where($m_alias.'name', 'like', '%' . $value['name'] . '%');
        }
        if ($m_alias && !empty($value['tree_id'])) {
            $query->where($m_alias.'tree_id', '=', $value['tree_id']);
        }		
        if (isset($value['status']) && $value['status'] !== '') {
            $query->where($alias.'status', '=', $value['status']);
        }
        if (!empty($value['sn'])) {
            $query->where($alias.'sn', 'like', '%' . $value['sn'] . '%');
        }
        if (isset($value['check_status']) && $value['check_status'] !== '') {
            $query->where($alias.'check_status', '=', $value['check_status']);
        }		
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($alias.$value['sort'],$value['order']);
		}
    }

}