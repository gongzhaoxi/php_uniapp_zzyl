<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\{ErpDrawingLogic,ErpMaterialTreeLogic};

class Drawing extends \app\admin\controller\Base
{

    // 列表
    public function list(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpDrawingLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('list',['tree' => ErpMaterialTreeLogic::tree(0,false),]);
    }

    // 添加
    public function add(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpDrawingLogic::goAdd($this->request->param()));
        }
		
        return $this->fetch('',[]);
    }

    // 编辑
    public function edit(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpDrawingLogic::goEdit($this->request->param())); 
        }
        return $this->fetch('',['model' => ErpDrawingLogic::getOne($this->request->param('id/d'))]);
    }

    // 删除
    public function remove(){
        return $this->getJson(ErpDrawingLogic::goRemove($this->request->only(['ids'])));
    }	
	
	// 导入
	public function import(){
        if($this->request->isPost()){
			return $this->getJson(ErpDrawingLogic::goImport($this->request->param('excel')));
        }else{
			return $this->fetch('');
		}
    }
	
    // 初审
    public function firstCheck(){
		return $this->getJson(ErpDrawingLogic::goFirstCheck($this->request->param('id')));
    }	
	
	// 终审
    public function finalCheck(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpDrawingLogic::goFinalCheck($this->request->only(['id','final_pic']))); 
        }
        return $this->fetch('',['model' => ErpDrawingLogic::getOne($this->request->param('id/d'))]);
    }
}
