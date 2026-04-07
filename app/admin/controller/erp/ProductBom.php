<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\ErpProductBomLogic;
use app\admin\logic\ErpMaterialLogic;

class ProductBom extends \app\admin\controller\Base
{

    // 列表
    public function list(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpProductBomLogic::getList($this->request->only(['product_id','data_type','project_id','keyword']),$this->request->param('limit')));
        }
        return $this->fetch('',[]);
    }

    // 添加
    public function add(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpProductBomLogic::goAdd($this->request->only(['product_id','material_id','color_follow','bill_type','can_replace','num','data_type','project_id'])));
        }
        return $this->fetch('',['bill_type'=>ErpProductBomLogic::getBillType(),'project_id'=>$this->request->param('project_id',0),'material_type'=>$this->request->param('material_type',''),'data_type'=>$this->request->param('data_type',''),'product_id'=>$this->request->param('product_id',''),'table'=>$this->request->param('table','')]);
    }

    // 物料列表
    public function material(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialLogic::getList($this->request->param(),$this->request->param('limit')));
        }
    }

    // 编辑
    public function edit(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpProductBomLogic::goEdit($this->request->only(['id','color_follow','bill_type','can_replace','num']))); 
        }
    }

    // 删除
    public function remove(){
        return $this->getJson(ErpProductBomLogic::goRemove($this->request->only(['ids'])));
    }	


	// 导入
    public function import(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpProductBomLogic::goImport($this->request->param('excel')));
        }
        return $this->fetch();
    }


    // 回收站
    public function recycle(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpProductBomLogic::getRecycle($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch();
    }
	
	// 恢复/删除回收站
	public function batchRecycle(){
		return $this->getJson(ErpProductBomLogic::goRecycle($this->request->param('ids'),$this->request->param('type')));
    }
    
}
