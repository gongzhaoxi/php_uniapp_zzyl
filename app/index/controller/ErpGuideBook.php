<?php
declare (strict_types = 1);
namespace app\index\controller;
use app\admin\logic\{ErpGuideBookLogic};

class ErpGuideBook extends Base
{

    // 列表
    public function index(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpGuideBookLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('index',['data_type'=>$this->request->param('data_type',1)]);
    }
	
}
