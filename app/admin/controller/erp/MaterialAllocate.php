<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\{ErpMaterialAllocateLogic,ErpWarehouseLogic,ErpMaterialWarehouseLogic};
use app\common\enum\{ErpMaterialStockEnum,ErpMaterialAllocateMaterialEnum};
use app\common\model\AdminAdmin;
class MaterialAllocate extends \app\admin\controller\Base
{

    // 列表
    public function list(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialAllocateLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['material_status'=>ErpMaterialAllocateMaterialEnum::getStatusDesc(),'returned_status'=>ErpMaterialAllocateMaterialEnum::getReturnedStatusDesc(),'status'=>ErpMaterialStockEnum::getStatusDesc(),'default_status'=>ErpMaterialStockEnum::STATUS_HANDLE]);
    }

    // 添加
    public function add(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpMaterialAllocateLogic::goAdd($this->request->only(['type','order_id','remark','material','stock_date'])));
        }
		return $this->fetch('',['material_id'=>$this->request->param('material_id'),'warehouse'=>ErpWarehouseLogic::getAll(['type'=>'3']),'material_warehouse'=>ErpMaterialWarehouseLogic::getList(['ids'=>$this->request->param('material_warehouse_id',-1)],100)['data']]);
    }

    //调拨单物料列表
    public function material(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialAllocateLogic::getMaterial($this->request->param(),$this->request->param('limit')));
        }else{
			return $this->fetch('',['admins'=>AdminAdmin::where('status',1)->field('id,nickname')->select(),'warehouse'=>ErpWarehouseLogic::getAll(['type'=>'1']),'workstation'=>ErpWarehouseLogic::getAll(['type'=>'3']),'material_status'=>ErpMaterialAllocateMaterialEnum::getStatusDesc(),'send_status'=>ErpMaterialAllocateMaterialEnum::getSendStatusDesc(),'status'=>ErpMaterialStockEnum::getStatusDesc(),'default_status'=>ErpMaterialStockEnum::STATUS_HANDLE]);
		}
    }

    // 未签收退库
    public function returned(){
        return $this->getJson(ErpMaterialAllocateLogic::goReturned($this->request->param('id'),$this->request->param('ids')));
    }	
	
    // 已发出
    public function send(){
        return $this->getJson(ErpMaterialAllocateLogic::goSend($this->request->param('id'),$this->request->param('ids')));
    }	

    // 作废
    public function cancel(){
        return $this->getJson(ErpMaterialAllocateLogic::goCancel($this->request->param('id'),$this->request->param('ids')));
    }

    // 编辑
    public function edit(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpMaterialAllocateLogic::goEdit($this->request->only(['id','type','order_id','remark','material','stock_date']))); 
        }
        return $this->fetch('',['warehouse'=>ErpWarehouseLogic::getAll(['type'=>'3']),'model' => ErpMaterialAllocateLogic::getOne($this->request->param('id/d'))]);
    }	
	
	// 删除物料
    public function removeMaterial(){
		return $this->getJson(ErpMaterialAllocateLogic::goRemoveMaterial($this->request->param('id')));
    }
	
	
	public function report(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialAllocateLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['material_status'=>ErpMaterialAllocateMaterialEnum::getStatusDesc(),'returned_status'=>ErpMaterialAllocateMaterialEnum::getReturnedStatusDesc(),'status'=>ErpMaterialStockEnum::getStatusDesc(),'default_status'=>ErpMaterialStockEnum::STATUS_HANDLE]);
    }
}
