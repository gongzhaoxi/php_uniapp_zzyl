<?php
namespace app\common\model;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;
use app\common\enum\ErpProductProjectEnum;
use app\common\enum\YesNoEnum;
/**
 * 配置方案模型
 * Class ErpProject
 * @package app\common\model;
 */
class ErpProject extends BaseModel
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';

	public function bom(){
		return $this->hasMany('app\common\model\ErpProjectBom','project_id','id');
	}

    public function getTypeDescAttr($value, $data){
        return ErpProductProjectEnum::getTypeDesc($data['type']);
    }   
   
	public function getCategoryAttr($value, $data){
		$category = get_dict_data('product_project_category');
		return $category&&!empty($category[$data['cid']])?$category[$data['cid']]['name']:'';
    }  
	
    public function getStatusDescAttr($value, $data)
    {
        return YesNoEnum::getIsOpenDesc($data['status']);
    }	
   
	public function searchQueryAttr($query, $value, $data){
        if (!empty($value['type'])) {
			$query->where('type', '=', $value['type']);
        }
        if (!empty($value['keyword'])) {
            $query->where('code|name', 'like', '%' . $value['keyword'] . '%');
        }			
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($value['sort'],$value['order']);
		}
    }

}