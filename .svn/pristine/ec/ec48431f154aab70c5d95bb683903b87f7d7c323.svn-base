<?php
declare (strict_types = 1);
namespace app\index\controller;
use app\admin\logic\{ErpMaterialLogic,ErpMaterialTreeLogic,ErpSupplierLogic};
use app\common\enum\{ErpMaterialEnum};

class ErpMaterial extends Base
{

    // 列表
    public function index(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialLogic::getStockList(array_merge($this->request->param(),['type'=>ErpMaterialEnum::PARTN,'status'=>1]),$this->request->param('limit')));
        }
		return $this->fetch('',['supplier' => ErpSupplierLogic::getAll(),'tree' => ErpMaterialTreeLogic::tree(ErpMaterialEnum::PARTN,false),'material_type'=>ErpMaterialEnum::PARTN,'category'=>ErpMaterialLogic::getCategory('material_partn'),'bellow_safety_stock'=>ErpMaterialLogic::getStockCount(['stock_search'=>1,'type'=>ErpMaterialEnum::PARTN]),'bellow_min_stock'=>ErpMaterialLogic::getStockCount(['stock_search'=>2,'type'=>ErpMaterialEnum::PARTN])]);
    }
	
}
