<?php
declare (strict_types = 1);
namespace app\admin\controller\h5;
use app\admin\logic\ErpOrderLogic;
use app\common\enum\RegionTypeEnum;
use app\common\enum\ErpOrderEnum;

class ErpOrder extends \app\admin\controller\Base
{

    // 列表
    public function list(){
		$query 	= $this->request->only(['order_sn','customer_name','salesman_id'=>session('admin.id'),'create_start'=>date('Y-m-d',strtotime('-30 day')),'create_end'=>date('Y-m-d')]);
		$res 	= ErpOrderLogic::getList(array_merge($query,['data_type'=>ErpOrderEnum::DATA_TYPE_1]),$this->request->param('limit',10));
        return $this->fetch('',['salesman_pass'=>ErpOrderLogic::checkAuth('erp.order/salesmanPass'),'shipping'=>ErpOrderLogic::checkAuth('erp.order/shipping'),'query'=>$query,'admins'=>ErpOrderLogic::getAdmins(),'page' => $this->request->param('page/d',1),'last_page'=>ceil($res['extend']['count']/$res['extend']['limit']),'data'=>$res['data'],'count'=>$res['extend']['count'],'limit'=>$res['extend']['limit']]);
    }

    // 查看
    public function view(){
        return $this->fetch('',['model' => ErpOrderLogic::getOne($this->request->param('id/d'))]);
    }

	// 待销售审批
    public function salesman(){
        $query 	= $this->request->except(['page']);
		$res 	= ErpOrderLogic::getList(array_merge($query,['data_type'=>ErpOrderEnum::DATA_TYPE_1,'salesman_approve'=>0,'technician_approve'=>1]),$this->request->param('limit',10));
        return $this->fetch('list',['salesman_pass'=>true,'shipping'=>ErpOrderLogic::checkAuth('erp.order/shipping'),'query'=>$query,'admins'=>ErpOrderLogic::getAdmins(),'page' => $this->request->param('page/d',1),'last_page'=>ceil($res['extend']['count']/$res['extend']['limit']),'data'=>$res['data'],'count'=>$res['extend']['count'],'limit'=>$res['extend']['limit']]);
    }
	
    // 销售审批
    public function salesmanPass(){
        return $this->getJson(ErpOrderLogic::goSalesmanPass($this->request->param('id')));
    }
	
	// 发货通知
    public function shipping(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderLogic::goShipping($this->request->param('produce_id'),$this->request->param('address'),$this->request->param('shipping_date')));
        }
        return $this->fetch('',['model' => ErpOrderLogic::getOne($this->request->param('id/d'))]);
    }	
}
