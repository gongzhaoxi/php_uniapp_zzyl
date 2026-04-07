<?php
declare (strict_types = 1);
namespace app\index\controller;
use app\index\logic\ErpMaterialAllocateMaterialLogic;

class ErpMaterialAllocateMaterial extends Base
{

    // 列表
    public function index(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialAllocateMaterialLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',[]);
    }
	
	// 确认签收
    public function signed(){
        return $this->getJson(ErpMaterialAllocateMaterialLogic::goSigned($this->request->param('ids'),$this->request->param('signed_num')));
    }	

}
