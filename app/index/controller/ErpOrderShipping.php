<?php
declare (strict_types = 1);
namespace app\index\controller;
use app\index\logic\ErpOrderShippingLogic;
use app\admin\logic\{ErpWarehouseLogic};
use app\common\enum\{RegionTypeEnum};

class ErpOrderShipping extends Base
{

    // 列表
    public function index(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderShippingLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['region_type'=>RegionTypeEnum::getDesc()]);
    }
	
    // 确认出库
    public function confirm(){
		if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderShippingLogic::goConfirm($this->request->only(['shipping_sn','shipping_num','shipping_photo']),$this->request->userId));
		}
		return $this->fetch('',['shipping_sn'=>$this->request->param('shipping_sn')]);
    }

}
