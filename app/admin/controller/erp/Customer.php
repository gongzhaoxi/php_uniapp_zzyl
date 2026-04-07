<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\{ErpCustomerLogic,RegionLogic};
use app\common\enum\RegionTypeEnum;

class Customer extends \app\admin\controller\Base
{

    // 列表
    public function list(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpCustomerLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['region_type'=>RegionTypeEnum::getDesc()]);
    }

    // 添加
    public function add(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpCustomerLogic::goAdd($this->request->param()));
        }
        return $this->fetch('',['admins'=>ErpCustomerLogic::getAdmins(),'tree' => RegionLogic::tree(false,'0,1,2,3'),'region_type'=>RegionTypeEnum::getDesc()]);
    }

    // 编辑
    public function edit(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpCustomerLogic::goEdit($this->request->param())); 
        }
        return $this->fetch('',['admins'=>ErpCustomerLogic::getAdmins(),'tree' => RegionLogic::tree(false,'0,1,2,3'),'region_type'=>RegionTypeEnum::getDesc(),'model' => ErpCustomerLogic::getOne($this->request->param('id/d'))]);
    }

    // 删除
    public function remove(){
        return $this->getJson(ErpCustomerLogic::goRemove($this->request->only(['ids'])));
    }	

    // 回收站
    public function recycle(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpCustomerLogic::getRecycle($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch();
    }
	
	// 恢复/删除回收站
	public function batchRecycle(){
		return $this->getJson(ErpCustomerLogic::goRecycle($this->request->param('ids'),$this->request->param('type')));
    }
	
	public function import(){
        if($this->request->isPost()){
			return $this->getJson(ErpCustomerLogic::goImport($this->request->param('excel')));
        }else{
			return $this->fetch('');
		}
    }
}
