<?php
declare (strict_types = 1);
namespace app\admin\controller\h5;
use app\admin\logic\ErpOrderShippingLogic;

class OrderShipping extends \app\admin\controller\Base
{

    // 列表
    public function list(){
		$query 	= $this->request->only(['order_sn','customer_name']);
		$res 	= ErpOrderShippingLogic::getList(array_merge($query,['approve_status'=>0]),$this->request->param('limit',10));
        return $this->fetch('',['query'=>$query,'page' => $this->request->param('page/d',1),'last_page'=>ceil($res['extend']['count']/$res['extend']['limit']),'data'=>$res['data'],'count'=>$res['extend']['count'],'limit'=>$res['extend']['limit']]);
    }
}
