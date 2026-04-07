<?php
namespace app\admin\validate;
use think\Validate;
use app\common\model\ArticleCategory;

/**
 * 文章验证
 * Class ArticleValidate
 * @package app\admin\validate
 */
class ArticleValidate extends Validate{

    protected $rule = [
        'id' 		=> 'require',
        'title' 	=> 'require|length:1,255',
        'cid' 		=> 'require|checkCid',
        'status' 	=> 'require|in:0,1',
		'ids' 		=> 'require|array',
    ];

    protected $message = [
        'id.require' 		=> '参数缺失',
        'title.require' 	=> '请填写文章标题',
        'title.length' 		=> '文章标题长度须在1-255位字符',
        'cid.require'		=> '文章分类缺失',
        'status.require' 	=> '请选择数据状态',
        'status.in' 		=> '数据状态参数错误',
		'ids.require'		=> '请选择数据',
		'ids.array'			=> '请选择数据',
    ];

    public function sceneAdd(){
		return $this->only(['cid','title','status']);
    }

    public function sceneEdit(){
        return $this->only(['id','cid','title','status']);
    }

    protected function checkCid($value){
        if(ArticleCategory::where('id',$value)->count() == 0) {
			return '文章分类不存在';
        }
		return true;
    }

    public function sceneRemove(){
		return $this->only(['ids']);
    }
	
    public function sceneRecycle(){
		return $this->only(['ids']);
    }

}