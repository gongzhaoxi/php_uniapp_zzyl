<?php
declare (strict_types = 1);
namespace app\admin\controller\dict;
use app\admin\logic\DictDataLogic;

class Data extends \app\admin\controller\Base
{

    // 列表
    public function list(){
        if ($this->request->isAjax()) {
			return $this->getJson(DictDataLogic::getList($this->request->param(),$this->request->param('limit')));
        }
    }

    // 添加
    public function add(){
        if ($this->request->isAjax()) {
            return $this->getJson(DictDataLogic::goAdd($this->request->post()));
        }
		return $this->fetch('',['type_id'=>$this->request->param('type_id/d')]);
    }

    // 编辑
    public function edit(){
        if ($this->request->isAjax()) {
            return $this->getJson(DictDataLogic::goEdit($this->request->param())); 
        }
        return $this->fetch('',['model' => DictDataLogic::goFind($this->request->param('id/d'))]);
    }

    // 删除
    public function remove($id){
        return $this->getJson(DictDataLogic::goRemove($id));
    }

    // 批量删除
    public function batchRemove(){
        return $this->getJson(DictDataLogic::goBatchRemove($this->request->post('ids')));
    }	

    // 回收站
    public function recycle(){
        if ($this->request->isAjax()) {
            return $this->getJson(DictDataLogic::getRecycle($this->request->param(),$this->request->param('limit')));
        }
		return $this->fetch('',['type_id'=>$this->request->param('type_id/d')]);
    }
	
	public function batchRecycle(){
        return $this->getJson(DictDataLogic::goRecycle($this->request->param('ids'),$this->request->param('type')));
    }
	
	public function import(){
        if($this->request->isPost()){
			return $this->getJson(DictDataLogic::goImport($this->request->param('type_id/d'),$this->request->param('excel')));
        }else{
			return $this->fetch('',['type_id'=>$this->request->param('type_id/d')]);
		}
    }
}
