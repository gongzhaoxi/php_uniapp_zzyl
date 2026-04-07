<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\{ErpSupplierProcessLogic,ErpSupplierLogic};
use app\common\enum\RegionTypeEnum;

class SupplierProcess extends \app\admin\controller\Base
{

    // 列表
    public function list(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpSupplierProcessLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['supplier'=>ErpSupplierLogic::getAll()]);
    }

    // 添加
    public function add(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpSupplierProcessLogic::goAdd($this->request->param()));
        }
        return $this->fetch('',['supplier'=>ErpSupplierLogic::getAll()]);
    }

    // 编辑
    public function edit(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpSupplierProcessLogic::goEdit($this->request->param())); 
        }
        return $this->fetch('',['supplier'=>ErpSupplierLogic::getAll(),'model' => ErpSupplierProcessLogic::getOne($this->request->param('id/d'))]);
    }

    // 删除
    public function remove(){
        return $this->getJson(ErpSupplierProcessLogic::goRemove($this->request->only(['ids'])));
    }	
    
}
