<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\{ErpMaterialChangeLogic,ErpMaterialTreeLogic};
use app\common\enum\ErpMaterialStockEnum;
use app\common\enum\ErpMaterialEnum;

class MaterialChange extends \app\admin\controller\Base
{

    // 列表
    public function list(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialChangeLogic::getList($this->request->param(),$this->request->param('limit'),$this->request->param('sort'),$this->request->param('order')));
        }
        return $this->fetch('',['tree' => ErpMaterialTreeLogic::tree($this->request->param('material_type/d',1),false),'stock_create_admin'=>ErpMaterialChangeLogic::getStockCreateAdmin(),'create_admin'=>ErpMaterialChangeLogic::getCreateAdmin(),'material_type'=>$this->request->param('material_type/d',1),'data_type'=>ErpMaterialStockEnum::getDataTypeDesc(),'enter_type'=>ErpMaterialStockEnum::getEnterType(),'out_type'=>ErpMaterialStockEnum::getOutType(),'discard_type'=>ErpMaterialStockEnum::getDiscardType()]);
    }
    
}
