<?php
declare (strict_types = 1);
namespace app\index\controller;
use app\admin\logic\{ErpDrawingLogic,ErpMaterialTreeLogic};

class ErpDrawing extends Base
{

    // 列表
    public function index(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpDrawingLogic::getLista($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('index',['tree' => ErpMaterialTreeLogic::tree(0,false),]);
    }
	
}
