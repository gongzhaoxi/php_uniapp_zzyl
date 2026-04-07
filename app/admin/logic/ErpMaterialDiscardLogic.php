<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\ErpMaterial;
use app\common\model\ErpMaterialDiscard;
use app\common\model\ErpMaterialDiscardMaterial;
use app\common\model\ErpMaterialChange;
use app\common\model\ErpMaterialStockRecord;
use app\common\model\ErpOrderProduceBom;
use app\admin\validate\ErpMaterialDiscardValidate;
use app\common\enum\ErpMaterialStockEnum;
use app\common\enum\ErpMaterialDiscardMaterialEnum;
use think\facade\Db;
use app\common\model\{ErpMaterialEnterMaterial,ErpSupplier};

class ErpMaterialDiscardLogic extends BaseLogic{

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$field = 'id,supplier_status,status,order_sn,type,supplier_id,create_admin';
        $list = ErpMaterialDiscard::with(['supplier'=>function($query){return $query->field('id,name');}])->withSearch(['query'],['query'=>$query])->field($field)->where('data_type',ErpMaterialStockEnum::DATA_TYPE_DISCARD)->order('id','desc')->append(['can_cancel','can_settle','status_desc','type_desc','can_edit','can_send'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

	// 获取物料
    public static function getMaterial($query=[],$limit=10)
    {
		$field 		= 'a.id,a.material_stock_id,a.material_id,a.stock_num,a.stocked_num,a.status,a.stock_num-a.stocked_num as num,a.photo,a.enter_order_sn,a.remark,b.sn,b.name,c.order_sn,c.stock_date,c.supplier_id';	
		$query['_alias']= 'a';
		$query['_material_alias']= 'b';
		$query['_stock_alias']= 'c';
		$list 		= ErpMaterialDiscardMaterial::alias('a')
		->join('erp_material b','a.material_id = b.id','LEFT')
		->join('erp_material_stock c','a.material_stock_id = c.id','LEFT')
		->withSearch(['query'],['query'=>$query])->with(['material'=>function($query){return $query->field('id,status,type,name,sn,cid,stock,unit,material,surface,color,remark,photo,status');}])->field($field)->order('a.id','desc')->append(['status_desc','material.category_name','can_cancel','can_out','photo_link'])->paginate($limit);
		
		$data 		= $list->items();
		$supplier 	= ErpSupplier::where('id','in',array_column($data,'supplier_id'))->column('name','id');
		foreach($data as &$vo){
			$vo['supplier_name'] = empty($supplier[$vo['supplier_id']])?'':$supplier[$vo['supplier_id']];
		}
        return ['code'=>0,'data'=>$data,'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }


	public static function getMaterialCount($query=[]){
		$query['_alias']= 'a';
		$query['_material_alias']= 'b';
		$query['_stock_alias']= 'c';
        $count 	= ErpMaterialDiscardMaterial::alias('a')
		->join('erp_material b','a.material_id = b.id','LEFT')
		->join('erp_material_stock c','a.material_stock_id = c.id','LEFT')
		->withSearch(['query'],['query'=>$query])->count();
		return ['data'=>['count'=>$count,'key'=>rand_string()]];
	}
	
	public static function getMaterialExport($query=[],$limit=10000){
		$limit				= $limit>10000?10000:$limit;
		$data				= self::getMaterial($query,$limit)['data'];
		$return				= ['column'=>[],'setWidh'=>[],'keys'=>[],'list'=>$data,'image_fields'=>[]];
		$field 				= ['status_desc'=>'单据状态','order_sn'=>'退货单编号','supplier_name'=>'供应商','sn'=>'物料编码','name'=>'物料名称','stock_num'=>'总需出库量','stocked_num'=>'已出库量','stock_date'=>'退货日期'];
		foreach($field as $key=>$vo){
			$return['column'][] 	= $vo;
			$return['setWidh'][] 	= 10;
			$return['keys'][] 		= $key;				
		}
        return $return;	
	}



    // 添加
    public static function goAdd($data)
    {
        //验证
        $validate 	= new ErpMaterialDiscardValidate;
        if(!$validate->scene('add')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		$material	= $data['material'];
		if(!ErpMaterialLogic::checkOutMaterialStock(array_column($material,'id'))){
			return ['msg'=>'请先处理相关物料盘点单','code'=>201];
		}		
		foreach($material as $vo){
			//if(!isset($vo['enter_material_id']) || $vo['enter_material_id'] === ''){
			if(empty($vo['enter_material_id']) && $data['type'] != ErpMaterialStockEnum::DISCARD_QUALITY){
				return ['msg'=>'请选择入库批次号','code'=>201];
			}
		}		
		unset($data['material']);
        try {
			$count 					= ErpMaterialDiscard::withTrashed()->where('data_type',ErpMaterialStockEnum::DATA_TYPE_DISCARD)->whereDay('create_time')->count() + 1;
			$data['order_sn']		= 'BF'.date('Ymd').sprintf("%03d",$count);
			$data['create_admin'] 	= self::$adminUser['username'];
			$data['data_type']		= ErpMaterialStockEnum::DATA_TYPE_DISCARD;
            $model 					= ErpMaterialDiscard::create($data);
			self::insertMaterial($model,$material);
			return ['msg'=>'创建成功','code'=>200,'data'=>['id'=>$model->id]];
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
    
	public static function insertMaterial($model,$material)
    {
		$enter 		= ErpMaterialEnterMaterial::alias('a')->join('erp_material_stock b','a.material_stock_id = b.id','LEFT')->whereRaw('a.can_out_num>a.freeze_out_stock')->where('a.can_out_num','>',0)->where('a.id','in',array_column($material,'enter_material_id'))->column('a.enter_batch_number,a.warehouse_id,b.order_sn','a.id');
		$data 		= [];
		$update 	= [];
		foreach($material as $vo){
			$enter_material_id 		= 0;
			$enter_order_sn			= '';
			$enter_batch_number		= '';
			$warehouse_id			= 0;
			if(!empty($vo['enter_material_id']) && !empty($enter[$vo['enter_material_id']])){
				$enter_material_id 	= $vo['enter_material_id'];
				$enter_order_sn 	= $enter[$vo['enter_material_id']]['order_sn'];
				$enter_batch_number = $enter[$vo['enter_material_id']]['enter_batch_number'];
				$warehouse_id 		= $enter[$vo['enter_material_id']]['warehouse_id'];
				$update[] 			= ['id'=>$vo['enter_material_id'],'freeze_out_stock'=>Db::raw('freeze_out_stock+'.$vo['stock_num'])];
			}else{
				if(!empty($vo['enter_order_sn'])){
					$enter_order_sn 	= $vo['enter_order_sn'];
				}
				if(!empty($vo['enter_batch_number'])){
					$enter_batch_number	= $vo['enter_batch_number'];
				}	
				if(!empty($vo['warehouse_id'])){
					$warehouse_id		= $vo['warehouse_id'];
				}				
			}
			$data[] = ['material_stock_id'=>$model->id,'warehouse_id'=>$warehouse_id,'enter_material_id'=>$enter_material_id,'enter_order_sn'=>$enter_order_sn,'enter_batch_number'=>$enter_batch_number,'material_id'=>$vo['id'],'stock_num'=>$vo['stock_num'],'photo'=>empty($vo['photo'])?'':$vo['photo'],'data_id'=>empty($vo['data_id'])?0:$vo['data_id'],'remark'=>empty($vo['remark'])?'':$vo['remark']];
		}
		(new ErpMaterialDiscardMaterial)->saveAll($data);
		if($update){
			(new ErpMaterialEnterMaterial)->saveAll($update);
		}
    }
	
    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpMaterialDiscard::where($map)->find();
		}else{
			return ErpMaterialDiscard::find($map);
		}
    }
	
    // 编辑
    public static function goEdit($data)
    {
        //验证
        $validate 	= new ErpMaterialDiscardValidate;
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
			//ErpMaterialDiscardMaterial::where('material_stock_id',$model->id)->delete();
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
		$out_material 	= ErpMaterialDiscardMaterial::with(['out','detail','material'=>function($query){return $query->field('id,name,sn,stock,freeze_stock');}])->where('material_stock_id',$model->id)->where('id','in',$ids)->select();
		if($out_material->isEmpty() || $out_material->count() != count($ids)){
			return ['msg'=>'数据错误','code'=>201];
		}
		if(!ErpMaterialLogic::checkOutMaterialStock($out_material->column('material_id'))){
			return ['msg'=>'请先处理相关物料盘点单','code'=>201];
		}		
		
		$discard_material_update = [];
		$stock_record_insert 	= [];
		$stock_data				= [];
		$enter_material_update 	= [];
		
		$times					= ErpMaterialStockRecord::where('material_stock_id',$model->id)->max('times');
		$times					= $times?($times+1):1;
		$stock_type 			= ErpMaterialStockEnum::DISCARD_QUALITY==$model['type']?2:1;
		
		foreach($out_material as $key=>$vo){
			if(empty($num[$vo['id']])){
				return ['msg'=>$vo['material']['sn'].'出库数量不存在','code'=>201];
			}
			if(!$vo['can_out']){
				return ['msg'=>$vo['material']['sn'].'已全部出库或已作废','code'=>201];
			}
			$_num 		= $num[$vo['id']];
			if($_num != intval($_num) || $_num <= 0){
				return ['msg'=>$vo['material']['sn'].'出库数量必须为大于0的整数','code'=>201];
			}
			$max 		= $vo['stock_num'] - $vo['stocked_num'];
			if($_num > $max){
				return ['msg'=>$vo['material']['sn'].'出库数量最多只能为'.$max,'code'=>201];
			}
			$status 					= $_num == $max?ErpMaterialDiscardMaterialEnum::STATUS_FINISH:ErpMaterialDiscardMaterialEnum::STATUS_PART;
			$discard_material_update[] 	= ['id'=>$vo['id'],'stocked_num'=>$vo['stocked_num']+$_num,'status'=>$status];
			$stock_record_insert[] 		= ['data_type'=>'material_discard_material','data_id'=>$vo['id'],'material_stock_id'=>$vo['material_stock_id'],'material_id'=>$vo['material_id'],'stock_num'=>$_num,'times'=>$times];
			$stock_data[]				= ['stock_type'=>$stock_type,'enter_order_sn'=>$vo['enter_order_sn'],'enter_batch_number'=>$vo['enter_batch_number'],'num'=>$_num*-1,'material'=>$vo['material'],'material_id'=>$vo['material_id'],'warehouse_id'=>$vo['warehouse_id'],'material_stock_id'=>$model->id,'supplier_id'=>$model->supplier_id,'remark'=>''];
			
			if(!empty($vo['enter_material_id']) && ErpMaterialStockEnum::DISCARD_QUALITY != $model['type']){
				$enter_material_update[]= ['id'=>$vo['enter_material_id'],'can_out_num'=>Db::raw('can_out_num-'.$_num),'freeze_out_stock'=>Db::raw('freeze_out_stock-'.$_num)];
			}
		}
		Db::startTrans();
		try {
			(new ErpMaterialDiscardMaterial)->saveAll($discard_material_update);
			(new ErpMaterialStockRecord)->saveAll($stock_record_insert);
			
			ErpMaterialWarehouseLogic::goUpdateStock($stock_data,$stock_type);
			
			if(ErpMaterialDiscardMaterial::where('material_stock_id',$model->id)->where('status','in',[ErpMaterialDiscardMaterialEnum::STATUS_HANDLE,ErpMaterialDiscardMaterialEnum::STATUS_PART])->count() == 0){
				$model->save(['status'=>ErpMaterialStockEnum::STATUS_FINISH]);
			}
			
			if($enter_material_update){
				(new ErpMaterialEnterMaterial)->saveAll($enter_material_update);
			}
			Db::commit();
		}catch (\Exception $e){
			Db::rollback();
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
			$out_material 	= ErpMaterialDiscardMaterial::with(['material'=>function($query){return $query->field('id,name,sn,stock,freeze_stock');}])->append(['can_cancel'])->where('material_stock_id',$model->id)->where('id','in',$ids)->select();
		}else{
			$out_material 	= ErpMaterialDiscardMaterial::with(['material'=>function($query){return $query->field('id,name,sn,stock,freeze_stock');}])->append(['can_cancel'])->where('material_stock_id',$model->id)->select();
		}
		
		$discard_material_update		= [];
		$enter_material_update			= [];
		$material_update 				= [];
		
		foreach($out_material as $key=>$vo){
			if($vo['can_cancel']){
				$discard_material_update[]	= ['id'=>$vo['id'],'status'=>ErpMaterialDiscardMaterialEnum::STATUS_CANCEL];
				$num 						= $vo['stock_num'] - $vo['stocked_num'];
				if($num>0 && $vo['enter_material_id']){
					$enter_material_update[]= ['id'=>$vo['enter_material_id'],'freeze_out_stock'=>Db::raw('freeze_out_stock-'.$num)];
				}
				if($num>0 && ErpMaterialStockEnum::DISCARD_QUALITY == $model['type']){
					$material_update[] 		= ['id'=>$vo['material_id'],'freeze_stock'=>$vo['material']['freeze_stock']-$num];
				}
			}
		}
		try {
			(new ErpMaterialDiscardMaterial)->saveAll($discard_material_update);
			if(ErpMaterialDiscardMaterial::where('status','<>',ErpMaterialDiscardMaterialEnum::STATUS_CANCEL)->where('material_stock_id',$model->id)->count() == 0){
				$model->save(['status'=>ErpMaterialStockEnum::STATUS_CANCEL]);
			}
			if($enter_material_update){
				(new ErpMaterialEnterMaterial)->saveAll($enter_material_update);
			}
			if($material_update){
				(new ErpMaterial)->saveAll($material_update);
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
		
		$out_material 					= ErpMaterialDiscardMaterial::with(['material'=>function($query){return $query->field('id,name,sn,stock,freeze_stock');}])->append(['can_finish'])->where('material_stock_id',$model->id)->select();
		$discard_material_update		= [];
		$enter_material_update			= [];
		$material_update 				= [];
		foreach($out_material as $key=>$vo){
			if($vo['can_finish']){
				$discard_material_update[]	= ['id'=>$vo['id'],'status'=>ErpMaterialDiscardMaterialEnum::STATUS_FINISH];
				$num 					= $vo['stock_num'] - $vo['stocked_num'];
				if($num>0 && $vo['enter_material_id']){
					$enter_material_update[]= ['id'=>$vo['enter_material_id'],'freeze_out_stock'=>Db::raw('freeze_out_stock-'.$num)];
				}
				if($num>0 && ErpMaterialStockEnum::DISCARD_QUALITY == $model['type']){
					$material_update[] 	= ['id'=>$vo['material_id'],'freeze_stock'=>$vo['material']['freeze_stock']-$num];
				}
			}
		}		
		
		try {
			$model->save(['status'=>ErpMaterialStockEnum::STATUS_SETTLEMENT]);
			
			if($discard_material_update){
				(new ErpMaterialDiscardMaterial)->saveAll($discard_material_update);
			}
			if($enter_material_update){
				(new ErpMaterialEnterMaterial)->saveAll($enter_material_update);
			}
			if($material_update){
				(new ErpMaterial)->saveAll($material_update);
			}			
		}catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}

    public static function getCount($query){
		return ErpMaterialDiscard::withSearch(['query'],['query'=>$query])->field('id')->where('data_type',ErpMaterialStockEnum::DATA_TYPE_DISCARD)->order('id','desc')->count();
    }

	public static function goRemoveMaterial($id){
		$model 			= ErpMaterialDiscardMaterial::where('id',$id)->find();
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

	public static function goSend($id){
		$model 				= self::getOne($id);
        if(empty($model['id'])) {
			return ['msg'=>'出库单不存在','code'=>201];
		}
		if($model['can_send'] == false) {
			return ['msg'=>'状态错误','code'=>201];
		}
		try {
			$model->save(['supplier_status'=>ErpMaterialStockEnum::SUPPLIER_STATUS_YES]);		
		}catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}

}
