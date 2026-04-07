<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\{ErpMaterialPayout,ErpSupplierProcess,ErpMaterialPrice,ErpPurchaseOrderOut,ErpPurchaseOrder,ErpPurchaseOrderData,ErpPurchaseApply,ErpSupplier,AdminAdmin,ErpPurchaseOrderLog,ErpPurchaseOrderFeedback,ErpOrderProduce,ErpProductStock,ErpMaterialEnterMaterial};
use app\admin\validate\{ErpPurchaseOrderValidate,ErpPurchaseOrderFeedbackValidate,ErpMaterialEnterValidate,ErpProductStockValidate};
use app\common\enum\{ErpMaterialPayoutEnum,ErpPurchaseApplyEnum,ErpPurchaseOrderLogEnum,ErpPurchaseOrderEnum,ErpPurchaseOrderDataEnum,ErpOrderProduceEnum,ErpProductStockEnum,ErpMaterialStockEnum};
use think\facade\Db;

class ErpPurchaseOrderLogic extends BaseLogic{

	// 获取列表
    public static function getMaterial($query=[],$limit=10)
    {
		$field 				= 'a.*,b.name as supplier_name,c.username as follow_admin_name';
		$query['_alias']	= 'a';	
		$list 				= ErpPurchaseOrder::alias('a')
		->join('erp_supplier b','a.supplier_id = b.id','LEFT')
		->join('admin_admin c','a.follow_admin_id = c.id','LEFT')
		->withSearch(['query'],['query'=>$query])->field($field)->where('a.type',ErpPurchaseApplyEnum::TYPE_MATERIAL)->order('a.id','desc')->append(['can_edit','can_recheck','can_check','can_send','can_cancel'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

    public static function getMaterialData($query=[],$limit=10)
    {
		$field 				= 'a.*,b.cid,b.name,b.sn,b.unit,b.material,b.surface,b.color,b.type as material_type,b.warehouse_id as material_warehouse_id';	
		$query['_alias']	= 'a';
		$query['_material_alias']= 'b';
		$list 				= ErpPurchaseOrderData::alias('a')
		->join('erp_material b','a.data_id = b.id','LEFT')
		->withSearch(['query'],['query'=>$query])->field($field)->where('a.type',ErpPurchaseApplyEnum::TYPE_MATERIAL)->order('a.id','desc')->append(['over_day','can_warehous','can_remove'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

	public static function createOrderSn(){
		$count 	= ErpPurchaseOrder::withTrashed()->whereDay('create_time')->count() + 1;
		return 'CG'.date('Ymd').sprintf("%03d",$count);
	}

    // 添加
    public static function goProductAdd($param)
    {
        //验证
        $validate 	= new ErpPurchaseOrderValidate;
        if(!$validate->scene('add')->check($param)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		$material	= $param['material'];
		unset($param['material']);
		Db::startTrans();
        try {
			$param['order_sn']		= self::createOrderSn();
			$param['create_admin'] 	= empty(self::$adminUser['username'])?'':self::$adminUser['username'];
			$model 					= ErpPurchaseOrder::create($param);
			$data 					= [];
			foreach($material as $vo){
				$data[] 			= ['order_id'=>$model['id'],'data_id'=>$vo['data_id'],'delivery_date'=>$vo['delivery_date'],'apply_num'=>$vo['apply_num'],'apply_ids'=>$vo['apply_ids'],'supplier_id'=>$param['supplier_id'],'type'=>$param['type']];
			}
			if(!empty($param['apply_ids'])){
				ErpPurchaseApply::where('id','in',implode(',',$param['apply_ids']))->update(['status'=>ErpPurchaseApplyEnum::STATUS_YES]);
			}
			$res 					= (new ErpPurchaseOrderData)->saveAll($data);
			Db::commit();
			return ['msg'=>'创建成功','code'=>200,'data'=>['id'=>$model['id']]];
        }catch (\Exception $e){
			Db::rollback();
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

     public static function goMaterialAdd($param)
    {
        //验证
        $validate 	= new ErpPurchaseOrderValidate;
        if(!$validate->scene('add')->check($param)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		$material	= $param['material'];
		unset($param['material']);
		$material_price 	= ErpMaterialPrice::where('material_id','in',array_column($material,'data_id'))->where('supplier_id','=',$param['supplier_id'])->column('effective_date,last_price,price','material_id');
		
		Db::startTrans();
        try {
			$param['order_sn']			= self::createOrderSn();
			$param['create_admin'] 		= empty(self::$adminUser['nickname'])?'':self::$adminUser['nickname'];
			$param['follow_admin_username'] = $param['follow_admin_id']?AdminAdmin::where('id',$param['follow_admin_id'])->value('nickname'):'';
			$model 						= ErpPurchaseOrder::create($param);
			$data 						= [];
			foreach($material as $vo){
				if(empty($material_price[$vo['data_id']])){
					$price 			= 0;
				}else{
					$price			= $param['order_date']>$material_price[$vo['data_id']]['effective_date']?$material_price[$vo['data_id']]['price']:$material_price[$vo['data_id']]['last_price'];
				}
				$data[] 			= ['order_id'=>$model['id'],'material_price'=>$price,'data_id'=>$vo['data_id'],'delivery_date'=>$vo['delivery_date'],'apply_num'=>$vo['apply_num'],'apply_ids'=>$vo['apply_ids'],'remark'=>$vo['remark'],'supplier_id'=>$param['supplier_id'],'type'=>$param['type']];
			}
			if(!empty($param['apply_ids'])){
				ErpPurchaseApply::where('id','in',implode(',',$param['apply_ids']))->update(['status'=>ErpPurchaseApplyEnum::STATUS_YES]);
			}
			$res 					= (new ErpPurchaseOrderData)->saveAll($data);
			Db::commit();
			return ['msg'=>'创建成功','code'=>200,'data'=>['id'=>$model['id']]];
        }catch (\Exception $e){
			Db::rollback();
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }   
	
	
    public static function goOutsourcingAdd($param,$out_material)
    {

		//验证
        $validate 	= new ErpPurchaseOrderValidate;
        if(!$validate->scene('add')->check($param)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		$material					= $param['material'];
		unset($param['material']);
		$material_price 			= ErpSupplierProcess::where('id',$param['process_id'])->find();
	
		Db::startTrans();
        try {
			$param['order_sn']		= self::createOrderSn();
			$param['create_admin'] 	= empty(self::$adminUser['nickname'])?'':self::$adminUser['nickname'];
			$param['follow_admin_username'] = $param['follow_admin_id']?AdminAdmin::where('id',$param['follow_admin_id'])->value('nickname'):'';
			$param['process_name']	= $material_price['name'];
			$model 					= ErpPurchaseOrder::create($param);
			$out_data				= [];
			$order_out				= [];
			foreach($material as $vo){
				$price				= $param['order_date']>$material_price['effective_date']?$material_price['price']:$material_price['last_price'];
				$data 				= ['order_id'=>$model['id'],'process_id'=>$param['process_id'],'material_price'=>$price,'data_id'=>$vo['data_id'],'delivery_date'=>$vo['delivery_date'],'apply_num'=>$vo['apply_num'],'apply_ids'=>$vo['apply_ids'],'supplier_id'=>$param['supplier_id'],'type'=>$param['type']];
				$res 				= ErpPurchaseOrderData::create($data);
				
				if(!empty($out_material[$vo['data_id']])){
					foreach($out_material[$vo['data_id']] as $item){
						$out_data[$item['material_type']][] = $item;
						$order_out[]						= ['order_id'=>$model['id'],'order_data_id'=>$res['id'],'out_material_unit'=>$item['out_material_unit'],'order_material_unit'=>$item['order_material_unit'],'out_material_id'=>$item['out_material_id'],'order_material_id'=>$item['order_material_id'],'num'=>$item['num'],'theory_num'=>$item['theory_num'],'stock_num'=>$item['stock_num'],'loss_rate'=>$item['loss_rate']];
					}	
				}
			}
			if(!empty($param['apply_ids'])){
				ErpPurchaseApply::where('id','in',implode(',',$param['apply_ids']))->update(['status'=>ErpPurchaseApplyEnum::STATUS_YES]);
			}

			foreach($out_data as $k=>$vo){
				$res			= ErpMaterialOutLogic::goAdd(['material_type'=>$k,'type'=>ErpMaterialStockEnum::TYPE_OUT_OUTSOURCING,'stock_date'=>$param['order_date'],'material'=>$vo]);
				if($res['code'] != 200){
					throw new \Exception($res['msg']);
				}
				$out_order_sn	= $res['data']['order_sn'];
			}
			if($order_out){
				foreach($order_out as $k=>$vo){
					$order_out[$k]['out_order_sn'] = $out_order_sn;
				}
				(new ErpPurchaseOrderOut)->saveAll($order_out);
			}

			Db::commit();
			return ['msg'=>'创建成功','code'=>200,'data'=>['id'=>$model['id']]];
        }catch (\Exception $e){
			Db::rollback();
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }	
	
	
	
    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpPurchaseOrder::with(['order_data.material'])->where($map)->find();
		}else{
			return ErpPurchaseOrder::find($map);
		}
    }
	
    // 编辑
    public static function goEdit($data)
    {
        //验证
        $validate 	= new ErpPurchaseOrderValidate;
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
		$material	= $data['material'];
		unset($data['material']);
        try {
			$log 			= [];
			$filed_check   	= ['supplier_id'=>'供应商','follow_admin_id'=>'采购跟进人','order_date'=>'采购日期','delivery_date'=>'要求交货日期','remark'=>'备注'];
			foreach($filed_check as $k=>$vo){
				if(isset($data[$k]) && $data[$k] != $model[$k]){
					if($k == 'supplier_id'){
						$before	= ErpSupplier::where('id',$model[$k])->value('name');
						$after 	= ErpSupplier::where('id',$data[$k])->value('name');
					}else if($k == 'follow_admin_id'){
						$before	= AdminAdmin::where('id',$model[$k])->value('username');
						$after	= AdminAdmin::where('id',$data[$k])->value('username');
					}else{
						$before = $model[$k];
						$after 	= $data[$k];
					}
					$log[]		= ['log'=>$vo.'从`'.($before?$before:'无').'`到`'.($after?$after:'无').'`','data_type'=>ErpPurchaseOrderLogEnum::ORDER_FILED_CHANGE,'order_id'=>$model['id'],'operator'=>self::$adminUser['username']];
				}
			}
            $model->save($data);
			
			$order_data 		= ErpPurchaseOrderData::alias('a')->join('erp_material b','a.data_id = b.id','LEFT')->where('order_id',$model->id)->column('a.id,a.apply_num,a.delivery_date,b.name','a.id');
			$filed_check   		= ['apply_num'=>'采购量','delivery_date'=>'要求交货日期'];
			
			$update 			= [];
			foreach($material as $vo){
				if(!empty($vo['id']) && !empty($order_data[$vo['id']])){					
					foreach($filed_check as $k=>$v){
						if(isset($vo[$k]) && $vo[$k] != $order_data[$vo['id']][$k]){
							$before = $order_data[$vo['id']][$k];
							$after 	= $vo[$k];
							$log[]	= ['log'=>'`'.$order_data[$vo['id']]['name'].'`的`'.$v.'`从`'.($before?$before:'无').'`到`'.($after?$after:'无').'`','data_type'=>ErpPurchaseOrderLogEnum::ORDER_DATA_FILED_CHANGE,'order_id'=>$model['id'],'order_data_id'=>$vo['id'],'operator'=>self::$adminUser['username']];
						}
					}
					$update[] 	= ['id'=>$vo['id'],'delivery_date'=>$vo['delivery_date'],'apply_num'=>$vo['apply_num'],'supplier_id'=>$data['supplier_id']];
				}else{
					$update[] 	= ['order_id'=>$model['id'],'data_id'=>$vo['data_id'],'delivery_date'=>$vo['delivery_date'],'apply_num'=>$vo['apply_num'],'supplier_id'=>$data['supplier_id'],'type'=>ErpPurchaseApplyEnum::TYPE_MATERIAL];
				}
			}
			
			if($log){
				(new ErpPurchaseOrderLog)->saveAll($log);
			}
			
			if($update){
				(new ErpPurchaseOrderData)->saveAll($update);
			}		
			
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }


    // 删除
    public static function goMaterialRemove($id)
    {
		Db::startTrans();
        try{
			$model = ErpPurchaseOrderData::where('id',$id)->find();
			ErpPurchaseOrderLog::create(['log'=>'删除物料:'.$model['material']['name'],'data_type'=>ErpPurchaseOrderLogEnum::ORDER_DATA_DELETE,'order_id'=>$model['order_id'],'order_data_id'=>$model['id'],'operator'=>self::$adminUser['username']]);
			$model->delete();
			Db::commit();
		}catch (\Exception $e){
			Db::rollback();
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
	
    // 审批
    public static function goCheck($id)
    {
		$model = ErpPurchaseOrder::where('id',$id)->find();
		if(empty($model['id'])){
			return ['msg'=>'数据不存在','code'=>201];
		}
		if(!$model['can_check']) {
			return ['msg'=>'状态错误','code'=>201];
		}
		Db::startTrans();
        try{
			ErpPurchaseOrderLog::create(['log'=>'审批采购单','data_type'=>ErpPurchaseOrderLogEnum::ORDER_CHECK,'order_id'=>$model['id'],'operator'=>self::$adminUser['username']]);
			$model->save(['status'=>ErpPurchaseOrderEnum::STATUS_YES,'check_admin'=>self::$adminUser['username'],'check_date'=>date('Y-m-d')]);
			Db::commit();
		}catch (\Exception $e){
			Db::rollback();
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }	
	
    // 反审
    public static function goRecheck($id)
    {
		$model = ErpPurchaseOrder::where('id',$id)->find();
		if(empty($model['id'])){
			return ['msg'=>'数据不存在','code'=>201];
		}
		if(!$model['can_recheck']) {
			return ['msg'=>'状态错误','code'=>201];
		}
		if(ErpPurchaseOrderData::where('order_id',$model['id'])->where('warehous_num','>',0)->count() > 0) {
			return ['msg'=>'此单据已经有入库，不可反审','code'=>201];
		}
		
		Db::startTrans();
        try{
			ErpPurchaseOrderLog::create(['log'=>'反审采购单','data_type'=>ErpPurchaseOrderLogEnum::ORDER_RECHECK,'order_id'=>$model['id'],'operator'=>self::$adminUser['username']]);
			$model->save(['status'=>ErpPurchaseOrderEnum::STATUS_NO,'supplier_status'=>ErpPurchaseOrderEnum::SUPPLIER_STATUS_WAIT_SEND]);
			Db::commit();
		}catch (\Exception $e){
			Db::rollback();
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }	
	
    // 发供应商审核
    public static function goSend($id)
    {
		$model = ErpPurchaseOrder::where('id',$id)->find();
		if(empty($model['id'])){
			return ['msg'=>'数据不存在','code'=>201];
		}
		if(!$model['can_send']) {
			return ['msg'=>'状态错误','code'=>201];
		}
		Db::startTrans();
        try{
			ErpPurchaseOrderLog::create(['log'=>'发供应商审核','data_type'=>ErpPurchaseOrderLogEnum::ORDER_SEND,'order_id'=>$model['id'],'operator'=>self::$adminUser['username']]);
			$model->save(['supplier_status'=>ErpPurchaseOrderEnum::SUPPLIER_STATUS_WAIT_CONFIRM]);
			Db::commit();
		}catch (\Exception $e){
			Db::rollback();
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }	
	
	public static function getLog($id){
		return ErpPurchaseOrderLog::where('order_id',$id)->order('id desc')->select();
	}

	// 复制采购单
    public static function goCopy($id)
    {
		$order = ErpPurchaseOrder::where('id',$id)->find();
		if(empty($order['id'])){
			return ['msg'=>'数据不存在','code'=>201];
		}
		Db::startTrans();
        try{
			$model 				= ErpPurchaseOrder::create(['remark'=>$order['remark'],'order_date'=>$order['order_date'],'supplier_id'=>$order['supplier_id'],'follow_admin_id'=>$order['follow_admin_id'],'type'=>$order['type'],'delivery_date'=>$order['delivery_date'],'order_sn'=>self::createOrderSn(),'create_admin'=>self::$adminUser['username']]);
			$order_data			= [];
			foreach($order['order_data'] as $vo){
				$order_data[] 	= ['order_id'=>$model['id'],'data_id'=>$vo['data_id'],'apply_num'=>$vo['apply_num'],'delivery_date'=>$vo['delivery_date'],'supplier_id'=>$vo['supplier_id']];
			}
			(new ErpPurchaseOrderData)->saveAll($order_data);
			Db::commit();
		}catch (\Exception $e){
			Db::rollback();
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
	
	// 获取列表
    public static function getMaterialFollow($query=[],$limit=10)
    {
		$field 				= 'a.*,b.name as supplier_name,c.username as follow_admin_name';
		$query['_alias']	= 'a';	
		$list 				= ErpPurchaseOrder::alias('a')
		->join('erp_supplier b','a.supplier_id = b.id','LEFT')
		->join('admin_admin c','a.follow_admin_id = c.id','LEFT')
		->where('a.status',ErpPurchaseOrderEnum::STATUS_YES)
		->withSearch(['query'],['query'=>$query])->field($field)->where('a.type',ErpPurchaseApplyEnum::TYPE_MATERIAL)->order('a.id','desc')->append(['over_day','last_feedback'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }
	
	public static function getFeedback($id){
		return ErpPurchaseOrderFeedback::where('order_id',$id)->order('id desc')->select();
	}

    // 反馈
    public static function goFeedback($data)
    {
		$validate 	= new ErpPurchaseOrderFeedbackValidate;
        if(!$validate->scene('add')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		$model = ErpPurchaseOrder::where('id',$data['order_id'])->find();
		if(empty($model['id'])){
			return ['msg'=>'数据不存在','code'=>201];
		}
        try{
			$data['operator'] 	= self::$adminUser['username'];
			$data['type']		= 1;
			ErpPurchaseOrderFeedback::create($data);
		}catch (\Exception $e){
			
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

	public static function goEditOverdueReason($id,$overdue_reason){
		if(empty($id)){
			return ['msg'=>'参数错误','code'=>201];
		}
		$model = ErpPurchaseOrder::where('id',$id)->find();
		if(empty($model['id'])){
			return ['msg'=>'订单不存在','code'=>201];
		}
		try {
			$model->save(['overdue_reason'=>$overdue_reason]);
		}catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}	
	
	// 作废订单
    public static function goRemove($id)
    {
		$model = ErpPurchaseOrder::where('id',$id)->find();
		if(empty($model['id'])){
			return ['msg'=>'数据不存在','code'=>201];
		}
		if(!$model['can_cancel']) {
			return ['msg'=>'状态错误','code'=>201];
		}		
		if(ErpPurchaseOrderData::where('order_id',$model['id'])->where('warehous_num','>',0)->count() > 0) {
			return ['msg'=>'此单据已经有入库，不可作废','code'=>201];
		}		
		Db::startTrans();
        try{
			ErpPurchaseOrderLog::create(['log'=>'作废订单','data_type'=>ErpPurchaseOrderLogEnum::ORDER_REMOVE,'order_id'=>$model['id'],'operator'=>self::$adminUser['username']]);
			
			$data = ErpPurchaseOrderData::where('order_id',$model['id'])->select();
			self::removeData($data);
			
			$model->save(['status'=>ErpPurchaseOrderEnum::STATUS_CANCEL]);
			Db::commit();
		}catch (\Exception $e){
			Db::rollback();
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }	
	
	public static function removeData($data){
		$ids 						= [];
		$produce_ids				= [];
		$order_ids					= [];
		foreach($data as $model){
			if($model['can_remove']){
				$ids[] 				= $model['id'];
				if($model['type'] == ErpPurchaseApplyEnum::TYPE_PRODUCT){
					$produce_ids[]	= $model['data_id'];
				}
				if(!in_array($model['order_id'],$order_ids)){
					$order_ids[] = $model['order_id'];
				}
			}
		}
		if($ids){
			ErpPurchaseOrderData::where('id','in',$ids)->update(['status'=>ErpPurchaseOrderDataEnum::STATUS_CANCEL]);
		}		
		//成品更改为未审核
		if($produce_ids){
			ErpOrderProduce::where('id','in',$produce_ids)->update(['approve_status'=>ErpOrderProduceEnum::APPROVE_STATUS_NO]);
		}
		foreach($order_ids as $vo){
			//if(ErpPurchaseOrderData::where('order_id',$vo)->whereRaw('apply_num > warehous_num and status < 2')->count() == 0){
			if(ErpPurchaseOrderData::where('order_id',$vo)->where('status','<>',ErpPurchaseOrderDataEnum::STATUS_CANCEL)->count() == 0){	
				ErpPurchaseOrder::where('id',$vo)->update(['status'=>ErpPurchaseOrderEnum::STATUS_CANCEL]);
			}
		}	
		
	}
	
	// 作废订单产品
    public static function goRemoveData($id)
    {
		$model = ErpPurchaseOrderData::where('id',$id)->find();
		if(empty($model['id'])){
			return ['msg'=>'数据不存在','code'=>201];
		}
		if(!$model['can_remove']){
			return ['msg'=>'状态错误','code'=>201];
		}
		Db::startTrans();
        try{
			ErpPurchaseOrderLog::create(['log'=>'作废订单产品','data_type'=>ErpPurchaseOrderLogEnum::ORDER_DATA_REMOVE,'order_id'=>$model['order_id'],'order_data_id'=>$model['id'],'operator'=>self::$adminUser['username']]);
			self::removeData([$model]);
			Db::commit();
		}catch (\Exception $e){
			Db::rollback();
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

	
    public static function goWarehous($warehous_type,$param,$username='',$check_status=1)
    {
		//验证
		if($warehous_type == 2){
			$validate 	= new ErpProductStockValidate;
		}else{
			$validate 	= new ErpMaterialEnterValidate;
		}
        if(!$validate->scene('add')->check($param)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		$material	= $param['material'];
		unset($param['material']);
		
		if($warehous_type == 2){
			$order_data	= self::getProductData(['ids'=>array_column($material,'id')],10000)['data'];
			$count 		= ErpProductStock::whereDay('create_time')->group('order_sn')->count() + 1;
			$order_sn	= 'RH'.date('Ymd').sprintf("%03d",$count);
		}else if($warehous_type == 3){
			$order_data	= self::getOutsourcingData(['ids'=>array_column($material,'id')],10000)['data'];
		}else{
			$order_data	= self::getMaterialData(['ids'=>array_column($material,'id')],10000)['data'];
		}
		
		$data 		= [];
		$update 	= [];
		$date 		= date('Y-m-d');
		$order_ids 	= [];
		$payout		= [];
		
		foreach($order_data as $vo){
			$name 	= $warehous_type == 2?$vo['produce_sn']:$vo['name'];
			if(!$vo['can_warehous']){
				return ['msg'=>$name.'状态错误','code'=>201];
			}
			if(empty($material[$vo['id']])){
				return ['msg'=>$name.'数据错误','code'=>201];
			}
			$max 	= $vo['apply_num']*1.2;
			if($max<$material[$vo['id']]['stock_num']){
				return ['msg'=>$name.'入库数量最多只能为:'.$max,'code'=>201];
			}
			
			$update[] 									= ['id'=>$vo['id'],'status'=>ErpPurchaseOrderDataEnum::STATUS_YES,'warehous_date'=>$date,'warehous_overdue'=>$vo['over_day'],'warehous_num'=>Db::raw('warehous_num+'.$material[$vo['id']]['stock_num'])];
			
			if($warehous_type == 1 || $warehous_type == 3){
				if(empty($data[$vo['material_type']])){
					$data[$vo['material_type']]			= array_merge($param,['material'=>[],'username'=>$username,'material_type'=>$vo['material_type']]);
				}
				$data[$vo['material_type']]['material'][] 	= ['id'=>$vo['data_id'],'purchase_order_id'=>$vo['order_id'],'purchase_order_data_id'=>$vo['id'],'warehouse_id'=>$material[$vo['id']]['warehouse_id'],'stock_num'=>$material[$vo['id']]['stock_num'],'remark'=>$material[$vo['id']]['remark']];
				$payout[$vo['material_type']][]				= ['supplier_process'=>empty($vo['process_name'])?'':$vo['process_name'],'material_id'=>$vo['data_id'],'purchase_order_id'=>$vo['order_id'],'supplier_id'=>$vo['supplier_id'],'num'=>$material[$vo['id']]['stock_num'],'price'=>$vo['material_price'],'total_price'=>$vo['material_price']*$material[$vo['id']]['stock_num'],'username'=>$username?$username:self::$adminUser['username'],'data_type'=>$vo['type']==1?ErpMaterialPayoutEnum::TYPE_1:ErpMaterialPayoutEnum::TYPE_2,'data_id'=>$vo['id'],'order_sn'=>'','order_date'=>$param['stock_date']];
			}
			
			if($warehous_type == 2){
				$data[] 	= ['order_sn'=>$order_sn,'username'=>$username,'type'=>ErpProductStockEnum::TYPE_PURCHASE,'order_produce_id'=>$vo['data_id'],'warehouse_id'=>$material[$vo['id']]['warehouse_id'],'product_id'=>$vo['order_product']['product_id'],'order_id'=>$vo['order_product']['order_id'],'order_product_id'=>$vo['order_product']['id'],'supplier_id'=>$param['supplier_id'],'stock_date'=>$param['stock_date'],'purchase_date'=>$param['purchase_date'],'remark'=>$param['remark']];
			}
			if(!in_array($vo['order_id'],$order_ids)){
				$order_ids[] = $vo['order_id'];
			}
		}

		Db::startTrans();
        try{
			(new ErpPurchaseOrderData)->saveAll($update);		
			$return 			= [];
			if(($warehous_type == 1 || $warehous_type == 3) && $data){
				$payout_data 	= [];
				foreach($data as $key=>$vo){
					$res 		= ErpMaterialEnterLogic::goAdd($vo,$check_status);
					$return[] 	= $res['data']['id'];
					foreach($payout[$key] as $item){
						$item['order_sn'] 	= $res['data']['order_sn'];
						$payout_data[] 		= $item;
					}
				}
				$return			= ErpMaterialEnterMaterial::where('material_stock_id','in',$return)->column('id');
				(new ErpMaterialPayout)->saveAll($payout_data);
			}
			if($warehous_type == 2 && $data){
				$res 			= (new ErpProductStock)->saveAll($data);
				$return 		= $res->column('id');
			}
			
			foreach($order_ids as $vo){
				if(ErpPurchaseOrderData::where('order_id',$vo)->whereRaw('apply_num > warehous_num and status < 2')->count() == 0){
					ErpPurchaseOrder::where('id',$vo)->update(['status'=>ErpPurchaseOrderEnum::STATUS_FINISH]);
				}
			}
			
			Db::commit();
			return ['msg'=>'创建成功','code'=>200,'data'=>['id'=>$return]];
		}catch (\Exception $e){
			Db::rollback();
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }	
	
	// 获取成品采购单
    public static function getProduct($query=[],$limit=10)
    {
		$field 				= 'a.*,b.name as supplier_name,c.username as follow_admin_name';
		$query['_alias']	= 'a';	
		$list 				= ErpPurchaseOrder::alias('a')
		->join('erp_supplier b','a.supplier_id = b.id','LEFT')
		->join('admin_admin c','a.follow_admin_id = c.id','LEFT')
		->withSearch(['query'],['query'=>$query])->field($field)->where('a.type',ErpPurchaseApplyEnum::TYPE_PRODUCT)->order('a.id','desc')->append(['can_edit','can_recheck','can_check','can_send'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

    public static function getProductData($query=[],$limit=10)
    {
		$field 					= 'a.*,b.produce_sn,b.order_product_id,b.queue_num,c.product_model,c.product_specs,c.product_num,c.remark as order_product_remark,d.order_sn,d.address,d.order_type,e.username';	
		$query['_alias']		= 'a';
		$query['_produce_alias']= 'b';
		$query['_product_alias']= 'c';
		$query['_order_alias']	= 'd';
		$list 	= ErpPurchaseOrderData::alias('a')
		->join('erp_order_produce b','a.data_id = b.id','LEFT')
		->join('erp_order_product c','b.order_product_id = c.id','LEFT')
		->join('erp_order d','b.order_id = d.id','LEFT')
		->join('admin_admin e','d.salesman_id = e.id','LEFT')
		->with(['order_product'])
		->withSearch(['query'],['query'=>$query])->field($field)->where('a.type',ErpPurchaseApplyEnum::TYPE_PRODUCT)->order('a.id','desc')->append(['status_desc','data_type_desc','over_day','can_warehous','can_remove','order_type_desc'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }
	
	public static function getProductFollow($query=[],$limit=10)
    {
		$field 				= 'a.*,b.name as supplier_name,c.username as follow_admin_name';
		$query['_alias']	= 'a';	
		$list 				= ErpPurchaseOrder::alias('a')
		->join('erp_supplier b','a.supplier_id = b.id','LEFT')
		->join('admin_admin c','a.follow_admin_id = c.id','LEFT')
		->where('a.status',ErpPurchaseOrderEnum::STATUS_YES)
		->withSearch(['query'],['query'=>$query])->field($field)->where('a.type',ErpPurchaseApplyEnum::TYPE_PRODUCT)->order('a.id','desc')->append(['over_day','last_feedback'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }
	
	
	public static function getOutsourcing($query=[],$limit=10)
    {
		$field 				= 'a.*,b.name as supplier_name,c.username as follow_admin_name';
		$query['_alias']	= 'a';	
		$list 				= ErpPurchaseOrder::alias('a')
		->join('erp_supplier b','a.supplier_id = b.id','LEFT')
		->join('admin_admin c','a.follow_admin_id = c.id','LEFT')
		->withSearch(['query'],['query'=>$query])->field($field)->where('a.type',ErpPurchaseApplyEnum::TYPE_OUTSOURCING)->order('a.id','desc')->append(['can_edit','can_recheck','can_check','can_send'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }
	
    public static function getOutsourcingData($query=[],$limit=10)
    {
		$field 				= 'a.*,b.cid,b.name,b.sn,b.unit,b.material,b.surface,b.color,b.type as material_type,b.warehouse_id as material_warehouse_id,c.process_name';	
		$query['_alias']	= 'a';
		$query['_material_alias']= 'b';
		$list 				= ErpPurchaseOrderData::alias('a')
		->join('erp_material b','a.data_id = b.id','LEFT')
		->join('erp_purchase_order c','a.order_id = c.id','LEFT')
		->withSearch(['query'],['query'=>$query])->field($field)->where('a.type',ErpPurchaseApplyEnum::TYPE_OUTSOURCING)->order('a.id','desc')->append(['over_day','can_warehous','can_remove','no_warehous_num'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }	
	
    public static function getOutsourcingFollow($query=[],$limit=10)
    {
		$field 				= 'a.*,b.name as supplier_name,c.username as follow_admin_name';
		$query['_alias']	= 'a';	
		$list 				= ErpPurchaseOrder::alias('a')
		->join('erp_supplier b','a.supplier_id = b.id','LEFT')
		->join('admin_admin c','a.follow_admin_id = c.id','LEFT')
		->where('a.status',ErpPurchaseOrderEnum::STATUS_YES)
		->withSearch(['query'],['query'=>$query])->field($field)->where('a.type',ErpPurchaseApplyEnum::TYPE_OUTSOURCING)->order('a.id','desc')->append(['over_day','last_feedback'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }	
	
	
	public static function goReApply($param)
    {
		$data 	= ErpPurchaseOrderData::alias('a')->join('erp_material b','a.data_id = b.id','LEFT')->field('a.*,b.sn')->where('a.id','in',array_keys($param['material']))->select();
		$update = [];
		foreach($data as $key=>$vo){
			if($vo['no_warehous_num'] < $param['material'][$vo['id']]['apply_num']){
				return ['msg'=>$vo['sn'].'再次委外数最大只能为'.$vo['no_warehous_num'],'code'=>201];
			}	
			$update[] = ['id'=>$vo['id'],'re_apply_num'=>$vo['re_apply_num'] + $param['material'][$vo['id']]['apply_num']];
		}

		Db::startTrans();
        try{
			$res = ErpPurchaseApplyLogic::goAdd($param);
			if($res['code'] != 200){
				throw new \Exception($res['msg']);
			}
			(new ErpPurchaseOrderData)->saveAll($update);
			Db::commit();
			return $res;
		}catch (\Exception $e){
			Db::rollback();
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
	
}
