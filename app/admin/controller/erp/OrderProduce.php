<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\{ErpOrderProduceLogic,ErpMaterialPlanLogic,ErpSupplierLogic,ErpPurchaseApplyLogic};
use app\common\enum\{RegionTypeEnum,ErpPurchaseApplyEnum};
use app\admin\logic\ErpMaterialLogic;
use app\common\model\{ErpProcess,ErpOrderProduceRework};

class OrderProduce extends \app\admin\controller\Base
{

    // 待排产列表
    public function wait(){
		$query = $this->request->only(['customer_name','order_sn','delivery_time','create_time','region_type','address','product_model','produce_finish_sn']);
		return $this->fetch('',['query'=>$query,'list'=>ErpOrderProduceLogic::getWait($query),'region_type'=>RegionTypeEnum::getDesc()]);
    }

	//排查
    public function check(){
        return $this->getJson(ErpOrderProduceLogic::goCheck($this->request->param('id')));
    }	

    //下达生产
    public function start($id,$produce_date=''){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpOrderProduceLogic::goStart($id,$produce_date)); 
        }
		$produce_date	= $produce_date?$produce_date:date('Y-m-d');
        return $this->fetch('',['id'=>$id,'bill_type'=>get_dict_data('product_bill_type'),'produce_type'=>get_dict_data('material_produce_type'),'produce_date'=>$produce_date,'production'=>ErpOrderProduceLogic::getProduction($produce_date),'model' => ErpOrderProduceLogic::getStart($id)]);
    }
	
    //领料出库
    public function out($id,$produce_date=''){
		return $this->getJson(ErpOrderProduceLogic::goOut($this->request->param('id'),$this->request->param('material_ids'))); 
	}
	
    // 设置默认日产值
    public function setDefaultProduction(){
        return $this->getJson(ErpOrderProduceLogic::goSetDefaultProduction($this->request->only(['default_produce_num'])));
    }
	
	// 调整七天内日产值
    public function setProduction(){
        return $this->getJson(ErpOrderProduceLogic::goSetProduction($this->request->only(['produce_date','produce_num'])));
    }
	
	// 查看七天内日产值
    public function production(){
        return $this->fetch('',['production'=>ErpOrderProduceLogic::getProduction($this->request->param('produce_date',date('Y-m-d')))]);
    }	
	
	// 已排产产品详情
   // public function view($id,$type=1,$order_product_bom_id=0){
		//return $this->fetch('',['id'=>$id,'type'=>$type,'category_partn'=>ErpMaterialLogic::getCategory('material_partn'),'category_component'=>ErpMaterialLogic::getCategory('material_component'),'bom' => ErpOrderProduceLogic::getBom($id,$type,$order_product_bom_id)]);
    //}
	
    //已排产产品详情
    public function view($id){
        return $this->fetch('',['id'=>$id,'bill_type'=>get_dict_data('product_bill_type'),'produce_type'=>get_dict_data('material_produce_type'),'model' => ErpOrderProduceLogic::getStart($id)]);
    }	
	

    // 已排产列表
    public function list(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderProduceLogic::getList($this->request->param(),$this->request->param('limit')));
        }
		return $this->fetch('',['region_type'=>RegionTypeEnum::getDesc(),'process'=>ErpProcess::where('is_default',1)->where('status',1)->order('sort asc,id asc')->select()]);
    }
	
	//撤回排产
    public function cancel(){
        return $this->getJson(ErpOrderProduceLogic::goCancel($this->request->param('ids')));
    }
	
	//欠缺一键重新排查
    public function recheck(){
        return $this->getJson(ErpOrderProduceLogic::getRecheck($this->request->param('id')));
    }

	//保存排查后结果
    public function saveRecheck(){
        return $this->getJson(ErpOrderProduceLogic::goRecheck($this->request->param('id')));
    }
	
	// 进度统计
    public function schedule(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderProduceLogic::getSchedule($this->request->param(),$this->request->param('limit')));
        }
		return $this->fetch('',['region_type'=>RegionTypeEnum::getDesc()]);
    }
	
	// 进度详情
    public function scheduleDetail(){
		return $this->fetch('',ErpOrderProduceLogic::getScheduleDetail($this->request->param('id')));
    }
	
	// 查阅随工单
    public function follow(){
		return $this->fetch('index@erp_order_produce/process_finish',ErpOrderProduceLogic::getFollowDetail($this->request->param('id'),$this->request->param('follow_id')));
    }
	
	// 随工单变更记录
    public function processLog(){
		return $this->fetch('',ErpOrderProduceLogic::getFollowLog($this->request->param('id'),$this->request->param('process_id')));
    }	
	
	// 产品编码
    public function editSn(){
        return $this->getJson(ErpOrderProduceLogic::goEditSn($this->request->only(['id','produce_sn'])));
    }	
	
	// 修改备注
    public function editRemark(){
		return $this->getJson(ErpOrderProduceLogic::goEditRemark($this->request->param('id'),$this->request->param('remark')));
    }	
	
	// 进度统计
    public function saleSchedule(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderProduceLogic::getSchedule($this->request->param(),$this->request->param('limit')));
        }
		return $this->fetch('',['admins' => ErpOrderProduceLogic::getAdmins(),'region_type'=>RegionTypeEnum::getDesc()]);
    }	
	
	//发外采购
    public function purchase(){
        return $this->getJson(ErpOrderProduceLogic::goPurchase($this->request->param('ids')));
    }
	
	//审批
    public function approve(){
        return $this->getJson(ErpOrderProduceLogic::goApprove($this->request->param('ids')));
    }	
	
	//计划下达
    public function plan(){
		return $this->getJson(ErpMaterialPlanLogic::goProductPlan($this->request->param('id'),$this->request->param('material_ids'))); 
	}	
	
	// 申请采购
    public function purchaseApply(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpPurchaseApplyLogic::goMaterialAdd($this->request->only(['supplier_id','apply_date','remark','material','username'=>session('admin.username'),'data_type'=>ErpPurchaseApplyEnum::DATA_TYPE_PLAN])));
        }
		return $this->fetch('purchase_apply',['list'=>ErpOrderProduceLogic::getBom($this->request->param('id'),0,0,$this->request->param('material_ids')),'supplier' => ErpSupplierLogic::getAll()]);
    }	

	//取消生产
    public function cancelProduce(){
        return $this->getJson(ErpOrderProduceLogic::goCancelProduce($this->request->param('ids')));
    }	
	
	//关联产品
    public function produceByEnter(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderProduceLogic::getSchedule($this->request->param(),$this->request->param('limit')));
        }
		return $this->fetch('',['data' => ErpOrderProduceLogic::getProduceByEnter($this->request->param('enter_material_id'))]);
    }

	//成品发货汇总
    public function stat(){
        return $this->fetch('',ErpOrderProduceLogic::getStat($this->request->param('region_type',1),$this->request->param('year')));
    }

	//成品发货统计
    public function monthStat(){
		if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderProduceLogic::getList($this->request->param(),10000));
        }
		$region_type 	= $this->request->param('region_type',1);
		$month 			= $this->request->param('month',date('n'));
		$year 			= $this->request->param('year',date('Y'));
		$finish_date 	= date($year.'-'.sprintf("%02d",$month).'-01').'至'.date($year.'-'.sprintf("%02d",$month).'-'.date('t',strtotime(date($year.'-'.sprintf("%02d",$month).'-01'))));
        return $this->fetch('',['region_type'=>$region_type,'month'=>$month,'year'=>$year,'finish_date'=>$finish_date]);
    }
	
	
	//代理商卖机情况
    public function supplierStat(){
        return $this->fetch('',ErpOrderProduceLogic::getSupplierStat($this->request->param('region',''),$this->request->param('year',''),$this->request->param('month','')));
    }
	
	//省份卖机情况
    public function regionStat(){
        return $this->fetch('',ErpOrderProduceLogic::getRegionStat($this->request->param('year','')));
    }	
	
	
	//不合格/返工评审处理单详情
	public function reworkDetail(){
       
		return $this->fetch('',['model' => ErpOrderProduceRework::where('id',$this->request->param('id'))->find()]);
    }
	
	// 修改产品序列号
    public function editFinishSn(){
        return $this->getJson(ErpOrderProduceLogic::goEditFinishSn($this->request->only(['id','produce_finish_sn'])));
    }


	// 查看产品档案生产过程及检验记录
    public function report(){
		if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderProduceLogic::goSaveReport($this->request->param('id/d'),$this->request->param('data/a')));
		}
		return $this->fetch('index@erp_order_produce/product_check',ErpOrderProduceLogic::getProduceReport($this->request->param('id'),$this->request->param('process_id')));
	}

	
	// 修改UDI标签
    public function editUdi(){
		return $this->getJson(ErpOrderProduceLogic::goEditUdi($this->request->param('id'),$this->request->param('udi')));
    }		
	
	// 报表
    public function statement(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderProduceLogic::getStatement($this->request->param(),$this->request->param('limit')));
        }
		return $this->fetch('',['region_type'=>RegionTypeEnum::getDesc()]);
    }
	
}
