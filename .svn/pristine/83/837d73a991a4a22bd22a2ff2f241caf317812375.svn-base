<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\{ErpOrderLogic,ErpOrderShippingLogic};
use app\common\enum\RegionTypeEnum;
use app\common\enum\ErpOrderEnum;
use app\common\util\Excel;
use app\common\util\FileUtil;
use app\common\util\ZipUtils;

class Aftersale extends \app\admin\controller\Base
{

	// 售后
    public function list(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderLogic::getList(array_merge($this->request->param(),['data_type'=>ErpOrderEnum::DATA_TYPE_2]),$this->request->param('limit')));
        }
        return $this->fetch('',['query'=>$this->request->only(['order_status','salesman_approve','technician_approve']),'region_type'=>RegionTypeEnum::getDesc(),'order_status'=>ErpOrderLogic::getOrderStatusCount(ErpOrderEnum::DATA_TYPE_2),'shipping_status'=>ErpOrderEnum::getShippingStatusDesc(),'produce_status'=>ErpOrderEnum::getProduceStatusDesc()]);
    }
	
    // 添加售后
    public function add(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderLogic::goAdd($this->request->only(['sale_order_id','customer_id','customer_name','delivery_time','address','region_type','contacts','phone','order_type','shipping_type','order_remark','is_special'=>0]),ErpOrderEnum::DATA_TYPE_2));
        }
        return $this->fetch('',['order_type'=>ErpOrderEnum::getOrderTypeDesc(true,ErpOrderEnum::DATA_TYPE_2),'shipping_type'=>ErpOrderEnum::getShippingTypeDesc(),'region_type'=>RegionTypeEnum::getDesc()]);
    }	
	
	// 查看售后
    public function view(){
        return $this->fetch('',['admins'=>ErpOrderLogic::getAdmins(),'region_type'=>RegionTypeEnum::getDesc(),'order_type'=>ErpOrderEnum::getOrderTypeDesc(true,ErpOrderEnum::DATA_TYPE_2),'shipping_type'=>ErpOrderEnum::getShippingTypeDesc(),'model' => ErpOrderLogic::getOne($this->request->param('id/d'))]);
    }
	
	// 编辑售后
    public function edit(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpOrderLogic::goEdit($this->request->only(['id','sale_order_id','salesman_id','order_sn','customer_id','customer_name','delivery_time','address','region_type','contacts','phone','shipping_type','order_remark','order_type','is_special'=>0])));
        }
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
	
	// 发货通知
    public function shipping(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderShippingLogic::goAftersaleNotice($this->request->param('aftersale_id'),$this->request->param('address'),$this->request->param('shipping_date')));
        }
        return $this->fetch('',['model' => ErpOrderLogic::getOne($this->request->param('id/d'))]);
    }
	
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
			Excel::go("售后处理", $data['column'], $data['setWidh'], $data['list'], $data['keys'],$page,$data['image_fields'],$dir);
			return $this->getJson();
		}else if($export_act == 3 && $key && is_dir('./download/'.$key)){
			ZipUtils::zip('./download/'.$key,'./download/'.$key.'.zip');
			return json(['path'=>'/download/'.$key.'.zip']);
		}else{
			$data 		= ErpOrderLogic::getExport($this->request->param(),$this->request->param('limit',10000));
			Excel::go('售后处理', $data['column'], $data['setWidh'], $data['list'], $data['keys'],"售后处理",$data['image_fields']);
			exit;
		}
    }
}
