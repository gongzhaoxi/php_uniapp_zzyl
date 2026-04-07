<?php
namespace app\admin\validate;
use think\Validate;

/**
 * 物料验证
 * Class CustomerValidate
 * @package app\admin\validate
 */
class ErpMaterialValidate extends Validate{

    protected $rule = [
        'id' 						=> 'require',
        'sn|物料编码' 				=> 'max:100|unique:app\common\model\ErpMaterial',
        'name|物料名称' 			=> 'require|max:100',
		'cid' 						=> 'require|number',
		'safety_stock|安全库存' 	=> 'require|number|length:1,11',
		'min_stock|最低库存'		=> 'require|number|length:1,11',
		'max_stock|最高库存'		=> 'require|number|length:1,11',
		'warehouse_id' 				=> 'require|number',
		'unit|单位' 				=> 'max:100',
		'processing_type|加工类型' 	=> 'max:255',
		'material|材料' 			=> 'max:255',
		'surface|表面'				=> 'max:255',
		'color|颜色'				=> 'max:255',
		'remark|备注'				=> 'max:255',
        'status' 					=> 'require|in:0,1',	
		'ids' 						=> 'require|array',
		'type|类别' 				=> 'require|in:1,2',
		'stock|现有库存' 			=> 'require|number',
    ];

    protected $message = [
        'id.require' 			=> '参数缺失',
        'status.require' 		=> '请选择状态',
        'status.in' 			=> '客户状态参数错误',
		'cid.require' 			=> '请选择物料分类',
        'cid.number' 			=> '物料分类错误',
		'warehouse_id.require' 	=> '请选择仓库',
        'warehouse_id.number' 	=> '仓库错误',	
		'ids.require'			=> '请选择数据',
		'ids.array'				=> '请选择数据',
    ];

    public function sceneAdd(){
		return $this->only(['sn','name','cid','safety_stock','min_stock','max_stock','unit','processing_type','material','surface','remark','status','color','type']);
    }

    public function sceneEdit(){
		return $this->only(['id','sn','name','cid','safety_stock','min_stock','max_stock','unit','processing_type','material','surface','remark','color','status'])->append('sn','require');
    }

    public function sceneRemove(){
		return $this->only(['ids']);
    }
	
    public function sceneRecycle(){
		return $this->only(['ids']);
    }

}