<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\ErpOrder;
use app\common\model\ErpOrderLog;
use app\common\model\{ErpOrderProduct,ErpOrderAccessory};
use app\common\model\ErpOrderProductBom;
use app\common\model\ErpOrderProduce;
use app\common\model\ErpOrderProduceBom;
use app\common\model\ErpOrderShipping;
use app\admin\validate\ErpOrderValidate;
use app\common\enum\ErpOrderEnum;
use think\facade\Db;
use app\common\enum\RegionTypeEnum;
use app\common\enum\YesNoEnum;
use app\common\enum\ErpOrderLogEnum;
use app\common\enum\ErpOrderProduceEnum;


class ErpAftersaleLogic extends BaseLogic{

	// 获取列表
    public static function getList($query=[],$limit=10){
		$field = 'id,order_sn,create_time,customer_id,customer_name,region_type,order_status,contacts,address,phone,shipping_type,order_amount,order_product_num,shipping_status,salesman_id,delivery_time,salesman_approve,produce_status,sale_order_id';
        $list = ErpOrder::withSearch(['query'],['query'=>$query])->with(['salesman','sale_order'=>function($query){return $query->field('id,order_sn');}])->field($field)->where('data_type','=',ErpOrderEnum::DATA_TYPE_2)->order('id','desc')->append(['shipping_type_desc','order_status_desc','shipping_status_desc','region_type_desc','create_date','can_cancel','can_remove','salesman_approve_desc'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

	public static function getOrderStatusCount(){
		$status 		= ErpOrderEnum::getOrderStatusDesc();
		$data 			= [];
		foreach($status as $key=>$vo){
			$data[$key]	= ['name'=>$vo,'value'=>'?order_status='.$key,'count'=>ErpOrder::where('order_status',$key)->where('data_type','=',ErpOrderEnum::DATA_TYPE_2)->count()];
		}
		$data[12]		= ['name'=>'待销售审批','value'=>'?salesman_approve=0','count'=>ErpOrder::where('salesman_approve',0)->where('data_type','=',ErpOrderEnum::DATA_TYPE_2)->count()];
		ksort($data);
		return $data;
	}

    // 添加
    public static function goAdd($data)
    {
        //验证
        $validate 	= new ErpOrderValidate;
        if(!$validate->scene('add')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
        try {
			$count 						= ErpOrder::withTrashed()->whereDay('create_time')->where('data_type','=',ErpOrderEnum::DATA_TYPE_2)->count() + 1;
			$data['order_sn']			= 'SH'.date('y-m-d').'-'.sprintf("%03d",$count);
			$data['salesman_id'] 		= self::$adminUser['id'];
			$data['data_type']			= ErpOrderEnum::DATA_TYPE_2;
			$data['technician_approve'] = 1;
			$model 						= ErpOrder::create($data);

			return ['msg'=>'创建成功','code'=>200,'data'=>['id'=>$model->id],'model'=>$model];
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
    
    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpOrder::where($map)->find();
		}else{
			return ErpOrder::find($map);
		}
    }
	
    // 编辑
    public static function goEdit($data)
    {
        //验证
        $validate 	= new ErpOrderValidate;
        if(!$validate->scene('edit')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		$model 		= self::getOne($data['id']);
        if(empty($model['id'])) {
			return ['msg'=>'数据不存在','code'=>201];
		}
		if(!$model['can_edit']) {
			return ['msg'=>'当前状态不能修改','code'=>201];
		}
        try {
			$log 			= [];
			$filed_check   	= ['order_remark'=>'订单备注','delivery_remark'=>'交货备注','delivery_time'=>'交货日期','address'=>'收货地址/国家','address_short'=>'收货地址/国家(简称)','contacts'=>'联系人','phone'=>'联系电话','cabinet_num'=>'装柜号','technical_parameter'=>'技术参数','motor_code'=>'电机编码','shipping_type'=>'发货类型','region_type'=>'发货类型','is_special'=>'特殊订单'];
			foreach($filed_check as $k=>$vo){
				if($data[$k] != $model[$k]){
					
					if($k == 'shipping_type'){
						$before	= ErpOrderEnum::getShippingTypeDesc($model[$k]);
						$after 	= ErpOrderEnum::getShippingTypeDesc($data[$k]);
					}else if($k == 'region_type'){
						$before	= RegionTypeEnum::getDesc($model[$k]);
						$after 	= RegionTypeEnum::getDesc($data[$k]);
					}else if($k == 'region_type'){
						$before	= YesNoEnum::getBooleanDesc($model[$k]);
						$after 	= YesNoEnum::getBooleanDesc($data[$k]);
					}else{
						$before = $model[$k];
						$after 	= $data[$k];
					}
					$log[]		= ['log'=>$vo.'从`'.($before?$before:'无').'`到`'.($after?$after:'无').'`','data_type'=>ErpOrderLogEnum::ORDER_FILED_CHANGE,'order_id'=>$model['id'],'operator'=>self::$adminUser['username']];
				}
			}
            $model->save($data);
			if($log){
				(new ErpOrderLog)->saveAll($log);
			}
			
            $model->save($data);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 删除
    public static function goRemove($data)
    {
		//验证
        $validate 	= new ErpOrderValidate;
        if(!$validate->scene('remove')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}	
        try{
			$list 	= ErpOrder::where('id','in',$data['ids'])->select();
			foreach($list as $vo){
				if($vo['can_remove']){
					$vo->delete();
					$log	= ['log'=>'删除订单：'.$vo['order_sn'],'data_type'=>ErpOrderLogEnum::ORDER_DELETE,'order_id'=>$vo['id'],'operator'=>self::$adminUser['username']];
					ErpOrderLog::create($log);
				}	
			}
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 取消
    public static function goCancel($data)
    {
		//验证
        $validate 	= new ErpOrderValidate;
        if(!$validate->scene('remove')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}	
        try{
			$list 	= ErpOrder::where('id','in',$data['ids'])->select();
			foreach($list as $vo){
				if($vo['can_cancel']){
					$vo->save(['order_status'=>ErpOrderEnum::ORDER_STATUS_CLOSE]);
					$log	= ['log'=>'取消订单：'.$vo['order_sn'],'data_type'=>ErpOrderLogEnum::ORDER_CANCEL,'order_id'=>$vo['id'],'operator'=>self::$adminUser['username']];
					ErpOrderLog::create($log);
				}	
			}
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 获取回收站
    public static function getRecycle($query=[],$limit=10)
	{
		$field = 'id,order_sn,customer_id,customer_name,region_type,is_special,order_status,contacts,address,address_short,phone,shipping_type,order_amount,order_product_num,shipping_status,salesman_id,produce_status,delivery_time,delete_time';
        $list = ErpOrder::onlyTrashed()->withSearch(['query'],['query'=>$query])->with(['salesman'])->field($field)->order('id','desc')->append(['shipping_type_desc','order_status_desc','shipping_status_desc','produce_status_desc','region_type_desc','create_date','can_cancel','can_remove'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

	//恢复/删除回收站
    public static function goRecycle($ids,$action)
    {
        $validate 		= new ErpOrderValidate;
        if(!$validate->scene('recycle')->check(['ids'=>$ids])){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		try{
			if($action){
				$data 	= ErpOrder::onlyTrashed()->whereIn('id', $ids)->select();
				foreach($data as $k){
					$k->restore();
					$log	= ['log'=>'回收站恢复订单：'.$k['order_sn'],'data_type'=>ErpOrderLogEnum::ORDER_RESTORE,'order_id'=>$k['id'],'operator'=>self::$adminUser['username']];
					ErpOrderLog::create($log);
				}				
			}else{				
				ErpOrder::destroy($ids,true);
				ErpOrderLog::where('order_id','in',$ids)->delete();
				ErpOrderProduct::where('order_id','in',$ids)->delete();
				ErpOrderProductBom::where('order_id','in',$ids)->delete();
			}
		}catch (\Exception $e){
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
		}
		return ['msg'=>'操作成功'];
    }
	
	public static function getLog($query=[]){
		$field 	= '*';
        $list 	= ErpOrderLog::withSearch(['query'],['query'=>$query])->with(['order_product'])->field($field)->order('id','desc')->append(['data_type_desc'])->select();
        return $list;
    }
	
	
	public static function getTechnicianStatusCount(){
		$data 		= [];
		$data[1]	= ['name'=>'待处理','value'=>'0','count'=>ErpOrder::where('technician_approve',0)->where('is_special',1)->count()];
		$data[2]	= ['name'=>'已审批','value'=>'1','count'=>ErpOrder::where('technician_approve',1)->where('is_special',1)->count()];
		return $data;
	}
	
	public static function getSalesmanStatusCount(){
		$data 		= [];
		$data[1]	= ['name'=>'待处理','value'=>'0','count'=>ErpOrder::where('salesman_approve',0)->where('technician_approve',1)->count()];
		$data[2]	= ['name'=>'已审批','value'=>'1','count'=>ErpOrder::where('salesman_approve',1)->where('technician_approve',1)->count()];
		return $data;
	}	
	
	
	public static function  goTechnicianPass($id){
		$model 		= self::getOne($id);
        if(empty($model['id'])) {
			return ['msg'=>'订单不存在','code'=>201];
		}
		if($model['technician_approve']) {
			return ['msg'=>'已审批','code'=>201];
		}
		if(!$model['is_special']) {
			return ['msg'=>'非特殊订单','code'=>201];
		}
		if($model->order_status != ErpOrderEnum::ORDER_STATUS_WAIT_HANDLE && $model->order_status != ErpOrderEnum::ORDER_STATUS_WAIT_PRODUCE){
			return ['msg'=>'订单已排产','code'=>201];
		}		
		try{
			$model->save(['technician_approve'=>1]);
			
			$log	= ['log'=>'技术审核订单：'.$model['order_sn'],'data_type'=>ErpOrderLogEnum::TECHNICIAN_PASS,'order_id'=>$model['id'],'operator'=>self::$adminUser['username']];
			ErpOrderLog::create($log);
			
		}catch (\Exception $e){
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
		}
	}	
	
	public static function  goTechnicianReset($id){
		$model 		= self::getOne($id);
        if(empty($model['id'])) {
			return ['msg'=>'订单不存在','code'=>201];
		}
		if(!$model['technician_approve']) {
			return ['msg'=>'状态错误','code'=>201];
		}
		if(!$model['is_special']) {
			return ['msg'=>'非特殊订单','code'=>201];
		}
		if($model->order_status != ErpOrderEnum::ORDER_STATUS_WAIT_HANDLE && $model->order_status != ErpOrderEnum::ORDER_STATUS_WAIT_PRODUCE){
			return ['msg'=>'订单已排产','code'=>201];
		}		
		try{
			$model->save(['technician_approve'=>0]);
			
			$log	= ['log'=>'技术反审订单：'.$model['order_sn'],'data_type'=>ErpOrderLogEnum::TECHNICIAN_RESET,'order_id'=>$model['id'],'operator'=>self::$adminUser['username']];
			ErpOrderLog::create($log);
			
		}catch (\Exception $e){
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
		}
	}		
	
	public static function  goSalesmanPass($id){
		$model 		= self::getOne($id);
        if(empty($model['id'])) {
			return ['msg'=>'订单不存在','code'=>201];
		}
		if($model['salesman_approve']) {
			return ['msg'=>'已审批','code'=>201];
		}
		if($model->order_status != ErpOrderEnum::ORDER_STATUS_WAIT_HANDLE && $model->order_status != ErpOrderEnum::ORDER_STATUS_WAIT_PRODUCE){
			return ['msg'=>'订单已排产','code'=>201];
		}
		Db::startTrans();
		try{
			
			$data						= ['salesman_approve'=>1];
			if($model->order_status == ErpOrderEnum::ORDER_STATUS_WAIT_HANDLE){
				$data['order_status']	= ErpOrderEnum::ORDER_STATUS_WAIT_PRODUCE;
			}
			$model->save($data);
			
			ErpOrderLog::create(['log'=>'销售审核订单：'.$model['order_sn'],'data_type'=>ErpOrderLogEnum::TECHNICIAN_PASS,'order_id'=>$model['id'],'operator'=>self::$adminUser['username']]);
			
			$date					= date('ymd');
			
			foreach($model->order_product as $vo){
				$produce 			= [];
				$count 				= ErpOrderProduce::withTrashed()->whereDay('create_time')->where('product_id',$vo['product_id'])->count();
				for($i=1;$i<=$vo['product_num'];$i++){
					$count			= $count + 1;
					$produce[] 		= ['queue_num'=>$i,'product_id'=>$vo['product_id'],'order_id'=>$vo['order_id'],'order_product_id'=>$vo['id'],'produce_sn'=>$vo['product_sn'].'-'.$date.sprintf("%03d",$count)];	
				}
				(new ErpOrderProduce)->saveAll($produce);
			}
			
			Db::commit();
        }catch (\Exception $e){
			Db::rollback();
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}	
	
	public static function  goSalesmanReset($id){
		$model 		= self::getOne($id);
        if(empty($model['id'])) {
			return ['msg'=>'订单不存在','code'=>201];
		}
		if(!$model['salesman_approve']) {
			return ['msg'=>'状态错误','code'=>201];
		}
		if($model->order_status != ErpOrderEnum::ORDER_STATUS_WAIT_HANDLE && $model->order_status != ErpOrderEnum::ORDER_STATUS_WAIT_PRODUCE){
			return ['msg'=>'订单已排产','code'=>201];
		}
		try{
			$data						= ['salesman_approve'=>0];
			if($model->order_status == ErpOrderEnum::ORDER_STATUS_WAIT_PRODUCE){
				$data['order_status']	= ErpOrderEnum::ORDER_STATUS_WAIT_HANDLE;
			}
			$model->save($data);
			
			$log	= ['log'=>'销售反审订单：'.$model['order_sn'],'data_type'=>ErpOrderLogEnum::TECHNICIAN_RESET,'order_id'=>$model['id'],'operator'=>self::$adminUser['username']];
			ErpOrderLog::create($log);
			
			ErpOrderProduce::destroy(function($query) use($model){
				$query->where('order_id',$model['id']);
			});
			
			ErpOrderProduceBom::destroy(function($query) use($model){
				$query->where('order_id',$model['id']);
			});
			
		}catch (\Exception $e){
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
		}
	}	


	public static function  goShipping($produce_id,$address,$shipping_date){
		if(empty($produce_id)) {
			return ['msg'=>'请选择未通知的产品','code'=>201];
		}
		try{
			ErpOrderShippingLogic::goNotice($produce_id,$address,$shipping_date);		
		}catch (\Exception $e){
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
		}
	}	

	//获取选取订单数据
    public static function getSelect($query=[],$limit=10){
		$field = 'id,order_sn,customer_name,region_type,contacts,address,address_short,phone,order_amount,order_product_num';
        $list = ErpOrder::withSearch(['query'],['query'=>$query])->field($field)->order('id','desc')->append(['region_type_desc'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }
	
	public static function updateAmount($order_id){
		$update 						= [];
		$update['order_product_num'] 	= ErpOrderProduct::where('order_id',$order_id)->sum('product_num');
		$update['order_amount']			= ErpOrderProduct::where('order_id',$order_id)->sum('total_price') + ErpOrderAccessory::where('order_id',$order_id)->sum('total_price');
		ErpOrder::where('id',$order_id)->update($update);
	}
	
	public static function goCopy($id){
		$model 		= self::getOne($id);
        if(empty($model['id'])) {
			return ['msg'=>'订单不存在','code'=>201];
		}
		Db::startTrans();
        try {
			$data 			= [];
			$filed 			= ['customer_id','customer_name','delivery_time','delivery_remark','address','address_short','region_type','contacts','phone','cabinet_num','technical_parameter','customer_remark','shipping_type','motor_code','is_special','order_remark'];
			foreach($filed as $vo){
				$data[$vo] 	= $model[$vo];
			}
			$result 		= self::goAdd($data);
			if($result['code'] != 200){
				throw new \Exception($result['msg']);
			}
			$order_id 		= $result['data']['id'];
			$product 		= ErpOrderProduct::where('order_id',$model['id'])->select();
			foreach($product as $vo){
				ErpOrderProductLogic::doCopy($order_id,$vo);
			}
			
			$accessory				= ErpOrderAccessory::where('order_product_id',0)->where('order_id',$model['id'])->select();
			$accessory_data			= [];
			foreach($accessory as $vo){
				$accessory_data[] 	= ['order_id'=>$order_id,'order_product_id'=>0,'product_num'=>$vo['product_num'],'product_name'=>$vo['product_name'],'product_model'=>$vo['product_model'],'product_price'=>$vo['product_price'],'total_price'=>$vo['total_price'],'remark'=>$vo['remark'],'shipping_time'=>$vo['shipping_time']];
			}
			if($accessory_data){
				(new ErpOrderAccessory)->saveAll($accessory_data);
			}
			
			self::updateAmount($order_id);
			Db::commit();
			return ['msg'=>'创建成功','code'=>200];
        }catch (\Exception $e){
			Db::rollback();
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}

}
