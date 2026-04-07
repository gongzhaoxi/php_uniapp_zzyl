<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\{ErpMaterialCheckLogic,ErpWarehouseLogic};
use app\common\enum\ErpMaterialStockEnum;
use app\admin\logic\ErpMaterialLogic;
use app\common\enum\{ErpMaterialEnum,ErpMaterialCheckMaterialEnum};

class MaterialCheck extends \app\admin\controller\Base
{

    // 列表
    public function list(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialCheckLogic::getList($this->request->param(),$this->request->param('limit')));
        }
		$material_type 	= $this->request->param('material_type');
		$category 		= [];
		if($material_type == ErpMaterialEnum::PARTN){
			$category 	= ErpMaterialLogic::getCategory('material_partn');
		}else if($material_type == ErpMaterialEnum::COMPONENT){
			$category 	= ErpMaterialLogic::getCategory('material_component');
		}
        return $this->fetch('',['material_type'=>$material_type,'category'=>$category,'type'=>ErpMaterialStockEnum::getCheckType(),'material_status'=>ErpMaterialCheckMaterialEnum::getStatusDesc(),'status'=>ErpMaterialStockEnum::getStatusDesc(),'default_status'=>ErpMaterialStockEnum::STATUS_HANDLE]);
    }

    // 添加
    public function add(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpMaterialCheckLogic::goAdd($this->request->only(['material_type','type','order_id','remark','material','stock_date'])));
        }
		return $this->fetch('',['stock_after'=>$this->request->param('stock_after'),'material_id'=>$this->request->param('material_id'),'warehouse_id'=>$this->request->param('warehouse_id'),'material_type'=>$this->request->param('material_type'),'type'=>ErpMaterialStockEnum::getCheckType(),'warehouse'=>ErpWarehouseLogic::getAll()]);
    }

    // 编辑
    public function edit(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpMaterialCheckLogic::goEdit($this->request->only(['id','type','order_id','remark','material','stock_date']))); 
        }
        return $this->fetch('',['type'=>ErpMaterialStockEnum::getCheckType(),'warehouse'=>ErpWarehouseLogic::getAll(),'model' => ErpMaterialCheckLogic::getOne($this->request->param('id/d'),true)]);
    }

    // 物料列表
    public function material(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialCheckLogic::getMaterial($this->request->param(),$this->request->param('limit')));
        }
    }

    // 确认保存并校正库存
    public function confirm(){
        return $this->getJson(ErpMaterialCheckLogic::goConfirm($this->request->param('id'),$this->request->param('ids'),$this->request->param('num')));
    }	

    // 作废
    public function cancel(){
        return $this->getJson(ErpMaterialCheckLogic::goCancel($this->request->param('id'),$this->request->param('ids')));
    }	
	
	// 结算
    public function settle(){
        return $this->getJson(ErpMaterialCheckLogic::goSettle($this->request->param('id')));
    }	  
}
