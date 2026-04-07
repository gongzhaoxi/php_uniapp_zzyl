<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\{ErpPurchaseOrderDataLogic,ErpSupplierLogic,ErpSupplierProcessLogic};
use app\common\enum\{ErpPurchaseOrderDataEnum,ErpPurchaseOrderEnum};
use app\common\util\Excel;
use app\common\util\FileUtil;
use app\common\util\ZipUtils;

class PurchaseOrderData extends \app\admin\controller\Base
{
	//采购单明细
    public function materialList(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpPurchaseOrderDataLogic::getMaterial($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['supplier' => ErpSupplierLogic::getAll(),'admins'=>ErpPurchaseOrderDataLogic::getAdmins(),'status' => ErpPurchaseOrderEnum::getStatusDesc(),'default_status' => ErpPurchaseOrderEnum::STATUS_YES]);
    }
	
	//导出采购单明细
	public function materialListExport($export_act=0){
		ini_set("memory_limit","-1");
		ini_set('max_execution_time', '60');
		$key 			= preg_replace( '/[\W]/', '', $this->request->param('key',''));
		if($export_act == 1){
			return $this->getJson(ErpPurchaseOrderDataLogic::getMaterialExportCount($this->request->param()));
		}else if($export_act == 2 && $key){
			$page 		= $this->request->param('page');
			$data 		= ErpPurchaseOrderDataLogic::getMaterialListExport($this->request->param(),$this->request->param('limit',10000));
			$dir 		= './download/'.$key.'/';
			$fileUtil 	= new FileUtil();
			$fileUtil->createDir($dir);
			Excel::go("采购单明细", $data['column'], $data['setWidh'], $data['list'], $data['keys'],$page,$data['image_fields'],$dir);
			return $this->getJson();
		}else if($export_act == 3 && $key && is_dir('./download/'.$key)){
			ZipUtils::zip('./download/'.$key,'./download/'.$key.'.zip');
			return json(['path'=>'/download/'.$key.'.zip']);
		}else{
			$data 		= ErpPurchaseOrderDataLogic::getMaterialListExport($this->request->param(),$this->request->param('limit',10000));
			Excel::go('采购单明细', $data['column'], $data['setWidh'], $data['list'], $data['keys'],"采购单明细",$data['image_fields']);
			exit;
		}
    }

	//采购入库明细
    public function material(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpPurchaseOrderDataLogic::getMaterial($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['supplier' => ErpSupplierLogic::getAll(),'admins'=>ErpPurchaseOrderDataLogic::getAdmins(),'status'=>ErpPurchaseOrderDataEnum::getStatusDesc()]);
    }

	//导出采购入库明细
	public function materialExport($export_act=0){
		ini_set("memory_limit","-1");
		ini_set('max_execution_time', '60');
		$key 			= preg_replace( '/[\W]/', '', $this->request->param('key',''));
		if($export_act == 1){
			return $this->getJson(ErpPurchaseOrderDataLogic::getMaterialExportCount($this->request->param()));
		}else if($export_act == 2 && $key){
			$page 		= $this->request->param('page');
			$data 		= ErpPurchaseOrderDataLogic::getMaterialExport($this->request->param(),$this->request->param('limit',10000));
			$dir 		= './download/'.$key.'/';
			$fileUtil 	= new FileUtil();
			$fileUtil->createDir($dir);
			Excel::go("采购入库明细", $data['column'], $data['setWidh'], $data['list'], $data['keys'],$page,$data['image_fields'],$dir);
			return $this->getJson();
		}else if($export_act == 3 && $key && is_dir('./download/'.$key)){
			ZipUtils::zip('./download/'.$key,'./download/'.$key.'.zip');
			return json(['path'=>'/download/'.$key.'.zip']);
		}else{
			$data 		= ErpPurchaseOrderDataLogic::getMaterialExport($this->request->param(),$this->request->param('limit',10000));
			Excel::go('采购入库明细', $data['column'], $data['setWidh'], $data['list'], $data['keys'],"采购入库明细",$data['image_fields']);
			exit;
		}
    }
	
    public function materialStat(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpPurchaseOrderDataLogic::getMaterialStat($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['supplier' => ErpSupplierLogic::getAll(),'admins'=>ErpPurchaseOrderDataLogic::getAdmins(),'status'=>ErpPurchaseOrderDataEnum::getStatusDesc()]);
    }
	
	//导出采购入库汇总
	public function materialStatExport($export_act=0){
		ini_set("memory_limit","-1");
		ini_set('max_execution_time', '60');
		$key 			= preg_replace( '/[\W]/', '', $this->request->param('key',''));
		if($export_act == 1){
			return $this->getJson(ErpPurchaseOrderDataLogic::getMaterialStatExportCount($this->request->param()));
		}else if($export_act == 2 && $key){
			$page 		= $this->request->param('page');
			$data 		= ErpPurchaseOrderDataLogic::getMaterialStatExport($this->request->param(),$this->request->param('limit',10000));
			$dir 		= './download/'.$key.'/';
			$fileUtil 	= new FileUtil();
			$fileUtil->createDir($dir);
			Excel::go("采购入库汇总", $data['column'], $data['setWidh'], $data['list'], $data['keys'],$page,$data['image_fields'],$dir);
			return $this->getJson();
		}else if($export_act == 3 && $key && is_dir('./download/'.$key)){
			ZipUtils::zip('./download/'.$key,'./download/'.$key.'.zip');
			return json(['path'=>'/download/'.$key.'.zip']);
		}else{
			$data 		= ErpPurchaseOrderDataLogic::getMaterialStatExport($this->request->param(),$this->request->param('limit',10000));
			Excel::go('采购入库汇总', $data['column'], $data['setWidh'], $data['list'], $data['keys'],"采购入库汇总",$data['image_fields']);
			exit;
		}
    }	
	
    public function product(){
		$query = $this->request->only(['order_sn'=>'','status'=>1,'supplier_id'=>'','order_date'=>date('Y-m-01').' 至 '.date('Y-m-'.date('t')),'delivery_date'=>'']);
        return $this->fetch('',['query'=>$query,'list'=>ErpPurchaseOrderDataLogic::getProduct($query,$this->request->param('limit',10)),'supplier' => ErpSupplierLogic::getAll(),'admins'=>ErpPurchaseOrderDataLogic::getAdmins(),'status'=>ErpPurchaseOrderDataEnum::getStatusDesc()]);
    }

    public function productStat(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpPurchaseOrderDataLogic::getProductStat($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['supplier' => ErpSupplierLogic::getAll(),'admins'=>ErpPurchaseOrderDataLogic::getAdmins(),'status'=>ErpPurchaseOrderDataEnum::getStatusDesc()]);
    }	
    
	//委外入库历史
    public function outsourcing(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpPurchaseOrderDataLogic::getOutsourcing($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['supplier' => ErpSupplierLogic::getAll(),'admins'=>ErpPurchaseOrderDataLogic::getAdmins(),'status'=>ErpPurchaseOrderDataEnum::getStatusDesc(),'process' => ErpSupplierProcessLogic::getAll()]);
    }	
	
	//导出委外入库历史
	public function outsourcingExport($export_act=0){
		ini_set("memory_limit","-1");
		ini_set('max_execution_time', '60');
		$key 			= preg_replace( '/[\W]/', '', $this->request->param('key',''));
		if($export_act == 1){
			return $this->getJson(ErpPurchaseOrderDataLogic::getOutsourcingExportCount($this->request->param(),$this->request->param('limit',10000)));
		}else if($export_act == 2 && $key){
			$page 		= $this->request->param('page');
			$data 		= ErpPurchaseOrderDataLogic::getOutsourcingExport($this->request->param(),$this->request->param('limit',10000));
			$dir 		= './download/'.$key.'/';
			$fileUtil 	= new FileUtil();
			$fileUtil->createDir($dir);
			Excel::go("委外入库历史", $data['column'], $data['setWidh'], $data['list'], $data['keys'],$page,$data['image_fields'],$dir);
			return $this->getJson();
		}else if($export_act == 3 && $key && is_dir('./download/'.$key)){
			ZipUtils::zip('./download/'.$key,'./download/'.$key.'.zip');
			return json(['path'=>'/download/'.$key.'.zip']);
		}else{
			$data 		= ErpPurchaseOrderDataLogic::getOutsourcingExport($this->request->param(),$this->request->param('limit',10000));
			Excel::go('委外入库历史', $data['column'], $data['setWidh'], $data['list'], $data['keys'],"委外入库历史",$data['image_fields']);
			exit;
		}
    }	
	
	//出库损耗对比
    public function outsourcingLoss(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpPurchaseOrderDataLogic::getOutsourcingLoss($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['supplier' => ErpSupplierLogic::getAll(),'admins'=>ErpPurchaseOrderDataLogic::getAdmins(),'status'=>ErpPurchaseOrderDataEnum::getStatusDesc(),'process' => ErpSupplierProcessLogic::getAll()]);
    }

	//导出出库损耗对比
	public function outsourcingLossExport($export_act=0){
		ini_set("memory_limit","-1");
		ini_set('max_execution_time', '60');
		$key 			= preg_replace( '/[\W]/', '', $this->request->param('key',''));
		if($export_act == 1){
			return $this->getJson(ErpPurchaseOrderDataLogic::getOutsourcingLossExportCount($this->request->param(),$this->request->param('limit',10000)));
		}else if($export_act == 2 && $key){
			$page 		= $this->request->param('page');
			$data 		= ErpPurchaseOrderDataLogic::getOutsourcingLossExport($this->request->param(),$this->request->param('limit',10000));
			$dir 		= './download/'.$key.'/';
			$fileUtil 	= new FileUtil();
			$fileUtil->createDir($dir);
			Excel::go("出库损耗对比", $data['column'], $data['setWidh'], $data['list'], $data['keys'],$page,$data['image_fields'],$dir);
			return $this->getJson();
		}else if($export_act == 3 && $key && is_dir('./download/'.$key)){
			ZipUtils::zip('./download/'.$key,'./download/'.$key.'.zip');
			return json(['path'=>'/download/'.$key.'.zip']);
		}else{
			$data 		= ErpPurchaseOrderDataLogic::getOutsourcingLossExport($this->request->param(),$this->request->param('limit',10000));
			Excel::go('出库损耗对比', $data['column'], $data['setWidh'], $data['list'], $data['keys'],"出库损耗对比",$data['image_fields']);
			exit;
		}
    }	
	
}
