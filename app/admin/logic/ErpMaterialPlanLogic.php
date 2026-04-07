<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\ErpMaterialPlan;
use app\common\model\ErpMaterial;
use app\common\model\ErpOrderProduceBom;
use app\common\model\ErpMaterialBom;
use app\common\model\ErpProduct;
use app\common\model\{ErpProductBom,ErpMaterialEnter,ErpMaterialEnterMaterial,ErpMaterialPlanProcess,ErpProcess,ErpProcessWage,ErpMaterialPlanFollow,ErpSupplier,ErpMaterialCode};
use app\admin\validate\ErpMaterialPlanValidate;
use app\common\enum\{ErpMaterialEnum,ErpMaterialPlanEnum,ErpMaterialStockEnum};
use think\facade\Db;


class ErpMaterialPlanLogic extends BaseLogic{

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$map 		= [];
		if (!empty($query['plan_sn'])) {
			$map[]	= ['a.plan_sn', 'like', '%' . $query['plan_sn'] . '%'];
        }
		if (!empty($query['name'])) {
			$map[]	= ['b.name', 'like', '%' . $query['name'] . '%'];
        }		
        if (!empty($query['status'])) {
			$map[] 	= ['a.status', 'in', $query['status']];
        }
        if (!empty($query['type'])) {
			$map[] 	= ['a.type', '=', $query['type']];
        }		
        if (!empty($query['create_time'])) {
			$create_time = is_array($query['create_time'])?$query['create_time']:explode('至',$query['create_time']);
			if(!empty($create_time[0])){
				$map[] 	= ['a.create_time', '>', strtotime(trim($create_time[0]))];
			}
			if(!empty($create_time[1])){
				$map[] 	= ['a.create_time', '<', strtotime(trim($create_time[1]))+24*3600];
			}
        }
        if (!empty($query['start_date'])) {
			$start_date = is_array($query['start_date'])?$query['start_date']:explode('至',$query['start_date']);
			if(!empty($start_date[0])){
				$map[] 	= ['a.start_date', '>=', (trim($start_date[0]))];
			}
			if(!empty($start_date[1])){
				$map[] 	= ['a.start_date', '<=', (trim($start_date[1]))];
			}
        }
		if (!empty($query['sn'])) {
			$map[]		= ['b.sn', 'like', '%' . $query['sn'] . '%'];
        }		
		$field	= 'a.*,b.name,b.sn';
        $list 	= ErpMaterialPlan::alias('a')->join('erp_material b','a.material_id = b.id','LEFT')->where($map)->field($field)->order('a.id','desc')->append(['status_desc','type_desc'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }


	// 获取部件
    public static function getMaterial($query=[],$limit=10)
    {
		$query['type'] 	= ErpMaterialEnum::COMPONENT;
		$field 			= 'id,status,type,name,sn,cid,safety_stock,min_stock,max_stock,unit,processing_type,material,surface,color,remark,photo,warehouse_id,status,stock';
        $list 			= ErpMaterial::withSearch(['query'],['query'=>$query])->field($field)->order('id','desc')->paginate($limit)->each(function($item, $key){
			$item['num']= ErpOrderProduceBom::where('material_id',$item['id'])->where('create_time','>=',strtotime("-3 month") )->sum('num');
			return $item;
		});
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }
	
	// 获取部件bom
    public static function getMaterialBom($material_id,$num)
    {
		$bom = ErpMaterialBom::where('material_id',$material_id)->with(['related_material'])->select()->each(function($item, $key) use($num){
			$item['num']		= $item['num']*$num;
			$item['lack_num']	= $item['related_material']['stock']>0&&$item['related_material']['stock']<=$item['num']?($item['num'] -  $item['related_material']['stock']):$item['num'];
			return $item;
		});
		return $bom;
    }

    // 添加
    public static function goAdd($param)
    {
        //验证
        $validate 		= new ErpMaterialPlanValidate;
        if(!$validate->scene('add')->check($param)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
        try {
			$data 		= [];
			$sn			= self::getPlanSn();
			foreach($param['material'] as $vo){
				$data[]	= ['plan_sn'=>$sn,'type'=>ErpMaterialPlanEnum::TYPE_PLAN,'num'=>$vo['num'],'material_id'=>$vo['id']];
			}
			(new ErpMaterialPlan)->saveAll($data);
			
			return ['msg'=>'操作成功','code'=>200,'data'=>['id'=>implode(',',ErpMaterialPlan::where('plan_sn',$sn)->column('id'))]];
			
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
    
    public static function getPlanSn()
    {
		$count 		= ErpMaterialPlan::withTrashed()->whereDay('create_time')->group('plan_sn')->count() + 1;
		return date('ymd').sprintf("%02d",$count);
    }	

	
    public static function getPlans($id)
    {
		$data 	= ErpMaterialPlan::where('id','in',$id)->with(['material'])->append(['material.category_name'])->select()->each(function($item, $key){
			$item['newly_num']= ErpOrderProduceBom::where('material_id',$item['material_id'])->where('create_time','>=',strtotime("-3 month") )->sum('num');
			return $item;
		});
		return $data;
    }	
	
    // 下达
    public static function goStart($data)
    {
        //验证
        $validate 	= new ErpMaterialPlanValidate;
        if(!$validate->scene('start')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		$plan 			= ErpMaterialPlan::where('id','in',$data['id'])->where('status','=',ErpMaterialPlanEnum::STATUS_NO_ASSIGN)->select();
		$process 		= ErpProcess::where('type',2)->with(['follow'])->order(['sort'=>'asc','id'=>'asc'])->select();
		$tmp 			= ErpProcessWage::where('process_id','in',$process->column('id'))->where('product_id','in',$plan->column('material_id'))->select();
		$process_wage	= [];
		foreach($tmp as $vo){
			$process_wage[$vo['product_id']][$vo['process_id']] = $vo['price'];
		}

		$plan_process 	= [];
		foreach($plan as $v){
			foreach($process as $vo){
				$plan_process[]	= ['plan_id'=>$v['id'],'process_id'=>$vo['id'],'process_name'=>$vo['name'],'follow_name'=>empty($vo['follow'])?'':$vo['follow']['name'],'material_id'=>$v['material_id'],'price'=>empty($process_wage[$v['material_id']][$vo['id']])?0:$process_wage[$v['material_id']][$vo['id']]];
			}
		}		
        try {
			(new ErpMaterialPlanProcess)->saveAll($plan_process);
			ErpMaterialPlan::where('id','in',$data['id'])->where('status','=',ErpMaterialPlanEnum::STATUS_NO_ASSIGN)->update(['start_date'=>$data['start_date'],'status'=>ErpMaterialPlanEnum::STATUS_ASSIGNED]);
			
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }	
	
    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpMaterialPlan::where($map)->find();
		}else{
			return ErpMaterialPlan::find($map);
		}
    }

	// 撤回下达
    public static function goCancel($data){
		//验证
        $validate 	= new ErpMaterialPlanValidate;
        if(!$validate->scene('remove')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}	
        try{
			$ids = ErpMaterialPlan::where('id','in',$data['ids'])->where('status','=',ErpMaterialPlanEnum::STATUS_ASSIGNED)->column('id');
			ErpMaterialPlan::where('id','in',$ids)->update(['start_date'=>'','status'=>ErpMaterialPlanEnum::STATUS_NO_ASSIGN]);
			ErpMaterialPlanProcess::where('plan_id','in',$ids)->delete();
		}catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

	// 入库
    public static function goWarehouse($id){
		$check_material 	= ErpMaterialPlan::with(['material'=>function($query){return $query->field('id,name,sn,stock,freeze_stock');}])->where('id','in',$id)->select();
		if($check_material->isEmpty()){
			return ['msg'=>'数据错误','code'=>201];
		}
		Db::startTrans();
        try{
			ErpMaterialPlan::where('id','in',$id)->update(['status'=>ErpMaterialPlanEnum::STATUS_WAREHOUSED]);
			$enter_material			= [];
			foreach($check_material as $key=>$vo){
				$enter_material[] 	= ['id'=>$vo['material_id'],'stock_num'=>$vo['num'],'stocked_num'=>0,'can_out_num'=>0,'quality_num'=>$vo['num'],'qualities_num'=>$vo['num']];
			}
			$order_sn 				= ErpMaterialEnterLogic::createOrderSn();
			$enter 					= ErpMaterialEnter::create(['create_admin'=>self::$adminUser['username'],'data_type'=>ErpMaterialStockEnum::DATA_TYPE_ENTER,'order_sn'=>$order_sn,'batch_number'=>$order_sn,'enter_batch_number'=>'','type'=>ErpMaterialStockEnum::TYPE_ENTER_PLAN,'remark'=>'计划自动入库','status'=>ErpMaterialStockEnum::STATUS_HANDLE,'material_type'=>2,'stock_date'=>date('Y-m-d'),'supplier_id'=>0]);
			ErpMaterialEnterLogic::insertMaterial($enter,$enter_material);
			$res 					= ErpMaterialEnterLogic::goConfirm($enter->id,ErpMaterialEnterMaterial::where('material_stock_id',$enter->id)->column('id'));
			if(isset($res['code']) && $res['code'] == 201){
				throw new \Exception($res['msg']);
			}
			Db::commit();
        }catch (\Exception $e){
			Db::rollback();
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }


	// 获取列表
    public static function getProduct($query=[],$limit=10)
    {
		$field = 'id,status,name,sn,cid,unit,specs,model,remark,photo,status';
        $list = ErpProduct::withSearch(['query'],['query'=>$query])->field($field)->order('id','desc')->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }
	
	
	public static function goProductAnalyse($products){
		$bom 		= self::getProductBom($products);
		if($bom['code'] != 200){
			return $bom;
		}
		if(empty($bom['data'][2])){
			return ['msg'=>'无部件','code'=>201];
		}
		try{
			$data 		= [];
			$sn			= self::getPlanSn();
			foreach($bom['data'][2] as $vo){
				$data[]	= ['plan_sn'=>$sn,'type'=>ErpMaterialPlanEnum::TYPE_ANALYSE,'num'=>$vo['num'],'material_id'=>$vo['id']];
			}
			(new ErpMaterialPlan)->saveAll($data);	       
		}catch (\Exception $e){
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}


    public static function getProductBom($products)
    {
		$data 				= [1=>[],2=>[]];
		foreach($products as $product_id => $product_num){
			$product_bom 	= ErpProductBom::where('product_id','=',$product_id)->with(['material'])->append(['material.category_name'])->where('data_type',1)->select()->toArray();

			foreach($product_bom as $vo){
				if($vo['material'] && $vo['material']['status'] == 1){
					$vo['num'] 	= $vo['num']*$product_num;
					if(empty($data[$vo['material']['type']][$vo['material_id']])){
						$data[$vo['material']['type']][$vo['material_id']] = $vo['material'];
						$data[$vo['material']['type']][$vo['material_id']]['num'] = $vo['num'];
					}else{
						$data[$vo['material']['type']][$vo['material_id']]['num'] = $data[$vo['material']['type']][$vo['material_id']]['num'] + $vo['num'];
					}
					
					if($vo['material']['type'] == 2){
						$material_bom 	= ErpMaterialBom::where('material_id',$vo['material']['id'])->with(['related_material'])->append(['related_material.category_name'])->select()->toArray();
						foreach($material_bom as $vv){
							if($vv['related_material'] && $vv['related_material']['status'] == 1){
								$vv['num'] 	= $vv['num']*$vo['num'];
								
								if(empty($data[$vv['related_material']['type']][$vv['related_material']['id']])){
									$data[$vv['related_material']['type']][$vv['related_material']['id']] = $vv['related_material'];
									$data[$vv['related_material']['type']][$vv['related_material']['id']]['num'] = $vo['num'];
								}else{
									$data[$vv['related_material']['type']][$vv['related_material']['id']]['num'] = $data[$vv['related_material']['type']][$vv['related_material']['id']]['num'] + $vo['num'];
								}
							}
						}
					}
				}

			}
		}
		return ['data'=>$data,'code'=>200];
		
    }


    // 删除
    public static function goRemove($data)
    {
		//验证
        $validate 	= new ErpMaterialPlanValidate;
        if(!$validate->scene('remove')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}	
        try{
			ErpMaterialPlan::destroy($data['ids']);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
	
	
	// 计划下达
    public static function goProductPlan($id,$material_ids)
    {
		$map 			= [];
		$map[]			= ['order_produce_id','in',$id];
		$map[]			= ['material_type','=',2];
		$map[]			= ['lack_num','>',0];
		if($material_ids){
			$map[]		= ['material_id','in',$material_ids];
		}
		$bom 			= ErpOrderProduceBom::where($map)->select();
		if($bom->isEmpty()){
			return ['msg'=>'请选择物料','code'=>201];
		}
		$data 			= [];
		$sn				= self::getPlanSn();
		foreach($bom as $vo){
			if(empty($data[$vo['material_id']])){
				$data[$vo['material_id']]		= ['plan_sn'=>$sn,'type'=>ErpMaterialPlanEnum::TYPE_PRODUCT,'num'=>$vo['lack_num'],'material_id'=>$vo['material_id']];
			}else{
				$data[$vo['material_id']]['num']= $data[$vo['material_id']]['num'] + $vo['lack_num'];
			}
		}
        try {			
			(new ErpMaterialPlan)->saveAll(array_values($data)); 
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
	
	
	public static function getScheduleDetail($id)
    {
		$model 	= ErpMaterialPlan::where('id',$id)->find();
		$bom 	= ErpMaterialBom::alias('a')
		->join('erp_material b','a.related_material_id = b.id','LEFT')
		->field('a.*,b.sn,b.name,b.unit,b.cid,b.material,b.surface,b.color,b.remark')
		->where('a.material_id',$model['material_id'])->select();
		
		$follow = ErpMaterialPlanFollow::alias('a')
		->join('erp_material_plan_process b','a.plan_id = b.plan_id and a.process_id = b.process_id','LEFT')
		->field('a.*,b.process_name,b.follow_name')
		->where('a.plan_id',$id)->select()->toArray();
		$code 	= [];
	
		foreach($follow as $vo){
			if($vo['num']){
				$code[$vo['num']] = $vo;
			}
			if($vo['after_num']){
				$code[$vo['after_num']] = $vo;
			}
		}
		$res 	= ErpMaterialCode::alias('a')
		->join('erp_material b','a.material_id = b.id','LEFT')
		->join('erp_purchase_order c','a.purchase_order_id = c.id','LEFT')
		->join('erp_supplier d','c.supplier_id = d.id','LEFT')
		->where('a.code','in',array_keys($code))->column('a.code,a.data_id,a.data_type,b.sn,b.name,c.order_sn as purchase_order_sn,c.order_date as purchase_order_date','a.code');
		
		$enter 		= ErpMaterialEnterMaterial::alias('a')->join('erp_material_stock b','a.material_stock_id = b.id','LEFT')->where('a.id','in',array_column($res,'data_id'))->column('a.check_date,a.check_username,b.order_sn as enter_order_sn,b.stock_date as enter_stock_date,b.supplier_id','a.id');
		$supplier	= ErpSupplier::where('id','in',array_column($enter,'supplier_id'))->column('name','id');
		
		foreach($code as $key=>&$vo){
			if(!empty($res[$key])){
				$vo = array_merge($res[$key],$vo);
			}
			if(!empty($vo['data_type']) && $vo['data_type'] == 'erp_material_enter_material' && !empty($enter[$vo['data_id']])){
				$vo = array_merge($vo,$enter[$vo['data_id']]);
			}
			if(!empty($vo['supplier_id']) && !empty($supplier[$vo['supplier_id']])){
				$vo['supplier_name'] = $supplier[$vo['supplier_id']];
			}			
			
		}
	
		return ['model'=>$model,'bom'=>$bom,'code'=>$code];
	}
	
	public static function getFollowDetail($id,$process_id)
    {
		$model 				= ErpMaterialPlan::where('id',$id)->find();
		$follow 			= ErpMaterialPlanFollow::where('plan_id',$id)->where('process_id',$process_id)->order(['id'=>'asc'])->select()->toArray();
		$process 			= ErpProcess::where('id',$process_id)->find();
		$produce_product 	= [];
		$produce_process 	= [];
		foreach($follow as $k=>$v){
			if($v['follow_item']['type'] == 1){
				$produce_product[] = $v;
			}
			if($v['follow_item']['type'] == 2){
				$produce_process[] = $v;
			}			
		}
		return ['model'=>$model,'process'=>$process,'produce_product'=>$produce_product,'produce_process'=>$produce_process];
	}	
	
	public static function getFollowLog($id,$process_id){
		$list = ErpMaterialPlanProcessLog::where('plan_id',$id)->where('process_id',$process_id)->order(['id'=>'asc'])->select();
		return ['list'=>$list];
	}
	
	
	

}
