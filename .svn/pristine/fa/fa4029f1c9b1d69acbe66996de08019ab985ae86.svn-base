<?php
declare (strict_types = 1);
namespace app\supplier\controller;
use app\supplier\logic\{ErpMaterialDiscardLogic};
use app\common\enum\ErpMaterialDiscardEnum;
class ErpMaterialDiscard extends \app\supplier\controller\Base
{

    public function index(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialDiscardLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',[]);
    }

    public function material(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialDiscardLogic::getMaterial($this->request->param(),$this->request->param('limit')));
        }
    }	
	
    public function confirm(){
        return $this->getJson(ErpMaterialDiscardLogic::goConfirm($this->request->param('id')));
    }	
	
	// 反馈
    public function feedback(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialDiscardLogic::goFeedback($this->request->post(['stock_id','content'])));
        }else{
			return $this->fetch('',['id'=>$this->request->param('id/d'),'list' => ErpMaterialDiscardLogic::getFeedback($this->request->param('id/d'))]);
		}
    }
}
