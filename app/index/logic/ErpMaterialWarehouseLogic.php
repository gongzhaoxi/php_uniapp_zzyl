<?php
declare (strict_types = 1);
namespace app\index\logic;
use app\index\logic\BaseLogic;
use app\common\model\{ErpMaterialApproval,ErpMaterialWarehouseReturn,ErpMaterialWarehouse,ErpMaterialOutMaterial,ErpMaterialEnterMaterial,ErpMaterial,ErpMaterialOut,ErpMaterialAllocateMaterial,ErpMaterialEnter,ErpMaterialScrap};
use app\common\enum\{ErpMaterialOutMaterialEnum,ErpMaterialStockEnum,ErpMaterialEnterMaterialEnum};
use think\facade\Db;
use app\admin\logic\{ErpMaterialEnterLogic,ErpMaterialOutLogic,ErpMaterialChangeLogic,ErpMaterialAllocateLogic};

use app\admin\logic\ErpMaterialWarehouseLogic as AdminErpMaterialWarehouseLogic;


class ErpMaterialWarehouseLogic extends BaseLogic{

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$user_info 			= request()->userInfo;
		$field 				= 'a.*,"" as num,b.name,b.sn,b.unit,b.type as material_type,c.name as warehouse_name';	
		$query['_alias']	= 'a';
		$query['_material_alias']= 'b';
		$list 				= ErpMaterialWarehouse::alias('a')
		->join('erp_material b','a.material_id = b.id','LEFT')
		->join('erp_warehouse c','a.warehouse_id = c.id','LEFT')
		->withSearch(['query'],['query'=>$query])
		//->where('a.stock','>',0)
		->where('a.warehouse_id','in',$user_info['warehouse_id'])->whereNotNull('b.id')
		->field($field)->order('a.stock','desc')->paginate($limit);

        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }


	public static function goPull($ids,$num){
		$user_info 				= request()->userInfo;
		$allocate_material 		= ErpMaterialWarehouse::where('id','in',$ids)->select();
		if($allocate_material->isEmpty() || $allocate_material->count() != count($ids)){
			return ['msg'=>'数据错误','code'=>201];
		}
		$material				= ErpMaterial::where('id','in',$allocate_material->column('material_id'))->column('id,name,sn,type,stock,freeze_stock','id');
		
		$enter_data 			= ErpMaterialEnterMaterial::alias('a')->join('erp_material_stock b','a.material_stock_id = b.id','LEFT')->
		fieldRaw('a.id,a.material_id,a.can_out_num,a.freeze_out_stock,a.can_out_num - a.freeze_out_stock as num,b.order_sn')
		->whereRaw('a.can_out_num>a.freeze_out_stock')->where('a.can_out_num','>',0)->where('a.material_id','in',$allocate_material->column('material_id'))->where('a.warehouse_id','in',$user_info['warehouse_id'])->order('a.id asc')->select()->toArray();
		$enter					= [];
		foreach($enter_data as $vo){
			$enter[$vo['material_id']][] = $vo;
		}

		$update 				= [];
		$enter_update			= [];
		$out_material			= [];
		$out_material_change	= [];
		
		foreach($allocate_material as $key=>$vo){
			if(empty($num[$vo['id']])){
				return ['msg'=>$material[$vo['material_id']]['sn'].'操作数量不存在','code'=>201];
			}
			$_num 		= $num[$vo['id']];
			if($_num != intval($_num) || $_num <= 0){
				return ['msg'=>$material[$vo['material_id']]['sn'].'操作数量必须为大于0的整数','code'=>201];
			}		
			if($_num > $vo['stock']){
				return ['msg'=>$material[$vo['material_id']]['sn'].'操作数量最多只能为'.$vo['stock'],'code'=>201];
			}
			$material_type 				= $material[$vo['material_id']]['type'];
			$stock_num 					= $_num;			
			if(!empty($enter[$vo['material_id']])){
				foreach($enter[$vo['material_id']] as $k=>$v){
					if($v['num']){
						if($stock_num > $v['num']){	
							$enter[$vo['material_id']][$k]['num'] 		= 0;
							$enter_update[] 							= ['id'=>$v['id'],'can_out_num'=>Db::raw('can_out_num-'.$v['num'])];
							//$material[$vo['material_type']][$vo['order_id']][] 	= ['enter_material_id'=>,'stock_num'=>$v['num'],'id'=>$vo['material_id'],'data_id'=>$vo['id']];	
							$out_material[$material_type][]				= ['warehouse_id'=>$vo['warehouse_id'],'material_stock_id'=>0,'enter_material_id'=>$v['id'],'enter_order_sn'=>$v['order_sn'],'stock_num'=>$v['num'],'stocked_num'=>$v['num'],'material_id'=>$vo['material_id'],'data_id'=>0,'status'=>ErpMaterialOutMaterialEnum::STATUS_FINISH];	
							$out_material_change[$material_type][]		= ['stock_type'=>1,'num'=>$_num*-1,'material'=>$vo,'material_id'=>$vo['material_id'],'warehouse_id'=>$vo['warehouse_id'],'material_stock_id'=>0,'supplier_id'=>0];		
							$stock_num									= $stock_num - $v['num'];
							
						}else{
							$enter[$vo['material_id']][$k]['num'] 		= $v['num'] - $stock_num;
							$enter_update[] 							= ['id'=>$v['id'],'can_out_num'=>Db::raw('can_out_num-'.$stock_num)];
							//$material[$vo['material_type']][$vo['order_id']][] 	= ['enter_material_id'=>$v['id'],'stock_num'=>$stock_num,'id'=>$vo['material_id'],'data_id'=>$vo['id']];	
							$out_material[$material_type][]				= ['warehouse_id'=>$vo['warehouse_id'],'material_stock_id'=>0,'enter_material_id'=>$v['id'],'enter_order_sn'=>$v['order_sn'],'stock_num'=>$stock_num,'stocked_num'=>$stock_num,'material_id'=>$vo['material_id'],'data_id'=>0,'status'=>ErpMaterialOutMaterialEnum::STATUS_FINISH];	
							$out_material_change[$material_type][]		= ['stock_type'=>1,'num'=>$_num*-1,'material'=>$vo,'material_id'=>$vo['material_id'],'warehouse_id'=>$vo['warehouse_id'],'material_stock_id'=>0,'supplier_id'=>0];		
							$stock_num 									= 0;
							break;
						}
					}
				}
			}else{
				return ['msg'=>$material[$vo['material_id']]['name'].'没库存批次','code'=>201];
			}
			if($stock_num){
				return ['msg'=>$$material[$vo['material_id']]['name'].'库存不足','code'=>201];
				//$material[$vo['material_type']][$vo['order_id']][] 				= ['enter_material_id'=>0,'stock_num'=>$stock_num,'id'=>$vo['material_id'],'data_id'=>$vo['id']];
			}
			$update[] 								= ['id'=>$vo['id'],'stock'=>$vo['stock'] - $_num];
		}
		
		try {
			if(!empty($out_material[1])){
				$order_sn 				= ErpMaterialOutLogic::createOrderSn();
				$out 					= ErpMaterialOut::create(['create_admin'=>$user_info['name'],'data_type'=>ErpMaterialStockEnum::DATA_TYPE_OUT,'order_sn'=>$order_sn,'batch_number'=>$order_sn,'type'=>ErpMaterialStockEnum::TYPE_OUT_PULL,'remark'=>'零件领用出库','status'=>ErpMaterialStockEnum::STATUS_FINISH,'material_type'=>1,'stock_date'=>date('Y-m-d'),'supplier_id'=>0]);
				foreach($out_material[1] as $k=>$vo){
					$out_material[1][$k]['material_stock_id'] 		= $out['id'];
				}
				(new ErpMaterialOutMaterial)->saveAll($out_material[1]);
				
				foreach($out_material_change[1] as $k=>$vo){
					$out_material_change[1][$k]['material_stock_id']= $out['id'];
				}
				ErpMaterialChangeLogic::goAdd($out_material_change[1]);
			}
			
			if(!empty($out_material[2])){
				$order_sn 				= ErpMaterialOutLogic::createOrderSn();
				$out 					= ErpMaterialOut::create(['create_admin'=>$user_info['name'],'data_type'=>ErpMaterialStockEnum::DATA_TYPE_OUT,'order_sn'=>$order_sn,'batch_number'=>$order_sn,'type'=>ErpMaterialStockEnum::TYPE_OUT_PULL,'remark'=>'零件领用出库','status'=>ErpMaterialStockEnum::STATUS_FINISH,'material_type'=>2,'stock_date'=>date('Y-m-d'),'supplier_id'=>0]);
				foreach($out_material[2] as $k=>$vo){
					$out_material[2][$k]['material_stock_id'] 		= $out['id'];
				}
				(new ErpMaterialOutMaterial)->saveAll($out_material[2]);
				
				foreach($out_material_change[2] as $k=>$vo){
					$out_material_change[2][$k]['material_stock_id']= $out['id'];
				}
				ErpMaterialChangeLogic::goAdd($out_material_change[2]);
			}			
			
			(new ErpMaterialWarehouse)->saveAll($update);
			
			(new ErpMaterialEnterMaterial)->saveAll($enter_update);
			
		}catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}

	
	public static function getBack($material_id){
		$user_info 				= request()->userInfo;
		$enter_data 			= ErpMaterialEnterMaterial::alias('a')->join('erp_material_stock b','a.material_stock_id = b.id','LEFT')->
		fieldRaw('a.id,a.material_id,a.can_out_num,a.freeze_out_stock,a.can_out_num - a.freeze_out_stock as num,b.order_sn')
		->whereRaw('a.can_out_num>a.freeze_out_stock')->where('a.can_out_num','>',0)->where('a.material_id','in',$material_id)->where('a.warehouse_id','in',$user_info['warehouse_id'])->order('a.id asc')->select()->toArray();
		$enter					= [];
		foreach($enter_data as $vo){
			$enter[$vo['material_id']][] = $vo;
		}
		
		$material				= ErpMaterial::where('id','in',$material_id)->field('id,name,sn,type,stock,freeze_stock')->select()->toArray();
		foreach($material as &$vo){
			$vo['enter']		= empty($enter[$vo['id']])?[]:$enter[$vo['id']];
		}
		return  $material;
	}

	public static function goBack($param){
		if(empty($param)){
			return ['msg'=>'数据不能为空','code'=>201];
		}
		$data 					= self::getList(['ids'=>array_column($param,'id')],10000)['data'];

		$enter_material			= [];
		$enter_material_change	= [];

		foreach($data as $key=>$vo){
			if(empty($param[$vo['id']]['stock_num'])){
				return ['msg'=>$vo['sn'].'退回数量不存在','code'=>201];
			}
			$_num 				= $param[$vo['id']]['stock_num'];
			if($_num != intval($_num) || $_num <= 0){
				return ['msg'=>$vo['sn'].'退回数量必须为大于0的整数','code'=>201];
			}		

			$material_type 							= $vo['material_type'];
			$enter_material[$material_type][] 		= ['id'=>$vo['material_id'],'enter_batch_number'=>'','enter_order_sn'=>'','warehouse_id'=>$vo['warehouse_id'],'stock_num'=>$_num,'stocked_num'=>$_num,'can_out_num'=>$_num,'qualities_num'=>$_num,'status'=>ErpMaterialEnterMaterialEnum::STATUS_FINISH,'remark'=>$param[$vo['id']]['remark']];
			$enter_material_change[$material_type][]= ['stock_type'=>1,'num'=>$_num,'enter_batch_number'=>'','enter_order_sn'=>'','material_id'=>$vo['material_id'],'warehouse_id'=>$vo['warehouse_id'],'material_stock_id'=>0,'supplier_id'=>0];			
		}			

		Db::startTrans();
		try {
			foreach($enter_material as $type=>$v){
				$order_sn 						= ErpMaterialEnterLogic::createOrderSn();
				$enter 							= ErpMaterialEnter::create(['create_admin'=>'','data_type'=>ErpMaterialStockEnum::DATA_TYPE_ENTER,'order_sn'=>$order_sn,'batch_number'=>$order_sn,'type'=>ErpMaterialStockEnum::TYPE_ENTER_BACK_WAREHOUSE,'remark'=>'','status'=>ErpMaterialStockEnum::STATUS_FINISH,'material_type'=>$type,'stock_date'=>date('Y-m-d'),'supplier_id'=>0]);				
				
				foreach($v as $k=>$vo){
					$v[$k]['enter_batch_number']= $order_sn;
					$v[$k]['enter_order_sn']	= $order_sn;
				}
				ErpMaterialEnterLogic::insertMaterial($enter,$v);
				
				foreach($enter_material_change[$type] as $k=>$vo){
					$enter_material_change[$type][$k]['material_stock_id']	= $enter['id'];
					$enter_material_change[$type][$k]['enter_batch_number']	= $order_sn;
					$enter_material_change[$type][$k]['enter_order_sn']		= $order_sn;
				}
				AdminErpMaterialWarehouseLogic::goUpdateStock($enter_material_change[$type],1);
			}

			Db::commit();
			return ['msg'=>'操作成功','code'=>200];
			
			/*
			$res 		= ErpMaterialAllocateLogic::goAdd($param);
			if($res['code'] != 200){
				throw new \Exception($res['msg']);	
			}
			$data 		= ErpMaterialAllocateMaterial::where('material_stock_id',$res['data']['id'])->select();
			$ids		= [];
			$signed_num = [];
			foreach($data as $vo){
				$ids[] 					= $vo['id'];
				$signed_num[$vo['id']] 	= $vo['stock_num'];
			}
			$res 		= ErpMaterialAllocateMaterialLogic::goSigned($ids,$signed_num,'back_warehouse');
			if($res['code'] != 200){
				throw new \Exception($res['msg']);	
			}
			Db::commit();
			return ['msg'=>'操作成功','code'=>200];*/
        }catch (\Exception $e){
			Db::rollback();
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}
	
	
	public static function goScrap($approval_id,$param){
		if(empty($param) || empty($approval_id)){
			return ['msg'=>'数据不能为空','code'=>201];
		}
		$approval 					= ErpMaterialApproval::where('id',$approval_id)->find();
		if($approval['status'] != 0){
			return ['msg'=>'已审核','code'=>201];
		}

		try {
			$data 					= self::getList(['ids'=>array_column($param,'id')],10000)['data'];
			$user_info 				= request()->userInfo;
			$enter_data 			= ErpMaterialEnterMaterial::alias('a')->join('erp_material_stock b','a.material_stock_id = b.id','LEFT')->
			fieldRaw('a.id,a.material_id,a.warehouse_id,a.can_out_num,a.freeze_out_stock,a.can_out_num - a.freeze_out_stock as num,b.order_sn')
			->whereRaw('a.can_out_num>a.freeze_out_stock')->where('a.can_out_num','>',0)->where('a.material_id','in',array_column($data,'material_id'))->where('a.warehouse_id','in',$user_info['warehouse_id'])->order('a.id asc')->select()->toArray();
			$enter					= [];
			foreach($enter_data as $vo){
				$enter[$vo['material_id'].'-'.$vo['warehouse_id']][] = $vo;
			}		
			$scrap 					= [];
			foreach($data as $vo){
				if(empty($param[$vo['id']]['stock_num'])){
					return ['msg'=>$vo['sn'].'报废量不存在','code'=>201];
				}
				$_num 				= $param[$vo['id']]['stock_num'];
				if($_num != intval($_num) || $_num <= 0){
					return ['msg'=>$vo['sn'].'报废量必须为大于0的整数','code'=>201];
				}
				
				$stock_num 			= $_num;	
				$key 				= $vo['material_id'].'-'.$vo['warehouse_id'];
				
				if(!empty($enter[$key])){
					foreach($enter[$key] as $k=>$v){
						if($v['num']){
							if($stock_num > $v['num']){	
								$enter[$key][$k]['num'] = 0;
								$scrap[]				= ['approval_id'=>$approval_id,'is_confirm'=>0,'material_id'=>$vo['material_id'],'enter_material_id'=>$v['id'],'stock_num'=>$v['num'],'stock_date'=>$param[$vo['id']]['stock_date'],'remark'=>$param[$vo['id']]['remark'],'cid'=>$param[$vo['id']]['cid'],'create_admin'=>$user_info['name']];	
								$stock_num				= $stock_num - $v['num'];
								
							}else{
								$enter[$key][$k]['num'] = $v['num'] - $stock_num;
								$scrap[]				= ['approval_id'=>$approval_id,'is_confirm'=>0,'material_id'=>$vo['material_id'],'enter_material_id'=>$v['id'],'stock_num'=>$stock_num,'stock_date'=>$param[$vo['id']]['stock_date'],'remark'=>$param[$vo['id']]['remark'],'cid'=>$param[$vo['id']]['cid'],'create_admin'=>$user_info['name']];					
								$stock_num 				= 0;
								break;
							}
						}
					}
				}else{
					return ['msg'=>$vo['name'].'没库存批次','code'=>201];
				}
				if($stock_num){
					return ['msg'=>$vo['name'].'库存不足','code'=>201];
					//$material[$vo['material_type']][$vo['order_id']][] 				= ['enter_material_id'=>0,'stock_num'=>$stock_num,'id'=>$vo['material_id'],'data_id'=>$vo['id']];
				}
			}
			
			(new ErpMaterialScrap)->saveAll($scrap);
			
			$approval->save(['status'=>1,'check_by'=>$user_info['name'],'check_date'=>date('Y-m-d')]);
			
			return ['msg'=>'创建成功','code'=>200];
		}catch (\Exception $e){
			
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}
	
	public static function goReturnAdd($userInfo,$param){
		if(empty($param)){
			return ['msg'=>'数据不能为空','code'=>201];
		}
		$data 			= self::getList(['ids'=>array_column($param,'id')],10000)['data'];
		$return 		= [];

		foreach($data as $key=>$vo){
			if(empty($param[$vo['id']]['stock_num'])){
				return ['msg'=>$vo['sn'].'退回数量不存在','code'=>201];
			}
			$_num 		= $param[$vo['id']]['stock_num'];
			if($_num != intval($_num) || $_num <= 0){
				return ['msg'=>$vo['sn'].'退回数量必须为大于0的整数','code'=>201];
			}		
			$return[] 	= ['material_id'=>$vo['material_id'],'material_warehouse_id'=>$vo['id'],'warehouse_id'=>$vo['warehouse_id'],'stock_num'=>$_num,'stocked_num'=>$_num,'remark'=>$param[$vo['id']]['remark'],'username'=>$userInfo['name']];
		}			

		try {
			(new ErpMaterialWarehouseReturn)->saveAll($return);
			return ['msg'=>'操作成功','code'=>200];
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}
	
	public static function getReturn($id){
		return ErpMaterialWarehouseReturn::where('id',$id)->find();
	}
	
	
	public static function goReturnCheck($userInfo,$param){
		$model 					= ErpMaterialWarehouseReturn::where('id',$param['id'])->find();
		if($model['status'] == 1){
			return ['msg'=>'状态错误','code'=>201];
		}
		$param['status'] 		= 1;
		$param['check_username']= $userInfo['name'];

		Db::startTrans();
		try {
			$model->save($param);

			$enter_material			= [];
			$enter_material_change	= [];

			$order_sn 						= ErpMaterialEnterLogic::createOrderSn();
			$enter 							= ErpMaterialEnter::create(['create_admin'=>'','data_type'=>ErpMaterialStockEnum::DATA_TYPE_ENTER,'order_sn'=>$order_sn,'batch_number'=>$order_sn,'type'=>ErpMaterialStockEnum::TYPE_ENTER_BACK_WAREHOUSE,'remark'=>'','status'=>ErpMaterialStockEnum::STATUS_FINISH,'material_type'=>$model['material']['type'],'stock_date'=>date('Y-m-d'),'supplier_id'=>0]);				
			$enter_material[] 				= ['id'=>$model['material_id'],'enter_batch_number'=>$order_sn,'enter_order_sn'=>$order_sn,'warehouse_id'=>$model['warehouse_id'],'stock_num'=>$model['stock_num'],'stocked_num'=>$model['stock_num'],'can_out_num'=>$model['stock_num'],'qualities_num'=>$model['stock_num'],'status'=>ErpMaterialEnterMaterialEnum::STATUS_FINISH,'remark'=>$model['remark']];
			$enter_material_change[]		= ['stock_type'=>1,'num'=>$model['stock_num'],'enter_batch_number'=>$order_sn,'enter_order_sn'=>$order_sn,'material_id'=>$model['material_id'],'warehouse_id'=>$model['warehouse_id'],'material_stock_id'=>$enter['id'],'supplier_id'=>0];			
			
			ErpMaterialEnterLogic::insertMaterial($enter,$enter_material);
			
			AdminErpMaterialWarehouseLogic::goUpdateStock($enter_material_change,1);
			
			Db::commit();
			return ['msg'=>'操作成功','code'=>200];

        }catch (\Exception $e){
			Db::rollback();
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}	
	
	
	

	// 获取列表
    public static function getListGroupByMaterial($query=[],$limit=10)
    {
		$user_info 				= request()->userInfo;
		$field 			= 'a.id,a.stock,a.safety_stock,a.min_stock,a.max_stock,a.material_id,a.warehouse_id,b.type,b.name,b.sn,b.unit,b.material,b.surface,b.color,b.remark,b.processing_type';	
		$query['_alias']= 'a';
		$query['_material_alias']= 'b';
		$query['_warehouse_alias']= 'c';
        $list 			= ErpMaterialWarehouse::alias('a')
		->join('erp_material b','a.material_id = b.id','LEFT')->withSearch(['query'],['query'=>$query])
		->join('erp_warehouse c','a.warehouse_id = c.id','LEFT')
		->whereNotNull('b.id')
		->where('b.type',1)
		->where('a.warehouse_id','in',$user_info['warehouse_id'])
		->field($field)->group('a.material_id')->order('a.id','desc')->paginate($limit);
		
		$data 			 = $list->items();
		foreach($data as &$item){
			$enter 			= ErpMaterialAllocateMaterial::alias('a')
			->join('erp_material_enter_material b','a.enter_material_id = b.id','LEFT')
			->field('a.id,a.enter_material_id,a.enter_order_sn,b.stock_num,b.can_out_num,b.freeze_back_num,b.stock_num - b.can_out_num - b.freeze_back_num as num')
			->where('a.material_id',$item['material_id'])
			->whereRaw('b.stock_num>b.can_out_num+b.freeze_back_num')
			->group('a.enter_material_id')->order('a.enter_material_id desc')->limit(5)->select();
			$item['enter']	= $enter;
		}
        return ['code'=>0,'data'=>$data,'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }
	
	
	public static function goBackAdd($userInfo,$param,$need_check){
	
		$approval 	= ErpMaterialApproval::where('id',$param['approval_id'])->find();
		if($approval['status'] != 0){
			return ['msg'=>'已审核','code'=>201];
		}	
	
		$material	= $param['material'];
		$enter 		= ErpMaterialEnterMaterial::alias('a')
			->join('erp_material b','a.material_id = b.id','LEFT')
			->join('erp_material_stock c','a.material_stock_id = c.id','LEFT')
			->where('a.id','in',implode(',',array_column($material,'enter_material_id')))
			->column('a.id,a.purchase_order_id,a.purchase_order_data_id,a.enter_batch_number,a.warehouse_id,a.material_id,a.stock_num,a.can_out_num,a.freeze_back_num,a.stock_num - a.can_out_num - a.freeze_back_num as num,b.sn,b.name,c.order_sn,c.supplier_id','a.id');

		$datas 			= [];
		$supplier_id 	= [];
		
		foreach($material as $vo){
			if(empty($vo['stock_num'])){
				return ['msg'=>'退回数量不存在','code'=>201];
			}
			if(empty($vo['enter_material_id'])){
				return ['msg'=>'请选择入库单','code'=>201];
			}
			$ids 			= explode(',',$material[$vo['id']]['enter_material_id']);
			$stock_num 		= $vo['stock_num'];
			foreach($ids as $v){
				if(empty($enter[$v])){
					return ['msg'=>'入库单不存在','code'=>201];
				}
				$supplier_id[$enter[$v]['order_sn']]= $enter[$v]['supplier_id'];
				if($enter[$v]['num'] >=  $stock_num){							
					$datas[$enter[$v]['order_sn']][]= ['from_id'=>$enter[$v]['id'],'purchase_order_id'=>$enter[$v]['purchase_order_id'],'purchase_order_data_id'=>$enter[$v]['purchase_order_data_id'],'material_stock_id'=>0,'enter_batch_number'=>$enter[$v]['enter_batch_number'],'material_id'=>$enter[$v]['material_id'],'warehouse_id'=>$enter[$v]['warehouse_id'],'stock_num'=>$stock_num,'remark'=>empty($vo['remark'])?'':$vo['remark']];
					$update[] 						= ['id'=>$enter[$v]['id'],'freeze_back_num'=>Db::raw('freeze_back_num+'.$stock_num)];
					$stock_num						= 0;
					break;
				}else{
					$update[] 						= ['id'=>$enter[$v]['id'],'freeze_back_num'=>Db::raw('freeze_back_num+'.$enter[$v]['num'])];
					$datas[$enter[$v]['order_sn']][]= ['from_id'=>$enter[$v]['id'],'purchase_order_id'=>$enter[$v]['purchase_order_id'],'purchase_order_data_id'=>$enter[$v]['purchase_order_data_id'],'material_stock_id'=>0,'enter_batch_number'=>$enter[$v]['enter_batch_number'],'material_id'=>$enter[$v]['material_id'],'warehouse_id'=>$enter[$v]['warehouse_id'],'stock_num'=>$enter[$v]['num'],'remark'=>empty($vo['remark'])?'':$vo['remark']];
					$stock_num						= $stock_num - $enter[$v]['num'];
				}
			}
			
			if($stock_num>0){
				return ['msg'=>$vo['sn'].'退回数量不足','code'=>201];
			}
		}
		
        try {
			$material_data 				= [];
			foreach($datas as $key=>$vo){
				$data					= [];
				$data['order_sn']		= str_replace('RK','DR',ErpMaterialEnterLogic::createOrderSn());
				$data['create_admin'] 	= $userInfo['name'];
				$data['stock_date'] 	= $param['stock_date'];
				$data['remark'] 		= $param['remark'];
				$data['approval_id'] 	= $param['approval_id'];
				$data['batch_number']	= $key;
				$data['supplier_id']	= $supplier_id[$key];
				$data['material_type']	= 1;
				$data['data_type']		= ErpMaterialStockEnum::DATA_TYPE_ENTER;
				$data['type']			= ErpMaterialStockEnum::TYPE_ENTER_WORKSHOP;
				$model 					= ErpMaterialEnter::create($data);
				foreach($vo as $k=>$v){
					$v['material_stock_id'] = $model->id;
					if($need_check == 0){
						$v['need_check'] 	= 0;
						$v['check_username']= $data['create_admin'];
						$v['check_date'] 	= date('Y-m-d');
						$v['check_status'] 	= ErpMaterialEnterMaterialEnum::CHECK_STATUS_FINISH;
						$v['quality_num'] 	= $v['stock_num'];
						$v['qualities_num'] = $v['stock_num'];
					}
					$material_data[]	= $v;
				}
			}
			
			(new ErpMaterialEnterMaterial)->saveAll($material_data);
			
			(new ErpMaterialEnterMaterial)->saveAll($update);
			
			$approval->save(['status'=>1,'check_by'=>$userInfo['name'],'check_date'=>date('Y-m-d')]);

			return ['msg'=>'创建成功','code'=>200,'data'=>['id'=>$model->id,'order_sn'=>$model->order_sn]];
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}
	
	
	public static function goApprovalAdd($userInfo,$param){
		$material	= $param['material'];
		$enter 		= ErpMaterialEnterMaterial::alias('a')
			->join('erp_material b','a.material_id = b.id','LEFT')
			->join('erp_material_stock c','a.material_stock_id = c.id','LEFT')
			->where('a.id','in',implode(',',array_column($material,'enter_material_id')))
			->column('a.id,a.purchase_order_id,a.purchase_order_data_id,a.enter_batch_number,a.warehouse_id,a.material_id,a.stock_num,a.can_out_num,a.freeze_back_num,a.stock_num - a.can_out_num - a.freeze_back_num as num,b.sn,b.name,c.order_sn,c.supplier_id','a.id');

		foreach($material as $vo){
			if(empty($vo['stock_num'])){
				return ['msg'=>'退回数量不存在','code'=>201];
			}
			if(empty($vo['enter_material_id'])){
				return ['msg'=>'请选择入库单','code'=>201];
			}
			$ids 			= explode(',',$material[$vo['id']]['enter_material_id']);
			$stock_num 		= $vo['stock_num'];
			foreach($ids as $v){
				if(empty($enter[$v])){
					return ['msg'=>'入库单不存在','code'=>201];
				}
				
				if($enter[$v]['num'] >=  $stock_num){							
					$stock_num			= 0;
					break;
				}else{
					$stock_num			= $stock_num - $enter[$v]['num'];
				}
			}
			if($stock_num>0){
				return ['msg'=>$vo['sn'].'退回数量不足','code'=>201];
			}
		}
		
        try {
			ErpMaterialApproval::create(['type'=>1,'data'=>$param,'create_by'=>$userInfo['name'],'create_date'=>date('Y-m-d')]);
			return ['msg'=>'创建成功','code'=>200];
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}
	
	
	public static function getApproval($id){
		return ErpMaterialApproval::where('id',$id)->find();
	}
	
	
	public static function goScrapApprovalAdd($param){
		if(empty($param)){
			return ['msg'=>'数据不能为空','code'=>201];
		}
		try {
			$data 					= self::getList(['ids'=>array_column($param,'id')],10000)['data'];
			$user_info 				= request()->userInfo;
			$enter_data 			= ErpMaterialEnterMaterial::alias('a')->join('erp_material_stock b','a.material_stock_id = b.id','LEFT')->
			fieldRaw('a.id,a.material_id,a.warehouse_id,a.can_out_num,a.freeze_out_stock,a.can_out_num - a.freeze_out_stock as num,b.order_sn')
			->whereRaw('a.can_out_num>a.freeze_out_stock')->where('a.can_out_num','>',0)->where('a.material_id','in',array_column($data,'material_id'))->where('a.warehouse_id','in',$user_info['warehouse_id'])->order('a.id asc')->select()->toArray();
			$enter					= [];
			foreach($enter_data as $vo){
				$enter[$vo['material_id'].'-'.$vo['warehouse_id']][] = $vo;
			}		
			$scrap 					= [];
			foreach($data as $vo){
				if(empty($param[$vo['id']]['stock_num'])){
					return ['msg'=>$vo['sn'].'报废量不存在','code'=>201];
				}
				$_num 				= $param[$vo['id']]['stock_num'];
				if($_num != intval($_num) || $_num <= 0){
					return ['msg'=>$vo['sn'].'报废量必须为大于0的整数','code'=>201];
				}
				
				$stock_num 			= $_num;	
				$key 				= $vo['material_id'].'-'.$vo['warehouse_id'];
				
				if(!empty($enter[$key])){
					foreach($enter[$key] as $k=>$v){
						if($v['num']){
							if($stock_num > $v['num']){	
								$enter[$key][$k]['num'] = 0;
								$scrap[]				= ['is_confirm'=>0,'material_id'=>$vo['material_id'],'enter_material_id'=>$v['id'],'stock_num'=>$v['num'],'stock_date'=>$param[$vo['id']]['stock_date'],'remark'=>$param[$vo['id']]['remark'],'cid'=>$param[$vo['id']]['cid'],'create_admin'=>$user_info['name']];	
								$stock_num				= $stock_num - $v['num'];
								
							}else{
								$enter[$key][$k]['num'] = $v['num'] - $stock_num;
								$scrap[]				= ['is_confirm'=>0,'material_id'=>$vo['material_id'],'enter_material_id'=>$v['id'],'stock_num'=>$stock_num,'stock_date'=>$param[$vo['id']]['stock_date'],'remark'=>$param[$vo['id']]['remark'],'cid'=>$param[$vo['id']]['cid'],'create_admin'=>$user_info['name']];					
								$stock_num 				= 0;
								break;
							}
						}
					}
				}else{
					return ['msg'=>$vo['name'].'没库存批次','code'=>201];
				}
				if($stock_num){
					return ['msg'=>$vo['name'].'库存不足','code'=>201];
					//$material[$vo['material_type']][$vo['order_id']][] 				= ['enter_material_id'=>0,'stock_num'=>$stock_num,'id'=>$vo['material_id'],'data_id'=>$vo['id']];
				}
			}
			ErpMaterialApproval::create(['type'=>2,'data'=>$param,'create_by'=>$user_info['name'],'create_date'=>date('Y-m-d')]);
			
			return ['msg'=>'创建成功','code'=>200];
		}catch (\Exception $e){
			
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}	
	
	
	
	
	
}
