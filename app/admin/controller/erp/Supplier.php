<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\ErpSupplierLogic;

class Supplier extends  \app\admin\controller\Base{

    // 列表
    public function list(){
        if($this->request->isAjax()) {
			return $this->getJson(ErpSupplierLogic::getList($this->request->only(['name','create_time']),$this->request->param('limit')));
        }
        return $this->fetch('');
    }

    // 添加
    public function add(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpSupplierLogic::goAdd($this->request->only(['file','name','tel','contact','status','remark','address','code','is_survey','quality_date','contract_date','certificate','score'])));
        }
        return $this->fetch('',[]);
    }

    // 编辑
    public function edit(){
        if ($this->request->isAjax()) {
           return $this->getJson(ErpSupplierLogic::goEdit($this->request->only(['id','file','name','tel','contact','status','remark','address','code','is_survey','quality_date','contract_date','certificate','score'])));
        }
        return $this->fetch('',['model' => ErpSupplierLogic::getOne($this->request->param('id/d'))]);
    }

    // 删除
    public function remove(){
		return $this->getJson(ErpSupplierLogic::goRemove($this->request->post('ids')));
    }

    // 回收站
    public function recycle(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpSupplierLogic::getRecycle($this->request->only(['name','create_time']),$this->request->param('limit')));
        }
        return $this->fetch();
    }

	//恢复/删除回收站
    public function batchRecycle(){
		return $this->getJson(ErpSupplierLogic::batchRecycle($this->request->param('ids'),$this->request->param('type')));
    }

	// 导入
    public function import(){
        if($this->request->isPost()){
			return $this->getJson(ErpSupplierLogic::goImport($this->request->param('excel')));
        }else{
			return $this->fetch('');
		}
    }

}
