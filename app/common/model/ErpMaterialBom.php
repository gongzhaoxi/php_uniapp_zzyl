<?php
namespace app\common\model;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

/**
 * 物料bom模型
 * Class ErpMaterialBom
 * @package app\common\model;
 */
class ErpMaterialBom extends BaseModel
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';

	public function material(){
		return $this->belongsTo('app\common\model\ErpMaterial','material_id','id');
	}

  	public function relatedMaterial(){
		return $this->belongsTo('app\common\model\ErpMaterial','related_material_id','id');
	}
   
	public function searchQueryAttr($query, $value, $data){
        if (!empty($value['material_id'])) {
			$query->where('material_id', '=', $value['material_id']);
        }
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($value['sort'],$value['order']);
		}
    }

}