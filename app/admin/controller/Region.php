<?php
namespace app\admin\controller;
use app\admin\logic\{RegionLogic};

class Region extends  \app\admin\controller\Base{
    
	public function list(){
		if ($this->request->isAjax()) {
			return $this->getJson(RegionLogic::getList($this->request->param(),$this->request->param('limit')));
        }
		return $this->fetch('',[]);
    }

    public function add(){
        if ($this->request->isAjax()) {
			return $this->getJson(RegionLogic::goAdd($this->request->only(['id','name','parent_id','status'])));
        }
        return $this->fetch('',['tree' => RegionLogic::tree(),'parent_id'=>$this->request->get('parent_id/d',0)]);
    }

    public function edit(){
        if ($this->request->isAjax()) {
			return $this->getJson(RegionLogic::goEdit($this->request->only(['id','name','parent_id','status']))); 
        }
        return $this->fetch('',['tree' => RegionLogic::tree(),'model'=>RegionLogic::getOne($this->request->get('id'))]);
    }
	
	
    public function remove(){
        return $this->getJson(RegionLogic::goRemove($this->request->param('id/d')));
    }	

}