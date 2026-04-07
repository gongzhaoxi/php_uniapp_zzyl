<?php
namespace app\admin\validate;
use think\Validate;
use app\common\model\ErpMaterialBom;

/**
 * 物料bom验证
 * Class CustomerValidate
 * @package app\admin\validate
 */
class ErpMaterialBomValidate extends Validate{

    protected $rule = [
        'id' 					=> 'require',
        'material_id' 			=> 'require|number',
        'related_material_id' 	=> 'require|checkMaterial',
        'color_follow' 			=> 'require|in:0,1',
		'ids' 					=> 'require|array',
    ];

    protected $message = [
        'id.require' 					=> '参数缺失',
        'material_id.require' 			=> '物料不能为空',
        'material_id.number' 			=> '物料只能为数字',
        'related_material_id.require' 	=> '绑定物料不能为空',
        'related_material_id.number' 	=> '绑定物料只能为数字',
        'color_follow.require' 			=> '请选择颜色是否跟随产品',
        'color_follow.in' 				=> '颜色是否跟随产品错误',
		'ids.require'					=> '请选择数据',
		'ids.array'						=> '请选择数据',
    ];

	protected function checkMaterial($value,$rule,$data){
		
		$check = ErpMaterialBom::alias('a')->join('erp_material b','a.related_material_id = b.id','LEFT')->where('a.material_id',$data['material_id'])->where('a.related_material_id','in',$data['related_material_id'])->column('b.name');

		if($check){
			return implode(',',$check).'已绑定';
		}
		if(in_array($data['material_id'],explode(',',$data['related_material_id']))){
			return '不能绑定自己';
		}
		return true;
    }

    public function sceneAdd(){
		return $this->only(['material_id','related_material_id','color_follow']);
    }

    public function sceneEdit(){
        return $this->only(['id']);
    }

    public function sceneRemove(){
		return $this->only(['ids']);
    }
	
    public function sceneRecycle(){
		return $this->only(['ids']);
    }

}