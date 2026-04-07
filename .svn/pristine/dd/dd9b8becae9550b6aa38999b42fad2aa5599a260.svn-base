<?php
declare (strict_types = 1);
namespace app\index\controller;
use app\index\logic\{ErpMaterialPlanLogic,ErpOrderProduceProcessLogic};
use app\common\enum\{RegionTypeEnum};
use app\admin\logic\{ErpWarehouseLogic};
use app\admin\logic\ErpMaterialPlanLogic as AdminErpMaterialPlanLogic;
class ErpMaterialPlan extends Base
{

    // 列表
    public function index(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialPlanLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',[]);
    }
	
    // 添加
    public function add(){
        if ($this->request->isAjax()) {
			$res = AdminErpMaterialPlanLogic::goAdd($this->request->only(['material']));
			if($res['code'] != 200){
				return $this->getJson($res);
			}
            return $this->getJson(AdminErpMaterialPlanLogic::goStart(['id'=>$res['data']['id'],'start_date'=>date('Y-m-d')]));
        }
        return $this->fetch('',[]);
    }	
	
    //QC品检作业指导书
    public function qcFile(){
        return $this->fetch('',['model'=>ErpMaterialPlanLogic::getOne($this->request->param('id'))]);
    }	
	
    //QC品检作业指导书
    public function produceFile(){
        return $this->fetch('',['model'=>ErpMaterialPlanLogic::getOne($this->request->param('id'))]);
    }	
	
    //报工
    public function report(){
        return $this->fetch('',['warehouse'=>ErpWarehouseLogic::getAll(['type'=>'2']),'process'=>ErpMaterialPlanLogic::getProcess($this->request->param('id'),$this->request->userId),'model'=>ErpMaterialPlanLogic::getOne($this->request->param('id'))]);
    }	
	
	// 标签编码
    public function code(){
		return $this->getJson(ErpMaterialPlanLogic::goSetCode($this->request->param('id')));
    }	
	
	//提交异常
    public function errors(){
		return $this->getJson(ErpMaterialPlanLogic::goError($this->request->param(),$this->request->userInfo));
    }
	
	// 完成工序
    public function finish(){
		return $this->getJson(ErpMaterialPlanLogic::goFinish($this->request->param('plan_id'),$this->request->param('process_id'),$this->request->only(['finish_num','inspect_num','assembled_num','warehouse_id']),$this->request->userInfo,$this->request->param('data/a')));
    }
	
	// 保存随工单
    public function follow(){
		if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialPlanLogic::goFollow($this->request->param('data/a'),$this->request->userInfo));
		}
		return $this->fetch('',ErpMaterialPlanLogic::getFollow($this->request->param('id'),$this->request->param('process_id'),$this->request->userId));
    }
}
