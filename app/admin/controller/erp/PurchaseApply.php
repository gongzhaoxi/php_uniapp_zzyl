<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\{ErpPurchaseApplyLogic,ErpSupplierLogic,ErpMaterialLogic,ErpSupplierProcessLogic,ErpOrderProduceLogic};
use app\common\enum\{ErpPurchaseApplyEnum,RegionTypeEnum};
use app\common\model\{ErpMaterial};

class PurchaseApply extends \app\admin\controller\Base
{

    // 物料申购列表
    public function material(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpPurchaseApplyLogic::getMaterial($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['supplier' => ErpSupplierLogic::getAll(['ids'=>ErpMaterial::whereNotNull('supplier_id')->group('supplier_id')->column('supplier_id')]),'partn'=>ErpMaterialLogic::getCategory('material_partn'),'component'=>ErpMaterialLogic::getCategory('material_component')]);
    }

    // 添加物料申购
    public function materialAdd(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpPurchaseApplyLogic::goMaterialAdd($this->request->only(['type'=>1,'supplier_id','apply_date','remark','material','username'=>session('admin.username'),'data_type'=>ErpPurchaseApplyEnum::DATA_TYPE_INPUT])));
        }
		return $this->fetch('',['supplier' => ErpSupplierLogic::getAll()]);
    }

    // 删除物料申购
    public function materialRemove(){
        return $this->getJson(ErpPurchaseApplyLogic::goRemove($this->request->only(['ids'])));
    }	
	
	// 成品申购列表
    public function product(){
		$query = $this->request->only(['customer_name'=>'','produce_sn'=>'','product'=>'','salesman_id'=>'','apply_date'=>'','delivery_date'=>'']);
		return $this->fetch('',['query'=>$query,'list'=>ErpPurchaseApplyLogic::getProduct($query,$this->request->param('limit',10)),'admins'=>ErpPurchaseApplyLogic::getAdmins()]);
    }


    // 物料委外申购
    public function outsourcing(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpPurchaseApplyLogic::getMaterial($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['supplier' => ErpSupplierLogic::getAll(['ids'=>ErpMaterial::whereNotNull('supplier_id')->group('supplier_id')->column('supplier_id')]),'partn'=>ErpMaterialLogic::getCategory('material_partn'),'component'=>ErpMaterialLogic::getCategory('material_component')]);
    }

    // 添加委外申购
    public function outsourcingAdd(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpPurchaseApplyLogic::goMaterialAdd($this->request->only(['type'=>3,'process_id','supplier_id','apply_date','remark','material','username'=>session('admin.username'),'data_type'=>ErpPurchaseApplyEnum::DATA_TYPE_INPUT])));
        }
		return $this->fetch('',['supplier' => ErpSupplierLogic::getAll(),'process' => ErpSupplierProcessLogic::getAll()]);
    }

    // 删除委外申购
    public function outsourcingRemove(){
        return $this->getJson(ErpPurchaseApplyLogic::goRemove($this->request->only(['ids'])));
    }
	
    // 待排产列表
    public function orderProduce(){
		$query = $this->request->only(['customer_name','order_sn','delivery_time','create_time','region_type','address','product_model']);
		return $this->fetch('erp/order_produce/wait',['hidden'=>1,'query'=>$query,'list'=>ErpOrderProduceLogic::getWait($query),'region_type'=>RegionTypeEnum::getDesc()]);
    }	
	

}
