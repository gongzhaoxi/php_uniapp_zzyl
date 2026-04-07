<?php
declare (strict_types = 1);
namespace app\index\controller;
use app\common\util\Upload as Up;
use app\admin\logic\{ErpMaterialLogic,ErpMaterialTreeLogic,ErpSupplierLogic};
class Common extends Base
{
	protected $middleware = ['\app\index\middleware\LoginCheck'];
	
    // 通用上传
    public function upload(){
        return $this->getJson(Up::putFile($this->request->file(),$this->request->post('path')));
    }
	
    // 物料库存
    public function materialStock(){
        if ($this->request->isAjax()) {
			return json(ErpMaterialLogic::getMaterialStock($this->request->param(),$this->request->param('limit')));
        }else{
			$type = $this->request->param('type/d');
			return $this->fetch('admin@common/material_stock',['tree' => ErpMaterialTreeLogic::tree($type,false),'type'=>$type]);
		}
    }	
	
	public function material(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialLogic::getSelect($this->request->param(),$this->request->param('limit')));
        }else{
			$type = $this->request->param('type/d');
			return $this->fetch('',['tree' => ErpMaterialTreeLogic::tree($type,false),'type'=>$type]);
		}
    }


    // 物料库存
    public function checkMaterialStock(){
        if ($this->request->isAjax()) {
			return json(ErpMaterialLogic::getCheckMaterialStock($this->request->param(),$this->request->param('limit')));
        }else{
			$type = $this->request->param('type/d');
			return $this->fetch('',['supplier'=>ErpSupplierLogic::getAll(),'tree' => ErpMaterialTreeLogic::tree($type,false),'type'=>$type]);
		}
    }
	
}
