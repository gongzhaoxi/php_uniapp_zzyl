<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\ErpProductProjectLogic;
use app\common\enum\ErpProductProjectEnum;

class ProductProject extends \app\admin\controller\Base
{
    // 列表
    public function list(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpProductProjectLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('');
    }

    // 添加
    public function add(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpProductProjectLogic::goAdd($this->request->param()));
        }
        return $this->fetch('',['product_id'=>$this->request->param('product_id',0),'add_type'=>$this->request->param('add_type',0),'category'=>get_dict_data('product_project_category'),'type'=>ErpProductProjectEnum::getTypeDesc()]);
    }

    // 编辑
    public function edit(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpProductProjectLogic::goEdit($this->request->param())); 
        }
        return $this->fetch('',['category'=>get_dict_data('product_project_category'),'type'=>ErpProductProjectEnum::getTypeDesc(),'model' => ErpProductProjectLogic::getOne($this->request->param('id/d'))]);
    }

    // 删除
    public function remove(){
        return $this->getJson(ErpProductProjectLogic::goRemove($this->request->only(['ids'])));
    }	

	// 复制
    public function copy(){
        return $this->getJson(ErpProductProjectLogic::goCopy($this->request->param('id')));
    }	
    
	// 导入
    public function import(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpProductProjectLogic::goImport($this->request->param('excel')));
        }
        return $this->fetch();
    }	
	
}
