<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\ErpMaterial;
use app\common\model\ErpMaterialOut;
use app\common\model\ErpMaterialOutMaterial;
use app\common\model\ErpMaterialChange;
use app\common\model\ErpMaterialStockRecord;
use app\common\model\ErpOrderProduceBom;
use app\admin\validate\ErpMaterialOutValidate;
use app\common\enum\ErpMaterialStockEnum;
use app\common\enum\ErpMaterialOutMaterialEnum;
use think\facade\Db;
use app\common\model\ErpMaterialEnterMaterial;

class ErpMaterialOutLogic extends BaseLogic{

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$field = 'id,status,order_sn,type,produce_sn,department,create_admin,batch_number';
        $list = ErpMaterialOut::withSearch(['query'],['query'=>$query])->field($field)->where('create_admin','<>','')->where('data_type',ErpMaterialStockEnum::DATA_TYPE_OUT)->order('id','desc')->append(['can_cancel','can_settle','status_desc','type_desc','can_edit'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

	// 获取物料
    public static function getMaterial($query=[],$limit=10)
    {
		$field 		= 'a.id,a.material_stock_id,a.material_id,a.stock_num,a.stocked_num,a.status,a.stock_num-a.stocked_num as num,a.photo,a.enter_order_sn,a.remark';	
		$query['_alias']= 'a';
		$query['_material_alias']= 'b';
		$list 		= ErpMaterialOutMaterial::alias('a')->join('erp_material b','a.material_id = b.id','LEFT')->withSearch(['query'],['query'=>$query])->with(['material'=>function($query){return $query->field('id,status,type,name,sn,cid,stock,unit,material,surface,color,remark,photo,status');}])->field($field)->order('a.id','desc')->append(['status_desc','material.category_name','can_cancel','can_out','photo_link','can_edit'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }


	public static function createOrderSn(){
		$count 	= ErpMaterialOut::withTrashed()->where('data_type',ErpMaterialStockEnum::DATA_TYPE_OUT)->whereDay('create_time')->count() + 1;
		return 'CK'.date('Ymd').sprintf("%03d",$count);
	}


    // 添加
    public static function goAdd($data)
    {
        //验证
        $validate 	= new ErpMaterialOutValidate;
        if(!$validate->scene('add')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		$material	= $data['material'];
		if(!ErpMaterialLogic::checkOutMaterialStock(array_column($material,'id'))){
			return ['msg'=>'请先处理相关物料盘点单','code'=>201];
		}
		unset($data['material']);
		foreach($material as $vo){
			//if(!isset($vo['enter_material_id']) || $vo['enter_material_id'] === ''){
			if(empty($vo['enter_material_id'])){
				return ['msg'=>'请选择入库批次号','code'=>201];
			}
		}
		Db::startTrans();
        try {
			$data['order_sn']		= self::createOrderSn();
			$data['create_admin'] 	= empty(self::$adminUser['username'])?'':self::$adminUser['username'];
			$data['data_type']		= ErpMaterialStockEnum::DATA_TYPE_OUT;
            $model 					= ErpMaterialOut::create($data);
			self::insertMaterial($model,$material);
			Db::commit();
			return ['msg'=>'创建成功','code'=>200,'data'=>['id'=>$model->id,'order_sn'=>$model->order_sn]];
        }catch (\Exception $e){
			Db::rollback();
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
    
	public static function insertMaterial($model,$material)
    {
		$enter 		= ErpMaterialEnterMaterial::alias('a')->join('erp_material_stock b','a.material_stock_id = b.id','LEFT')->whereRaw('a.can_out_num>a.freeze_out_stock')->where('a.can_out_num','>',0)->where('a.id','in',implode(',',array_column($material,'enter_material_id')))->field('a.id,b.order_sn,a.can_out_num,a.freeze_out_stock,a.can_out_num-a.freeze_out_stock as num')->column('a.id,b.order_sn,a.can_out_num,a.freeze_out_stock,a.can_out_num-a.freeze_out_stock as num,a.enter_batch_number,a.warehouse_id','a.id');
		$data 		= [];
		$update 	= [];
		foreach($material as $vo){		
			$stock_num							= $vo['stock_num']	;
			if(!empty($vo['enter_material_id'])){				
				$ids 							= explode(',',(string)$vo['enter_material_id']);
				foreach($ids as $v){
					if(!empty($enter[$v])){						
						if($enter[$v]['num'] >=  $stock_num){							
							$update[] 			= ['id'=>$enter[$v]['id'],'freeze_out_stock'=>Db::raw('freeze_out_stock+'.$stock_num)];
							$data[] 			= ['material_stock_id'=>$model->id,'warehouse_id'=>$enter[$v]['warehouse_id'],'enter_material_id'=>$enter[$v]['id'],'enter_order_sn'=>$enter[$v]['order_sn'],'enter_batch_number'=>$enter[$v]['enter_batch_number'],'material_id'=>$vo['id'],'stock_num'=>$stock_num,'photo'=>empty($vo['photo'])?'':$vo['photo'],'data_id'=>empty($vo['data_id'])?0:$vo['data_id'],'remark'=>empty($vo['remark'])?'':$vo['remark']];
							$stock_num			= 0;
							$enter[$v]['num']	= $enter[$v]['num'] - $stock_num; 
							break;
						}else{
							$update[] 			= ['id'=>$enter[$v]['id'],'freeze_out_stock'=>Db::raw('freeze_out_stock+'.$enter[$v]['num'])];
							$data[] 			= ['material_stock_id'=>$model->id,'warehouse_id'=>$enter[$v]['warehouse_id'],'enter_material_id'=>$enter[$v]['id'],'enter_order_sn'=>$enter[$v]['order_sn'],'enter_batch_number'=>$enter[$v]['enter_batch_number'],'material_id'=>$vo['id'],'stock_num'=>$enter[$v]['num'],'photo'=>empty($vo['photo'])?'':$vo['photo'],'data_id'=>empty($vo['data_id'])?0:$vo['data_id'],'remark'=>empty($vo['remark'])?'':$vo['remark']];
							$stock_num			= $stock_num - $enter[$v]['num'];
							$enter[$v]['num']	= 0; 
						}
					}
				}
			}
			if($stock_num){
				$data[] 	= ['material_stock_id'=>$model->id,'material_id'=>$vo['id'],'stock_num'=>$vo['stock_num'],'photo'=>empty($vo['photo'])?'':$vo['photo'],'data_id'=>empty($vo['data_id'])?0:$vo['data_id'],'remark'=>empty($vo['remark'])?'':$vo['remark']];		
			}
		}
		(new ErpMaterialOutMaterial)->saveAll($data);
		if($update){
			(new ErpMaterialEnterMaterial)->saveAll($update);
		}
    }
	
    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpMaterialOut::where($map)->find();
		}else{
			return ErpMaterialOut::find($map);
		}
    }
	
    // 编辑
    public static function goEdit($data)
    {
        //验证
        $validate 	= new ErpMaterialOutValidate;
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
		$material	= empty($data['material'])?[]:$data['material'];
		if($material && !ErpMaterialLogic::checkOutMaterialStock(array_column($material,'id'))){
			return ['msg'=>'请先处理相关物料盘点单','code'=>201];
		}
		unset($data['material']);
        try {
            $model->save($data);	
			//ErpMaterialOutMaterial::where('material_stock_id',$model->id)->delete();
			if($material){
				self::insertMaterial($model,$material);
			}
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

	public static function goConfirm($id,$ids,$num){
		$model 			= self::getOne($id);
        if(empty($model['id'])) {
			return ['msg'=>'数据不存在','code'=>201];
		}
		if($model['status'] != ErpMaterialStockEnum::STATUS_HANDLE){
			return ['msg'=>'状态错误','code'=>201];
		}
		$out_material 	= ErpMaterialOutMaterial::with(['out','detail'])->where('material_stock_id',$model->id)->where('id','in',$ids)->select();
		if($out_material->isEmpty() || $out_material->count() != count($ids)){
			return ['msg'=>'数据错误','code'=>201];
		}
		if(!ErpMaterialLogic::checkOutMaterialStock($out_material->column('material_id'))){
			return ['msg'=>'请先处理相关物料盘点单','code'=>201];
		}		
		$material				= ErpMaterial::where('id','in',$out_material->column('material_id'))->column('id,name,sn,stock,freeze_stock','id');
		$out_material_update 	= [];
		$stock_record_insert 	= [];
		$stock_data				= [];
		$material_update 		= [];
		$times					= ErpMaterialStockRecord::where('material_stock_id',$model->id)->max('times');
		$times					= $times?($times+1):1;
		
		$enter_material_update 	= [];
		
		foreach($out_material as $key=>$vo){
			if(empty($num[$vo['id']])){
				return ['msg'=>$material[$vo['material_id']]['sn'].'出库数量不存在','code'=>201];
			}
			if(!$vo['can_out']){
				return ['msg'=>$material[$vo['material_id']]['sn'].'已全部出库或已作废','code'=>201];
			}
			$_num 		= $num[$vo['id']];
			if($_num != intval($_num) || $_num <= 0){
				return ['msg'=>$material[$vo['material_id']]['sn'].'出库数量必须为大于0的整数','code'=>201];
			}
			$max 		= $vo['stock_num'] - $vo['stocked_num'];
			if($_num > $max){
				return ['msg'=>$material[$vo['material_id']]['sn'].'出库数量最多只能为'.$max,'code'=>201];
			}
			$status 				= $_num == $max?ErpMaterialOutMaterialEnum::STATUS_FINISH:ErpMaterialOutMaterialEnum::STATUS_PART;
			$out_material_update[] 	= ['id'=>$vo['id'],'stocked_num'=>$vo['stocked_num']+$_num,'status'=>$status];
			$stock_record_insert[] 	= ['data_type'=>'material_out_material','data_id'=>$vo['id'],'material_stock_id'=>$vo['material_stock_id'],'material_id'=>$vo['material_id'],'stock_num'=>$_num,'times'=>$times];

			$stock_data[]			= ['stock_type'=>1,'enter_order_sn'=>$vo['enter_order_sn'],'enter_batch_number'=>$vo['enter_batch_number'],'num'=>$_num*-1,'material_id'=>$vo['material_id'],'warehouse_id'=>$vo['warehouse_id'],'material_stock_id'=>$model->id,'supplier_id'=>$model->supplier_id,'remark'=>''];
			
			if(!empty($vo['enter_material_id'])){
				$enter_material_update[]= ['id'=>$vo['enter_material_id'],'can_out_num'=>Db::raw('can_out_num-'.$_num),'freeze_out_stock'=>Db::raw('freeze_out_stock-'.$_num)];
			}
		}
		
		try {
			(new ErpMaterialOutMaterial)->saveAll($out_material_update);
			(new ErpMaterialStockRecord)->saveAll($stock_record_insert);
			
			ErpMaterialWarehouseLogic::goUpdateStock($stock_data,1);
			
			if(ErpMaterialOutMaterial::where('material_stock_id',$model->id)->where('status','in',[ErpMaterialOutMaterialEnum::STATUS_HANDLE,ErpMaterialOutMaterialEnum::STATUS_PART])->count() == 0){
				$model->save(['status'=>ErpMaterialStockEnum::STATUS_FINISH]);
			}
			
			if($enter_material_update){
				(new ErpMaterialEnterMaterial)->saveAll($enter_material_update);
			}
			
		}catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}	
	
	public static function goCancel($id,$ids){
		$model 				= self::getOne($id);
        if(empty($model['id'])) {
			return ['msg'=>'出库单不存在','code'=>201];
		}
		if($model['can_cancel'] == false) {
			return ['msg'=>'出库单已作废或已结算','code'=>201];
		}	
		if($ids){
			$out_material 	= ErpMaterialOutMaterial::with(['material'=>function($query){return $query->field('id,name,sn,stock');}])->append(['can_cancel'])->where('material_stock_id',$model->id)->where('id','in',$ids)->select();
		}else{
			$out_material 	= ErpMaterialOutMaterial::with(['material'=>function($query){return $query->field('id,name,sn,stock');}])->append(['can_cancel'])->where('material_stock_id',$model->id)->select();
		}
		
		$out_material_update			= [];
		$enter_material_update			= [];
		foreach($out_material as $key=>$vo){
			if($vo['can_cancel']){
				$out_material_update[]	= ['id'=>$vo['id'],'status'=>ErpMaterialOutMaterialEnum::STATUS_CANCEL];
				$num 					= $vo['stock_num'] - $vo['stocked_num'];
				if($num>0 && $vo['enter_material_id']){
					$enter_material_update[]= ['id'=>$vo['enter_material_id'],'freeze_out_stock'=>Db::raw('freeze_out_stock-'.$num)];
				}
			}
		}
		try {
			(new ErpMaterialOutMaterial)->saveAll($out_material_update);
			if(ErpMaterialOutMaterial::where('status','<>',ErpMaterialOutMaterialEnum::STATUS_CANCEL)->where('material_stock_id',$model->id)->count() == 0){
				$model->save(['status'=>ErpMaterialStockEnum::STATUS_FINISH]);
			}
			foreach($enter_material_update as $vo){
				ErpMaterialEnterMaterial::where('id',$vo['id'])->update($vo);
			}
			
		}catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}	
	
	public static function goSettle($id){
		$model 				= self::getOne($id);
        if(empty($model['id'])) {
			return ['msg'=>'出库单不存在','code'=>201];
		}
		if($model['can_settle'] == false) {
			return ['msg'=>'出库单已作废或已结算','code'=>201];
		}
		
		$out_material 					= ErpMaterialOutMaterial::with(['material'=>function($query){return $query->field('id,name,sn,stock');}])->append(['can_finish'])->where('material_stock_id',$model->id)->select();
		$out_material_update			= [];
		$enter_material_update			= [];
		foreach($out_material as $key=>$vo){
			if($vo['can_finish']){
				$out_material_update[]	= ['id'=>$vo['id'],'status'=>ErpMaterialOutMaterialEnum::STATUS_FINISH];
				$num 					= $vo['stock_num'] - $vo['stocked_num'];
				if($num>0 && $vo['enter_material_id']){
					$enter_material_update[]= ['id'=>$vo['enter_material_id'],'freeze_out_stock'=>Db::raw('freeze_out_stock-'.$num)];
				}
			}
		}		
		
		try {
			$model->save(['status'=>ErpMaterialStockEnum::STATUS_SETTLEMENT]);
			
			if($out_material_update){
				(new ErpMaterialOutMaterial)->saveAll($out_material_update);
			}
			if($enter_material_update){
				(new ErpMaterialEnterMaterial)->saveAll($enter_material_update);
			}
			
		}catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}

    public static function getCount($query){
		return ErpMaterialOut::withSearch(['query'],['query'=>$query])->field('id')->where('data_type',ErpMaterialStockEnum::DATA_TYPE_OUT)->order('id','desc')->count();
    }

	public static function goRemoveMaterial($id){
		$model 			= ErpMaterialOutMaterial::where('id',$id)->find();
        if(empty($model['id'])) {
			return ['msg'=>'数据不存在','code'=>201];
		}		
		$stock 			= self::getOne($model['material_stock_id']);
        if(empty($stock['id'])) {
			return ['msg'=>'数据不存在','code'=>201];
		}
		if(!$stock['can_edit']){
			return ['msg'=>'当前状态不能修改','code'=>201];
		}
		
		Db::startTrans();
		try {
			if($model['enter_material_id']){
				$enter 	= ErpMaterialEnterMaterial::where('id',$model['enter_material_id'])->find();
				$num 	= $enter['freeze_out_stock'] - $model['stock_num'];
				$enter->save(['freeze_out_stock'=>$num>0?$num:0]);
			}
			$model->delete();
			Db::commit();
			return ['msg'=>'删除成功','code'=>200];
        }catch (\Exception $e){
			Db::rollback();
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}

}
