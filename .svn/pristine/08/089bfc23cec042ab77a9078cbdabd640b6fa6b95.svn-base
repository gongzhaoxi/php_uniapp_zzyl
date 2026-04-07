<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\ErpMaterial;
use app\common\model\ErpMaterialEnter;
use app\common\model\ErpMaterialEnterMaterial;
use app\common\model\ErpMaterialChange;
use app\common\model\{ErpMaterialStockRecord,ErpMaterialWarehouse,ErpMaterialEnterMaterialReport,ErpMaterialApproval,ErpMaterialScrap,ErpMaterialCode,ErpPurchaseOrderData};
use app\admin\validate\ErpMaterialEnterValidate;
use app\common\enum\ErpMaterialStockEnum;
use app\common\enum\ErpMaterialEnterMaterialEnum;
use think\facade\Db;

class ErpMaterialEnterLogic extends BaseLogic{

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$field 			= 'id,status,stock_date,order_sn,type,remark,create_admin,supplier_id,batch_number,purchase_order';
        $list	 		= ErpMaterialEnter::withSearch(['query'],['query'=>$query])->with(['supplier'=>function($query){return $query->field('id,name');}])->field($field)
		->where('type','<>',ErpMaterialStockEnum::TYPE_ENTER_WORKSHOP)->where('data_type',ErpMaterialStockEnum::DATA_TYPE_ENTER)->order('id','desc')
		->where('create_admin','<>','')
		->append(['can_cancel','can_finish','status_desc','type_desc','can_edit'])->paginate($limit);
        $data 	 		= $list->items();
		//$ids 			= ErpMaterialEnterMaterial::where('material_stock_id','in',array_column($data,'id'))->whereRaw('quality_num > 0 or (defective_num = stock_num and status <> '.ErpMaterialEnterMaterialEnum::STATUS_CANCEL.')')->column('material_stock_id');
		$ids 			= ErpMaterialEnterMaterial::where('material_stock_id','in',array_column($data,'id'))->whereRaw('(quality_num > 0 && check_status = '.ErpMaterialEnterMaterialEnum::CHECK_STATUS_FINISH.') or (defective_num = stock_num and status <> '.ErpMaterialEnterMaterialEnum::STATUS_CANCEL.')')->column('material_stock_id');
		
		$tmp			= ErpMaterialEnterMaterial::alias('a')->join('erp_purchase_order b','a.purchase_order_id = b.id','LEFT')->where('a.material_stock_id','in',array_column($data,'id'))->where('a.purchase_order_id','>',0)->field('a.material_stock_id,b.order_sn')->group('a.material_stock_id,a.purchase_order_id')->select();
		$purchase_order	= [];
		foreach($tmp as $item){
			$purchase_order[$item['material_stock_id']][] = $item['order_sn'];
		}
		
		foreach($data as &$vo){
			$vo['tips'] = $vo['status']!=ErpMaterialStockEnum::STATUS_CANCEL&&$ids&&in_array($vo['id'],$ids);
			if(empty($purchase_order[$vo['id']])){
				//$vo['purchase_order'] = '';
			}else{
				$vo['purchase_order'] = implode(',',$purchase_order[$vo['id']]);
			}
		}
		return ['code'=>0,'data'=>$data,'extend'=>['count' => $list->total(), 'limit' => $limit, 'tmp' => $tmp]];
    }

	// 获取入库单物料
    public static function getMaterial($query=[],$limit=10)
    {
		$field 			= 'a.id,a.need_check,a.check_status,a.warehouse_id,a.material_stock_id,a.material_id,a.stock_num,a.stocked_num,a.quality_num,a.status,a.stock_num-a.stocked_num-a.quality_num as num,a.qualities_num,defective_num,a.remark,c.stock,d.status as report_status';	
		$query['_alias']= 'a';
		$query['_material_alias']= 'b';
        $list 			= ErpMaterialEnterMaterial::alias('a')
		->join('erp_material b','a.material_id = b.id','LEFT')->withSearch(['query'],['query'=>$query])
		->join('erp_material_warehouse c','a.material_id = c.material_id and a.warehouse_id = c.warehouse_id','LEFT')
		->join('erp_material_enter_material_report d','a.id = d.material_enter_material_id','LEFT')->withSearch(['query'],['query'=>$query])
		->with(['material'=>function($query){return $query->field('id,status,type,name,sn,cid,stock,unit,material,surface,color,remark,photo,status');},'warehouse'=>function($query){return $query->field('id,type,name,sn');}])
		->field($field)->order('a.id','desc')->append(['status_desc','material.category_name','can_cancel','can_enter','test_num','can_check','can_notice','check_status_desc'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

	public static function createOrderSn(){
		$count 	= ErpMaterialEnter::withTrashed()->where('data_type',ErpMaterialStockEnum::DATA_TYPE_ENTER)->whereDay('create_time')->count() + 1;
		return 'RK'.date('Ymd').sprintf("%03d",$count);
	}

    // 发起质检
    public static function goNotice($ids,$param)
    {
        try {
			$data 		= [];
			$model 		= ErpMaterialEnterMaterial::with(['report'=>function($query){return $query->field('id,material_enter_material_id');}])->where('id','in',$ids)->where('check_status',ErpMaterialEnterMaterialEnum::CHECK_STATUS_HANDLE)->select();
			$code 		= (int)ErpMaterialEnterMaterialReport::getReportCode();
			$date 		= date('Ymd');
			$add 		= [];
			$edit		= [];
			$update 	= [];
			foreach($model as $vo){
				if(empty($vo['report']['id'])){
					$add[] 	= array_merge($param,['code'=>$code,'material_stock_id'=>$vo['material_stock_id'],'material_id'=>$vo['material_id'],'material_enter_material_id'=>$vo['id']]);
					$code++;
				}else{
					$edit[] = $param;
				}
			}
			
			ErpMaterialEnterMaterial::where('id','in',$ids)->where('check_status',ErpMaterialEnterMaterialEnum::CHECK_STATUS_HANDLE)->update(['check_status'=>ErpMaterialEnterMaterialEnum::CHECK_STATUS_NOTICED]);
			if($add){
				(new ErpMaterialEnterMaterialReport)->saveAll($add);
			}
			if($edit){
				(new ErpMaterialEnterMaterialReport)->saveAll($edit);
			}		

			ErpMaterialEnterMaterial::where('id','in',$ids)->where('receive_date','')->update(['receive_by'=>self::$adminUser['username'],'receive_date'=>date('Y-m-d')]);

			return ['msg'=>'操作成功','code'=>200];
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }


    // 添加
    public static function goAdd($data,$check_status=1)
    {
        //验证
        $validate 	= new ErpMaterialEnterValidate;
        if(!$validate->scene('add')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		$material	= $data['material'];
		unset($data['material']);
        try {
		
			$data['order_sn']		= self::createOrderSn();
			$data['create_admin'] 	= empty($data['username'])?(empty(self::$adminUser['username'])?'':self::$adminUser['username']):$data['username'];
			$data['data_type']		= ErpMaterialStockEnum::DATA_TYPE_ENTER;
            $model 					= ErpMaterialEnter::create($data);
			self::insertMaterial($model,$material);
			if($check_status == 0){
				ErpMaterialEnterMaterial::where('material_stock_id',$model['id'])->update(['need_check'=>0,'check_username'=>$data['create_admin'],'check_date'=>date('Y-m-d'),'check_status'=>ErpMaterialEnterMaterialEnum::CHECK_STATUS_FINISH,'quality_num'=>Db::raw('stock_num'),'qualities_num'=>Db::raw('stock_num')]);
			}
			return ['msg'=>'创建成功','code'=>200,'data'=>['id'=>$model->id,'order_sn'=>$model->order_sn]];
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
    
	public static function insertMaterial($model,$material)
    {
		$data 		= [];
		foreach($material as $vo){
			$data[] = ['purchase_order_id'=>empty($vo['purchase_order_id'])?0:$vo['purchase_order_id'],'purchase_order_data_id'=>empty($vo['purchase_order_data_id'])?0:$vo['purchase_order_data_id'],'material_stock_id'=>$model->id,'enter_batch_number'=>!empty($vo['enter_batch_number'])?$vo['enter_batch_number']:($model->batch_number?$model->batch_number:''),'material_id'=>$vo['id'],'warehouse_id'=>$vo['warehouse_id'],'stock_num'=>$vo['stock_num'],'stocked_num'=>empty($vo['stocked_num'])?0:$vo['stocked_num'],'quality_num'=>empty($vo['quality_num'])?0:$vo['quality_num'],'qualities_num'=>empty($vo['qualities_num'])?0:$vo['qualities_num'],'can_out_num'=>empty($vo['can_out_num'])?0:$vo['can_out_num'],'status'=>empty($vo['status'])?1:$vo['status'],'remark'=>empty($vo['remark'])?'':$vo['remark']];
		}
		
		(new ErpMaterialEnterMaterial)->saveAll($data);
    }
	
    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpMaterialEnter::with(['materials.material'])->where($map)->find();
		}else{
			return ErpMaterialEnter::with(['materials.material'])->find($map);
		}
    }
	
    // 编辑
    public static function goEdit($data,$check_status=1)
    {
        //验证
        $validate 	= new ErpMaterialEnterValidate;
        if(!$validate->scene('edit')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		$model 		= self::getOne($data['id']);
        if(empty($model['id'])) {
			return ['msg'=>'数据不存在','code'=>201];
		}
		if(!$model['can_edit']){
			return ['msg'=>'当前状态不能修改','code'=>201];
		}
		$material	= $data['material'];
		unset($data['material']);
        try {
            $model->save($data);	
			ErpMaterialEnterMaterial::where('material_stock_id',$model->id)->delete();
			self::insertMaterial($model,$material);
			if($check_status == 0){
				ErpMaterialEnterMaterial::where('material_stock_id',$model['id'])->update(['need_check'=>0,'check_username'=> empty(self::$adminUser['username'])?'':self::$adminUser['username'],'check_date'=>date('Y-m-d'),'check_status'=>ErpMaterialEnterMaterialEnum::CHECK_STATUS_FINISH,'quality_num'=>Db::raw('stock_num'),'qualities_num'=>Db::raw('stock_num')]);
			}
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }


	public static function goCheck($id,$ids,$num,$defective){
		$model 			= self::getOne($id);
        if(empty($model['id'])) {
			return ['msg'=>'数据不存在','code'=>201];
		}
		$enter_material = ErpMaterialEnterMaterial::with(['material'=>function($query){return $query->field('id,name,sn,stock');}])->where('material_stock_id',$model->id)->where('id','in',$ids)->select();
		if($enter_material->isEmpty() || $enter_material->count() != count($ids)){
			return ['msg'=>'数据错误','code'=>201];
		}
		$update 		= [];
		
		foreach($enter_material as $key=>$vo){
			if(empty($num[$vo['id']])){
				return ['msg'=>$vo['material']['sn'].'本次正品量不存在','code'=>201];
			}
			if(!$vo['can_check']){
				return ['msg'=>$vo['material']['sn'].'已全部品检或已作废','code'=>201];
			}
			$_num 			= $num[$vo['id']];
			if($_num != intval($_num) || $_num <= 0){
				return ['msg'=>$vo['material']['sn'].'本次正品量必须为大于0的整数','code'=>201];
			}
			$_defective 	= $defective[$vo['id']];
			if($_defective != intval($_defective) || $_defective < 0){
				return ['msg'=>$vo['material']['sn'].'本次次品量必须为大于等于0的整数','code'=>201];
			}			
			$max 			= $vo['stock_num'] - $vo['stocked_num'] - $vo['quality_num'];
			if($_num > $max){
				return ['msg'=>$vo['material']['sn'].'本次正品量最多只能为'.$max,'code'=>201];
			}
			$inspection		= $vo['inspection']? $vo['inspection']:[];
			$inspection[]	= ['create_time'=>time(),'quality_num'=>$_num,'defective_num'=>$_defective,'admin'=>self::$adminUser['username']];
			$update[] 		= ['id'=>$vo['id'],'quality_num'=>$vo['quality_num']+$_num,'defective_num'=>$vo['defective_num']+$_defective,'qualities_num'=>$vo['qualities_num']+$_num,'inspection'=>$inspection];
		}
		try {
			(new ErpMaterialEnterMaterial)->saveAll($update);
		}catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}

	public static function goConfirm($id,$ids){
		$model 					= self::getOne($id);
        if(empty($model['id'])) {
			return ['msg'=>'数据不存在','code'=>201];
		}
		$enter_material 		= ErpMaterialEnterMaterial::with(['material'=>function($query){return $query->field('id,name,sn,stock,freeze_stock');}])->where('material_stock_id',$model->id)->where('id','in',$ids)->select();
		if($enter_material->isEmpty() || $enter_material->count() != count($ids)){
			return ['msg'=>'数据错误','code'=>201];
		}
		$enter_material_update 	= [];
		$stock_record_insert 	= [];
		$stock_data	= [];
		
		$times					= ErpMaterialStockRecord::where('material_stock_id',$model->id)->max('times');
		$times					= $times?($times+1):1;

		foreach($enter_material as $key=>$vo){
			if(!$vo['can_enter']){
				return ['msg'=>$vo['material']['sn'].'未品检或状态错误','code'=>201];
			}
			$num 				= $vo['quality_num'];
			$stocked_num		= $vo['stocked_num'] + $num;
			$status 			= $stocked_num + $vo['defective_num'] >= $vo['stock_num']?ErpMaterialEnterMaterialEnum::STATUS_FINISH:ErpMaterialEnterMaterialEnum::STATUS_PART;
			if($vo['material']['stock']>=0){
				$can_out_num	= $num;
			}else if($num > abs($vo['material']['stock'])){
				$can_out_num	= $num + $vo['material']['stock'];
			}else{
				$can_out_num	= 0;
			}
			
			if($model['type'] != ErpMaterialStockEnum::TYPE_ENTER_WORKSHOP){
				$enter_material_update[]= ['id'=>$vo['id'],'stocked_num'=>$stocked_num,'status'=>$status,'quality_num'=>0,'can_out_num'=>$vo['can_out_num']+$can_out_num];
				$stock_record_insert[] 	= ['data_type'=>'material_enter_material','data_id'=>$vo['id'],'material_stock_id'=>$vo['material_stock_id'],'material_id'=>$vo['material_id'],'stock_num'=>$num,'times'=>$times];
				$stock_data[]			= ['stock_type'=>1,'num'=>$num,'material_id'=>$vo['material_id'],'enter_order_sn'=>$model['order_sn'],'enter_batch_number'=>$vo['enter_batch_number'],'warehouse_id'=>$vo['warehouse_id'],'material_stock_id'=>$model->id,'supplier_id'=>$model->supplier_id];
		
			}else{
				$enter_material_update[]= ['id'=>$vo['id'],'stocked_num'=>$stocked_num,'status'=>$status,'quality_num'=>0,'back_num'=>$vo['back_num']+$num,'freeze_back_num'=>$vo['freeze_back_num']-$num];
				$enter_material_update[]= ['id'=>$vo['from_id'],'can_out_num'=>Db::raw('can_out_num+'.$can_out_num)];
				$stock_record_insert[] 	= ['data_type'=>'material_enter_material','data_id'=>$vo['id'],'material_stock_id'=>$vo['material_stock_id'],'material_id'=>$vo['material_id'],'stock_num'=>$num,'times'=>$times];
				$stock_data[]			= ['stock_type'=>1,'num'=>$num,'material_id'=>$vo['material_id'],'enter_order_sn'=>$model['batch_number'],'enter_batch_number'=>$vo['enter_batch_number'],'warehouse_id'=>$vo['warehouse_id'],'material_stock_id'=>$model->id,'supplier_id'=>$model->supplier_id];
			}
		}
		

		
		Db::startTrans();
		try {
			(new ErpMaterialEnterMaterial)->saveAll($enter_material_update);
			(new ErpMaterialStockRecord)->saveAll($stock_record_insert);

			if(ErpMaterialEnterMaterial::where('material_stock_id',$model->id)->where('status','in',[ErpMaterialEnterMaterialEnum::STATUS_HANDLE,ErpMaterialEnterMaterialEnum::STATUS_PART])->count() == 0){
				$model->save(['status'=>ErpMaterialStockEnum::STATUS_FINISH]);
			}
			
			ErpMaterialWarehouseLogic::goUpdateStock($stock_data,1);
			
			ErpMaterialEnterMaterial::where('id','in',$ids)->where('receive_date','')->update(['receive_by'=>self::$adminUser['username'],'receive_date'=>date('Y-m-d')]);
			
			Db::commit();
		}catch (\Exception $e){
			Db::rollback();
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}	
	
	public static function goCancel($id,$ids){
		$model 				= self::getOne($id);
        if(empty($model['id'])) {
			return ['msg'=>'数据不存在','code'=>201];
		}
		if($model['can_cancel'] == false) {
			return ['msg'=>'入库单已作废或已结算','code'=>201];
		}	
		if($ids){
			$enter_material = ErpMaterialEnterMaterial::with(['material'=>function($query){return $query->field('id,name,sn,stock');}])->append(['can_cancel'])->where('material_stock_id',$model->id)->where('id','in',$ids)->select();
		}else{
			$enter_material = ErpMaterialEnterMaterial::with(['material'=>function($query){return $query->field('id,name,sn,stock');}])->append(['can_cancel'])->where('material_stock_id',$model->id)->select();
		}
		
		$enter_material_update			= [];
		foreach($enter_material as $key=>$vo){
			if($vo['status'] > 2){
				return ['msg'=>'不可操作，'.$vo['material']['sn'].'状态错误','code'=>201];
			}
			if($vo['stocked_num'] > 0){
				return ['msg'=>'不可操作，'.$vo['material']['sn'].'已有入库','code'=>201];
			}
			if($vo['defective_num'] > 0){
				return ['msg'=>'不可操作，'.$vo['material']['sn'].'已有不良品记录','code'=>201];
			}			
			if($vo['can_cancel']){
				$enter_material_update[]= ['id'=>$vo['id'],'status'=>ErpMaterialEnterMaterialEnum::STATUS_CANCEL,'check_status'=>ErpMaterialEnterMaterialEnum::CHECK_STATUS_FINISH];
			}else{
				if($ids){
					return ['msg'=>$vo['material']['sn'].'状态错误','code'=>201];
				}
				$enter_material_update[]= ['id'=>$vo['id'],'check_status'=>ErpMaterialEnterMaterialEnum::CHECK_STATUS_FINISH];
			}
		}
		try {
			(new ErpMaterialEnterMaterial)->saveAll($enter_material_update);
			if(ErpMaterialEnterMaterial::where('status','in',[ErpMaterialEnterMaterialEnum::STATUS_HANDLE,ErpMaterialEnterMaterialEnum::STATUS_PART])->where('material_stock_id',$model->id)->count() == 0){
				$model->save(['status'=>ErpMaterialStockEnum::STATUS_CANCEL]);
			}
			
			foreach($enter_material as $key=>$vo){
				if($vo['purchase_order_data_id']){
					ErpPurchaseOrderData::where('id',$vo['purchase_order_data_id'])->update(['warehous_num'=>Db::raw('warehous_num-'.$vo['stock_num'])]);
				}
			}
			
		}catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}	
	
	public static function goSettle($id){
		$model 				= self::getOne($id);
        if(empty($model['id'])) {
			return ['msg'=>'入库单不存在','code'=>201];
		}
		if($model['can_settle'] == false) {
			return ['msg'=>'入库单已作废或已结算','code'=>201];
		}
		try {
			$model->save(['status'=>ErpMaterialStockEnum::STATUS_FINISH]);
			
			ErpMaterialEnterMaterial::where('material_stock_id',$model->id)->where('status','<>',ErpMaterialEnterMaterialEnum::STATUS_CANCEL)->update(['status'=>ErpMaterialEnterMaterialEnum::STATUS_FINISH]);
			
		}catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}

    public static function getCount($query){
		return ErpMaterialEnter::withSearch(['query'],['query'=>$query])->field('id')->where('data_type',ErpMaterialStockEnum::DATA_TYPE_ENTER)->order('id','desc')->count();
    }

	public static function goRefund($id,$ids){
		$enter 					= self::getOne($id);
        if(empty($enter['id'])) {
			return ['msg'=>'数据不存在','code'=>201];
		}
		$enter_material 		= ErpMaterialEnterMaterial::with(['material'=>function($query){return $query->field('id,name,sn,stock,freeze_stock');}])->where('material_stock_id',$enter->id)->where('id','in',$ids)->select();
		if($enter_material->isEmpty() || $enter_material->count() != count($ids)){
			return ['msg'=>'数据错误','code'=>201];
		}
		Db::startTrans();
		try {
			//生成不良品入库单(不用审核直接完成，增加物料表freeze_stock数量)
			$count  				= ErpMaterialEnter::where('order_sn','like',"%".$enter['order_sn']."%")->where('order_sn','<>',$enter['order_sn'])->where('type',ErpMaterialStockEnum::TYPE_ENTER_DISCARD)->count()+1;
			$model 					= ErpMaterialEnter::create(['create_admin'=>self::$adminUser['username'],'data_type'=>ErpMaterialStockEnum::DATA_TYPE_ENTER,'order_sn'=>$enter->order_sn.'-'.sprintf("%02d",$count),'type'=>ErpMaterialStockEnum::TYPE_ENTER_DISCARD,'remark'=>'不良品待退货','status'=>ErpMaterialStockEnum::STATUS_FINISH,'material_type'=>$enter['material_type'],'stock_date'=>date('Y-m-d'),'supplier_id'=>$enter['supplier_id']]);
			$material				= [];
			$stock_data				= [];
			$enter_material_update 	= [];
			
			foreach($enter_material as $key=>$vo){
				if(!$vo['can_return']){
					return ['msg'=>$vo['material']['sn'].'状态错误或已检次品量为0','code'=>201];
				}
				$material[] 				= ['id'=>$vo['material_id'],'enter_material_id'=>0,'enter_order_sn'=>$enter['order_sn'],'enter_batch_number'=>$vo['enter_batch_number'],'warehouse_id'=>$vo['warehouse_id'],'stock_num'=>$vo['defective_num'],'stocked_num'=>$vo['defective_num'],'status'=>ErpMaterialEnterMaterialEnum::STATUS_FINISH];
				$stock_data[]				= ['stock_type'=>2,'num'=>$vo['defective_num'],'enter_order_sn'=>$enter['order_sn'],'enter_batch_number'=>$vo['enter_batch_number'],'material_id'=>$vo['material_id'],'warehouse_id'=>$vo['warehouse_id'],'material_stock_id'=>$model->id,'supplier_id'=>$model->supplier_id,'remark'=>'库存增加到锁定库存'];
				$enter_material_update[] 	= ['id'=>$vo['id'],'status'=>ErpMaterialEnterMaterialEnum::STATUS_RETURN];
			}		
			
			self::insertMaterial($model,$material);
			
			//生成不良品出库单
			ErpMaterialDiscardLogic::goAdd(['type'=>ErpMaterialStockEnum::DISCARD_QUALITY,'material'=>$material,'remark'=>'不良品报废退货','material_type'=>$model['material_type'],'stock_date'=>date('Y-m-d'),'supplier_id'=>$model['supplier_id']]);
			
			(new ErpMaterialEnterMaterial)->saveAll($enter_material_update);
			
	
			ErpMaterialWarehouseLogic::goUpdateStock($stock_data,2);
			
			Db::commit();
		}catch (\Exception $e){
			Db::rollback();
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}
	
	public static function  goPrior($id){
		ErpMaterialEnterMaterial::where('material_stock_id',$id)->where('is_prior',0)->update(['is_prior'=>1]);
	}
	
	// 获取列表
    public static function getBack($query=[],$limit=10)
    {
		$field 	= 'id,status,stock_date,order_sn,type,remark,create_admin,supplier_id,batch_number';
        $list 	= ErpMaterialEnter::withSearch(['query'],['query'=>$query])->with(['supplier'=>function($query){return $query->field('id,name');}])->field($field)->where('type','=',ErpMaterialStockEnum::TYPE_ENTER_WORKSHOP)->where('data_type',ErpMaterialStockEnum::DATA_TYPE_ENTER)->order('id','desc')->append(['can_cancel','can_finish','status_desc','type_desc','can_edit'])->paginate($limit);
        $data 	= $list->items();
		$ids 	= ErpMaterialEnterMaterial::where('material_stock_id','in',array_column($data,'id'))->whereRaw('quality_num > 0 or (defective_num = stock_num and status <> '.ErpMaterialEnterMaterialEnum::STATUS_CANCEL.')')->column('material_stock_id');
		foreach($data as &$vo){
			$vo['tips'] = $ids&&in_array($vo['id'],$ids);
		}        
		return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }	
	
	
	
	
	
	
	public static function goReset($id,$ids){
		$enter 					= self::getOne($id);
        if(empty($enter['id'])) {
			return ['msg'=>'数据不存在','code'=>201];
		}
		$enter_material 		= ErpMaterialEnterMaterial::where('status','<=',2)->where('stocked_num',0)->where('material_stock_id',$enter->id)->where('id','in',$ids)->column('id');
		if(empty($enter_material) || count($enter_material) != count($ids)){
			return ['msg'=>'数据错误','code'=>201];
		}
		Db::startTrans();
		try {
			ErpMaterialEnterMaterial::where('id','in',$enter_material)->update(['inspection'=>'','quality_num'=>0,'stocked_num'=>0,'defective_num'=>0,'qualities_num'=>0,'can_out_num'=>0,'freeze_out_stock'=>0,'status'=>1,'check_status'=>2,'check_username'=>'','check_date'=>'']);
			
			ErpMaterialEnterMaterialReport::where('material_enter_material_id','in',$enter_material)->update(['status'=>1,'effective_date'=>'','print'=>0,'finish_time'=>0,'handling_sign'=>'','approval_sign'=>'','inspector_sign'=>'','unqualified_num'=>'','handling_sn3'=>'','handling_date'=>'','handling_sn2'=>'','handling_sn1'=>'','handling_suggestion'=>'','approval_date'=>'','inspector_date'=>'','inspection_result'=>'','pass_rate'=>'','pass_num'=>'','ng_num'=>'','ng_item'=>'','instrument_number'=>'','unqualified_description'=>'','inspection_items'=>'','inspection_basis'=>'','sampling_level_size'=>'','sampling_level_appearance'=>'','sampling_quantity_size'=>'','sampling_quantity_appearance'=>'','inspection_quantity'=>'','aql'=>'','sampling_plan'=>'','inspection_method'=>'']);
			
			ErpMaterialScrap::where('enter_material_id','in',$enter_material)->delete();
			
			ErpMaterialCode::where('data_type','erp_material_enter_material')->where('data_id','in',$enter_material)->delete();
			
			
			Db::commit();
		}catch (\Exception $e){
			Db::rollback();
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}
	
	
	public static function goDelete($id,$ids){
		$enter 				= self::getOne($id);
        if(empty($enter['id'])) {
			return ['msg'=>'数据不存在','code'=>201];
		}
		$enter_material 	= ErpMaterialEnterMaterial::where('status','<=',2)->where('stocked_num',0)->where('defective_num',0)->where('material_stock_id',$enter->id)->where('id','in',$ids)->select();
		if($enter_material->isEmpty() || $enter_material->count() != count($ids)){
			return ['msg'=>'数据错误','code'=>201];
		}
		
		Db::startTrans();
		try {
			$ids = $enter_material->column('id');
			ErpMaterialEnterMaterial::where('id','in',$ids)->delete();			
			ErpMaterialEnterMaterialReport::where('material_enter_material_id','in',$ids)->delete();
			ErpMaterialScrap::where('enter_material_id','in',$ids)->delete();
			ErpMaterialCode::where('data_type','erp_material_enter_material')->where('data_id','in',$ids)->delete();
			foreach($enter_material as $key=>$vo){
				if($vo['purchase_order_data_id']){
					ErpPurchaseOrderData::where('id',$vo['purchase_order_data_id'])->update(['warehous_num'=>Db::raw('warehous_num-'.$vo['stock_num'])]);
				}
			}
			if(ErpMaterialEnterMaterial::where('material_stock_id',$enter->id)->count() == 0){
				$enter->save(['status'=>ErpMaterialStockEnum::STATUS_CANCEL]);
			}

			Db::commit();
		}catch (\Exception $e){
			Db::rollback();
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}	
	
}
