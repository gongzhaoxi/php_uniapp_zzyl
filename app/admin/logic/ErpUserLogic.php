<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use think\facade\Db;
use app\common\model\ErpUser;
use app\common\model\ErpUserAuth;
use app\common\model\ErpUserSession;
use app\admin\validate\ErpUserValidate;

class ErpUserLogic extends BaseLogic{
    	
	// 获取列表
    public static function getList($query=[],$limit=10)
    {
        $list 	= ErpUser::withSearch(['query'],['query'=>$query])->append(['status_desc'])->order('id','desc')->withoutField('password,delete_time')->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpUser::where($map)->find();
		}else{
			return ErpUser::find($map);
		}
    }
	
    // 添加
    public static function goAdd($data)
    {
        //验证
        $validate = new ErpUserValidate;
        if(!$validate->scene('add')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
        try {
            ErpUser::create($data);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }	
	
    // 编辑
    public static function goEdit($data){
        //验证
        $validate 	= new ErpUserValidate;
        if(!$validate->scene('edit')->check($data))
			return ['msg'=>$validate->getError(),'code'=>201];
        try {
            $model 	= self::getOne($data['id']);
			if ($model->isEmpty())  
				return ['msg'=>'数据不存在','code'=>201];
            $model->save($data); 
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 删除
    public static function goRemove($ids)
    {
        try{
			ErpUser::destroy($ids);
  			ErpUserAuth::destroy(function($query) use($ids){
				$query->where('user_id','in',$ids);
			});			
			ErpUserSession::destroy(function($query) use($ids){
				$query->where('user_id','in',$ids);
			});	         
		   
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }


    // 获取列表
    public static function getRecycle($query=[],$limit=10)
    {
        $list 		= ErpUser::onlyTrashed()->withSearch(['query'],['query'=>$query])->append(['status_desc'])->order('id','desc')->withoutField('password')->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
        
    }

    // 获取列表
    public static function batchRecycle($ids,$type)
    {
		if (!is_array($ids)) 
			return ['msg'=>'参数错误','code'=>'201'];
		try{
			if($type){
				$data = ErpUser::onlyTrashed()->whereIn('id', $ids)->select();
				foreach($data as $k){
					$k->restore();
				}
				$data 	= ErpUserAuth::onlyTrashed()->whereIn('user_id', $ids)->select();
				foreach($data as $k){
					$k->restore();
				}
				$data 	= ErpUserSession::onlyTrashed()->whereIn('user_id', $ids)->select();
				foreach($data as $k){
					$k->restore();
				}	
			}else{
				ErpUser::destroy($ids,true);
				ErpUserAuth::destroy(function($query) use($ids){
					$query->whereIn('user_id',$ids);
				},true);
				ErpUserSession::destroy(function($query) use($ids){
					$query->whereIn('user_id',$ids);
				},true);				
			}
		}catch (\Exception $e){
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
		}
    }
	
}
