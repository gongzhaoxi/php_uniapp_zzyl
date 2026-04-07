<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\ErpMaterialOutLogic;
use app\common\enum\ErpMaterialStockEnum;
use app\admin\logic\ErpMaterialLogic;
use app\common\enum\{ErpMaterialEnum,ErpMaterialOutMaterialEnum};

class MaterialOut extends \app\admin\controller\Base
{

    // 列表
    public function list(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialOutLogic::getList($this->request->param(),$this->request->param('limit')));
        }
		$material_type 	= $this->request->param('material_type');
		$category 		= [];
		if($material_type == ErpMaterialEnum::PARTN){
			$category 	= ErpMaterialLogic::getCategory('material_partn');
		}else if($material_type == ErpMaterialEnum::COMPONENT){
			$category 	= ErpMaterialLogic::getCategory('material_component');
		}
        return $this->fetch('',['material_produce_type'=>get_dict_data('material_produce_type'),'material_status'=>ErpMaterialOutMaterialEnum::getStatusDesc(),'material_type'=>$material_type,'category'=>$category,'type'=>ErpMaterialStockEnum::getOutType(),'status'=>ErpMaterialStockEnum::getStatusDesc(),'default_status'=>ErpMaterialStockEnum::STATUS_HANDLE]);
    }

    // 添加
    public function add(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpMaterialOutLogic::goAdd($this->request->only(['material_type','type','order_id','remark','material','stock_date','department','batch_number'])));
        }
		return $this->fetch('',['hidden_key'=>ErpMaterialStockEnum::getIgnoreType(),'department'=>get_dict_data('material_receiving_department'),'material_type'=>$this->request->param('material_type'),'type'=>ErpMaterialStockEnum::getOutType()]);
    }

    // 编辑
    public function edit(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpMaterialOutLogic::goEdit($this->request->only(['id','type','order_id','remark','material','stock_date','department','batch_number']))); 
        }
        return $this->fetch('',['hidden_key'=>ErpMaterialStockEnum::getIgnoreType(),'department'=>get_dict_data('material_receiving_department'),'type'=>ErpMaterialStockEnum::getOutType(),'model' => ErpMaterialOutLogic::getOne($this->request->param('id/d'))]);
    }

    // 出库单物料列表
    public function material(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialOutLogic::getMaterial($this->request->param(),$this->request->param('limit')));
        }
    }

    // 确认保存并扣减库存
    public function confirm(){
        return $this->getJson(ErpMaterialOutLogic::goConfirm($this->request->param('id'),$this->request->param('ids'),$this->request->param('num')));
    }	

    // 作废
    public function cancel(){
        return $this->getJson(ErpMaterialOutLogic::goCancel($this->request->param('id'),$this->request->param('ids')));
    }	
	
	// 结算
    public function settle(){
        return $this->getJson(ErpMaterialOutLogic::goSettle($this->request->param('id')));
    }

	// 删除物料
    public function removeMaterial(){
		return $this->getJson(ErpMaterialOutLogic::goRemoveMaterial($this->request->param('id')));
    }
	
	// 报表
    public function report(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialOutLogic::getList($this->request->param(),$this->request->param('limit')));
        }
		$material_type 	= 1;
		$category 		= [];
		if($material_type == ErpMaterialEnum::PARTN){
			$category 	= ErpMaterialLogic::getCategory('material_partn');
		}else if($material_type == ErpMaterialEnum::COMPONENT){
			$category 	= ErpMaterialLogic::getCategory('material_component');
		}
        return $this->fetch('',['material_produce_type'=>get_dict_data('material_produce_type'),'material_status'=>ErpMaterialOutMaterialEnum::getStatusDesc(),'material_type'=>$material_type,'category'=>$category,'type'=>ErpMaterialStockEnum::getOutType(),'status'=>ErpMaterialStockEnum::getStatusDesc(),'default_status'=>ErpMaterialStockEnum::STATUS_HANDLE]);
    }
}
