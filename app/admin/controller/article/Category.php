<?php
declare (strict_types = 1);
namespace app\admin\controller\article;
use app\admin\logic\ArticleCategoryLogic;

class Category extends \app\admin\controller\Base
{

    // 列表
    public function list(){
        if ($this->request->isAjax()) {
			return $this->getJson(ArticleCategoryLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch();
    }

    // 添加
    public function add(){
        if ($this->request->isAjax()) {
            return $this->getJson(ArticleCategoryLogic::goAdd($this->request->param()));
        }
        return $this->fetch();
    }

    // 编辑
    public function edit(){
        if ($this->request->isAjax()) {
            return $this->getJson(ArticleCategoryLogic::goEdit($this->request->param())); 
        }
        return $this->fetch('',['model' => ArticleCategoryLogic::goFind($this->request->param('id/d'))]);
    }

    // 删除
    public function remove(){
        return $this->getJson(ArticleCategoryLogic::goRemove($this->request->only(['ids'])));
    }	

    // 回收站
    public function recycle(){
        if ($this->request->isAjax()) {
            return $this->getJson(ArticleCategoryLogic::getRecycle($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch();
    }
	
	// 恢复/删除回收站
	public function batchRecycle(){
		return $this->getJson(ArticleCategoryLogic::goRecycle($this->request->param('ids'),$this->request->param('type')));
    }
    
}
