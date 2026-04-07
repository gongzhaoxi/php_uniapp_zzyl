<?php
declare (strict_types = 1);
namespace app\index\controller;
use app\admin\logic\{ErpPurchaseOrderLogic,ErpSupplierLogic,ErpWarehouseLogic,ErpPurchaseApplyLogic};
use app\common\enum\{ErpPurchaseOrderEnum,ErpPurchaseApplyEnum};
class ErpPurchaseOrder extends Base
{

    // 物料采购单数据
    public function materialData(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpPurchaseOrderLogic::getMaterialData($this->request->param(),$this->request->param('limit')));
        }
    }

    // 物料采购跟踪
    public function materialFollow(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpPurchaseOrderLogic::getMaterialFollow($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['admins' => ErpPurchaseOrderLogic::getAdmins(),'supplier' => ErpSupplierLogic::getAll(),'status' => ErpPurchaseOrderEnum::getStatusDesc(),'default_status' => ErpPurchaseOrderEnum::STATUS_NO]);
    }	

	// 采购入库
    public function warehous($warehous_type=1){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpPurchaseOrderLogic::goWarehous($warehous_type,$this->request->only(['supplier_id','batch_number','purchase_date','material_type','type','order_id','remark','material','stock_date']),$this->request->userInfo['name']));
        }else{
			if($warehous_type == 2){
				$warehouse 	= ErpWarehouseLogic::getAll(['type'=>4]);
				$data 		= ErpPurchaseOrderLogic::getProductData($this->request->only(['ids']),10000)['data'];
			}else{
				$warehouse 	= ErpWarehouseLogic::getAll(['type'=>'1,2']);
				$data 		= ErpPurchaseOrderLogic::getMaterialData($this->request->only(['ids']),10000)['data'];
			}
			return $this->fetch('admin@erp/purchase_order/warehous',['supplier_id'=>$this->request->param('supplier_id'),'warehous_type'=>$warehous_type,'supplier'=>ErpSupplierLogic::getAll(),'warehouse'=>$warehouse,'list' =>$data,'model' => ErpPurchaseOrderLogic::getOne($this->request->param('id/d'))]);
		}
    }
	
    // 成品采购单数据
    public function productData($from=1){
		$order_id 	= $this->request->param('order_id');
		$data 		= [];
		if($order_id){
			$data 	= ErpPurchaseOrderLogic::getProductData($this->request->param(),$this->request->param('limit'));
		}
        return $this->fetch('',['order_id'=>$order_id,'data'=>$data,'from'=>$from]);
    }

	// 成品采购跟踪
    public function productFollow(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpPurchaseOrderLogic::getProductFollow($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['admins' => ErpPurchaseOrderLogic::getAdmins(),'supplier' => ErpSupplierLogic::getAll()]);
    }
	
}
