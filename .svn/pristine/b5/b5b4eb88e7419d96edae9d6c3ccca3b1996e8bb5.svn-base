<?php
namespace app\admin\validate;
use think\Validate;


class ErpMaterialPlanValidate extends Validate{

    protected $rule = [
        'id' 			=> 'require',
		'material' 		=> 'require|array|checkMaterial',
		'start_date' 	=> 'require|date',
		'ids' 			=> 'require|array',
    ];

    protected $message = [
        'id.require' 			=> '参数缺失',
		'material.require' 		=> '请选择物料',
        'material.array' 		=> '物料错误',
		'ids.require'			=> '请选择数据',
		'ids.array'				=> '请选择数据',
    ];

    public function sceneAdd(){
		return $this->only(['material']);
    }

    public function sceneStart(){
        return $this->only(['id','start_date']);
    }

    public function sceneRemove(){
		return $this->only(['ids']);
    }
	
    public function sceneRecycle(){
		return $this->only(['ids']);
    }

	protected function checkMaterial($value,$rule,$data){
		foreach($value as $key=>$vo){
			if(empty($vo['num'])){
				return '数量错误';
			}
			if($vo['num'] != intval($vo['num']) ||$vo['num'] <= 0){
				return '数量只能为大于0的整数';
			}
		}
		return true;
    }

}