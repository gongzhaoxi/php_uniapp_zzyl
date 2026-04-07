<?php
namespace app\common\model;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

/**
 * 资讯管理模型
 * Class Article
 * @package app\common\model;
 */
class Article extends BaseModel
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';

	public function category(){
		return $this->belongsTo('app\common\model\ArticleCategory','cid','id');
	}

    public function getCategoryNameAttr($value, $data)
    {
		$category = get_article_category($data['cid']);
		return $category?$category['name']:'';
    }

    public function getClickAttr($value, $data)
    {
        return $data['click_actual'] + $data['click_virtual'];
    }

    public function getContentAttr($value, $data)
    {
        return get_file_domain($value);
    }

    public function setContentAttr($value, $data)
    {
        return clear_file_domain($value);
    }
	
    public function getStatusDescAttr($value, $data)
    {
        return $data['status'] ? '显示' : '隐藏';
    }	
	
	public function searchQueryAttr($query, $value, $data)
    {
        if (!empty($value['title'])) {
            $query->where('title', 'like', '%' . $value['title'] . '%');
        }
        if (isset($value['status']) && $value['status'] !== '') {
            $query->where('status', '=', $value['status']);
        }
        if (!empty($value['cid'])) {
			$query->where('cid', '=', $value['cid']);
        }		
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($value['sort'],$value['order']);
		}
    }
	
	public function getImageLinkAttr($value,$data)
    {
        return get_browse_url($data['image']);
    }

}