<?php
declare (strict_types = 1);
namespace app\index\logic;
use app\index\logic\BaseLogic;
use app\common\model\{ErpOrderProduce,ErpOrderProduceBom,ErpMaterialBom,ErpOrderProduceProcess};
use app\common\enum\{ErpOrderProduceEnum};
use think\facade\Db;
use app\admin\logic\{ErpMaterialEnterLogic};

class ErpOrderProduceLogic extends BaseLogic{

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$user_info 	= request()->userInfo;
		$map	 	= [];
		$map[]		= ['a.produce_status', '<>', ErpOrderProduceEnum::PRODUCE_STATUS_NO];
		$map[]		= ['a.approve_status', '=', ErpOrderProduceEnum::APPROVE_STATUS_YES];
		$map[]		= ['a.produce_type', '<>', ErpOrderProduceEnum::PRODUCE_TYPE_2];
		$map[]		= ['a.finish_date', '=', ''];
		

		if(!empty($query['produce_status'])) {
			$map[]	= ['a.produce_status', '=', $query['produce_status']];
        }
		if(!empty($query['region_type'])) {
			$map[]	= ['b.region_type', '=', $query['region_type']];
        }		
		if(!empty($query['customer_name'])) {
			$map[]	= ['b.customer_name', 'like', '%' . $query['customer_name'] . '%'];
        }
        if(!empty($query['order_sn'])) {
			$map[]	= ['b.order_sn', 'like', '%' . $query['order_sn'] . '%'];
        }
		if(!empty($query['delivery_time'])) {
			$time 		= is_array($query['delivery_time'])?$query['delivery_time']:explode('至',$query['delivery_time']);
			if(!empty($time[0])){
				$map[]	= ['b.delivery_time', '>=', strtotime(trim($time[0]))];
			}
			if(!empty($time[1])){
				$map[]	= ['b.delivery_time', '<', strtotime(trim($time[1]))+24*3600];
			}
        }
		if(!empty($query['create_time'])) {
			$time 		= is_array($query['create_time'])?$query['create_time']:explode('至',$query['create_time']);
			if(!empty($time[0])){
				$map[]	= ['b.create_time', '>=', strtotime(trim($time[0]))];
			}
			if(!empty($time[1])){
				$map[]	= ['b.create_time', '<', strtotime(trim($time[1]))+24*3600];
			}
        }	
		if(!empty($query['produce_date'])) {
			$time 		= is_array($query['produce_date'])?$query['produce_date']:explode('至',$query['produce_date']);
			if(!empty($time[0])){
				$map[]	= ['a.produce_date', '>=', (trim($time[0]))];
			}
			if(!empty($time[1])){
				$map[]	= ['a.produce_date', '<=', (trim($time[1]))];
			}
        }		
		$field 			= 'a.*,b.create_time as order_create_time,b.order_sn as sale_order_sn,b.delivery_time,b.address,b.customer_name,b.salesman_id,b.order_remark,b.shipping_type,b.order_type,c.product_name,c.product_model,c.product_specs,c.is_pause,c.remark,d.qc_file,d.produce_file';		
		$list 			= ErpOrderProduce::alias('a')
		->join('erp_order b','a.order_id = b.id','LEFT')
		->join('erp_order_product c','a.order_product_id = c.id','LEFT')
		->join('erp_product d','a.product_id = d.id','LEFT')
		->with(['order_product'])
		->where($map)->field($field)->order('a.produce_date asc,a.order_sn asc')->append(['order_type_desc','order_product.project_html'])->paginate($limit);
		
		$data 			= $list->items();

		$process 		= ErpOrderProduceProcess::alias('a')
		->join('erp_process b','a.process_id = b.id','LEFT')
		->where('a.order_produce_id','in',array_column($data,'id'))
		->where('a.status',0)
		->where('b.user_id','find in set',$user_info['user_id'])->group('a.order_produce_id')->column('a.order_produce_id');
		
		foreach($data as &$vo){
			$vo['show_follow'] 	= $process&&in_array($vo['id'],$process)?1:0;
			$vo['order_create_time'] = date('Y-m-d',$vo['order_create_time']);
		}

        return ['code'=>0,'data'=>$data,'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpOrderProduce::where($map)->find();
		}else{
			return ErpOrderProduce::find($map);
		}
    }

	public static function goSigned($ids,$num,$from='allocate'){
		$user_info 			= request()->userInfo;
		$allocate_material = ErpOrderProduce::with(['allocate'=>function($query){return $query->field('id,from_warehouse_id,to_warehouse_id');}])->where('id','in',$ids)->where('status',ErpOrderProduceEnum::STATUS_HANDLE)->select();
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
			$update[] 				= ['id'=>$vo['id'],'signed_date'=>date('Y-m-d'),'signed_username'=>$user_info['name'],'status'=>ErpOrderProduceEnum::STATUS_SIGNED,'signed_num'=>$_num];
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
			
			(new ErpOrderProduce)->saveAll($update);
			
			foreach($enter_material_update as $vo){
				ErpMaterialEnterMaterial::where('id',$vo['id'])->update($vo);
			}
			
			foreach($material_stock_update as $vo){
				if(ErpOrderProduce::where('status',ErpOrderProduceEnum::STATUS_HANDLE)->where('material_stock_id',$vo)->count() == 0){
					ErpMaterialAllocate::where('id',$vo)->update(['status'=>ErpMaterialStockEnum::STATUS_FINISH]);
				}
			}
			return ['msg'=>'操作成功','code'=>200];
		}catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}

	public static function getProduceBom($id){
		$tmp 			= ErpOrderProduceBom::alias('a')
		->join('erp_material b','a.material_id = b.id','LEFT')
		->field('a.num,a.material_type,a.material_id,a.material_color,b.produce_type,b.name as material_name,b.sn as material_sn,b.unit as material_unit,b.surface as material_surface,b.material as material_stuff,b.remark,b.cid,b.stock')
		->where('a.order_produce_id','=',$id)->where('a.type',1)
		->select()->toArray();
		
		$bom 			= [];
		foreach($tmp as $vo){
			
			if($vo['material_type'] == 1){
				$bom[] 		= $vo;
			}else{
				$tmp2 	= ErpMaterialBom::alias('a')
					->join('erp_material b','a.related_material_id = b.id','LEFT')
					->field('a.num,a.related_material_id as material_id,b.type as material_type,b.produce_type,b.name as material_name,b.sn as material_sn,b.unit as material_unit,b.surface as material_surface,b.material as material_stuff,b.remark,b.cid,b.stock')
					->where('a.material_id','=',$vo['material_id'])
					->select()->toArray();
				foreach($tmp2 as $v2){
					$v2['num'] 	= $vo['num']*$v2['num'];
					$bom[] 		= $v2;
				}
			}
		}
		return $bom;
	}
	
	
    // 编辑
    public static function goEdit($data)
    {
		$model 		= self::getOne($data['id']);
        if($model->isEmpty()) {
			return ['msg'=>'数据不存在','code'=>201];
		}
        try {
            $model->save($data);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }	
	
    public static function getStat()
    {
		$map	 	= [];
		$map[]		= ['a.produce_status', '<>', ErpOrderProduceEnum::PRODUCE_STATUS_NO];
		$map[]		= ['a.approve_status', '=', ErpOrderProduceEnum::APPROVE_STATUS_YES];
		$map[]		= ['a.produce_type', '<>', ErpOrderProduceEnum::PRODUCE_TYPE_2];
		$map[]		= ['a.finish_date', '=', ''];
		
		$field 			= 'a.*,b.create_time as order_create_time,b.order_sn as sale_order_sn,b.delivery_time,b.region_type,b.address,b.customer_name,b.salesman_id,b.order_remark,b.shipping_type,b.order_type';		
		$list 			= ErpOrderProduce::alias('a')
		->join('erp_order b','a.order_id = b.id','LEFT')
		->with(['order_product'])
		->where($map)->field($field)->order('a.produce_date asc,a.order_sn asc')->append(['order_type_desc','order_product.project_html'])->group('order_id,order_sn,product_id')->select();
		
		$data = [];
		foreach($list as $vo){
			$vo['num'] = ErpOrderProduce::where('order_id',$vo['order_id'])->where('order_sn',$vo['order_sn'])->where('product_id',$vo['product_id'])->field('id')->count();
			$vo['order_create_time'] = date('Y-m-d',$vo['order_create_time']);
			$data[$vo['region_type']][] = $vo;
		}
		return $data;
    }	
	
	
}
