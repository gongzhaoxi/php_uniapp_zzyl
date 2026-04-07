<?php
namespace app\common\model;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

/**
 * 资讯分类管理模型
 * Class ArticleCategory
 * @package app\common\model\ArticleCategory;
 */
class ArticleCategory extends BaseModel
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
	protected $autoWriteTimestamp = 'int';
	protected $dateFormat = 'Y-m-d H:i:s';

    public function searchQueryAttr($query, $value, $data)
    {
        if (!empty($value['name'])) {
            $query->where('name', 'like', '%' . $value['name'] . '%');
        }
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($value['sort'],$value['order']);
		}
    }

    public function article()
    {
        return $this->hasMany(Article::class, 'cid', 'id');
    }

    public function getStatusDescAttr($value, $data)
    {
        return $data['status'] ? '启用' : '停用';
    }

	public static function onAfterUpdate($record){
		cache('article_category',null);
	}

	public static function onAfterInsert($record){
		cache('article_category',null);
	}

    public static function onAfterDelete($record){
		cache('article_category',null);
    }	

	public function getImageLinkAttr($value,$data)
    {
        return get_browse_url($data['image']);
    }
}