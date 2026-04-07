<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\{ErpPurchaseOrderLogic,ErpSupplierLogic,ErpWarehouseLogic,ErpPurchaseApplyLogic,ErpSupplierProcessLogic,ErpMaterialLogic};
use app\common\enum\{ErpPurchaseOrderEnum,ErpPurchaseApplyEnum};
use app\common\model\ErpSupplierProcessBom;
class PurchaseOrder extends \app\admin\controller\Base
{

    // 物料采购单列表
    public function material(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpPurchaseOrderLogic::getMaterial($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['supplier' => ErpSupplierLogic::getAll(),'status' => ErpPurchaseOrderEnum::getStatusDesc(),'default_status' => ErpPurchaseOrderEnum::STATUS_NO]);
    }

    // 物料采购单数据
    public function materialData(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpPurchaseOrderLogic::getMaterialData($this->request->param(),$this->request->param('limit')));
        }
    }

    // 添加物料采购单
    public function materialAdd(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpPurchaseOrderLogic::goMaterialAdd($this->request->only(['material_category','supplier_id','order_date','delivery_date','remark','material','follow_admin_id','apply_ids','type'=>ErpPurchaseApplyEnum::TYPE_MATERIAL])));
        }
		$tmp 	= ErpPurchaseApplyLogic::getMaterial($this->request->only(['ids']),10000)['data'];
		$data 	= [];
		foreach($tmp as $vo){
			if(empty($data[$vo['data_id']])){
				$vo['ids'] 					= [$vo['id']];
				$data[$vo['data_id']] 		= $vo->toArray();
				$data[$vo['data_id']]['ids']= [];
				$data[$vo['data_id']]['ids'][] 		= $vo['id'];
			}else{
				$data[$vo['data_id']]['apply_num'] 	= $data[$vo['data_id']]['apply_num'] + $vo['apply_num'];
				$data[$vo['data_id']]['ids'][] 		= $vo['id'];
			}
		}
		return $this->fetch('',['supplier_id'=>$this->request->param('supplier_id'),'data'=>$data,'supplier'=>ErpSupplierLogic::getAll(),'admins'=>ErpPurchaseOrderLogic::getAdmins()]);
    }

    // 编辑物料采购单
    public function materialEdit(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpPurchaseOrderLogic::goEdit($this->request->only(['id','material_category','supplier_id','order_date','delivery_date','remark','material','follow_admin_id'])));
        }
        return $this->fetch('',['supplier'=>ErpSupplierLogic::getAll(),'admins'=>ErpPurchaseOrderLogic::getAdmins(),'model' => ErpPurchaseOrderLogic::getOne($this->request->param('id/d'))]);
    }

    // 删除物料采购单
    public function materialRemove(){
        return $this->getJson(ErpPurchaseOrderLogic::goMaterialRemove($this->request->param('id')));
    }	
	
    // 审批采购单
    public function check(){
        return $this->getJson(ErpPurchaseOrderLogic::goCheck($this->request->param('id')));
    }	
	
	// 反审采购单
    public function recheck(){
        return $this->getJson(ErpPurchaseOrderLogic::goReCheck($this->request->param('id')));
    }
	
	// 发供应商审核
    public function send(){
        return $this->getJson(ErpPurchaseOrderLogic::goSend($this->request->param('id')));
    }
	
	// 作废采购单
    public function cancel(){
        return $this->getJson(ErpPurchaseOrderLogic::goRemove($this->request->param('id')));
    }	
	
	
	// 变更日志
	public function log(){
        return $this->fetch('',['log' => ErpPurchaseOrderLogic::getLog($this->request->param('id/d'))]);
    }

	// 复制采购单
    public function copy(){
		return $this->getJson(ErpPurchaseOrderLogic::goCopy($this->request->param('id')));
    }
	
    // 物料采购跟踪
    public function materialFollow(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpPurchaseOrderLogic::getMaterialFollow($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['admins' => ErpPurchaseOrderLogic::getAdmins(),'supplier' => ErpSupplierLogic::getAll(),'status' => ErpPurchaseOrderEnum::getStatusDesc(),'default_status' => ErpPurchaseOrderEnum::STATUS_NO]);
    }	
	
	// 反馈
    public function feedback(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpPurchaseOrderLogic::goFeedback($this->request->post(['order_id','content'])));
        }else{
			return $this->fetch('',['id'=>$this->request->param('id/d'),'list' => ErpPurchaseOrderLogic::getFeedback($this->request->param('id/d'))]);
		}
    }
	
	// 修改超期原因
    public function editOverdueReason(){
		return $this->getJson(ErpPurchaseOrderLogic::goEditOverdueReason($this->request->param('id'),$this->request->param('overdue_reason')));
    }
	
	// 作废采购单
    public function remove(){
		return $this->getJson(ErpPurchaseOrderLogic::goRemove($this->request->param('id')));
    }
	
	// 作废采购单选项
    public function removeData(){
		return $this->getJson(ErpPurchaseOrderLogic::goRemoveData($this->request->param('id')));
    }	
	
	// 采购入库
    public function warehous($warehous_type=1){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpPurchaseOrderLogic::goWarehous($warehous_type,$this->request->only(['supplier_id','batch_number','purchase_date','material_type','type','order_id','remark','material','stock_date']),session('admin.username'),$this->request->param('check_status/d',1)));
        }else{
			if($warehous_type == 2){
				$warehouse 	= ErpWarehouseLogic::getAll(['type'=>4]);
				$data 		= ErpPurchaseOrderLogic::getProductData($this->request->only(['ids']),10000)['data'];
			}else if($warehous_type == 3){
				$warehouse 	= ErpWarehouseLogic::getAll(['type'=>'1,2']);
				$data 		= ErpPurchaseOrderLogic::getOutsourcingData($this->request->only(['ids']),10000)['data'];
			}else{
				$warehouse 	= ErpWarehouseLogic::getAll(['type'=>'1,2']);
				$data 		= ErpPurchaseOrderLogic::getMaterialData($this->request->only(['ids']),10000)['data'];
			}
			return $this->fetch('',['supplier_id'=>$this->request->param('supplier_id'),'warehous_type'=>$warehous_type,'supplier'=>ErpSupplierLogic::getAll(),'warehouse'=>$warehouse,'list' =>$data,'model' => ErpPurchaseOrderLogic::getOne($this->request->param('id/d'))]);
		}
    }
	
	// 成品采购单列表
    public function product(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpPurchaseOrderLogic::getProduct($this->request->param(),$this->request->param('limit')));
        }
		$order_id 	= $this->request->param('order_id');
		$data 		= [];
		if($order_id){
			$data 	= ErpPurchaseOrderLogic::getProductData($this->request->param(),$this->request->param('limit'));
		}
        return $this->fetch('',['order_id'=>$order_id,'data'=>$data,'supplier' => ErpSupplierLogic::getAll(),'status' => ErpPurchaseOrderEnum::getStatusDesc(),'default_status' => ErpPurchaseOrderEnum::STATUS_NO]);
    }

    // 成品采购单数据
    public function productData($from=1){
		$order_id 	= $this->request->param('order_id');
		$data 		= [];
		if($order_id){
			$data 	= ErpPurchaseOrderLogic::getProductData($this->request->param(),$this->request->param('limit'));
		}
        return $this->fetch('',['order_id'=>$order_id,'data'=>$data,'from'=>$from]);
    }

	// 添加成品采购单
    public function productAdd(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpPurchaseOrderLogic::goProductAdd($this->request->only(['supplier_id','order_date','delivery_date','remark','material','follow_admin_id','apply_ids','type'=>ErpPurchaseApplyEnum::TYPE_PRODUCT])));
        }
		$data 	= ErpPurchaseApplyLogic::getProduct($this->request->param(),10000)['data'];
		return $this->fetch('',['data'=>$data,'supplier'=>ErpSupplierLogic::getAll(),'admins'=>ErpPurchaseOrderLogic::getAdmins()]);
    }
	
    // 编辑成品采购单
    public function productEdit(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpPurchaseOrderLogic::goEdit($this->request->only(['id','supplier_id','order_date','delivery_date','remark','material','follow_admin_id'])));
        }
        return $this->fetch('',['data'=>ErpPurchaseOrderLogic::getProductData(['order_id'=>$this->request->param('id/d')],10000),'supplier'=>ErpSupplierLogic::getAll(),'admins'=>ErpPurchaseOrderLogic::getAdmins(),'model' => ErpPurchaseOrderLogic::getOne($this->request->param('id/d'))]);
    }	
	
	// 成品采购跟踪
    public function productFollow(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpPurchaseOrderLogic::getProductFollow($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['admins' => ErpPurchaseOrderLogic::getAdmins(),'supplier' => ErpSupplierLogic::getAll()]);
    }
	
	
	// 添加委外单
    public function outsourcingAdd(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpPurchaseOrderLogic::goOutsourcingAdd($this->request->only(['process_id','supplier_id','order_date','delivery_date','remark','material','follow_admin_id','apply_ids','type'=>3]),$this->request->param('out_material')));
        }
		$tmp 	= ErpPurchaseApplyLogic::getMaterial($this->request->only(['ids']),10000)['data'];
		
		$data 	= [];
		$related_material_id = [];
		foreach($tmp as $vo){
			if(empty($data[$vo['data_id']])){
				$vo 						= $vo->toArray();
				$vo['ids'] 					= [$vo['id']];
				$vo['bom']					= ErpSupplierProcessBom::alias('a')
				->join('erp_material b','a.related_material_id = b.id','LEFT')
				->field('a.*,b.sn,b.name,b.type as material_type')
				->where('a.material_id',$vo['data_id'])->where('a.process_id',$vo['process_id'])->select()->toArray();

				if($vo['bom']){
					$related_material_id[] 	= implode(',',array_column($vo['bom'],'related_material_id'));
					$vo['rowspan']			= count($vo['bom']);
				}else{
					$vo['rowspan']			= 1;
				}
				$data[$vo['data_id']] 		= $vo;
				
			}else{
				$data[$vo['data_id']]['apply_num'] 	= $data[$vo['data_id']]['apply_num'] + $vo['apply_num'];
				$data[$vo['data_id']]['ids'][] 		= $vo['id'];
			}
		}
		
		foreach($data as $k1=>$vo){
			foreach($vo['bom'] as $k2=>$item){
				$data[$k1]['bom'][$k2]['theory_num']= $item['num']*$vo['apply_num'];
				$data[$k1]['bom'][$k2]['stock_num'] = ceil($item['num']*$vo['apply_num']);
				$data[$k1]['bom'][$k2]['loss_rate']	= bcdiv((string)($data[$k1]['bom'][$k2]['stock_num'] - $data[$k1]['bom'][$k2]['theory_num']),(string)$data[$k1]['bom'][$k2]['stock_num'],2);	
			}
		}
		$stock 		= [];
		if($related_material_id){
			$tmp 	= ErpMaterialLogic::getMaterialStock(['ids'=>implode(',',$related_material_id)],1000)['data'];
			foreach($tmp as $vo){
				$stock[$vo['id']] = $vo;
			}
		}
		return $this->fetch('',['data'=>$data,'stock'=>$stock,'supplier'=>ErpSupplierLogic::getAll(),'admins'=>ErpPurchaseOrderLogic::getAdmins(),'supplier_id'=>$this->request->param('supplier_id'),'process' => ErpSupplierProcessLogic::getAll()]);
    }	
	
    // 委外审批
    public function outsourcing(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpPurchaseOrderLogic::getOutsourcing($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['supplier' => ErpSupplierLogic::getAll(),'status' => ErpPurchaseOrderEnum::getStatusDesc(),'default_status' => ErpPurchaseOrderEnum::STATUS_NO]);
    }
	

    // 物料委外单数据
    public function outsourcingData(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpPurchaseOrderLogic::getOutsourcingData($this->request->param(),$this->request->param('limit')));
        }
    }	
	
	// 委外跟踪
    public function outsourcingFollow(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpPurchaseOrderLogic::getOutsourcingFollow($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['admins' => ErpPurchaseOrderLogic::getAdmins(),'supplier' => ErpSupplierLogic::getAll(),'status' => ErpPurchaseOrderEnum::getStatusDesc(),'default_status' => ErpPurchaseOrderEnum::STATUS_NO]);
    }	
	
	// 再次委外
    public function outsourcingReapply(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpPurchaseOrderLogic::goReApply($this->request->only(['process_id','type','supplier_id','apply_date','remark','material','username'=>session('admin.username'),'data_type'=>ErpPurchaseApplyEnum::DATA_TYPE_REAPPLY])));
        }else{
			$data 		= ErpPurchaseOrderLogic::getOutsourcingData($this->request->only(['ids']),10000)['data'];
			return $this->fetch('',['process_id'=>$this->request->param('process_id'),'supplier_id'=>$this->request->param('supplier_id'),'processing_way'=>$this->request->param('type',2),'data' => $data,'supplier' => ErpSupplierLogic::getAll(),'process' => ErpSupplierProcessLogic::getAll()]);
		}
    }	
	
	
	
	// 报表-物料采购订单
    public function materialReport(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpPurchaseOrderLogic::getMaterial($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['supplier' => ErpSupplierLogic::getAll(),'status' => ErpPurchaseOrderEnum::getStatusDesc(),'default_status' => ErpPurchaseOrderEnum::STATUS_NO]);
    }
	
}
