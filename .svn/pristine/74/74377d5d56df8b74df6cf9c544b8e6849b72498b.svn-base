<?php
declare (strict_types = 1);
namespace app\index\logic;
use app\index\logic\BaseLogic;
use app\common\model\{ErpMaterialPlan,ErpMaterialPlanProcess,ErpMaterialPlanError,ErpProcess,ErpFollow,ErpFollowItem,ErpMaterialPlanFollow,ErpMaterialPlanProcessLog,ErpMaterialEnter,ErpMaterialEnterMaterial,ErpMaterialCode};
use app\common\enum\{ErpMaterialPlanEnum,ErpMaterialStockEnum};
use think\facade\Db;
use app\index\validate\{ErpMaterialPlanErrorValidate};
use app\admin\logic\{ErpMaterialEnterLogic};

class ErpMaterialPlanLogic extends BaseLogic{

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$user_info 	= request()->userInfo;
		$map	 	= [];
		$map[]		= ['a.status', '=', 0];
		if(!empty($query['produce_status'])) {
			$map[]	= ['a.produce_status', '=', $query['produce_status']];
        }
		if(!empty($query['region_type'])) {
			$map[]	= ['b.region_type', '=', $query['region_type']];
        }		
		if(!empty($query['keyword'])) {
			$map[]	= ['c.sn|c.name', 'like', '%' . $query['keyword'] . '%'];
        }
        if(!empty($query['plan_sn'])) {
			$map[]	= ['b.plan_sn', 'like', '%' . $query['plan_sn'] . '%'];
        }
		if(!empty($query['start_date'])) {
			$time 		= is_array($query['start_date'])?$query['start_date']:explode('至',$query['start_date']);
			if(!empty($time[0])){
				$map[]	= ['b.start_date', '>=', (trim($time[0]))];
			}
			if(!empty($time[1])){
				$map[]	= ['b.start_date', '<=', (trim($time[1]))];
			}
        }		
		$field 			= 'a.*,b.start_date,b.plan_sn,b.num,b.finish_num,b.assembled_num,b.inspect_num,c.name,c.sn,c.qc_file,c.produce_file';		
		$list 			= ErpMaterialPlanProcess::alias('a')
		->join('erp_material_plan b','a.plan_id = b.id','LEFT')
		->join('erp_material c','a.material_id = c.id','LEFT')
		->join('erp_process d','a.process_id = d.id','LEFT')->where('d.user_id','find in set',$user_info['user_id'])
		->where($map)->field($field)->order('a.id','asc')->group('a.plan_id')->paginate($limit);

        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpMaterialPlan::where($map)->find();
		}else{
			return ErpMaterialPlan::find($map);
		}
    }
	
	public static function getProcess($plan_id,$user_id)
    {
		$process 		= ErpMaterialPlanProcess::alias('a')
		->join('erp_process b','a.process_id = b.id','LEFT')
		->join('erp_follow c','b.follow_id = c.id','LEFT')
		->field('a.id,a.process_id,a.process_name,a.price,a.status,a.confirm_date,a.username,b.follow_id,c.name as follow_name')
		->where('a.plan_id',$plan_id)
		->where('b.user_id','find in set',$user_id)->order(['b.sort'=>'asc','b.id'=>'asc'])->select()->toArray();

		return $process ;
	}
	
    public static function goSetCode($id)
    {
        $model 		= self::getOne($id);
        if(empty($model['id'])) {
			return ['msg'=>'数据不存在','code'=>201];
		}
        try {
			$count = ErpMaterialCode::where('data_id',$model['id'])->where('data_type','erp_material_plan')->count()+1;
			$code 	= $model['plan_sn'].'|'.$model['material']['sn'].'|'.sprintf("%05d",$count);
			ErpMaterialCode::create(['data_id'=>$model['id'],'data_type'=>'erp_material_plan','code'=>$code,'material_id'=>$model['material_id']]);

			return ['msg'=>'操作成功','code'=>200,'data'=>['code'=>$code]];
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
	
	public static function goError($data,$userInfo)
    {
		$user_id	= $userInfo['user_id'];
        //验证
        $validate	= new ErpMaterialPlanErrorValidate;
        if(!$validate->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		$process 	= ErpProcess::where('id',$data['process_id'])->find();
		if(empty($process['id'])) {
			return ['msg'=>'流程不存在','code'=>201];
		}
		if(!in_array($user_id,$process['user_id'])){
			return ['msg'=>'你没权限操作该流程','code'=>201];
		}
		$plan 		= ErpMaterialPlan::where('id',$data['plan_id'])->find();
		if($plan['status'] == ErpMaterialPlanEnum::STATUS_WAREHOUSED) {
			return ['msg'=>'已入库','code'=>201];
		}
		if(!empty($process['id']) && !empty($plan['id']) && ErpMaterialPlanProcess::where('process_id',$process['id'])->where('plan_id',$plan['id'])->where('status',1)->count()){
			return ['msg'=>'该流程已完成','code'=>201];
		}
		$data['user_id']			= $user_id;
		$data['plan_id']			= empty($plan['id'])?0:$plan['id'];
		$data['process_name']		= empty($process['name'])?'':$process['name'];
		$data['username']			= $userInfo['name'];
		$data['create_time']		= date('Y-m-d H:i:s');
        try {
			ErpMaterialPlanError::create($data);
			if(!empty($plan['id'])) {
				$plan->save(['error_time'=>time()]);
			}
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }	
	
	public static function getFollow($id,$process_id,$user_id)
    {
		$plan 			= ErpMaterialPlan::with(['material'=>function($query){return $query->field('id,name,sn');}])->where('id',$id)->find();
		if(empty($plan['id'])){
			 return ['msg'=>'数据不存在','code'=>201];
		}
		$process 		= ErpProcess::where('id',$process_id)->where('user_id','find in set',$user_id)->find();
		if(empty($process['id'])){
			return ['msg'=>'工序不存在','code'=>201];
		}
		$follow 		= ErpFollow::where('id',$process['follow_id'])->find();
		$follow_item	= ErpFollowItem::field('id,image,title,type,checked,is_num,follow_id')->where('follow_id','=',$process['follow_id'])->order(['id'=>'asc'])->select()->toArray();
		$produce_follow	= ErpMaterialPlanFollow::where('follow_id','=',$process['follow_id'])->where('process_id','=',$process['id'])->where('plan_id','=',$plan['id'])->column('id,remark,num,after_num','follow_item_id');
	
		$follow_product		= [];
		$follow_process		= [];
		foreach($follow_item as $k=>$v){
			$v['remark'] 				= '';
			$v['num'] 					= '';
			$v['after_num'] 			= '';
			$v['produce_follow_id'] 	= '';
			$v['image'] 				= get_browse_url($v['image']);
			$v['checked'] 				= $v['checked']?true:false;
			$v['plan_id'] 				= $plan['id'];
			$v['process_id'] 			= $process['id'];
			if(!empty($produce_follow[$v['id']])){
				$v['checked'] 			= true;
				$v['remark'] 			= $produce_follow[$v['id']]['remark'];
				$v['num'] 				= $produce_follow[$v['id']]['num'];
				$v['after_num'] 		= $produce_follow[$v['id']]['after_num'];
				$v['produce_follow_id']	= $produce_follow[$v['id']]['id'];
			}
			if($v['type'] == 1){
				$follow_product[]		= $v;
			}else{
				$follow_process[]		= $v;
			}
		}
		return ['process_list'=>self::getProcess($plan['id'],$user_id),'plan'=>$plan,'process'=>$process ,'follow'=>$follow ,'follow_product'=>$follow_product,'follow_process'=>$follow_process];
	}
	
	
	public static function goFollow($data,$userInfo)
    {
		try {
			if($data){
				$plan_follow_id		= array_filter(array_column($data,'plan_follow_id'));
				$produce_follow		= $plan_follow_id?ErpMaterialPlanFollow::where('id','in',$plan_follow_id)->column('id,remark,num,after_num','id'):[];
				
				$add				= [];
				$update				= [];
				$log 				= [];
				foreach($data as $k=>$v){
					if(!empty($v['id'])){
						$item			= ['title'=>$v['title'],'type'=>$v['type'],'image'=>$v['image'],'checked'=>$v['checked'],'is_num'=>$v['is_num']];
						if(empty($v['plan_follow_id'])){
							$add[] 		= ['plan_id'=>$v['plan_id'],'process_id'=>$v['process_id'],'follow_item'=>$item,'follow_id'=>$v['follow_id'],'follow_item_id'=>$v['id'],'remark'=>$v['remark'],'num'=>$v['num'],'after_num'=>$v['after_num']];
							$str 		= '';
							if($v['num']){
								$str	.= '录入编号`'.$v['num'].'`;';
							}
							if($v['after_num'] ){
								$str	.= '录入换后编号`'.$v['after_num'].'`;';
							}					
							if($v['remark']){
								$str	.= '录入备注`'.$v['remark'].'`;';
							}
							if($str){
								$log[] 	= ['remark'=>$v['title'].$str,'process_id'=>$v['process_id'],'plan_id'=>$v['plan_id'],'user_id'=>$userInfo['user_id'],'username'=>$userInfo['name']];
							}
						}else{
							$update[] 	= ['id'=>$v['plan_follow_id'],'follow_item'=>$item,'remark'=>$v['remark'],'num'=>$v['num'],'after_num'=>$v['after_num'],'user_id'=>$userInfo['user_id'],'username'=>$userInfo['name']];
							$str 		= '';
							if($v['num'] != $produce_follow[$v['plan_follow_id']]['num']){
								$str	.= '`编号`从`'.$produce_follow[$v['plan_follow_id']]['num'].'`到`'.$v['num'].'`;';
							}
							if($v['after_num'] != $produce_follow[$v['plan_follow_id']]['after_num']){
								$str	.= '`换后编号`从`'.$produce_follow[$v['plan_follow_id']]['after_num'].'`到`'.$v['after_num'].'`;';
							}					
							if($v['remark'] != $produce_follow[$v['plan_follow_id']]['remark']){
								$str	.= '`备注`从`'.$produce_follow[$v['plan_follow_id']]['remark'].'`到`'.$v['remark'].'`;';
							}
							if($str){
								$log[] 	= ['remark'=>$v['title'].'的'.$str,'process_id'=>$v['process_id'],'plan_id'=>$v['plan_id'],'user_id'=>$userInfo['user_id'],'username'=>$userInfo['name']];
							}
						}
					}

				}						
				if($add){
					(new ErpMaterialPlanFollow)->saveAll($add);
				}
				if($update){
					(new ErpMaterialPlanFollow)->saveAll($update);
				}
				if($log){
					(new ErpMaterialPlanProcessLog)->saveAll($log);
				}
			}
	
		}catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }		
	}	
	
	
    public static function goFinish($plan_id,$process_id,$data,$userInfo,$follow=[])
    {
		if($follow){
			self::goFollow($follow,$userInfo);
		}
		$user_id	= $userInfo['user_id'];
		$process 	= ErpProcess::where('id',$process_id)->find();
		if(empty($process['id'])) {
			return ['msg'=>'流程不存在','code'=>201];
		}
		if(!in_array($user_id,$process['user_id'])){
			return ['msg'=>'你没权限操作该流程','code'=>201];
		}
		$plan 		= ErpMaterialPlan::where('id',$plan_id)->find();
		if(empty($plan['id'])) {
			return ['msg'=>'产品不存在','code'=>201];
		}
		if($plan['status'] == ErpMaterialPlanEnum::STATUS_WAREHOUSED) {
			return ['msg'=>'已入库','code'=>201];
		}
		$model 		= ErpMaterialPlanProcess::where('plan_id',$plan['id'])->where('process_id',$process['id'])->find();
		if($model['status'] == 1){
			return ['msg'=>'该流程已完成','code'=>201];
		}
		
		$update						= [];
		$update['user_id']			= $user_id;
		$update['username']			= $userInfo['name'];
		$update['confirm_date']		= date('Y-m-d');
		
		$plan_update				= [];
		
		if($process['id'] == 5){
			$num 	= $plan['assembled_num'] + $data['assembled_num'];
			if($num > $plan['num']){
				return ['msg'=>'本次完成数量最大只能为'.($plan['num'] - $plan['assembled_num']),'code'=>201];
			}
			$plan_update['assembled_num'] 	= $num;
			$update['status']				= $num==$plan['num']?1:0;
		}	
		
		if($process['id'] == 6){
			$num 	= $plan['inspect_num'] + $data['inspect_num'];
			if($num > $plan['num']){
				return ['msg'=>'本次完成数量最大只能为'.($plan['num'] - $plan['inspect_num']),'code'=>201];
			}
			$plan_update['inspect_num'] 	= $num;
			$update['status']				= $num==$plan['num']?1:0;
		}	

		if($process['id'] == 7){
			$num 	= $plan['finish_num'] + $data['finish_num'];
			if($num > $plan['num']){
				return ['msg'=>'本次完成数量最大只能为'.($plan['num'] - $plan['finish_num']),'code'=>201];
			}
			$plan_update['finish_num'] 		= $num;
			$update['status']				= $num==$plan['num']?1:0;
		}
		
		if($plan['process_id'] == 0 || ErpProcess::where('id',$plan['process_id'])->value('sort') < $process['sort']){
			$plan_update['process_id'] 		= $process['id'];
		}
		Db::startTrans();
        try {
			if($process_id == 7 && $update['status'] == 1){
				$plan_update['finish_date'] 	= date('Y-m-d');
				$plan_update['finish_username'] = $userInfo['name'];
				$plan_update['status'] 			= ErpMaterialPlanEnum::STATUS_WAREHOUSED;
				
				$enter_material					= [];
				$enter_material[] 				= ['id'=>$plan['material_id'],'warehouse_id'=>$data['warehouse_id'],'stock_num'=>$data['finish_num'],'stocked_num'=>0,'can_out_num'=>0,'quality_num'=>$data['finish_num'],'qualities_num'=>$data['finish_num']];
				$order_sn 						= ErpMaterialEnterLogic::createOrderSn();
				$enter 							= ErpMaterialEnter::create(['create_admin'=>$userInfo['name'],'data_type'=>ErpMaterialStockEnum::DATA_TYPE_ENTER,'order_sn'=>$order_sn,'batch_number'=>$order_sn,'enter_batch_number'=>$order_sn,'type'=>ErpMaterialStockEnum::TYPE_ENTER_PLAN,'remark'=>'计划自动入库','status'=>ErpMaterialStockEnum::STATUS_HANDLE,'material_type'=>$plan['material']['type'],'stock_date'=>date('Y-m-d'),'supplier_id'=>0]);
				ErpMaterialEnterLogic::insertMaterial($enter,$enter_material);
				$res 							= ErpMaterialEnterLogic::goConfirm($enter->id,ErpMaterialEnterMaterial::where('material_stock_id',$enter->id)->column('id'));
				if(isset($res['code']) && $res['code'] == 201){
					throw new \Exception($res['msg']);
				}
			}
			
			$plan->save($plan_update);
			$model->save($update);
			ErpMaterialPlanProcessLog::create(['remark'=>$process['name'].'完成报工','process_id'=>$process['id'],'plan_id'=>$plan['id'],'user_id'=>$userInfo['user_id'],'username'=>$userInfo['name']]);
			
			Db::commit();
        }catch (\Exception $e){
            Db::rollback();
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
	
	
	
	
	
}
