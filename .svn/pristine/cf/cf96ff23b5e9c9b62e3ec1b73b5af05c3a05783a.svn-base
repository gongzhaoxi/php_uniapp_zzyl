<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\ErpOrderAftersaleLogic;
use app\admin\logic\ErpMaterialLogic;
use app\admin\logic\ErpOrderLogic;
use app\common\enum\ErpOrderEnum;

class OrderAftersale extends \app\admin\controller\Base
{
    // 计划-列表
    public function list(){
		if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderLogic::getList(array_merge($this->request->param(),['salesman_approve'=>1,'data_type'=>ErpOrderEnum::DATA_TYPE_2]),$this->request->param('limit')));
        }
        return $this->fetch('',['query'=>$this->request->param(['produce_status'=>10]),'order_status'=>ErpOrderLogic::getOrderStatusCount(ErpOrderEnum::DATA_TYPE_2),'shipping_status'=>ErpOrderEnum::getShippingStatusDesc()]);
    }

    //计划-处理完成
    public function check($id){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpOrderAftersaleLogic::goCheck($id)); 
        }
        return $this->fetch('',['id'=>$id,'bill_type'=>get_dict_data('product_bill_type'),'produce_type'=>get_dict_data('material_produce_type'),'model' => ErpOrderAftersaleLogic::getCheck($id)]);
    }

    //计划-领料出库
    public function out(){
		return $this->getJson(ErpOrderAftersaleLogic::goOut($this->request->param('id'),$this->request->param('material_ids'))); 
	}

    //计划-已处理售后详情
    public function view($id){
        return $this->fetch('',['id'=>$id,'bill_type'=>get_dict_data('product_bill_type'),'produce_type'=>get_dict_data('material_produce_type'),'model' => ErpOrderAftersaleLogic::getCheck($id)]);
    }	

    // 销售-添加
    public function add(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpOrderAftersaleLogic::goAdd($this->request->only(['remark','order_id','material_id','material_sn','material_name','material_category','material_num','material_type'])));
        }
        return $this->fetch('',['order_id'=>$this->request->param('order_id')]);
    }

    // 销售-编辑
    public function edit(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpOrderAftersaleLogic::goEdit($this->request->only(['remark','id','material_num'])));
        }
        return $this->fetch('',['model' => ErpOrderAftersaleLogic::getOne($this->request->param('id/d'))]);
    }

    // 销售-删除
    public function remove(){
		return $this->getJson(ErpOrderAftersaleLogic::goRemove($this->request->param('id/d')));
    }
	
	//财务-售后配件统计
    public function stat(){
		if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderAftersaleLogic::getList($this->request->param(),10000));
        }
		$region_type 	= $this->request->param('region_type',1);
		$month 			= $this->request->param('month',date('n'));
		$year 			= $this->request->param('year',date('Y'));
		$create_time 	= date($year.'-'.sprintf("%02d",$month).'-01').'至'.date($year.'-'.sprintf("%02d",$month).'-'.date('t',strtotime(date($year.'-'.sprintf("%02d",$month).'-01'))));
        return $this->fetch('',['region_type'=>$region_type,'month'=>$month,'year'=>$year,'create_time'=>$create_time]);
    }
    
}
