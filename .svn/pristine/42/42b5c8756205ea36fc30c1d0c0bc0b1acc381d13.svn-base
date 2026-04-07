<?php
declare (strict_types = 1);
namespace app\admin\controller\admin;
use app\admin\logic\AdminPhotoLogic;

class Photo extends \app\admin\controller\Base
{

    // 列表
    public function index(){
        if ($this->request->isAjax()) {
            return $this->getJson(AdminPhotoLogic::getPath());
        }
        return $this->fetch();
    }

    // 创建文件夹
    public function add(){
        if ($this->request->isAjax()) {
            return $this->getJson(AdminPhotoLogic::goAdd());
        }
        return $this->fetch();
    }

    // 删除文件夹
    public function del($name){
        return $this->getJson(AdminPhotoLogic::goDel($name));
    }

    // 列表
    public function list(){
        return $this->getJson(AdminPhotoLogic::getList($this->request->param(),$this->request->param('limit')));
    }

    // 添加单图
    public function addPhoto($name){
        return $this->fetch('',['name'=>$name]);
    }

    // 添加多图
    public function addPhotos($name){
        return $this->fetch('',['name'=>$name]);
    }

    // 删除
    public function remove($id){
        return $this->getJson(AdminPhotoLogic::goRemove($id));
    }

    // 批量删除
    public function batchRemove(){
        return $this->getJson(AdminPhotoLogic::goBatchRemove($this->request->post('ids')));
    }
}
