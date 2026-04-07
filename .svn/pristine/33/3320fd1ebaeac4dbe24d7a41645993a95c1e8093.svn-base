<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\ErpFollowLogic;

class Follow extends  \app\admin\controller\Base{

    // 列表
    public function list(){
        if($this->request->isAjax()) {
			return $this->getJson(ErpFollowLogic::getList($this->request->only(['name','create_time']),$this->request->param('limit')));
        }
        return $this->fetch('');
    }

    // 添加
    public function add(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpFollowLogic::goAdd($this->request->only(['name','cid','code','status','iso','according','remark','address','product','process'])));
        }
        return $this->fetch('',['category' => get_dict_data('follow_type')]);
    }

    // 编辑
    public function edit(){
        if ($this->request->isAjax()) {
           return $this->getJson(ErpFollowLogic::goEdit($this->request->only(['id','name','cid','code','status','iso','according','remark','address','product','process'])));
        }
        return $this->fetch('',['category' => get_dict_data('follow_type'),'model' => ErpFollowLogic::getOne($this->request->param('id/d'))]);
    }

    // 删除
    public function remove(){
		return $this->getJson(ErpFollowLogic::goRemove($this->request->post('ids')));
    }

    // 回收站
    public function recycle(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpFollowLogic::getRecycle($this->request->only(['name','create_time']),$this->request->param('limit')));
        }
        return $this->fetch();
    }

	//恢复/删除回收站
    public function batchRecycle(){
		return $this->getJson(ErpFollowLogic::batchRecycle($this->request->param('ids'),$this->request->param('type')));
    }

}
