<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\{ErpMaterialPriceLogic,ErpMaterialTreeLogic,ErpSupplierLogic};
use app\common\enum\ErpWarehouseEnum;
use app\common\enum\ErpMaterialEnum;
use app\common\util\Excel;
use app\common\model\{ErpMaterial};

class MaterialPrice extends \app\admin\controller\Base
{
    public function list(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialPriceLogic::getList($this->request->param(),$this->request->param('limit')));
        }
		return $this->fetch('',['supplier'=>ErpSupplierLogic::getAll(),'tree' => ErpMaterialTreeLogic::tree(ErpMaterialEnum::PARTN,false),'material_type'=>ErpMaterialEnum::PARTN]);
    }

    // 添加
    public function add(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpMaterialPriceLogic::goAdd($this->request->param()));
        }
        return $this->fetch('',['material'=>ErpMaterial::where('id',$this->request->param('material_id'))->find(),'supplier'=>ErpSupplierLogic::getAll()]);
    }

    // 编辑
    public function edit(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpMaterialPriceLogic::goEdit($this->request->param())); 
        }
        return $this->fetch('',['supplier'=>ErpSupplierLogic::getAll(),'model' => ErpMaterialPriceLogic::getOne($this->request->param('id/d'))]);
    }

    // 删除
    public function remove(){
        return $this->getJson(ErpMaterialPriceLogic::goRemove($this->request->only(['ids'])));
    }

	// 导入
    public function import(){
        if($this->request->isPost()){
			return $this->getJson(ErpMaterialPriceLogic::goImport($this->request->param('excel')));
        }else{
			return $this->fetch('');
		}
    }

	// 导出
    public function export(){
		ini_set("memory_limit","-1");
		ini_set('max_execution_time', '30');
		$data = ErpMaterialLogic::getExport(array_merge($this->request->param(),['type'=>ErpMaterialEnum::PARTN,'status'=>1]),10000);
		Excel::go('零件表', $data['column'], $data['setWidh'], $data['list'], $data['keys'],"零件表", $data['image_fields']);
		exit;	
    }
}
