<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\ErpOrder;
use app\common\model\DictData;
use app\common\model\ErpMaterial;
use app\common\model\ErpOrderAftersale;
use app\common\model\{ErpMaterialOutMaterial,ErpOrderLog};
use app\common\enum\ErpMaterialStockEnum;
use app\common\model\ErpMaterialEnterMaterial;
use app\common\enum\ErpOrderProduceEnum;
use app\common\enum\{ErpOrderEnum,ErpOrderLogEnum};

use app\admin\validate\ErpOrderAftersaleValidate;
use think\facade\Db;


class ErpOrderAftersaleLogic extends BaseLogic{


    public static function getCheck($id){
		$order 			= ErpOrder::where('id','in',$id)->column('id,order_sn','id');
		$data 			= ErpOrderAftersale::alias('a')->field('a.*')->where('a.order_id','in',$id)->select()->toArray();
		$material		= ErpMaterial::where('id','in',array_column($data,'material_id'))->column('id,stock','id');		
		$ids 			= ErpMaterialOutMaterial::alias('a')->join('erp_material_stock b','a.material_stock_id = b.id','LEFT')->where('b.type',ErpMaterialStockEnum::TYPE_OUT_PRODUCE_REGET)->where('b.order_id','in',$id)->column('a.material_id');
		$_material 		= $material;
		$bom1 = $bom2 = [];
		$lack_produce	= [];
		
		foreach($data as $vo){
			
			if($ids && in_array($vo['material_id'],$ids)){
				$vo['is_out']		= 1;
			}else{
				$vo['is_out']		= 0;
			}
			$vo['stock'] 			= $_material[$vo['material_id']]['stock'];
			if($material[$vo['material_id']]['stock']>=$vo['material_num']){
				$vo['lack_num']		= 0;
				$material[$vo['material_id']]['stock'] = $material[$vo['material_id']]['stock'] - $vo['material_num'];
			}else{
				$vo['lack_num']		= $vo['material_num'] - $material[$vo['material_id']]['stock'];
				$material[$vo['material_id']]['stock'] = 0;
			}
			
			if(empty($lack_produce[$vo['material_id']])){
				$lack_produce[$vo['material_id']] 	= [];
			}
			if(empty($lack_produce[$vo['material_id']][$vo['order_id']])){
				$lack_produce[$vo['material_id']][$vo['order_id']] = ['lack_num'=>$vo['lack_num'],'order_sn'=>$order[$vo['order_id']]['order_sn']];
			}else{
				$lack_produce[$vo['material_id']][$vo['order_id']]['lack_num'] += $vo['lack_num'];
			}	
			
			if($vo['material_type'] == 2){
				if(empty($bom1[$vo['material_id']])){
					$vo['ids']									= $vo['id'];
					$bom1[$vo['material_id']]					= $vo;
				}else{
					$bom1[$vo['material_id']]['material_num']	= $bom1[$vo['material_id']]['material_num'] + $vo['material_num'];
					$bom1[$vo['material_id']]['lack_num']		= $bom1[$vo['material_id']]['lack_num'] + $vo['lack_num'];
					$bom1[$vo['material_id']]['ids']			= $bom1[$vo['material_id']]['ids'].','.$vo['id'];
				}
			}
			if($vo['material_type'] == 1){
				if(empty($bom2[$vo['material_id']])){
					$vo['ids']									= $vo['id'];
					$bom2[$vo['material_id']]					= $vo;
				}else{
					$bom2[$vo['material_id']]['material_num']	= $bom2[$vo['material_id']]['material_num'] + $vo['material_num'];
					$bom2[$vo['material_id']]['lack_num']		= $bom2[$vo['material_id']]['lack_num'] + $vo['lack_num'];
					$bom2[$vo['material_id']]['ids']			= $bom2[$vo['material_id']]['ids'].','.$vo['id'];
				}
			}
		}

		return ['order'=>$order,'bom1'=>$bom1,'bom2'=>$bom2,'lack_produce'=>$lack_produce];
    }	



	// 创建物料出库单(领料出库)
    public static function goOut($id,$material_ids)
    {
		$map 			= [];
		$map[]			= ['order_id','in',$id];
		if($material_ids){
			$map[]		= ['material_id','in',$material_ids];
		}
		//$ids 			= ErpMaterialOutMaterial::alias('a')->join('erp_material_stock b','a.material_stock_id = b.id','LEFT')->where('b.type',ErpMaterialStockEnum::TYPE_OUT_PRODUCE_REGET)->where('b.order_id','in',ErpOrderProduce::where('id','in',$id)->column('order_id'))->column('a.material_id');
		//if($ids){
			//$map[]		= ['material_id','not in',$ids];
		//}
		$bom 			= ErpOrderAftersale::with(['material'])->where($map)->select();
		if($bom->isEmpty()){
			return ['msg'=>'请选择物料','code'=>201];
		}
		
		$enter_data 	= ErpMaterialEnterMaterial::
		fieldRaw('id,material_id,can_out_num,freeze_out_stock,can_out_num - freeze_out_stock as num')
		->whereRaw('can_out_num>freeze_out_stock')->where('can_out_num','>',0)->where('material_id','in',$bom->column('material_id'))->order('id asc')->select()->toArray();
		$enter			= [];
		foreach($enter_data as $vo){
			$enter[$vo['material_id']][] = $vo;
		}
		
		$material 			= [];
		foreach($bom as $vo){
			//if($vo['material_type'] == 1 || $vo['use_num']>0){
				$stock_num 	= $vo['material_num'];			
				if(!empty($enter[$vo['material_id']])){
					foreach($enter[$vo['material_id']] as $k=>$v){
						if($v['num']){
							if($stock_num > $v['num']){	
								$enter[$vo['material_id']][$k]['num'] 				= 0;
								$material[$vo['material_type']][$vo['order_id']][] 	= ['enter_material_id'=>$v['id'],'stock_num'=>$v['num'],'id'=>$vo['material_id'],'data_id'=>$vo['id']];	
								$stock_num											= $stock_num - $v['num'];
							}else{
								$enter[$vo['material_id']][$k]['num'] 				= $v['num'] - $stock_num;
								$material[$vo['material_type']][$vo['order_id']][] 	= ['enter_material_id'=>$v['id'],'stock_num'=>$stock_num,'id'=>$vo['material_id'],'data_id'=>$vo['id']];	
								$stock_num 											= 0;
								break;
							}
						}
					}
				}else{
					return ['msg'=>$vo['material']['name'].'没库存批次','code'=>201];
				}
				if($vo['material']['stock'] <= 0){
					return ['msg'=>$vo['material']['name'].'库存不足','code'=>201];
				}

				if($stock_num){
					return ['msg'=>$vo['material']['name'].'库存不足','code'=>201];
					$material[$vo['material_type']][$vo['order_id']][] 				= ['enter_material_id'=>0,'stock_num'=>$stock_num,'id'=>$vo['material_id'],'data_id'=>$vo['id']];
				}
			//}
		}
		if(!$material){
			return ['msg'=>'物料数量为空','code'=>201];
		}
		
		Db::startTrans();
        try {			
			foreach($material as $k1=>$v1){
				foreach($v1 as $k2=>$v2){
					if($v2){
						ErpMaterialOutLogic::goAdd(['material_type'=>$k1,'type'=>ErpMaterialStockEnum::TYPE_OUT_PRODUCE_REGET,'order_id'=>$k2,'material'=>$v2,'stock_date'=>date('Y-m-d'),'remark'=>'']);
					}
				}
			}
			
			Db::commit();
        }catch (\Exception $e){
			Db::rollback();
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }


    // 处理完成
    public static function goCheck($id)
    {
		$aftersale 			= ErpOrderAftersale::where('order_id','in',$id)->column('*','id');
		$aftersale_data 	= [];
		$order_ids 			= [];
		if(empty($aftersale)){
			return ['msg'=>'数据不存在','code'=>201];
		}

		$count 				= ErpOrderAftersale::whereDay('pass_down_time')->group('order_sn')->count() + 1;
		$order_sn			= date('ymd').sprintf("%02d",$count);
		
		foreach($aftersale as $vo){
			if($vo['produce_status'] != ErpOrderProduceEnum::PRODUCE_STATUS_NO){
				return ['msg'=>$vo['material_sn'].'已处理','code'=>201];
			}
			$aftersale_data[]	= ['id'=>$vo['id'],'order_sn'=>$order_sn,'produce_status'=>ErpOrderProduceEnum::PRODUCE_STATUS_FINISH,'pass_down_time'=>time()];
			$order_ids[]	= $vo['order_id'];
		}
		$order_ids 			= array_unique($order_ids);
		
		Db::startTrans();
        try {
			(new ErpOrderAftersale)->saveAll($aftersale_data);
            
			$order_data 			= [];
			foreach($order_ids as $vo){
				if(ErpOrderAftersale::where('order_id',$vo)->where('produce_status',ErpOrderProduceEnum::PRODUCE_STATUS_NO)->count()){
					$order_data[]	= ['id'=>$vo,'produce_status'=>ErpOrderEnum::PRODUCE_STATUS_PART,'order_status'=>ErpOrderEnum::ORDER_STATUS_PRODUCING];
				}else{
					$order_data[]	= ['id'=>$vo,'produce_status'=>ErpOrderEnum::PRODUCE_STATUS_All,'order_status'=>ErpOrderEnum::ORDER_STATUS_PRODUCING];
				}
			}
			(new ErpOrder)->saveAll($order_data);

			Db::commit();
        }catch (\Exception $e){
			Db::rollback();
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }


	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$query['_alias'] 		= 'a';
		$query['_order_alias'] 	= 'b';
		$field = 'a.id,a.status,a.material_sn,a.material_name,a.material_num,a.material_category,a.material_type,a.create_time,b.customer_name,b.order_type';
        $list = ErpOrderAftersale::alias('a')
		->join('erp_order b','a.order_id = b.id','LEFT')->withSearch(['query'],['query'=>$query])->field($field)->order('a.id','desc')->append(['material_type_desc','create_date','order_type_desc'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

    // 添加
    public static function goAdd($data)
    {
        //验证
        $validate 		= new ErpOrderAftersaleValidate;
        if(!$validate->scene('add')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		$material 		= ErpMaterial::where('id',$data['material_id'])->find();
		if(empty($material['id'])) {
			return ['msg'=>'物料不存在','code'=>201];
		}
		$order 			= ErpOrder::where('id',$data['order_id'])->find();
		if(empty($order['id'])) {
			return ['msg'=>'订单不存在','code'=>201];
		}
		if(!$order->can_save_aftersale){
			return ['msg'=>'该订单不能添加/修改物料','code'=>201];
		}
		
		Db::startTrans();
        try {
			$model 	= ErpOrderAftersale::create($data);
			ErpOrderLogic::updateAftersale($order->id);
			$log	= ['log'=>'添加物料：'.$model['material_sn'].'  '.$model['material_name'],'data_type'=>ErpOrderLogEnum::ORDER_MATERIAL_ADD,'data_id'=>$model['id'],'order_id'=>$model['order_id'],'operator'=>self::$adminUser['username']];
			ErpOrderLog::create($log);

			Db::commit();
        }catch (\Exception $e){
			Db::rollback();
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
    
    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpOrderAftersale::where($map)->find();
		}else{
			return ErpOrderAftersale::find($map);
		}
    }
	
    // 编辑
    public static function goEdit($data)
    {
        //验证
        $validate 	= new ErpOrderAftersaleValidate;
        if(!$validate->scene('edit')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		$model 		= self::getOne($data['id']);
        if(empty($model['id'])) {
			return ['msg'=>'数据不存在','code'=>201];
		}
		//$order 		= $model->order;
		//if(!$order['can_save_aftersale']){
			//return ['msg'=>'当前订单不能修改物料','code'=>201];
		//}		
		
		Db::startTrans();
        try {
			$log 			= [];
			$filed_check   	= ['material_num'=>'数量'];
			foreach($filed_check as $k=>$vo){
				if($data[$k] != $model[$k]){
					$log[]	= ['log'=>$vo.'从`'.$model[$k].'`到`'.$data[$k].'`','data_type'=>'order_material_filed_change','order_product_id'=>$model['id'],'order_id'=>$model['order_id'],'operator'=>self::$adminUser['username']];
				}
			}
            $model->save($data);
			if($log){
				(new ErpOrderLog)->saveAll($log);
			}
			ErpOrderLogic::updateAftersale($model['order_id']);

			Db::commit();
        }catch (\Exception $e){
			Db::rollback();
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
	
    // 删除
    public static function goRemove($id)
    {
		$model 		= self::getOne($id);
        if(empty($model['id'])) {
			return ['msg'=>'数据不存在','code'=>201];
		}
		$order 		= $model->order;
		if(!$order['can_save_aftersale']){
			return ['msg'=>'当前订单不能删除物料','code'=>201];
		}
        try{
			ErpOrderAftersale::destroy($id);
			$log	= ['log'=>'删除物料：'.$model['material_sn'].'  '.$model['material_name'],'data_type'=>ErpOrderLogEnum::ORDER_MATERIAL_DELETE,'data_id'=>$model['id'],'order_id'=>$model['order_id'],'operator'=>self::$adminUser['username']];
			ErpOrderLog::create($log);
			ErpOrderLogic::updateAftersale($order->id);

		}catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

}
