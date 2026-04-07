<?php
declare (strict_types = 1);
namespace app\admin\controller\dict;
use app\admin\logic\DictTypeLogic;

class Type extends \app\admin\controller\Base
{

    // 列表
    public function list(){
        if ($this->request->isAjax()) {
			return $this->getJson(DictTypeLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch();
    }

    // 添加
    public function add(){
        if ($this->request->isAjax()) {
            return $this->getJson(DictTypeLogic::goAdd($this->request->param()));
        }
        return $this->fetch();
    }

    // 编辑
    public function edit(){
        if ($this->request->isAjax()) {
            return $this->getJson(DictTypeLogic::goEdit($this->request->param())); 
        }
        return $this->fetch('',['model' => DictTypeLogic::goFind($this->request->param('id/d'))]);
    }

    // 删除
    public function remove($id){
        return $this->getJson(DictTypeLogic::goRemove($id));
    }
	
    // 批量删除
    public function batchRemove(){
        return $this->getJson(DictTypeLogic::goBatchRemove($this->request->post('ids')));
    }	

    // 回收站
    public function recycle(){
        if ($this->request->isAjax()) {
            return $this->getJson(DictTypeLogic::getRecycle($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch();
    }
	
	public function batchRecycle(){
		return $this->getJson(DictTypeLogic::goRecycle($this->request->param('ids'),$this->request->param('type')));
    }
    
}
