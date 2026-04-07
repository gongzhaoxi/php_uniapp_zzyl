<?php
declare (strict_types = 1);
namespace app\admin\controller\admin;
use app\admin\logic\AdminRoleLogic;

class Role extends \app\admin\controller\Base
{

    // 列表
    public function index(){
        if ($this->request->isAjax()) {
			return $this->getJson(AdminRoleLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch();
    }

    // 添加
    public function add(){
        if ($this->request->isAjax()) {
            return $this->getJson(AdminRoleLogic::goAdd($this->request->post()));
        }
        return $this->fetch();
    }

    // 编辑
    public function edit(){
        if ($this->request->isAjax()) {
            return $this->getJson(AdminRoleLogic::goEdit($this->request->param())); 
        }
        return $this->fetch('',['model' => AdminRoleLogic::goFind($this->request->param('id/d'))]);
    }

    // 删除
    public function remove($id){
        return $this->getJson(AdminRoleLogic::goRemove($id));
    }

    // 用户分配直接权限
    public function permission($id){
        if ($this->request->isAjax()) {
            return $this->getJson(AdminRoleLogic::goPermission($this->request->post('permissions'),$id));
        }
        return $this->fetch('',AdminRoleLogic::getPermission($id));
    }

    // 回收站
    public function recycle(){
        if ($this->request->isAjax()) {
            return $this->getJson(AdminRoleLogic::getRecycle($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch();
    }
	
	public function batchRecycle(){
        return $this->getJson(AdminAdminLogic::goRecycle($this->request->param('ids'),$this->request->param('type')));
    }
    
}
