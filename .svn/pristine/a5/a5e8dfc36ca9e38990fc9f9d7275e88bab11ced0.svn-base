<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\ErpOrderProduceErrorLogic;
use app\common\util\Excel;
class OrderProduceError extends \app\admin\controller\Base
{

    // 车间异常
    public function list(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderProduceErrorLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['count'=>ErpOrderProduceErrorLogic::getErrorCount(),'status'=>$this->request->param('status','')]);
    }

    // 确认处理异常
    public function confirm(){
        return $this->getJson(ErpOrderProduceErrorLogic::goConfirm($this->request->param('id')));
    }
	
	// 导出异常
    public function export(){
		ini_set("memory_limit","-1");
		ini_set('max_execution_time', '30');
		$data = ErpOrderProduceErrorLogic::getExport($this->request->param(),10000);
		Excel::go('车间异常表', $data['column'], $data['setWidh'], $data['list'], $data['keys'],"车间异常表", $data['image_fields']);
		exit;	
    }
	
}
