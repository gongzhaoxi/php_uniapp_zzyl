<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\ErpWarehouseLogic;
use app\common\enum\ErpWarehouseEnum;

class Warehouse extends \app\admin\controller\Base
{

    // 列表
    public function list(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpWarehouseLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',[]);
    }

    // 添加
    public function add(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpWarehouseLogic::goAdd($this->request->param()));
        }
        return $this->fetch('',['type'=>ErpWarehouseEnum::getTypeDesc()]);
    }

    // 编辑
    public function edit(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpWarehouseLogic::goEdit($this->request->param())); 
        }
        return $this->fetch('',['type'=>ErpWarehouseEnum::getTypeDesc(),'model' => ErpWarehouseLogic::getOne($this->request->param('id/d'))]);
    }

    // 删除
    public function remove(){
        return $this->getJson(ErpWarehouseLogic::goRemove($this->request->only(['ids'])));
    }	

    // 回收站
    public function recycle(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpWarehouseLogic::getRecycle($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch();
    }
	
	// 恢复/删除回收站
	public function batchRecycle(){
		return $this->getJson(ErpWarehouseLogic::goRecycle($this->request->param('ids'),$this->request->param('type')));
    }
    
}
