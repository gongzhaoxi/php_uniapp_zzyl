<?php
declare (strict_types = 1);
namespace app\index\controller;

use app\index\logic\ErpMaterialEnterMaterialLogic;

class ErpMaterialEnterMaterial extends Base
{

    // 列表
    public function index(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialEnterMaterialLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['quality_testing_area'=>get_dict_data('quality_testing_area')]);
    }
	
    //QC品检作业指导书
    public function qcFile(){
        return $this->fetch('',['model'=>ErpMaterialEnterMaterialLogic::getOne($this->request->param('id'))]);
    }	
	
	// 品检
    public function check(){
        return $this->getJson(ErpMaterialEnterMaterialLogic::goCheck($this->request->param('ids'),$this->request->param('num'),$this->request->param('defective'),$this->request->userInfo));
    }	
	
	// 标签编码
    public function code(){
		return $this->getJson(ErpMaterialEnterMaterialLogic::goSetCode($this->request->param('id'),$this->request->param('num/d',1)));
    }	
	
	// 标签编码
    public function checkCode(){
		return $this->getJson(ErpMaterialEnterMaterialLogic::goCheckCode($this->request->param('code'),$this->request->param('num/d',1)));
    }	
	
	
	
	
	//保存报告
    public function report(){
        if ($this->request->isAjax()) {
			if($this->request->param('get_guide_book') == 1){
				return $this->getJson(ErpMaterialEnterMaterialLogic::getGuideBook($this->request->param()));	
			}else{
				return $this->getJson(ErpMaterialEnterMaterialLogic::goReport($this->request->except(['id'])));
			}
        }
		return $this->fetch('',['model'=>ErpMaterialEnterMaterialLogic::getOne($this->request->param('id'))]);
    }	
		
	// 完成
    public function finish(){
		return $this->getJson(ErpMaterialEnterMaterialLogic::goFinish($this->request->param('id')));
    }
	
	// 设为已打印
    public function setPrint(){
		return $this->getJson(ErpMaterialEnterMaterialLogic::goSetPrint($this->request->param('id')));
    }	
	
}
