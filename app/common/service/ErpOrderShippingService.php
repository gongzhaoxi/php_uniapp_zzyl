<?php
namespace app\common\service;
use app\common\enum\ErpOrderShippingEnum;
use app\common\enum\ErpOrderEnum;
use app\common\model\ErpOrderShipping;
use app\common\model\ErpOrder;
use app\common\model\{ErpOrderProduce,ErpOrderAftersale,ErpProductStock};

class ErpOrderShippingService 
{

	public static function goConfirm($data,$model)
    {

        try {
			$update 		= [];
			$order_id 		= [];
			$order_type 	= [];
			$order_produce_id	= [];
			
			foreach($model as $vo){
				$update[] 					= ['id'=>$vo['id'],'out_warehouse_time'=>date('Y-m-d'),'shipping_num'=>$data['shipping_num'],'shipping_photo'=>empty($data['shipping_photo'])?[]:$data['shipping_photo'],'shipping_status'=>ErpOrderShippingEnum::SHIPPING_STATUS_FINISH];
				$order_id[$vo['order_id']] 	= $vo['order_id'];
				$order_type[$vo['order_id']]= $vo['data_type'];
				if($vo['data_type'] == 1){
					$order_produce_id[] 	= $vo['order_produce_id'];
				}
				
			}
			if($order_produce_id){
				ErpProductStock::where('order_produce_id','in',implode(',',$order_produce_id))->update(['is_out_warehouse'=>1]);
			}
			(new ErpOrderShipping)->saveAll($update);

			foreach($order_id as $vo){
				if($order_type[$vo] == 2){
					$m = new ErpOrderAftersale;
				}else{
					$m = new ErpOrderProduce;
				}
				if($m->where('order_shipping_id',0)->where('order_id',$vo)->count() == 0 && ErpOrderShipping::where('order_id','=',$vo)->where('shipping_status','<>',ErpOrderShippingEnum::SHIPPING_STATUS_FINISH)->count() == 0){
					ErpOrder::where('id',$vo)->update(['shipping_time'=>time(),'order_status'=>ErpOrderEnum::ORDER_STATUS_FINISH,'shipping_status'=>ErpOrderEnum::SHIPPING_STATUS_FINISH]);
				}else{
					ErpOrder::where('id',$vo)->update(['shipping_status'=>ErpOrderEnum::SHIPPING_STATUS_PART]);
				}	
			}
			return ['msg'=>'操作成功','code'=>200];		
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }


}