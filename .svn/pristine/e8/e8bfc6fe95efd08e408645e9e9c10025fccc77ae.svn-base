<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\ErpNoticeLogic;
use app\common\enum\ErpNoticeEnum;

class Notice extends \app\admin\controller\Base
{
    // 列表
    public function list(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpNoticeLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['status'=>ErpNoticeEnum::getStatusDesc()]);
    }

    // 添加
    public function add(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpNoticeLogic::goAdd($this->request->only(['auditing_admin_id','notice_admin_id','content','file'])));
        }
        return $this->fetch('',['admins'=>ErpNoticeLogic::getAdmins()]);
    }

    // 查看
    public function edit(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpNoticeLogic::goEdit($this->request->only(['id','auditing_admin_id','notice_admin_id','content','file'])));
        }
        return $this->fetch('',['admins'=>ErpNoticeLogic::getAdmins(),'model' => ErpNoticeLogic::getOne($this->request->param('id/d'))]);
    }

    // 删除
    public function remove(){
        return $this->getJson(ErpNoticeLogic::goRemove($this->request->only(['ids'])));
    }

    // 审批
    public function pass(){
		return $this->getJson(ErpNoticeLogic::goAuditing($this->request->param('id'),ErpNoticeEnum::STATUS_AUDITED));
    }
	
	// 反审
    public function reject(){
		return $this->getJson(ErpNoticeLogic::goAuditing($this->request->param('id'),ErpNoticeEnum::STATUS_REJECT));
    }
	
	// 已阅
    public function read(){
		return $this->getJson(ErpNoticeLogic::goRead($this->request->param('id'),ErpNoticeEnum::STATUS_REJECT));
    }	
	
}
