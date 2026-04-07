<?php
namespace app\admin\validate;
use app\common\model\DictType;
use think\Validate;
use app\common\model\Article;
use app\common\model\ArticleCategory;

/**
 * 文章分类验证
 * Class ArticleCategoryValidate
 * @package app\admin\validate
 */
class ArticleCategoryValidate extends Validate{
    
    protected $rule = [
        'id' 		=> 'require',
        'name' 		=> 'require|length:1,90',
        'status' 	=> 'require|in:0,1',
		'ids' 		=> 'require|array',
    ];

    protected $message = [
        'id.require' 	=> '参数缺失',
        'name.require' 	=> '请填写分类名称',
        'name.length' 	=> '分类长度须在1~90位字符',
        'status.require'=> '请选择状态',
		'status.in'		=> '状态错误',
		'ids.require'	=> '请选择数据',
		'ids.array'		=> '请选择数据',
    ];

    public function sceneAdd(){
        return $this->only(['name','status']);
    }
    
    public function sceneEdit(){
		return $this->only(['id','name','status']);
    }
	
    public function sceneRemove(){
		return $this->only(['ids'])->append('ids','checkRemove');
    }
	
    protected function checkRemove($value){
        if(Article::whereIn('cid',$value)->count() > 0) {
			return ArticleCategory::whereIn('id',$value)->value('name').':存在文章,请先删除';
        }
		return true;
    }

    public function sceneRecycle(){
		return $this->only(['ids']);
    }	
}