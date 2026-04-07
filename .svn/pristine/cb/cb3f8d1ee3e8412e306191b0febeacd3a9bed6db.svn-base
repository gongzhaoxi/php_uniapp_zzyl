<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\{ErpOrderProductLogic,ErpProductStockLogic};
use app\admin\logic\ErpOrderLogic;
use app\admin\logic\ErpProductLogic;
use app\common\enum\RegionTypeEnum;

class OrderProduct extends \app\admin\controller\Base
{
    // 列表
    public function list(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderProductLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',[]);
    }

    // 添加
    public function add(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpOrderProductLogic::goAdd($this->request->only(['remark','order_id','color','product_id','product_sn','product_name','product_model','product_specs','product_num','currency','exchange_rates','tax_rate','product_price','total_price']),$this->request->only(['replace_bom_id','replace_bom_value','replace_bom_bill_type']),$this->request->param('change_project/a',[]),$this->request->param('add_project/a',[])));
        }
        return $this->fetch('',['region_type'=>$this->request->param('region_type',''),'color'=>get_dict_data('order_product_color'),'bill_type'=>get_dict_data('product_bill_type'),'category'=>get_dict_data('product_project_category')]);
    }


    // 列表
    public function product(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpProductLogic::getListWithBom($this->request->param(),$this->request->param('limit')));
        }
    }

    // 编辑
    public function edit(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpOrderProductLogic::goEdit($this->request->only(['remark','id','color','product_model','product_specs','product_num','currency','exchange_rates','tax_rate','product_price','total_price'])));
        }
        return $this->fetch('',['color'=>get_dict_data('order_product_color'),'bill_type'=>get_dict_data('product_bill_type'),'category'=>get_dict_data('product_project_category'),'model' => ErpOrderProductLogic::getOne($this->request->param('id/d'))]);
    }
	
    // 增加/编辑Bom
    public function saveBom(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderProductLogic::goSaveBom($this->request->param('id'),$this->request->only(['replace_bom_id','replace_bom_value','replace_bom_bill_type']),$this->request->param('change_project',[]),$this->request->param('add_project',[])));
		}
    }

    // 删除Bom
    public function removeBom(){
        return $this->getJson(ErpOrderProductLogic::goRemoveBom($this->request->param('id/d')));
    }

    // 删除
    public function remove(){
		return $this->getJson(ErpOrderProductLogic::goRemove($this->request->param('id/d')));
    }
	
	// 技术编辑
    public function technicianEdit(){
		if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderProductLogic::technicianEdit($this->request->only(['id','product_bom_id','replace_product_bom_id','bill_type','order_product_id','type','num','order_id','material_id'])));
		}
        return $this->fetch('',['color'=>get_dict_data('order_product_color'),'category'=>get_dict_data('product_project_category'),'bill_type'=>get_dict_data('product_bill_type'),'model' => ErpOrderProductLogic::getOne($this->request->param('id/d'))]);
    }
	
    // 复制
    public function copy(){
		return $this->getJson(ErpOrderProductLogic::goCopy($this->request->param('id/d')));
    }
	
	
	// 添加
    public function addFromReturned(){
        if ($this->request->isAjax()) {
			if($this->request->param('act/d') == 1){
				return $this->getJson(ErpProductStockLogic::getList($this->request->param(),$this->request->param('limit')));
			}else{
				return $this->getJson(ErpOrderProductLogic::goAddFromReturned($this->request->param('order_id/d'),$this->request->param('ids/a')));
			}
		}
        return $this->fetch('',['order_id'=>$this->request->param('order_id/d')]);
    }
	
    // 暂停
    public function pause(){
		return $this->getJson(ErpOrderProductLogic::goPause($this->request->param('id/d')));
    }	
	
    // 取消暂停
    public function cancelPause(){
		return $this->getJson(ErpOrderProductLogic::goCancelPause($this->request->param('ids')));
    }	
	
    
}
