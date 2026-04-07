<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\{ErpOrderLogic,RegionLogic};
use app\common\enum\RegionTypeEnum;
use app\common\enum\ErpOrderEnum;
use app\common\util\Excel;
use app\common\util\FileUtil;
use app\common\util\ZipUtils;

class Order extends \app\admin\controller\Base
{

    // 列表
    public function list(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderLogic::getList(array_merge($this->request->param(),['data_type'=>ErpOrderEnum::DATA_TYPE_1]),$this->request->param('limit')));
        }
        return $this->fetch('',['query'=>$this->request->only(['order_status','salesman_approve','technician_approve']),'admins'=>ErpOrderLogic::getAdmins(),'region_type'=>RegionTypeEnum::getDesc(),'order_status'=>ErpOrderLogic::getOrderStatusCount(),'shipping_status'=>ErpOrderEnum::getShippingStatusDesc(),'produce_status'=>ErpOrderEnum::getProduceStatusDesc()]);
    }

    // 添加
    public function add(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpOrderLogic::goAdd($this->request->only(['sn','region_name','create_time','region','customer_id','order_type','customer_name','delivery_time','delivery_remark','address','address_short','region_type','contacts','phone','cabinet_num','technical_parameter','customer_remark','shipping_type','motor_code','is_special','order_remark'])));
        }
        return $this->fetch('',['admins'=>ErpOrderLogic::getAdmins(),'tree' => RegionLogic::tree(false,'0,1,2,3'),'order_type'=>ErpOrderEnum::getOrderTypeDesc(true,ErpOrderEnum::DATA_TYPE_1),'shipping_type'=>ErpOrderEnum::getShippingTypeDesc(),'region_type'=>RegionTypeEnum::getDesc()]);
    }

    // 查看
    public function view(){
        return $this->fetch('',['tree' => RegionLogic::tree(false,'0,1,2,3'),'admins'=>ErpOrderLogic::getAdmins(),'region_type'=>RegionTypeEnum::getDesc(),'order_type'=>ErpOrderEnum::getOrderTypeDesc(true,ErpOrderEnum::DATA_TYPE_1),'shipping_type'=>ErpOrderEnum::getShippingTypeDesc(),'model' => ErpOrderLogic::getOne($this->request->param('id/d'))]);
    }

    // 编辑
    public function edit(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpOrderLogic::goEdit($this->request->only(['id','sn','region_name','create_time','region','salesman_id','salesman_name','order_type','order_sn','customer_id','customer_name','delivery_time','delivery_remark','address','address_short','region_type','contacts','phone','cabinet_num','technical_parameter','customer_remark','shipping_type','motor_code','is_special','order_remark'])));
        }
    }
	// 日志
    public function log(){
        return $this->fetch('',['list' => ErpOrderLogic::getLog($this->request->only(['order_id','order_product_id','data_type']))]);
    }

    // 删除
    public function remove(){
        return $this->getJson(ErpOrderLogic::goRemove($this->request->only(['ids'])));
    }	

    // 取消订单
    public function cancel(){
        return $this->getJson(ErpOrderLogic::goCancel($this->request->only(['ids'])));
    }

    // 回收站
    public function recycle(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpOrderLogic::getRecycle($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch();
    }
	
	// 恢复/删除回收站
	public function batchRecycle(){
		return $this->getJson(ErpOrderLogic::goRecycle($this->request->param('ids'),$this->request->param('type')));
    }
	
	// 待技术审批
    public function technician(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['query'=>$this->request->only(['technician_approve'=>'']),'region_type'=>RegionTypeEnum::getDesc(),'order_status'=>ErpOrderLogic::getTechnicianStatusCount()]);
    }
	
	// 技术审核查看
    public function technicianView(){
        return $this->fetch('',['tree' => RegionLogic::tree(false,'0,1,2,3'),'region_type'=>RegionTypeEnum::getDesc(),'shipping_type'=>ErpOrderEnum::getShippingTypeDesc(),'model' => ErpOrderLogic::getOne($this->request->param('id/d'))]);
    }
	
	// 技术审批
    public function technicianPass(){
        return $this->getJson(ErpOrderLogic::goTechnicianPass($this->request->param('id')));
    }	
	
	// 技术反审
    public function technicianReset(){
        return $this->getJson(ErpOrderLogic::goTechnicianReset($this->request->param('id')));
    }	
	
	// 待销售审批
    public function salesman(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['query'=>$this->request->only(['salesman_approve'=>'']),'region_type'=>RegionTypeEnum::getDesc(),'order_status'=>ErpOrderLogic::getSalesmanStatusCount()]);
    }
	
	// 销售审核查看
    public function salesmanView(){
		$model =  ErpOrderLogic::getOne($this->request->param('id/d'));
		if($model['data_type'] == ErpOrderEnum::DATA_TYPE_2){
			return $this->fetch('erp/aftersale/view',['admins'=>ErpOrderLogic::getAdmins(),'order_type'=>ErpOrderEnum::getOrderTypeDesc(true,ErpOrderEnum::DATA_TYPE_2),'salesman_check'=>1,'region_type'=>RegionTypeEnum::getDesc(),'shipping_type'=>ErpOrderEnum::getShippingTypeDesc(),'model' =>$model]);
		}else{
			return $this->fetch('view',['tree' => RegionLogic::tree(false,'0,1,2,3'),'admins'=>ErpOrderLogic::getAdmins(),'order_type'=>ErpOrderEnum::getOrderTypeDesc(true,ErpOrderEnum::DATA_TYPE_1),'salesman_check'=>1,'region_type'=>RegionTypeEnum::getDesc(),'shipping_type'=>ErpOrderEnum::getShippingTypeDesc(),'model' => $model]);
		}
	}	
	
    // 销售审批
    public function salesmanPass(){
        return $this->getJson(ErpOrderLogic::goSalesmanPass($this->request->param('id')?$this->request->param('id'):$this->request->param('ids')));
    }
	
	// 销售反审
    public function salesmanReset(){
		return $this->getJson(ErpOrderLogic::goSalesmanReset($this->request->param('id')));
    }
	
	// 发货通知
    public function shipping(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderLogic::goShipping($this->request->param('produce_id'),$this->request->param('address'),$this->request->param('shipping_date')));
        }
        return $this->fetch('',['model' => ErpOrderLogic::getOne($this->request->param('id/d'))]);
    }
	// 复制订单
    public function copy(){
        return $this->getJson(ErpOrderLogic::goCopy($this->request->param('id')));
    }	
	// 导出订单	
	public function export($export_act=0){
		ini_set("memory_limit","-1");
		ini_set('max_execution_time', '60');
		$key 			= preg_replace( '/[\W]/', '', $this->request->param('key',''));
		if($export_act == 1){
			return $this->getJson(ErpOrderLogic::getExportCount($this->request->param(),$this->request->param('limit',10000)));
		}else if($export_act == 2 && $key){
			$page 		= $this->request->param('page');
			$data 		= ErpOrderLogic::getExport($this->request->param(),$this->request->param('limit',10000));
			$dir 		= './download/'.$key.'/';
			$fileUtil 	= new FileUtil();
			$fileUtil->createDir($dir);
			Excel::go("销售合同", $data['column'], $data['setWidh'], $data['list'], $data['keys'],$page,$data['image_fields'],$dir);
			return $this->getJson();
		}else if($export_act == 3 && $key && is_dir('./download/'.$key)){
			ZipUtils::zip('./download/'.$key,'./download/'.$key.'.zip');
			return json(['path'=>'/download/'.$key.'.zip']);
		}else{
			$data 		= ErpOrderLogic::getExport($this->request->param(),$this->request->param('limit',10000));
			Excel::go('销售合同', $data['column'], $data['setWidh'], $data['list'], $data['keys'],"销售合同",$data['image_fields']);
			exit;
		}
    }
	
}
