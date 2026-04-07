<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\ErpMaterialEnterMaterialLogic;

class MaterialEnterMaterial extends \app\admin\controller\Base
{
    // 列表
    public function list(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialEnterMaterialLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',[]);
	}

    // 检验报告
    public function report(){
		if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialEnterMaterialLogic::goReportEdit($this->request->only(['id','consignee','receiving_date','inspection_by','inspection_date','material_code','material_category']))); 
		}else{
			$model = ErpMaterialEnterMaterialLogic::getOne($this->request->param('id/d'));
			if(empty($model['report']) || $model['report']['status'] == 1){
				return '报告未完成';
			}
			return $this->fetch('index@erp_material_enter_material/report',['model' => $model,'hidden' => 1]);
		}

    }

    // 标签编码列表
    public function code(){
		$model = ErpMaterialEnterMaterialLogic::getOne($this->request->param('id/d'));
        return $this->fetch('',['model' => $model]);
    }
    
}
