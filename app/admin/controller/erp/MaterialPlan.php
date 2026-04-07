<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\ErpMaterialPlanLogic;
use app\admin\logic\ErpMaterialLogic;
use app\common\enum\ErpMaterialPlanEnum;

class MaterialPlan extends \app\admin\controller\Base
{

    // 列表
    public function list(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialPlanLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['status'=>ErpMaterialPlanEnum::getStatusDesc()]);
    }

    // 添加
    public function add(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpMaterialPlanLogic::goAdd($this->request->only(['material'])));
        }
        return $this->fetch('',[]);
    }

    // 下达
    public function view($id){
		if ($this->request->isAjax()) {
            return $this->getJson(ErpMaterialPlanLogic::goStart($this->request->only(['id','start_date']))); 
        }
        return $this->fetch('',['id'=>$id,'category'=>ErpMaterialLogic::getCategory('material_partn'),'list' => ErpMaterialPlanLogic::getPlans($id)]);
    }

	// 撤回下达
    public function cancel(){
        return $this->getJson(ErpMaterialPlanLogic::goCancel($this->request->only(['ids'])));
    }

    // 入库
    public function warehouse(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialPlanLogic::goWarehouse($this->request->param('id'))); 
        }
    }
	
    // 删除
    public function remove(){
        return $this->getJson(ErpMaterialPlanLogic::goRemove($this->request->only(['ids'])));
    }	

    public function material(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialPlanLogic::getMaterial(array_merge($this->request->param(),[]),$this->request->param('limit')));
        }
    }
	
    public function viewMaterialBom($material_id,$num){
		if ($this->request->isAjax()) {
			return $this->getJson(['code'=>200,'data'=>['bom'=>ErpMaterialPlanLogic::getMaterialBom($material_id,$num)]]); 
		}else{
			return $this->fetch('',['category'=>ErpMaterialLogic::getCategory('material_partn'),'bom' => ErpMaterialPlanLogic::getMaterialBom($material_id,$num)]);
		}
    }
	
	public function productAnalyse(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialPlanLogic::goProductAnalyse($this->request->param('product'))); 
        }else{
			return $this->fetch('',[]);
		}
    }
	
	public function product(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialPlanLogic::getProduct(array_merge($this->request->param(),[]),$this->request->param('limit')));
        }
    }
	
	public function productBom(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialPlanLogic::getProductBom($this->request->param('product'))); 
        }
    }	
	
	// 进度统计
    public function schedule(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialPlanLogic::getList($this->request->param(),$this->request->param('limit')));
        }
		return $this->fetch('',[]);
    }
	
	// 进度详情
    public function scheduleDetail(){
		return $this->fetch('',ErpMaterialPlanLogic::getScheduleDetail($this->request->param('id')));
    }
	
	// 查阅随工单
    public function follow(){
		return $this->fetch('',ErpMaterialPlanLogic::getFollowDetail($this->request->param('id'),$this->request->param('process_id')));
    }
	
	// 随工单变更记录
    public function processLog(){
		return $this->fetch('',ErpMaterialPlanLogic::getFollowLog($this->request->param('id'),$this->request->param('process_id')));
    }
	
}
