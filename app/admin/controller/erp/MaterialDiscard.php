<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\ErpMaterialDiscardLogic;
use app\common\enum\ErpMaterialStockEnum;
use app\admin\logic\ErpMaterialLogic;
use app\admin\logic\ErpSupplierLogic;
use app\common\enum\{ErpMaterialEnum,ErpMaterialDiscardMaterialEnum};
use app\common\util\Excel;
use app\common\util\FileUtil;
use app\common\util\ZipUtils;
class MaterialDiscard extends \app\admin\controller\Base
{

    // 列表
    public function list(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialDiscardLogic::getList($this->request->param(),$this->request->param('limit')));
        }
		$material_type 	= $this->request->param('material_type');
		$category 		= [];
		if($material_type == ErpMaterialEnum::PARTN){
			$category 	= ErpMaterialLogic::getCategory('material_partn');
		}else if($material_type == ErpMaterialEnum::COMPONENT){
			$category 	= ErpMaterialLogic::getCategory('material_component');
		}
        return $this->fetch('',['material_status'=>ErpMaterialDiscardMaterialEnum::getStatusDesc(),'material_type'=>$material_type,'category'=>$category,'type'=>ErpMaterialStockEnum::getDiscardType(),'status'=>ErpMaterialStockEnum::getStatusDesc(),'default_status'=>ErpMaterialStockEnum::STATUS_HANDLE]);
    }

    // 添加
    public function add(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpMaterialDiscardLogic::goAdd($this->request->only(['material_type','type','order_id','remark','material','stock_date','supplier_id'])));
        }
		return $this->fetch('',['hidden_key'=>ErpMaterialStockEnum::getIgnoreType(),'supplier'=>ErpSupplierLogic::getAll(),'material_type'=>$this->request->param('material_type'),'stock_type'=>$this->request->param('stock_type'),'type'=>ErpMaterialStockEnum::getDiscardType()]);
    }

    // 编辑
    public function edit(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpMaterialDiscardLogic::goEdit($this->request->only(['id','type','order_id','remark','material','stock_date','supplier_id']))); 
        }
        return $this->fetch('',['hidden_key'=>ErpMaterialStockEnum::getIgnoreType(),'supplier'=>ErpSupplierLogic::getAll(),'type'=>ErpMaterialStockEnum::getDiscardType(),'model' => ErpMaterialDiscardLogic::getOne($this->request->param('id/d'))]);
    }

    // 报废单物料列表
    public function material(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialDiscardLogic::getMaterial($this->request->param(),$this->request->param('limit')));
        }
    }

    // 确认保存并扣减库存
    public function confirm(){
        return $this->getJson(ErpMaterialDiscardLogic::goConfirm($this->request->param('id'),$this->request->param('ids'),$this->request->param('num')));
    }	

    // 作废
    public function cancel(){
        return $this->getJson(ErpMaterialDiscardLogic::goCancel($this->request->param('id'),$this->request->param('ids')));
    }	
	
	// 结算
    public function settle(){
        return $this->getJson(ErpMaterialDiscardLogic::goSettle($this->request->param('id')));
    }
	
	// 删除物料
    public function removeMaterial(){
		return $this->getJson(ErpMaterialDiscardLogic::goRemoveMaterial($this->request->param('id')));
    }
	
	// 通知供应商
    public function send(){
        return $this->getJson(ErpMaterialDiscardLogic::goSend($this->request->param('id')));
    }
	
	
    // 报废明细
    public function index(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialDiscardLogic::getMaterial($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['supplier'=>ErpSupplierLogic::getAll()]);
    }	
	
	//导出报废明细
    public function export($export_act=0){
		ini_set("memory_limit","-1");
		ini_set('max_execution_time', '60');
		$key 			= preg_replace( '/[\W]/', '', $this->request->param('key',''));
		if($export_act == 1){
			return $this->getJson(ErpMaterialDiscardLogic::getMaterialCount($this->request->param(),$this->request->param('limit',10000)));
		}else if($export_act == 2 && $key){
			$page 		= $this->request->param('page');
			$data 		= ErpMaterialDiscardLogic::getMaterialExport($this->request->param(),$this->request->param('limit',10000));
			$dir 		= './download/'.$key.'/';
			$fileUtil 	= new FileUtil();
			$fileUtil->createDir($dir);
			Excel::go("报废明细", $data['column'], $data['setWidh'], $data['list'], $data['keys'],$page,$data['image_fields'],$dir);
			return $this->getJson();
		}else if($export_act == 3 && $key && is_dir('./download/'.$key)){
			ZipUtils::zip('./download/'.$key,'./download/'.$key.'.zip');
			return json(['path'=>'/download/'.$key.'.zip']);
		}else{
			$data 		= ErpMaterialDiscardLogic::getMaterialExport($this->request->param(),$this->request->param('limit',10000));
			Excel::go('报废明细', $data['column'], $data['setWidh'], $data['list'], $data['keys'],"报废明细",$data['image_fields']);
			exit;
		}
    }
}
