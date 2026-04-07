<?php
namespace app\admin\validate;
use think\Validate;


class RegionValidate extends Validate{

    protected $rule = [
        'id|地区id' 	=> 'require|unique:app\common\model\Region',
		'parent_id|父级'=> 'require',
        'name|地区名称' => 'require|max:100',
		'ids' 			=> 'require|array',
    ];

    protected $message = [
		'ids.require'			=> '请选择数据',
		'ids.array'				=> '请选择数据',
    ];

    public function sceneAdd(){
		return $this->only(['id','parent_id','name']);
    }

    public function sceneEdit(){
		return $this->only(['id','parent_id','name']);
    }

    public function sceneRemove(){
		return $this->only(['ids']);
    }
	
    public function sceneRecycle(){
		return $this->only(['ids']);
    }

}