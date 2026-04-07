<?php
declare (strict_types = 1);
namespace app\admin\controller\admin;
use think\facade\Db;
use app\admin\logic\AdminAdminLogic;

class Admin extends  \app\admin\controller\Base{

    // 列表
    public function index(){
        if($this->request->isAjax()) {
			return $this->getJson(AdminAdminLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch();
    }

    // 添加
    public function add(){
        if ($this->request->isAjax()) {
            return $this->getJson(AdminAdminLogic::goAdd($this->request->only(['username','nickname','password'])));
        }
        return $this->fetch();
    }

    // 编辑
    public function edit(){
        if ($this->request->isAjax()) {
            return $this->getJson(AdminAdminLogic::goEdit($this->request->only(['id','username','nickname','password'])));
        }
        return $this->fetch('',['model' => AdminAdminLogic::goFind($this->request->param('id/d'))]);
    }

    // 状态
    public function status($id){
        return $this->getJson(AdminAdminLogic::goStatus($this->request->post('status/d'),$id));
    }

    // 删除
    public function remove($id){
        return $this->getJson(AdminAdminLogic::goRemove($id));
    }

    // 批量删除
    public function batchRemove(){
        return $this->getJson(AdminAdminLogic::goBatchRemove($this->request->post('ids')));
    }

    // 用户分配角色
    public function role($id){
        if ($this->request->isAjax()) {
            return $this->getJson(AdminAdminLogic::goRole($this->request->post('roles'),$id));
        }
        return $this->fetch('',AdminAdminLogic::getRole($id));
    }

    // 用户分配直接权限
    public function permission($id){
        if ($this->request->isAjax()) {
            return $this->getJson(AdminAdminLogic::goPermission($this->request->post('permissions'),$id));
        }
        return $this->fetch('',AdminAdminLogic::getPermission($id));
    }

    // 回收站
    public function recycle(){
        if ($this->request->isAjax()) {
            return $this->getJson(AdminAdminLogic::getRecycle($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch();
    }

    public function batchRecycle(){
        return $this->getJson(AdminAdminLogic::batchRecycle($this->request->param('ids'),$this->request->param('type')));
    }

    // 用户日志
    public function log(){
        if ($this->request->isAjax()) {
            return $this->getJson(AdminAdminLogic::getLog($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch();
    }

    // 清空日志
    public function removeLog(){
        $desc = Db::name('admin_admin_log')->order('id','desc')->find();
        if($desc){
            Db::name('admin_admin_log')->where('id','<',$desc['id'])->delete(true);
        }else{
            Db::name('admin_admin_log')->delete(true);
        }
        return $this->getJson();
    }

}
