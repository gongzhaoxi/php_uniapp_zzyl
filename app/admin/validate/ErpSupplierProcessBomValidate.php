<?php
namespace app\admin\validate;
use think\Validate;

class ErpSupplierProcessBomValidate extends Validate{

    protected $rule = [
        'id' 								=> 'require',
		'process_id|委外工序'				=> 'require',
		'material_id|委外物料' 				=> 'require|unique:app\common\model\ErpSupplierProcessBom,material_id^process_id^related_material_id',
		'related_material_id|出库坯料' 		=> 'require|different:material_id',
		'num|用量' 							=> 'require|float',
		'material_unit|委外物料单位'		=> 'require',
		'related_material_unit|出库坯料单位'=> 'require',
		'ids' 								=> 'require|array',
    ];

    protected $message = [
        'id.require' 			=> '参数缺失',
        'status.require' 		=> '请选择状态',
        'status.in' 			=> '状态参数错误',
		'ids.require'			=> '请选择数据',
		'ids.array'				=> '请选择数据',
    ];

    public function sceneAdd(){
		return $this->only(['process_id','material_id','related_material_id','num','material_unit','related_material_unit']);
    }

    public function sceneEdit(){
        return $this->only(['id','process_id','material_id','related_material_id','num','material_unit','related_material_unit']);
    }

    public function sceneRemove(){
		return $this->only(['ids']);
    }

}