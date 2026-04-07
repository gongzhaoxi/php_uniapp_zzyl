<?php
declare (strict_types = 1);
namespace app\admin\validate;
use think\Validate;

class ErpMaterialTreeValidate extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名'	=>	['规则1','规则2'...]
     *
     * @var array
     */	
    protected $rule = [
		'title|名称'	=> 'require|max:50|unique:app\common\model\ErpMaterialTree,title^type',
        'sort|排序' 	=> 'require|between:1,99',
		'id|id' 		=> 'require',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [];
	
	/**
     * 添加
     */
    public function sceneAdd(){
        return $this->only(['title','sort']);
    }

    /**
     * 编辑
     */
    public function sceneEdit()
    {
        return $this->only(['id','title','sort']);
    }
	
}