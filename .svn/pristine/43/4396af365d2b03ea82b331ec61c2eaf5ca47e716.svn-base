<?php
declare (strict_types = 1);
namespace app\admin\controller;
use app\common\enum\{ErpMaterialStockEnum,ErpMaterialEnterMaterialEnum};
use app\admin\logic\ErpMaterialOutLogic;
use app\admin\logic\ErpMaterialEnterLogic;
use app\admin\logic\ErpMaterialCheckLogic;
use app\admin\logic\ErpOrderLogic;
use app\admin\logic\ErpMaterialLogic;
use app\admin\logic\ErpOrderProduceProcessLogic;
use app\admin\logic\{ErpMaterialDiscardLogic,ErpMaterialTreeLogic,ErpProcessLogic,ErpProductLogic,ErpFollowLogic,ErpProjectLogic,ErpSupplierLogic,ErpOrderProduceLogic,RegionLogic};
use think\facade\Db;
use think\facade\Cookie;
use app\common\model\{ErpMaterialEnterMaterial,ErpMaterialEnter,ErpOrderProduce};

class Common extends Base{
	protected $middleware = ['\app\admin\middleware\AdminCheck'];
   
   
    public function warehouseStat($material_type){
		$data 					= [];
		$data['out_count']		= ErpMaterialOutLogic::getCount(['material_type'=>$material_type,'status'=>ErpMaterialStockEnum::STATUS_HANDLE]);
		$data['enter_count']	= ErpMaterialEnterLogic::getCount(['material_type'=>$material_type,'status'=>ErpMaterialStockEnum::STATUS_HANDLE,'no_type'=>ErpMaterialStockEnum::TYPE_ENTER_WORKSHOP]);
		$data['check_count']	= ErpMaterialCheckLogic::getCount(['material_type'=>$material_type,'status'=>ErpMaterialStockEnum::STATUS_HANDLE]);
		$data['discard_count']	= ErpMaterialDiscardLogic::getCount(['material_type'=>$material_type,'status'=>ErpMaterialStockEnum::STATUS_HANDLE]);
		$data['enter_back_count']	= ErpMaterialEnterLogic::getCount(['material_type'=>$material_type,'status'=>ErpMaterialStockEnum::STATUS_HANDLE,'type'=>ErpMaterialStockEnum::TYPE_ENTER_WORKSHOP]);
		return $this->getJson(['code'=>200,'data'=>$data]);
    }

    public function orderStat($type=1){
		$data 					= ErpOrderLogic::getOrderStatusCount($type);
		return $this->getJson(['code'=>200,'data'=>$data]);
    }
	
	
    public function orderTechnicianStat(){
		$data 					= ErpOrderLogic::getTechnicianStatusCount();
		return $this->getJson(['code'=>200,'data'=>$data]);
    }

    public function orderSalesmanStat(){
		$data 					= ErpOrderLogic::getSalesmanStatusCount();
		return $this->getJson(['code'=>200,'data'=>$data]);
    }
	
    public function produceProcessErrorCount(){
		return $this->getJson(['code'=>200,'data'=>['count'=>ErpOrderProduceProcessLogic::getErrorCount()]]);
    }	
	
    public function order(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderLogic::getSelect($this->request->param(),$this->request->param('limit')));
        }
    }
	
    public function material(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialLogic::getSelect($this->request->param(),$this->request->param('limit')));
        }else{
			$type = $this->request->param('type/d',0);
			return $this->fetch('',['supplier'=>ErpSupplierLogic::getAll(),'tree' => ErpMaterialTreeLogic::tree($type,false),'type'=>$type]);
		}
    }	
	
    // 物料库存
    public function materialStock(){
        if ($this->request->isAjax()) {
			return json(ErpMaterialLogic::getMaterialStock($this->request->param(),$this->request->param('limit')));
        }else{
			$type = $this->request->param('type/d');
			return $this->fetch('',['tree' => ErpMaterialTreeLogic::tree($type,false),'type'=>$type]);
		}
    }
	
    // 物料库存
    public function checkMaterialStock(){
        if ($this->request->isAjax()) {
			return json(ErpMaterialLogic::getCheckMaterialStock($this->request->param(),$this->request->param('limit')));
        }else{
			$type = $this->request->param('type/d');
			return $this->fetch('',['supplier'=>ErpSupplierLogic::getAll(),'tree' => ErpMaterialTreeLogic::tree($type,false),'type'=>$type]);
		}
    }	
	
	
	public function setColWidth(){
		$field 	= $this->request->param('field');
		$width 	= $this->request->param('width/d');
		$path 	= $this->request->param('path');
		$key 	= md5($path.$field);
		Cookie::forever($key, $width);
		return $this->getJson(['code'=>200]);
	}
	
	
	public function printAdd($data){
		$data			= !is_array($data)?explode(',',$data):$data;
		$key 			= rand_string();
		$insert 		= [];
		foreach($data as $vo){
			$insert[] 	= ['data_id'=>$vo,'data_key'=>$key];
		}
		Db::name('erp_print')->insertAll($insert);
		return json(['id'=>$key]);
	}
	
	public function warehouseStock(){
        if ($this->request->isAjax()) {
			return json(ErpMaterialLogic::getWarehouseStock($this->request->param('material_id')));
        }
    }
	
    public function processWage(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpProcessLogic::getList($this->request->param(),$this->request->param('limit')));
        }
    }

    // 产品
    public function product(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpProductLogic::getList($this->request->param(),$this->request->param('limit')));
        }
    }

    public function follow(){
        if($this->request->isAjax()) {
			return $this->getJson(ErpFollowLogic::getList($this->request->only(['name','create_time']),$this->request->param('limit')));
        }
        return $this->fetch('');
    }	
	
	// 列表
    public function project(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpProjectLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['bill_type'=>ErpProductBomLogic::getBillType()]);
    }
	
	public function orderProduceIds(){
		return $this->getJson(['code'=>200,'data'=>ErpOrderProduce::alias('a')->join('erp_order b','a.order_id = b.id','LEFT')->where(ErpOrderProduceLogic::getListMap($this->request->param()))->column('a.id')]);
    }	
	
	public function region(){
        if ($this->request->isAjax()) {
			return $this->getJson(RegionLogic::getList($this->request->param(),$this->request->param('limit')));
        }
    }
	
	
	public function paramCache(){
		$key = rand_string();
		cache($key,$this->request->post(),3600);
		return json(['key'=>$key]);
	}
}
