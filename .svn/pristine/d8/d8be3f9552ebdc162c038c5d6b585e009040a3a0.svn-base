<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\ErpUserLogic;
use app\admin\logic\ErpWarehouseLogic;

class User extends  \app\admin\controller\Base{

    // 列表
    public function list(){
        if($this->request->isAjax()) {
			return $this->getJson(ErpUserLogic::getList($this->request->only(['keyword','channel','create_time']),$this->request->param('limit')));
        }
        return $this->fetch('');
    }

    // 添加
    public function add(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpUserLogic::goAdd($this->request->only(['permission','name','title','sn','status','mobile','warehouse_id'])));
        }
        return $this->fetch('',['user_permission'=>config('project.user_permission'),'warehouse'=>ErpWarehouseLogic::getAll(['type'=>3])]);
    }

    // 编辑
    public function edit(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpUserLogic::goEdit($this->request->only(['id','permission','name','title','sn','status','mobile','warehouse_id'])));
        }
        return $this->fetch('',['user_permission'=>config('project.user_permission'),'warehouse'=>ErpWarehouseLogic::getAll(['type'=>3]),'model' => ErpUserLogic::getOne($this->request->param('id/d'))]);
    }

    // 删除
    public function remove(){
		return $this->getJson(ErpUserLogic::goRemove($this->request->post('ids')));
    }

    // 回收站
    public function recycle(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpUserLogic::getRecycle($this->request->only(['keyword','channel','create_time']),$this->request->param('limit')));
        }
        return $this->fetch();
    }

	//恢复/删除回收站
    public function batchRecycle(){
		return $this->getJson(ErpUserLogic::batchRecycle($this->request->param('ids'),$this->request->param('type')));
    }

}
