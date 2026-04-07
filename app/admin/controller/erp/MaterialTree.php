<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\ErpMaterialTreeLogic;

class MaterialTree extends \app\admin\controller\Base{

    // 列表
    public function index($type=1){
        if ($this->request->isAjax()) {
            return json(ErpMaterialTreeLogic::getList($type));
        }
        return $this->fetch('',['type'=>$type]);
    }

    // 添加
    public function add($type){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpMaterialTreeLogic::goAdd($this->request->param()));
        }
        return $this->fetch('',['type'=>$type,'tree' => ErpMaterialTreeLogic::tree($type)]);
    }

    // 编辑
    public function edit(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialTreeLogic::goEdit($this->request->param()));
        }
		$model = ErpMaterialTreeLogic::goFind($this->request->param('id/d'));
        return $this->fetch('',['model' =>$model ,'tree' => ErpMaterialTreeLogic::tree($model['type'])]);
    }

    // 状态
    public function status($id){
        return $this->getJson(ErpMaterialTreeLogic::goStatus($this->request->post('status'),$id));
    }

    // 删除
    public function remove($id,$type=''){
        return $this->getJson(ErpMaterialTreeLogic::goRemove($id,$type));
    }
}

