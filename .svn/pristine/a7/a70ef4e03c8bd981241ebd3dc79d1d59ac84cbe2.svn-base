<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\{ErpMaterialEnterLogic,ErpSupplierLogic,ErpWarehouseLogic};
use app\common\enum\ErpMaterialStockEnum;
use app\admin\logic\ErpMaterialLogic;
use app\common\enum\ErpMaterialEnum;
use app\common\enum\ErpMaterialEnterMaterialEnum;
use app\common\model\{ErpMaterial,ErpMaterialEnterMaterial};
class MaterialEnter extends \app\admin\controller\Base
{

    // 列表
    public function list(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialEnterLogic::getList($this->request->param(),$this->request->param('limit')));
        }
		$material_type 	= $this->request->param('material_type');
		$category 		= [];
		if($material_type == ErpMaterialEnum::PARTN){
			$category 	= ErpMaterialLogic::getCategory('material_partn');
		}else if($material_type == ErpMaterialEnum::COMPONENT){
			$category 	= ErpMaterialLogic::getCategory('material_component');
		}
        return $this->fetch('',['supplier'=>ErpSupplierLogic::getAll(),'material_status'=>ErpMaterialEnterMaterialEnum::getStatusDesc(),'material_type'=>$material_type,'category'=>$category,'type'=>ErpMaterialStockEnum::getEnterType(),'status'=>ErpMaterialStockEnum::getStatusDesc(),'default_status'=>ErpMaterialStockEnum::STATUS_HANDLE]);
    }

    // 添加
    public function add(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialEnterLogic::goAdd($this->request->only(['purchase_order','supplier_id','batch_number','material_type','type','order_id','remark','material','stock_date']),$this->request->param('check_status/d',1)));
        }
		return $this->fetch('',['hidden_key'=>ErpMaterialStockEnum::getIgnoreType(),'material_type'=>$this->request->param('material_type'),'type'=>ErpMaterialStockEnum::getEnterType(),'supplier'=>ErpSupplierLogic::getAll(),'warehouse'=>ErpWarehouseLogic::getAll(['type'=>$this->request->param('material_type')])]);
    }

    // 编辑
    public function edit(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpMaterialEnterLogic::goEdit($this->request->only(['id','purchase_order','supplier_id','batch_number','type','order_id','remark','material','stock_date']),$this->request->param('check_status/d',1))); 
        }
        return $this->fetch('',['hidden_key'=>ErpMaterialStockEnum::getIgnoreType(),'type'=>ErpMaterialStockEnum::getEnterType(),'supplier'=>ErpSupplierLogic::getAll(),'warehouse'=>ErpWarehouseLogic::getAll(),'model' => ErpMaterialEnterLogic::getOne($this->request->param('id/d'))]);
    }

    // 入库单物料列表
    public function material(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialEnterLogic::getMaterial($this->request->param(),$this->request->param('limit')));
        }
    }
	
	// 品检
    public function check(){
        return $this->getJson(ErpMaterialEnterLogic::goCheck($this->request->param('id'),$this->request->param('ids'),$this->request->param('num'),$this->request->param('defective')));
    }		
	
	// 确认保存并扣减库存
    public function confirm(){
        return $this->getJson(ErpMaterialEnterLogic::goConfirm($this->request->param('id'),$this->request->param('ids')));
    }	

    // 作废
    public function cancel(){
        return $this->getJson(ErpMaterialEnterLogic::goCancel($this->request->param('id'),$this->request->param('ids')));
    }	
	
	// 结算
    public function settle(){
        return $this->getJson(ErpMaterialEnterLogic::goSettle($this->request->param('id')));
    }	
	
	// 退货
    public function refund(){
        return $this->getJson(ErpMaterialEnterLogic::goRefund($this->request->param('id'),$this->request->param('ids')));
    }
	
	// 发起质检
    public function notice(){
		if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialEnterLogic::goNotice($this->request->param('ids'),$this->request->only(['ids','consignee','receiving_date','inspection_by','inspection_date','material_code'])));
		}else{
			return $this->fetch('',['material_code'=>ErpMaterial::where('id','in',ErpMaterialEnterMaterial::where('id','in',$this->request->param('ids'))->column('material_id'))->where('tag','<>','')->value('tag'),'ids'=>$this->request->param('ids'),'receiving_date'=>$this->request->param('receiving_date')]);
		}
    }	

	// 优先质检
    public function prior(){
        return $this->getJson(ErpMaterialEnterLogic::goPrior($this->request->param('id')));
    }	
	
	
    // 车间退回仓库
    public function back(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialEnterLogic::getBack($this->request->param(),$this->request->param('limit')));
        }
		$material_type 	= $this->request->param('material_type');
		$category 		= [];
		if($material_type == ErpMaterialEnum::PARTN){
			$category 	= ErpMaterialLogic::getCategory('material_partn');
		}else if($material_type == ErpMaterialEnum::COMPONENT){
			$category 	= ErpMaterialLogic::getCategory('material_component');
		}
        return $this->fetch('',['supplier'=>ErpSupplierLogic::getAll(),'material_status'=>ErpMaterialEnterMaterialEnum::getStatusDesc(),'material_type'=>$material_type,'category'=>$category,'type'=>ErpMaterialStockEnum::getEnterType(),'status'=>ErpMaterialStockEnum::getStatusDesc(),'default_status'=>ErpMaterialStockEnum::STATUS_HANDLE]);
    }	
	
	

	// 重新发起质检
    public function reset(){
        return $this->getJson(ErpMaterialEnterLogic::goReset($this->request->param('id'),$this->request->param('ids')));
    }	
	
	// 移除物料
    public function delete(){
        return $this->getJson(ErpMaterialEnterLogic::goDelete($this->request->param('id'),$this->request->param('ids')));
    }	
	
	
    public function report(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialEnterLogic::getList($this->request->param(),$this->request->param('limit')));
        }
		$material_type 	= 1;
		$category 		= [];
		if($material_type == ErpMaterialEnum::PARTN){
			$category 	= ErpMaterialLogic::getCategory('material_partn');
		}else if($material_type == ErpMaterialEnum::COMPONENT){
			$category 	= ErpMaterialLogic::getCategory('material_component');
		}
        return $this->fetch('',['supplier'=>ErpSupplierLogic::getAll(),'material_status'=>ErpMaterialEnterMaterialEnum::getStatusDesc(),'material_type'=>$material_type,'category'=>$category,'type'=>ErpMaterialStockEnum::getEnterType(),'status'=>ErpMaterialStockEnum::getStatusDesc(),'default_status'=>ErpMaterialStockEnum::STATUS_HANDLE]);
    }	
	
	
    public function backReport(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialEnterLogic::getBack($this->request->param(),$this->request->param('limit')));
        }
		$material_type 	= 1;
		$category 		= [];
		if($material_type == ErpMaterialEnum::PARTN){
			$category 	= ErpMaterialLogic::getCategory('material_partn');
		}else if($material_type == ErpMaterialEnum::COMPONENT){
			$category 	= ErpMaterialLogic::getCategory('material_component');
		}
        return $this->fetch('',['supplier'=>ErpSupplierLogic::getAll(),'material_status'=>ErpMaterialEnterMaterialEnum::getStatusDesc(),'material_type'=>$material_type,'category'=>$category,'type'=>ErpMaterialStockEnum::getEnterType(),'status'=>ErpMaterialStockEnum::getStatusDesc(),'default_status'=>ErpMaterialStockEnum::STATUS_HANDLE]);
    }	
	
}
