<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\{ErpProductStatLogic};

class ProductStat extends \app\admin\controller\Base
{

    // 列表
    public function list(){
		if ($this->request->isAjax()) {
			return $this->getJson(ErpProductStatLogic::getList($this->request->param(),10000));
        }
		$region_type 	= $this->request->param('region_type',1);
		$month 			= $this->request->param('month',date('n'));
		$year 			= $this->request->param('year',date('Y'));
        return $this->fetch('',['region_type'=>$region_type,'month'=>$month,'year'=>$year]);
    }
	
}
