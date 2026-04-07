<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\{ErpSupplierProcessBomLogic,ErpSupplierProcessLogic};
use app\common\util\Excel;

class SupplierProcessBom extends \app\admin\controller\Base
{
    public function list(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpSupplierProcessBomLogic::getList($this->request->param(),$this->request->param('limit')));
        }
		return $this->fetch('',['process'=>ErpSupplierProcessLogic::getAll()]);
    }

    // 添加
    public function add(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpSupplierProcessBomLogic::goAdd($this->request->param()));
        }
        return $this->fetch('',['process'=>ErpSupplierProcessLogic::getAll()]);
    }

    // 编辑
    public function edit(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpSupplierProcessBomLogic::goEdit($this->request->param())); 
        }
        return $this->fetch('',['process'=>ErpSupplierProcessLogic::getAll(),'model' => ErpSupplierProcessBomLogic::getOne($this->request->param('id/d'))]);
    }

    // 删除
    public function remove(){
        return $this->getJson(ErpSupplierProcessBomLogic::goRemove($this->request->only(['ids'])));
    }

	// 导入
    public function import(){
        if($this->request->isPost()){
			return $this->getJson(ErpSupplierProcessBomLogic::goImport($this->request->param('excel')));
        }else{
			return $this->fetch('');
		}
    }
}
