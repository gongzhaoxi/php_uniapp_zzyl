<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\ErpProjectBomLogic;
use app\admin\logic\ErpMaterialLogic;

class ProjectBom extends \app\admin\controller\Base
{

    // 列表
    public function list(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpProjectBomLogic::getList($this->request->only(['data_type','project_id','keyword']),$this->request->param('limit')));
        }
        return $this->fetch('',[]);
    }

    // 添加
    public function add(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpProjectBomLogic::goAdd($this->request->only(['material_id','color_follow','bill_type','can_replace','num','data_type','project_id'])));
        }
        return $this->fetch('',['bill_type'=>ErpProjectBomLogic::getBillType(),'project_id'=>$this->request->param('project_id',0),'material_type'=>$this->request->param('material_type',''),'data_type'=>$this->request->param('data_type',''),'table'=>$this->request->param('table','')]);
    }

    // 编辑
    public function edit(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpProjectBomLogic::goEdit($this->request->only(['id','color_follow','bill_type','can_replace','num']))); 
        }
    }

    // 删除
    public function remove(){
        return $this->getJson(ErpProjectBomLogic::goRemove($this->request->only(['ids'])));
    }	

	// 导入
    public function import(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpProjectBomLogic::goImport($this->request->param('excel')));
        }
        return $this->fetch();
    }
}
