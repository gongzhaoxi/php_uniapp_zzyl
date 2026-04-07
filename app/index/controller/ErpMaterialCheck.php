<?php
declare (strict_types = 1);
namespace app\index\controller;
use app\admin\logic\{ErpMaterialLogic,ErpMaterialTreeLogic,ErpMaterialWarehouseLogic,ErpWarehouseLogic,ErpMaterialCheckLogic};
use app\common\enum\{ErpMaterialEnum,ErpMaterialStockEnum};

class ErpMaterialCheck extends Base
{

    // 零件库存列表
    public function index(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialWarehouseLogic::getList(array_merge($this->request->param(),['type'=>ErpMaterialEnum::PARTN,'warehouse_type'=>'1']),$this->request->param('limit')));
        }
		return $this->fetch('',['warehouse'=>ErpWarehouseLogic::getAll(['type'=>'1']),'tree' => ErpMaterialTreeLogic::tree(ErpMaterialEnum::PARTN,false),'material_type'=>ErpMaterialEnum::PARTN,'category'=>ErpMaterialLogic::getCategory('material_partn')]);
    }

    // 添加
    public function add(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpMaterialCheckLogic::goAdd($this->request->only(['material_type','type','order_id','remark','material','stock_date','username'=>$this->request->userInfo['name']])));
        }
		return $this->fetch('admin@erp/material_check/add',['m_data'=>ErpMaterialWarehouseLogic::getList(array_merge($this->request->param(),['type'=>ErpMaterialEnum::PARTN]),$this->request->param('limit'))['data'],'material_type'=>$this->request->param('material_type',1),'type'=>ErpMaterialStockEnum::getCheckType(),'warehouse'=>ErpWarehouseLogic::getAll(['type'=>'1,2'])]);
    }
	
    // 编辑
    public function edit(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpMaterialCheckLogic::goEdit($this->request->only(['id','type','order_id','remark','material','stock_date']))); 
        }
        return $this->fetch('admin@erp/material_check/edit',['type'=>ErpMaterialStockEnum::getCheckType(),'warehouse'=>ErpWarehouseLogic::getAll(['type'=>'1,2']),'model' => ErpMaterialCheckLogic::getOne($this->request->param('id/d'),true)]);
    }
}
