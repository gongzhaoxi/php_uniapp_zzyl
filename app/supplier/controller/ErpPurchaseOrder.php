<?php
declare (strict_types = 1);
namespace app\supplier\controller;
use app\supplier\logic\{ErpPurchaseOrderLogic,ErpSupplierLogic};
use app\common\enum\ErpPurchaseOrderEnum;
class ErpPurchaseOrder extends \app\supplier\controller\Base
{

    // 物料采购单列表
    public function material(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpPurchaseOrderLogic::getMaterial($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['status' => ErpPurchaseOrderEnum::getStatusDesc()]);
    }

    // 物料采购单数据
    public function materialData(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpPurchaseOrderLogic::getMaterialData($this->request->param(),$this->request->param('limit')));
        }
    }	
	
    // 确认采购单
    public function confirm(){
        return $this->getJson(ErpPurchaseOrderLogic::goConfirm($this->request->param('id')));
    }	
	
	// 撤销采购单
    public function cancel(){
        return $this->getJson(ErpPurchaseOrderLogic::goCancel($this->request->param('id')));
    }
	
	// 变更日志
	public function log(){
		return $this->fetch('',['list' => ErpPurchaseOrderLogic::getLog($this->request->param('id/d'))]);
    }	
	
	// 反馈
    public function feedback(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpPurchaseOrderLogic::goFeedback($this->request->post(['order_id','content'])));
        }else{
			return $this->fetch('',['id'=>$this->request->param('id/d'),'list' => ErpPurchaseOrderLogic::getFeedback($this->request->param('id/d'))]);
		}
    }
	
	// 成品采购单列表
    public function product(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpPurchaseOrderLogic::getProduct($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['status' => ErpPurchaseOrderEnum::getStatusDesc()]);
    }
	
    // 成品采购单数据
    public function productData(){
		$data 	= $this->request->param('order_id')?ErpPurchaseOrderLogic::getProductData($this->request->param(),$this->request->param('limit')):[];
        return $this->fetch('',['data'=>$data]);
    }	
	
}
