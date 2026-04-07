<?php
declare (strict_types = 1);
namespace app\index\logic;
use app\index\logic\BaseLogic;
use app\common\model\{ErpMaterialAllocateMaterial,ErpMaterialOutMaterial,ErpMaterialEnterMaterial,ErpMaterialWarehouse,ErpMaterialEnter,ErpMaterialOut,ErpMaterial,ErpMaterialAllocate};
use app\common\enum\{ErpMaterialAllocateMaterialEnum,ErpMaterialEnterMaterialEnum,ErpMaterialOutMaterialEnum,ErpMaterialStockEnum};
use think\facade\Db;
use app\admin\logic\{ErpMaterialEnterLogic,ErpMaterialOutLogic,ErpMaterialChangeLogic,ErpMaterialWarehouseLogic};

class ErpMaterialAllocateMaterialLogic extends BaseLogic{

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$user_info 			= request()->userInfo;
		$field 				= 'a.id,a.enter_order_sn,a.stock_num,a.stock_num,a.send_status,a.stock_num as signed_num,b.name,b.sn,b.unit,c.order_sn';	
		$query['_alias']	= 'a';
		$query['_material_alias']= 'b';
		$list 				= ErpMaterialAllocateMaterial::alias('a')
		->join('erp_material b','a.material_id = b.id','LEFT')
		->join('erp_material_stock c','a.material_stock_id = c.id','LEFT')
		->withSearch(['query'],['query'=>$query])->where('a.status',ErpMaterialAllocateMaterialEnum::STATUS_HANDLE)->where('c.to_warehouse_id','in',$user_info['warehouse_id'])
		->where('a.send_status','1')->where('a.status','1')
		->field($field)->order('a.id','desc')->paginate($limit);

        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpMaterialAllocateMaterial::where($map)->find();
		}else{
			return ErpMaterialAllocateMaterial::find($map);
		}
    }

	public static function goSigned($ids,$num,$from='allocate'){
		$user_info 			= request()->userInfo;
		$allocate_material = ErpMaterialAllocateMaterial::with(['allocate'=>function($query){return $query->field('id,from_warehouse_id,to_warehouse_id');}])->where('id','in',$ids)->where('status',ErpMaterialAllocateMaterialEnum::STATUS_HANDLE)->select();
		if($allocate_material->isEmpty() || $allocate_material->count() != count($ids)){
			return ['msg'=>'数据错误','code'=>201];
		}
		$material					= ErpMaterial::where('id','in',$allocate_material->column('material_id'))->column('id,name,sn,type,stock,freeze_stock','id');
		
		$update 				= [];
		$enter_material			= [];
		$out_material			= [];
		$enter_material_update 	= [];
		$out_material_change	= [];
		$enter_material_change	= [];
		$material_stock_update 	= [];
		
		foreach($allocate_material as $key=>$vo){
			if(empty($num[$vo['id']])){
				return ['msg'=>$material[$vo['material_id']]['sn'].'签收数量不存在','code'=>201];
			}
			$_num 		= $num[$vo['id']];
			if($_num != intval($_num) || $_num <= 0){
				return ['msg'=>$material[$vo['material_id']]['sn'].'签收数量必须为大于0的整数','code'=>201];
			}		
			if($_num > $vo['stock_num']){
				return ['msg'=>$material[$vo['material_id']]['sn'].'签收数量最多只能为'.$vo['stock_num'],'code'=>201];
			}
			
			$material_type 			= $material[$vo['material_id']]['type'];
			$update[] 				= ['id'=>$vo['id'],'signed_date'=>date('Y-m-d'),'signed_username'=>$user_info['name'],'status'=>ErpMaterialAllocateMaterialEnum::STATUS_SIGNED,'signed_num'=>$_num];
			$enter_material_update[]= ['id'=>$vo['enter_material_id'],'freeze_out_stock'=>Db::raw('freeze_out_stock-'.$_num),'can_out_num'=>Db::raw('can_out_num-'.$_num)];

			$out_material[$material_type][]			= ['warehouse_id'=>$vo['allocate']['from_warehouse_id'],'material_stock_id'=>0,'enter_material_id'=>$vo['enter_material_id'],'enter_batch_number'=>$vo['enter_batch_number'],'enter_order_sn'=>$vo['enter_order_sn'],'stock_num'=>$_num,'stocked_num'=>$_num,'material_id'=>$vo['material_id'],'data_id'=>0,'status'=>ErpMaterialOutMaterialEnum::STATUS_FINISH];	
			$out_material_change[$material_type][]	= ['stock_type'=>1,'num'=>$_num*-1,'enter_batch_number'=>$vo['enter_batch_number'],'enter_order_sn'=>$vo['enter_order_sn'],'material_id'=>$vo['material_id'],'warehouse_id'=>$vo['allocate']['from_warehouse_id'],'material_stock_id'=>0,'supplier_id'=>0];		
			
			$enter_material[$material_type][] 		= ['id'=>$vo['material_id'],'enter_batch_number'=>$vo['enter_batch_number'],'enter_order_sn'=>$vo['enter_order_sn'],'warehouse_id'=>$vo['allocate']['to_warehouse_id'],'stock_num'=>$_num,'stocked_num'=>$_num,'can_out_num'=>$_num,'qualities_num'=>$_num,'status'=>ErpMaterialEnterMaterialEnum::STATUS_FINISH];
			$enter_material_change[$material_type][]= ['stock_type'=>1,'num'=>$_num,'enter_batch_number'=>$vo['enter_batch_number'],'enter_order_sn'=>$vo['enter_order_sn'],'material_id'=>$vo['material_id'],'warehouse_id'=>$vo['allocate']['to_warehouse_id'],'material_stock_id'=>0,'supplier_id'=>0];		
		
			if(!in_array($vo['material_stock_id'],$material_stock_update)){
				$material_stock_update[] 	= $vo['material_stock_id'];
			}
		}
		try {
			if($from == 'back_warehouse'){
				$enter_type = ErpMaterialStockEnum::TYPE_ENTER_BACK_WAREHOUSE;
				$out_type 	= ErpMaterialStockEnum::TYPE_OUT_BACK_WAREHOUSE;
			}else{
				$enter_type = ErpMaterialStockEnum::TYPE_ENTER_ALLOCATE;
				$out_type 	= ErpMaterialStockEnum::TYPE_OUT_ALLOCATE;
			}

			if(!empty($out_material[1])){
				$order_sn 				= ErpMaterialOutLogic::createOrderSn();
				$out 					= ErpMaterialOut::create(['create_admin'=>'','data_type'=>ErpMaterialStockEnum::DATA_TYPE_OUT,'order_sn'=>$order_sn,'batch_number'=>$order_sn,'type'=>$out_type,'remark'=>'','status'=>ErpMaterialStockEnum::STATUS_FINISH,'material_type'=>1,'stock_date'=>date('Y-m-d'),'supplier_id'=>0]);
				foreach($out_material[1] as $k=>$vo){
					$out_material[1][$k]['material_stock_id'] 		= $out['id'];
				}
				(new ErpMaterialOutMaterial)->saveAll($out_material[1]);
				
				foreach($out_material_change[1] as $k=>$vo){
					$out_material_change[1][$k]['material_stock_id']= $out['id'];
				}
				ErpMaterialWarehouseLogic::goUpdateStock($out_material_change[1],1);
			}
			
			if(!empty($out_material[2])){
				$order_sn 				= ErpMaterialOutLogic::createOrderSn();
				$out 					= ErpMaterialOut::create(['create_admin'=>'','data_type'=>ErpMaterialStockEnum::DATA_TYPE_OUT,'order_sn'=>$order_sn,'batch_number'=>$order_sn,'type'=>$out_type,'remark'=>'','status'=>ErpMaterialStockEnum::STATUS_FINISH,'material_type'=>2,'stock_date'=>date('Y-m-d'),'supplier_id'=>0]);
				foreach($out_material[2] as $k=>$vo){
					$out_material[2][$k]['material_stock_id'] 		= $out['id'];
				}
				(new ErpMaterialOutMaterial)->saveAll($out_material[2]);
				
				foreach($out_material_change[2] as $k=>$vo){
					$out_material_change[2][$k]['material_stock_id']= $out['id'];
				}
				ErpMaterialWarehouseLogic::goUpdateStock($out_material_change[2],1);
			}			
			
			if(!empty($enter_material[1])){
				$order_sn 				= ErpMaterialEnterLogic::createOrderSn();
				$enter 					= ErpMaterialEnter::create(['create_admin'=>'','data_type'=>ErpMaterialStockEnum::DATA_TYPE_ENTER,'order_sn'=>$order_sn,'batch_number'=>$order_sn,'type'=>$enter_type,'remark'=>'','status'=>ErpMaterialStockEnum::STATUS_FINISH,'material_type'=>1,'stock_date'=>date('Y-m-d'),'supplier_id'=>0]);
				ErpMaterialEnterLogic::insertMaterial($enter,$enter_material[1]);

				foreach($enter_material_change[1] as $k=>$vo){
					$enter_material_change[1][$k]['material_stock_id']= $enter['id'];
				}
				ErpMaterialWarehouseLogic::goUpdateStock($enter_material_change[1],1);
			}
			if(!empty($enter_material[2])){
				$order_sn 				= ErpMaterialEnterLogic::createOrderSn();
				$enter 					= ErpMaterialEnter::create(['create_admin'=>'','data_type'=>ErpMaterialStockEnum::DATA_TYPE_ENTER,'order_sn'=>$order_sn,'batch_number'=>$order_sn,'type'=>$enter_type,'remark'=>'','status'=>ErpMaterialStockEnum::STATUS_FINISH,'material_type'=>2,'stock_date'=>date('Y-m-d'),'supplier_id'=>0]);
				ErpMaterialEnterLogic::insertMaterial($enter,$enter_material[2]);
				foreach($enter_material_change[2] as $k=>$vo){
					$enter_material_change[2][$k]['material_stock_id']= $enter['id'];
				}
				ErpMaterialWarehouseLogic::goUpdateStock($enter_material_change[2],1);
			}
			
			(new ErpMaterialAllocateMaterial)->saveAll($update);
			
			foreach($enter_material_update as $vo){
				ErpMaterialEnterMaterial::where('id',$vo['id'])->update($vo);
			}
			
			foreach($material_stock_update as $vo){
				if(ErpMaterialAllocateMaterial::where('status',ErpMaterialAllocateMaterialEnum::STATUS_HANDLE)->where('material_stock_id',$vo)->count() == 0){
					ErpMaterialAllocate::where('id',$vo)->update(['status'=>ErpMaterialStockEnum::STATUS_FINISH]);
				}
			}
			return ['msg'=>'操作成功','code'=>200];
		}catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}

	
}
