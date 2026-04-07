<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\{ErpStatLogic,ErpWarehouseLogic};


class Stat extends \app\admin\controller\Base
{

	//产能分析
	public function capacity(){
	
		return $this->fetch('',ErpStatLogic::capacity($this->request->param('date1'),$this->request->param('date2')));
	}
	
	//异常分析
	public function errors(){
	
		return $this->fetch('',ErpStatLogic::errors($this->request->param('month1',''),$this->request->param('month2','')));
	}
	
	//销售分析
	public function sale(){
		if($this->request->isAjax()) {
			 
		}else{
			return $this->fetch('',ErpStatLogic::sale($this->request->param('create_time1',''),$this->request->param('create_time2',''),$this->request->param('year',''),$this->request->param('create_time4','')));
		}
	}	
	
	//工位用量统计
    public function useMaterial(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpStatLogic::getUseMaterial($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['warehouse'=>ErpWarehouseLogic::getAll(['type'=>3])]);
    }	
	
}
