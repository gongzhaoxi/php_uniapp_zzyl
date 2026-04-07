<?php
namespace app\admin\validate;
use think\Validate;

/**
 * 图纸验证
 * Class CustomerValidate
 * @package app\admin\validate
 */
class ErpDrawingValidate extends Validate{

    protected $rule = [
        'id' 			=> 'require',
        'sn' 			=> 'require|max:255',
        'pic' 			=> 'require',
        'status' 		=> 'require|in:0,1',
		'final_pic' 	=> 'require',
		'ids' 			=> 'require|array',
    ];

    protected $message = [
        'id.require' 			=> '参数缺失',
        'sn.require' 			=> '物料编码不能为空',
        'sn.max' 				=> '物料编码最多255位字符',
        'pic.require' 			=> '初始设计图纸不能为空',
		'final_pic.require' 	=> '终审图纸不能为空',
		'ids.require'			=> '请选择数据',
		'ids.array'				=> '请选择数据',
    ];

    public function sceneAdd(){
		return $this->only(['sn','pic','status']);
    }

    public function sceneEdit(){
        return $this->only(['id','sn','pic','status']);
    }


    public function sceneFinalCheck(){
        return $this->only(['id','final_pic']);
    }

    public function sceneRemove(){
		return $this->only(['ids']);
    }
	
    public function sceneRecycle(){
		return $this->only(['ids']);
    }

}