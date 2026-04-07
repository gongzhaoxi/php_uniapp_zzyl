<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\ErpOrderShippingLogic;
use app\common\enum\{RegionTypeEnum,ErpOrderEnum};
use app\admin\logic\ErpMaterialLogic;
use app\admin\logic\ErpOrderProduceProcessLogic;

class OrderShipping extends \app\admin\controller\Base
{
    // 发货通知
    public function list(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderShippingLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['region_type'=>RegionTypeEnum::getDesc(),]);
    }

    // 售后发货通知
    public function aftersale(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderShippingLogic::getAftersale($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['region_type'=>RegionTypeEnum::getDesc(),]);
    }


    // 取消发货通知
    public function cancel(){
        return $this->getJson(ErpOrderShippingLogic::goCancel($this->request->only(['ids'])));
    }	
	
    // 确认出库
    public function confirm(){
		if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderShippingLogic::goConfirm($this->request->only(['shipping_sn','shipping_num','shipping_photo'])));
		}
		return $this->fetch('',['shipping_sn'=>$this->request->param('shipping_sn')]);
    }
	
    // 打印
    public function print(){
        return $this->getJson(ErpOrderShippingLogic::goPrint($this->request->only(['ids'])));
    }	
	
	// 成品列表
    public function produce(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderShippingLogic::getProduce($this->request->param(),$this->request->param('limit')));
        }
		$no_out 			= $this->request->param('no_out/d',1);
		$shipping_status 	= $this->request->param('shipping_status','');
        return $this->fetch('',['shipping_type'=>ErpOrderEnum::getShippingTypeDesc(),'region_type'=>RegionTypeEnum::getDesc(),'no_out'=>$no_out,'shipping_status'=>$shipping_status]);
    }
    
	//查看溯源BOM
	public function produceBom($order_produce_id,$type=1,$order_product_bom_id=''){
        return $this->fetch('',['type'=>$type,'category'=>ErpMaterialLogic::getCategory($type==2?'material_partn':'material_component'),'list'=>ErpOrderShippingLogic::produceBom($order_produce_id,$type,$order_product_bom_id)]);
    }
	
	//修改溯源码
	public function saveProcessBomSn(){
		return $this->getJson(ErpOrderProduceProcessLogic::goEdit($this->request->only(['id','bom_sn'])));
	}
	
	// 成品出仓
	public function produceOut(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderShippingLogic::getProduceOut($this->request->param(),$this->request->param('limit')));
        }
    }
	
	// 发起发货通知
	public function noticeShipping(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderShippingLogic::goNoticeShipping($this->request->param('ids'),$this->request->param('address'),$this->request->param('shipping_date')));
        }
		return $this->fetch('',['ids'=>$this->request->param('ids'),'address'=>$this->request->param('address')]);
    }
	
	
	//审批发货通知
	public function approve(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderShippingLogic::goApprove($this->request->param('ids')));
        }
    }	
	
	
}
