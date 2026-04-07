<?php
declare (strict_types = 1);
namespace app\admin\controller\article;
use app\admin\logic\ArticleLogic;

class Article extends \app\admin\controller\Base
{

    // 列表
    public function list(){
        if ($this->request->isAjax()) {
			return $this->getJson(ArticleLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['category'=>get_article_category()]);
    }

    // 添加
    public function add(){
        if ($this->request->isAjax()) {
            return $this->getJson(ArticleLogic::goAdd($this->request->param()));
        }
        return $this->fetch('',['category'=>get_article_category()]);
    }

    // 编辑
    public function edit(){
        if ($this->request->isAjax()) {
            return $this->getJson(ArticleLogic::goEdit($this->request->param())); 
        }
        return $this->fetch('',['category'=>get_article_category(),'model' => ArticleLogic::goFind($this->request->param('id/d'))]);
    }

    // 删除
    public function remove(){
        return $this->getJson(ArticleLogic::goRemove($this->request->only(['ids'])));
    }	

    // 回收站
    public function recycle(){
        if ($this->request->isAjax()) {
            return $this->getJson(ArticleLogic::getRecycle($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch();
    }
	
	// 恢复/删除回收站
	public function batchRecycle(){
		return $this->getJson(ArticleLogic::goRecycle($this->request->param('ids'),$this->request->param('type')));
    }
    
}
