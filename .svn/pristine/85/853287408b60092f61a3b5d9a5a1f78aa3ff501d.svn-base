<?php
namespace app\admin\validate;
use think\Validate;
use app\common\model\ErpProjectBom;

/**
 * 方案bom验证
 * Class CustomerValidate
 * @package app\admin\validate
 */
class ErpProjectBomValidate extends Validate{

    protected $rule = [
        'id' 					=> 'require',
		'data_type|数据类型'	=> 'require|in:1,2,3,4',
        'material_id' 			=> 'require|number|checkMaterial',
		'num|数量' 				=> 'require|number',
		'bill_type|类型' 		=> 'require|number',
        'color_follow' 			=> 'require|in:0,1',
		'can_replace' 			=> 'require|in:0,1',
		'ids' 					=> 'require|array',
    ];

    protected $message = [
        'id.require' 					=> '参数缺失',
        'material_id.require' 			=> '物料不能为空',
        'material_id.number' 			=> '物料只能为数字',
        'related_material_id.require' 	=> '产品不能为空',
        'related_material_id.number' 	=> '产品只能为数字',
        'color_follow.require' 			=> '请选择颜色是否跟随产品',
        'color_follow.in' 				=> '颜色是否跟随产品错误',
		'ids.require'					=> '请选择数据',
		'ids.array'						=> '请选择数据',
    ];

	protected function checkMaterial($value,$rule,$data){
		if(ErpProjectBom::where('material_id',$data['material_id'])->where('project_id',$data['project_id'])->where('data_type',$data['data_type'])->count()){
			return '方案已绑定该物料';
		}
		return true;
    }

    public function sceneAdd(){
		return $this->only(['data_type','material_id','color_follow','num','bill_type','can_replace']);
    }

    public function sceneEdit(){
        return $this->only(['id','color_follow','num','bill_type','can_replace'])->remove('color_follow', 'require')->remove('num', 'require')->remove('bill_type', 'require')->remove('can_replace', 'require');
    }

    public function sceneRemove(){
		return $this->only(['ids']);
    }
	
    public function sceneRecycle(){
		return $this->only(['ids']);
    }

}