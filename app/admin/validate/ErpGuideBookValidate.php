<?php
namespace app\admin\validate;
use think\Validate;

/**
 * 客户验证
 * Class CustomerValidate
 * @package app\admin\validate
 */
class ErpGuideBookValidate extends Validate{

    protected $rule = [
        'id' 			=> 'require',
        'code' 			=> 'require|max:100|unique:app\common\model\ErpGuideBook',
        'name' 			=> 'require|max:100',
        'status' 		=> 'require|in:0,1',
		'ids' 			=> 'require|array',
    ];

    protected $message = [
        'id.require' 			=> '参数缺失',
        'code.require' 			=> '编码不能为空',
        'code.max' 				=> '编码最多100位字符',
        'name.require' 			=> '名称不能为空',
        'name.max' 				=> '名称最多100位字符',
		'ids.require'			=> '请选择数据',
		'ids.array'				=> '请选择数据',
    ];

    public function sceneAdd(){
		return $this->only(['code','name','status']);
    }

    public function sceneEdit(){
        return $this->only(['id','code','name','status']);
    }

    public function sceneRemove(){
		return $this->only(['ids']);
    }
	
    public function sceneRecycle(){
		return $this->only(['ids']);
    }

}