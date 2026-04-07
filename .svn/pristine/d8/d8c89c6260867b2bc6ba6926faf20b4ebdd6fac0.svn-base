<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\{ErpProcessLogic,ErpProductLogic};
use app\common\model\ErpUser;

class Process extends  \app\admin\controller\Base{

    // 列表
    public function list(){
        if($this->request->isAjax()) {
			return $this->getJson(ErpProcessLogic::getList($this->request->only(['keyword','create_time']),$this->request->param('limit')));
        }
        return $this->fetch('');
    }

    // 添加
    public function add(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpProcessLogic::goAdd($this->request->only(['is_inspect','is_default','monitor','type','name','user_id','sn','status','sort','is_end','follow_id','description']),$this->request->param('wage/a'),$this->request->param('material/a',[])));
        }
        return $this->fetch('',['user'=>ErpUser::where('status',1)->select()]);
    }

    // 编辑
    public function edit(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpProcessLogic::goEdit($this->request->only(['is_inspect','is_default','monitor','type','id','name','user_id','sn','status','sort','is_end','follow_id','description']),$this->request->param('wage/a'),$this->request->param('material/a',[])));
        }
        return $this->fetch('',['wage'=>ErpProcessLogic::getWage($this->request->param('id/d')),'material'=>ErpProcessLogic::getMaterial($this->request->param('id/d')),'user'=>ErpUser::where('status',1)->select(),'model' => ErpProcessLogic::getOne($this->request->param('id/d'))]);
    }

    // 删除
    public function remove(){
		return $this->getJson(ErpProcessLogic::goRemove($this->request->post('ids')));
    }

    // 回收站
    public function recycle(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpProcessLogic::getRecycle($this->request->only(['keyword','channel','create_time']),$this->request->param('limit')));
        }
        return $this->fetch();
    }

	//恢复/删除回收站
    public function batchRecycle(){
		return $this->getJson(ErpProcessLogic::batchRecycle($this->request->param('ids'),$this->request->param('type')));
    }



}
