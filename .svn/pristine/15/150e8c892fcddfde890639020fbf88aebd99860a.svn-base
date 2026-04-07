<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\{ErpOrderRemarkLogic,ErpOrderLogic};

class OrderRemark extends \app\admin\controller\Base
{
    // 列表
    public function list(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderRemarkLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',[]);
    }

    // 添加
    public function add(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpOrderRemarkLogic::goAdd($this->request->post('data')));
        }
        return $this->fetch('',['order_id'=>$this->request->param('order_id'),'model' => ErpOrderLogic::getOne($this->request->param('order_id/d'))]);
    }

    // 编辑
    public function edit(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpOrderRemarkLogic::goEdit($this->request->only(['id','product_num','product_name','product_model','product_price','total_price','shipping_time','remark','order_product_id'])));
        }
        return $this->fetch('',['model' => ErpOrderRemarkLogic::getOne($this->request->param('id/d'))]);
    }
	
    // 删除
    public function remove(){
		return $this->getJson(ErpOrderRemarkLogic::goRemove($this->request->param('id/d')));
    }
}
