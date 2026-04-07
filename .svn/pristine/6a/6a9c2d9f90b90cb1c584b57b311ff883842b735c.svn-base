<?php
namespace app\common\model;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;
use app\common\enum\YesNoEnum;

/**
 * 配置方案bom模型
 * Class ErpProductBom
 * @package app\common\model;
 */
class ErpProjectBom extends BaseModel
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';

	public function material(){
		return $this->belongsTo('app\common\model\ErpMaterial','material_id','id');
	}
	
    public function getCanReplaceDescAttr($value, $data){
        return YesNoEnum::getIsOpenDesc($data['can_replace']);
    }   
   
	public function getBillTypeNameAttr($value, $data){
		$category = get_dict_data('product_bill_type');
		return $category&&!empty($category[$data['bill_type']])?$category[$data['bill_type']]['name']:'';
    }  
   
	public function searchQueryAttr($query, $value, $data){
		$alias 		= '';
		$m_alias	= '';
		if (!empty($value['_alias'])) {
			$alias 	= $value['_alias'].'.';
        }
		if (!empty($value['_material_alias'])) {
			$m_alias= $value['_material_alias'].'.';
        }
        if (!empty($value['data_type'])) {
			$query->where($alias.'data_type', '=', $value['data_type']);
        }	
        if (isset($value['project_id']) && $value['project_id'] !== '') {
			$query->where($alias.'project_id', '=', $value['project_id']);
        }	
        if (!empty($value['keyword'])) {
            $query->where($m_alias.'sn|'.$m_alias.'name', 'like', '%' . $value['keyword'] . '%');
        }		
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($value['sort'],$value['order']);
		}
    }

}