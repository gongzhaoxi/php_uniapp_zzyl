<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\{ErpGuideBookLogic};

class GuideBook extends \app\admin\controller\Base
{

    // 列表
    public function list(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpGuideBookLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('list',['data_type'=>$this->request->param('data_type',1)]);
    }

    // 添加
    public function add($data_type){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpGuideBookLogic::goAdd($this->request->param()));
        }
		
        return $this->fetch('',['data_type'=>$data_type,'data'=>ErpGuideBookLogic::getData($data_type)]);
    }

    // 编辑
    public function edit(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpGuideBookLogic::goEdit($this->request->param())); 
        }
        return $this->fetch('',['model' => ErpGuideBookLogic::getOne($this->request->param('id/d')),'data'=>ErpGuideBookLogic::getData($this->request->param('data_type'))]);
    }

    // 删除
    public function remove(){
        return $this->getJson(ErpGuideBookLogic::goRemove($this->request->only(['ids'])));
    }	

	public function import(){
        if($this->request->isPost()){
			return $this->getJson(ErpGuideBookLogic::goImport($this->request->param('excel')));
        }else{
			return $this->fetch('');
		}
    }
}
