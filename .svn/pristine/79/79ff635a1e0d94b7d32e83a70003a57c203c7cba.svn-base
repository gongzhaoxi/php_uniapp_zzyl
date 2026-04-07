<?php
namespace app\common\model;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;
use app\common\enum\ErpProductProjectEnum;
use app\common\enum\YesNoEnum;
/**
 * 产品配置方案模型
 * Class ErpProductProject
 * @package app\common\model;
 */
class ErpProductProject extends BaseModel
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';


	public static function onBeforeWrite($record){
		if(empty($record->cid)){
			$category 			= get_dict_data('product_project_category');
			if($category&&!empty($category[$record->cid])){
				$record->sort	= $category[$record->cid]['sort'];
			}
		}
    }

	public function bom(){
		return $this->hasMany('app\common\model\ErpProductBom','project_id','id');
	}

  	public function product(){
		return $this->belongsTo('app\common\model\ErpProduct','product_id','id');
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
   
     public function getIsDefaultDescAttr($value, $data)
    {
        return YesNoEnum::getIsOpenDesc($data['is_default']);
    }  
   
   
   
	public function searchQueryAttr($query, $value, $data){
        if (!empty($value['type'])) {
			$query->where('type', '=', $value['type']);
        }
        if (!empty($value['product_id'])) {
			$query->where('product_id', '=', $value['product_id']);
        }
        if (!empty($value['keyword'])) {
            $query->where('code|name', 'like', '%' . $value['keyword'] . '%');
        }			
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($value['sort'],$value['order']);
		}
    }

}