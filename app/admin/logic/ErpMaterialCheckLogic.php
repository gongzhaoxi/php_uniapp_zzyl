<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\{ErpMaterial,ErpMaterialCheck,ErpMaterialCheckMaterial,ErpMaterialChange,ErpMaterialStockRecord,ErpMaterialEnterMaterial,ErpMaterialOutMaterial,ErpMaterialEnter,ErpMaterialOut,ErpMaterialWarehouse};
use app\admin\validate\ErpMaterialCheckValidate;
use app\common\enum\{ErpMaterialStockEnum,ErpMaterialCheckMaterialEnum,ErpMaterialEnterMaterialEnum,ErpMaterialOutMaterialEnum};
use think\facade\Db;

class ErpMaterialCheckLogic extends BaseLogic{

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$field = 'id,status,order_sn,type,stock_date,create_admin';
        $list = ErpMaterialCheck::withSearch(['query'],['query'=>$query])->field($field)->where('data_type',ErpMaterialStockEnum::DATA_TYPE_CHECK)->order('id','desc')->append(['can_cancel','can_settle','status_desc','type_desc','can_edit'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

	// 获取物料
    public static function getMaterial($query=[],$limit=10)
    {
		$field 		= 'a.id,a.material_stock_id,a.material_id,a.stock_num,a.status,a.stock_before,a.stock_after,a.remark,a.enter_order_sn,a.enter_batch_number';	
        $query['_alias']= 'a';
		$query['_material_alias']= 'b';
		$list 		= ErpMaterialCheckMaterial::alias('a')->join('erp_material b','a.material_id = b.id','LEFT')->withSearch(['query'],['query'=>$query])->with(['material'=>function($query){return $query->field('id,status,type,name,sn,cid,stock,unit,material,surface,color,remark,photo,status');}])->field($field)->order('a.id','desc')->append(['status_desc','material.category_name','can_cancel','can_check'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }


    // 添加
    public static function goAdd($data)
    {
        //验证
        $validate 	= new ErpMaterialCheckValidate;
        if(!$validate->scene('add')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		$material	= $data['material'];
		if(!ErpMaterialLogic::checkResetMaterialStock(array_column($material,'id'))){
			return ['msg'=>'请先处理物料出库单/退货单','code'=>201];
		}	
		foreach($material as $vo){
			if(empty($vo['enter_material_id'])){
				return ['msg'=>'请选择入库批次号','code'=>201];
			}
			if(!isset($vo['stock_after'])){
				return ['msg'=>$vo['id'].'盘后库存量不存在','code'=>201];
			}
			if($vo['stock_after'] != intval($vo['stock_after']) ){
				return ['msg'=>$vo['sn'].'盘后库存量必须为整数','code'=>201];
			}
			if($vo['stock_after'] < 0){
				return ['msg'=>$vo['sn'].'盘后库存量必须大于等于0','code'=>201];
			}	
		}		
		
		unset($data['material']);
        try {
			$count 					= ErpMaterialCheck::withTrashed()->where('data_type',ErpMaterialStockEnum::DATA_TYPE_CHECK)->whereDay('create_time')->count() + 1;
			$data['order_sn']		= 'PD'.date('Ymd').sprintf("%03d",$count);
			$data['create_admin'] 	= empty($data['username'])?(empty(self::$adminUser['username'])?'':self::$adminUser['username']):$data['username'];
			$data['data_type']		= ErpMaterialStockEnum::DATA_TYPE_CHECK;
            $model 					= ErpMaterialCheck::create($data);
			self::insertMaterial($model,$material);
			return ['msg'=>'创建成功','code'=>200,'data'=>['id'=>$model->id]];
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
    
	public static function insertMaterial($model,$material)
    {
		$enter 		= ErpMaterialEnterMaterial::alias('a')->join('erp_material_stock b','a.material_stock_id = b.id','LEFT')->where('a.id','in',array_column($material,'enter_material_id'))->column('a.enter_batch_number,b.order_sn','a.id');
		$data 		= [];
		foreach($material as $vo){
			$data[] = ['material_stock_id'=>$model->id,'enter_material_id'=>$vo['enter_material_id'],'enter_order_sn'=>$enter[$vo['enter_material_id']]['order_sn'],'enter_batch_number'=>$enter[$vo['enter_material_id']]['enter_batch_number'],'warehouse_id'=>$vo['warehouse_id'],'material_id'=>$vo['id'],'stock_num'=>$vo['stock_after']-$vo['stock_before'],'stock_before'=>$vo['stock_before'],'stock_after'=>$vo['stock_after'],'remark'=>empty($vo['remark'])?'':$vo['remark']];
		}
		(new ErpMaterialCheckMaterial)->saveAll($data);
    }
	
    public static function getOne($map,$with_materials=false)
    {
		if(is_array($map)){
			return $with_materials?ErpMaterialCheck::with(['materials.warehouse'])->where($map)->find():ErpMaterialCheck::where($map)->find();
		}else{
			return $with_materials?ErpMaterialCheck::with(['materials.warehouse'])->find($map):ErpMaterialCheck::find($map);
		}
    }
	
    // 编辑
    public static function goEdit($data)
    {
        //验证
        $validate 	= new ErpMaterialCheckValidate;
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
		if(!ErpMaterialLogic::checkResetMaterialStock(array_column($material,'id'))){
			return ['msg'=>'请先处理物料出库单/退货单','code'=>201];
		}
		foreach($material as $vo){
			if(empty($vo['enter_material_id'])){
				return ['msg'=>'请选择入库批次号','code'=>201];
			}
			if(empty($vo['stock_after'])){
				return ['msg'=>$vo['material']['sn'].'盘后库存量不存在','code'=>201];
			}
			if($vo['stock_after'] != intval($vo['stock_after']) ){
				return ['msg'=>$vo['material']['sn'].'盘后库存量必须为整数','code'=>201];
			}
			if($vo['stock_after'] < 0){
				return ['msg'=>$vo['material']['sn'].'盘后库存量必须大于等于0','code'=>201];
			}
		}	
		unset($data['material']);
        try {
            $model->save($data);	
			ErpMaterialCheckMaterial::where('material_stock_id',$model->id)->delete();
			self::insertMaterial($model,$material);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

	public static function goConfirm($id,$ids,$num){
		$model 			= self::getOne($id);
        if(empty($model['id'])) {
			return ['msg'=>'数据不存在','code'=>201];
		}
		$check_material 	= ErpMaterialCheckMaterial::with(['material'=>function($query){return $query->field('id,name,sn,stock,freeze_stock');}])->where('material_stock_id',$model->id)->where('id','in',$ids)->select();
		if($check_material->isEmpty() || $check_material->count() != count($ids)){
			return ['msg'=>'数据错误','code'=>201];
		}
		if(!ErpMaterialLogic::checkResetMaterialStock($check_material->column('material_id'))){
			return ['msg'=>'请先处理物料出库单/退货单','code'=>201];
		}		
		$check_material_update 	= [];
		$stock_data				= [];
		$enter_update 			= [];
		
		foreach($check_material as $key=>$vo){
			if(!isset($num[$vo['id']])){
				return ['msg'=>$vo['material']['sn'].'盘后库存量不存在','code'=>201];
			}
			if(!$vo['can_check']){
				return ['msg'=>$vo['material']['sn'].'已校正或已作废','code'=>201];
			}
			$_num 				= $num[$vo['id']];
			if($_num != intval($_num) ){
				return ['msg'=>$vo['material']['sn'].'盘后库存量必须为整数','code'=>201];
			}
			if($_num < 0){
				return ['msg'=>$vo['material']['sn'].'盘后库存量必须大于等于0','code'=>201];
			}
			$status 					= ErpMaterialCheckMaterialEnum::STATUS_FINISH;
			$stock_num					= $_num - $vo['stock_before'];//变动数量
			$check_material_update[] 	= ['id'=>$vo['id'],'stock_after'=>$_num,'stock_num'=>$stock_num,'status'=>$status];
			$stock_data[]				= ['stock_type'=>1,'num'=>$stock_num,'enter_batch_number'=>$vo['enter_batch_number'],'enter_order_sn'=>$vo['enter_order_sn'],'material_id'=>$vo['material_id'],'warehouse_id'=>$vo['warehouse_id'],'material_stock_id'=>$model->id,'supplier_id'=>$model->supplier_id,'remark'=>''];
			$enter_update[] 			= ['id'=>$vo['enter_material_id'],'can_out_num'=>$_num];
		}

		Db::startTrans();
		try {
			ErpMaterialWarehouseLogic::goUpdateStock($stock_data,1);
			(new ErpMaterialCheckMaterial)->saveAll($check_material_update);
			(new ErpMaterialEnterMaterial)->saveAll($enter_update);
			if(ErpMaterialCheckMaterial::where('material_stock_id',$model->id)->where('status','in',[ErpMaterialCheckMaterialEnum::STATUS_HANDLE])->count() == 0){
				$model->save(['status'=>ErpMaterialStockEnum::STATUS_FINISH]);
			}
			Db::commit();
		}catch (\Exception $e){
			Db::rollback();
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}	
	
	public static function goCancel($material_stock_id,$id){
		$model 				= self::getOne($material_stock_id);
        if(empty($model['id'])) {
			return ['msg'=>'盘点单不存在','code'=>201];
		}
		if($model['can_cancel'] == false) {
			return ['msg'=>'盘点单已作废或已结算','code'=>201];
		}	
		if($id){
			$check_material 	= ErpMaterialCheckMaterial::with(['material'=>function($query){return $query->field('id,name,sn,stock');}])->append(['can_cancel'])->where('material_stock_id',$model->id)->where('id','in',$id)->select();
		}else{
			$check_material 	= ErpMaterialCheckMaterial::with(['material'=>function($query){return $query->field('id,name,sn,stock');}])->append(['can_cancel'])->where('material_stock_id',$model->id)->select();
		}
		
		$check_material_update			= [];
		foreach($check_material as $key=>$vo){
			if($vo['can_cancel']){
				$check_material_update[]	= ['id'=>$vo['id'],'status'=>ErpMaterialCheckMaterialEnum::STATUS_CANCEL];
			}
		}
		try {
			(new ErpMaterialCheckMaterial)->saveAll($check_material_update);
			if(ErpMaterialCheckMaterial::where('status','<>',ErpMaterialCheckMaterialEnum::STATUS_CANCEL)->where('material_stock_id',$model->id)->count() == 0){
				$model->save(['status'=>ErpMaterialStockEnum::STATUS_CANCEL]);
			}
		}catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}	
	
	public static function goSettle($id){
		$model 				= self::getOne($id);
        if(empty($model['id'])) {
			return ['msg'=>'盘点单不存在','code'=>201];
		}
		if($model['can_settle'] == false) {
			return ['msg'=>'盘点单已作废或已结算','code'=>201];
		}
		try {
			$model->save(['status'=>ErpMaterialStockEnum::STATUS_SETTLEMENT]);
		}catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}

    public static function getCount($query){
		return ErpMaterialCheck::withSearch(['query'],['query'=>$query])->field('id')->where('data_type',ErpMaterialStockEnum::DATA_TYPE_CHECK)->order('id','desc')->count();
    }


}
