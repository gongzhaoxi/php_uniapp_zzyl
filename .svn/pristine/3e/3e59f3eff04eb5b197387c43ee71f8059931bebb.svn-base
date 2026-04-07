<?php
declare (strict_types = 1);
namespace app\index\controller;
use app\index\logic\{ErpOrderProduceLogic,ErpOrderProduceProcessLogic,ErpOrderProduceReworkLogic};
use app\common\enum\{RegionTypeEnum};
use app\admin\logic\{ErpWarehouseLogic};
use app\common\model\{ErpProcess};

class ErpOrderProduce extends Base
{

    // 列表
    public function index(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderProduceLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['region_type'=>RegionTypeEnum::getDesc()]);
    }
	
    //QC品检作业指导书
    public function qcFile(){
        return $this->fetch('',['model'=>ErpOrderProduceLogic::getOne($this->request->param('id'))]);
    }	
	
    //QC品检作业指导书
    public function produceFile(){
        return $this->fetch('',['model'=>ErpOrderProduceLogic::getOne($this->request->param('id'))]);
    }	
	
    //报工
    public function follow(){
        return $this->fetch('',['warehouse'=>ErpWarehouseLogic::getAll(['type'=>'4']),'process'=>ErpOrderProduceProcessLogic::getProcess($this->request->param('id'),$this->request->userId),'model'=>ErpOrderProduceLogic::getOne($this->request->param('id'))]);
    }	
	
	//提交异常
    public function errorAdd(){
		return $this->getJson(ErpOrderProduceProcessLogic::goErrorAdd($this->request->param(),$this->request->userInfo));
    }
	
	// 完成工序
    public function processFinish(){
		if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderProduceProcessLogic::goProcessFinish($this->request->param('produce_sn'),$this->request->param('process_id'),$this->request->userInfo,$this->request->param('warehouse_id')));
		}
		return $this->fetch('',array_merge(ErpOrderProduceProcessLogic::getProduceFollow($this->request->param('produce_sn'),$this->request->param('process_id'),$this->request->userId),['warehouse'=>ErpWarehouseLogic::getAll(['type'=>'4'])]));
    }

	//生产巡检完成
    public function processInspect(){
		return $this->getJson(ErpOrderProduceProcessLogic::goProcessInspect($this->request->param('produce_sn'),$this->request->param('process_id'),$this->request->userInfo));
    }

	// 产品检测
    public function productCheck(){
		if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderProduceProcessLogic::goProductCheck($this->request->param('id/d'),$this->request->param('data/a')));
		}
		return $this->fetch('',array_merge(ErpOrderProduceProcessLogic::getProduceFollow($this->request->param('produce_sn'),$this->request->param('process_id'),$this->request->userId),['warehouse'=>ErpWarehouseLogic::getAll(['type'=>'4']),'userInfo'=>$this->request->userInfo,'page'=>$this->request->param('page/d',1)]));
    }
	
	
	// 创建不合格/返工评审处理单
    public function reworkAdd(){
		if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderProduceReworkLogic::goAdd($this->request->only(['order_id','order_product_id','process_id','process_name','order_produce_id','remark','user_id','username'])));
		}
		return $this->fetch('',['process'=>ErpProcess::where('type',1)->order('sort asc,id asc')->append(['user'])->select(),'model'=>ErpOrderProduceLogic::getOne($this->request->param('order_produce_id'))]);
    }
	
	// 修改随工单
    public function followEdit(){
		if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderProduceProcessLogic::goFollowEdit($this->request->param('data/a'),$this->request->userInfo));
		}
		return $this->fetch('',ErpOrderProduceProcessLogic::getFollowEdit($this->request->param('order_produce_id')));
    }
	
	
	public function reworkEdit(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpOrderProduceReworkLogic::goEdit($this->request->only(['id','remark','comment','inspector_sign','produce_sign','qc_sign','inspector_sign_date','produce_sign_date','qc_sign_date','status','finish_date']))); 
        }
        return $this->fetch('',['model' => ErpOrderProduceReworkLogic::getOne($this->request->param('id/d'))]);
    }
	
	public function reworkRemove(){
        return $this->getJson(ErpOrderProduceReworkLogic::goRemove($this->request->only(['ids'])));
    }	

    public function udi(){
		if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderProduceLogic::goEdit($this->request->only(['id','udi']))); 
		}else{
			return $this->fetch('',['model'=>ErpOrderProduceLogic::getOne($this->request->param('id'))]);
		}
    }


    public function getProduceFollow(){
		return json(['code'=>200,'data'=>ErpOrderProduceProcessLogic::getProduceFollow($this->request->param('produce_sn'),$this->request->param('process_id'),$this->request->userId)]);
    }

    // 生产汇总
    public function stat(){
        return $this->fetch('',['data'=>ErpOrderProduceLogic::getStat()]);
    }

}
