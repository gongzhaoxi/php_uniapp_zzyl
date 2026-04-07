<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\{ErpMaterialWarehouseLogic,ErpWarehouseLogic};
use app\common\enum\{ErpMaterialEnum,ErpWarehouseEnum};
use app\common\util\Excel;
use app\common\util\FileUtil;
use app\common\util\ZipUtils;

class MaterialWarehouse extends \app\admin\controller\Base
{

    // 列表
    public function list(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialWarehouseLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['warehouse_type'=>ErpWarehouseEnum::PRODUCE,'warehouse'=>ErpWarehouseLogic::getAll(['type'=>ErpWarehouseEnum::PRODUCE]),'type'=>ErpMaterialEnum::getTypeDesc()]);
    }

	public function export($export_act=0){
		ini_set("memory_limit","-1");
		ini_set('max_execution_time', '60');
		$key 			= preg_replace( '/[\W]/', '', $this->request->param('key',''));
		if($export_act == 1){
			return $this->getJson(ErpMaterialWarehouseLogic::getExportCount($this->request->param()));
		}else if($export_act == 2 && $key){
			$page 		= $this->request->param('page');
			$data 		= ErpMaterialWarehouseLogic::getExport($this->request->param(),$this->request->param('limit',10000));
			$dir 		= './download/'.$key.'/';
			$fileUtil 	= new FileUtil();
			$fileUtil->createDir($dir);
			Excel::go("仓位库存", $data['column'], $data['setWidh'], $data['list'], $data['keys'],$page,$data['image_fields'],$dir);
			return $this->getJson();
		}else if($export_act == 3 && $key && is_dir('./download/'.$key)){
			ZipUtils::zip('./download/'.$key,'./download/'.$key.'.zip');
			return json(['path'=>'/download/'.$key.'.zip']);
		}else{
			$data 		= ErpMaterialWarehouseLogic::getExport($this->request->param(),$this->request->param('limit',10000));
			Excel::go('仓位库存', $data['column'], $data['setWidh'], $data['list'], $data['keys'],"仓位库存",$data['image_fields']);
			exit;
		}
    }

    // 调拨
    public function allocate(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpMaterialWarehouseLogic::goAllocate($this->request->param()));
        }
        return $this->fetch('',[]);
    }
	
    // 修改安全库存/最低库存/最高库存
    public function editSafetyStock(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpMaterialWarehouseLogic::goEditSafetyStock($this->request->only(['id','safety_stock','min_stock','max_stock'])));
        }
        return $this->fetch('',[]);
    }


    // 启用/停用
    public function show(){
        return $this->getJson(ErpMaterialWarehouseLogic::goShow($this->request->param('is_show',1),$this->request->param('ids')));
    }		
	
    
}
