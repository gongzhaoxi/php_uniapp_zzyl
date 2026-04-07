<?php
declare (strict_types = 1);
namespace app\admin\controller\admin;
use app\admin\logic\AdminPermissionLogic;

class Permission extends \app\admin\controller\Base{

    // 列表
    public function index(){
        if ($this->request->isAjax()) {
            return $this->getJson(AdminPermissionLogic::getList());
        }
        return $this->fetch();
    }

    // 添加
    public function add(){
        if ($this->request->isAjax()) {
            return $this->getJson(AdminPermissionLogic::goAdd($this->request->param()));
        }
        return $this->fetch('',['permissions' => AdminPermissionLogic::permissions()]);
    }

    // 编辑
    public function edit(){
        if ($this->request->isAjax()) {
			return $this->getJson(AdminPermissionLogic::goEdit($this->request->param()));
        }
        return $this->fetch('',['model' => AdminPermissionLogic::goFind($this->request->param('id/d')),'permissions' => AdminPermissionLogic::permissions()]);
    }

    // 状态
    public function status($id){
        return $this->getJson(AdminPermissionLogic::goStatus($this->request->post('status'),$id));
    }

    // 删除
    public function remove($id,$type=''){
        return $this->getJson(AdminPermissionLogic::goRemove($id,$type));
    }
}

