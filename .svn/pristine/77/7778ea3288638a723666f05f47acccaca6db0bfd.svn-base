<?php
declare (strict_types = 1);
namespace app\index\controller;
use app\index\logic\ErpMaterialWarehouseLogic;
use app\admin\logic\{ErpWarehouseLogic};

class ErpMaterialWarehouse extends Base
{

    // 列表
    public function index(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialWarehouseLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',[]);
    }
	
	// 零件领用
    public function pull(){
		return $this->getJson(ErpMaterialWarehouseLogic::goPull($this->request->param('ids'),$this->request->param('num')));
    }	

	// 退回车间库存
    public function returnAdd(){		
		if ($this->request->isAjax()) {
			 return $this->getJson(ErpMaterialWarehouseLogic::goReturnAdd($this->request->userInfo,$this->request->param('material/a')));
		}else{
			return $this->fetch('',['data'=>ErpMaterialWarehouseLogic::getList($this->request->only(['ids']),10000)['data']]);
		}
    }	

	// 审核物料报废
    public function scrap(){		
		if ($this->request->isAjax()) {
			 return $this->getJson(ErpMaterialWarehouseLogic::goScrap($this->request->param('approval_id/d'),$this->request->param('material/a')));
		}else{
			$model = ErpMaterialWarehouseLogic::getApproval($this->request->param('id/d'));
			return $this->fetch('',['model'=>$model,'data'=>ErpMaterialWarehouseLogic::getList(['ids'=>array_column($model['data'],'id')],10000)['data'],'material_scrap_type'=>get_dict_data('material_scrap_type')]);
		}
    }

	// 审核零件退仓
    public function returnCheck(){		
		if ($this->request->isAjax()) {
			 return $this->getJson(ErpMaterialWarehouseLogic::goReturnCheck($this->request->userInfo,$this->request->only(['id','stock_num','remark'])));
		}else{
			return $this->fetch('',['model'=>ErpMaterialWarehouseLogic::getReturn($this->request->param('id/d'))]);
		}
    }
	
	//审核车间退回仓库
    public function backAdd(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpMaterialWarehouseLogic::goBackAdd($this->request->userInfo,$this->request->only(['stock_date','remark','material','approval_id']),$this->request->param('need_check',1)));
        }
		return $this->fetch('',['model'=>ErpMaterialWarehouseLogic::getApproval($this->request->param('id/d'))]);
    }	
	
    // 列表
    public function listGroupByMaterial(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialWarehouseLogic::getListGroupByMaterial($this->request->param(),$this->request->param('limit')));
        }
	}	
	
	//创建车间退回仓库
    public function approvalAdd(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpMaterialWarehouseLogic::goApprovalAdd($this->request->userInfo,$this->request->only(['stock_date','remark','material','need_check'])));
        }
		return $this->fetch('',['material_id'=>$this->request->param('material_id','')]);
    }


	// 创建物料报废
    public function scrapApprovalAdd(){		
		if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialWarehouseLogic::goScrapApprovalAdd($this->request->param('material/a')));
		}else{
			return $this->fetch('',['data'=>ErpMaterialWarehouseLogic::getList($this->request->only(['ids']),10000)['data'],'material_scrap_type'=>get_dict_data('material_scrap_type')]);
		}
    }
	
}
