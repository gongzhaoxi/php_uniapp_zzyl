<?php
namespace app\admin\validate;
use think\Validate;

/**
 * 产品验证
 * Class CustomerValidate
 * @package app\admin\validate
 */
class ErpProductValidate extends Validate{

    protected $rule = [
        'id' 						=> 'require',
        'sn|物料编码' 				=> 'max:100|unique:app\common\model\ErpProduct',
        'name|物料名称' 			=> 'require|max:100',
		'cid' 						=> 'require|number',
		'model|产品型号' 			=> 'max:255',
		'specs|产品款式' 			=> 'max:255',
		'remark|备注'				=> 'max:255',
        'status' 					=> 'require|in:0,1',	
		'ids' 						=> 'require|array',
    ];

    protected $message = [
        'id.require' 			=> '参数缺失',
        'status.require' 		=> '请选择状态',
        'status.in' 			=> '产品状态参数错误',
		'cid.require' 			=> '请选择产品分类',
        'cid.number' 			=> '产品分类错误',
		'warehouse_id.require' 	=> '请选择仓库',
        'warehouse_id.number' 	=> '仓库错误',	
		'ids.require'			=> '请选择数据',
		'ids.array'				=> '请选择数据',
    ];

    public function sceneAdd(){
		return $this->only(['sn','name','cid','unit','model','remark','status']);
    }

    public function sceneEdit(){
		return $this->only(['id','sn','name','cid','unit','model','remark','status'])->append('sn','require');
    }

    public function sceneRemove(){
		return $this->only(['ids']);
    }
	
    public function sceneRecycle(){
		return $this->only(['ids']);
    }

}