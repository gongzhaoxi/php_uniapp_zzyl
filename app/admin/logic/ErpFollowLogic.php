<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use think\facade\Db;
use app\common\model\ErpFollow;
use app\common\model\ErpFollowItem;
use app\admin\validate\ErpFollowValidate;

class ErpFollowLogic extends BaseLogic{
    	
	// 获取列表
    public static function getList($query=[],$limit=10)
    {
        $list 	= ErpFollow::withSearch(['query'],['query'=>$query])->field('id,name,code,status,cid,iso,address,according,remark')->append(['status_desc','category_name'])->order(['id'=>'desc'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpFollow::where($map)->find();
		}else{
			return ErpFollow::find($map);
		}
    }
	
    // 添加
    public static function goAdd($data)
    {
        //验证
        $validate 	= new ErpFollowValidate;
        if(!$validate->scene('add')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		$product 	= empty($data['product'])?[]:$data['product'];
		$process 	= empty($data['process'])?[]:$data['process'];
		unset($data['product']);unset($data['process']);
        try {
            $model = ErpFollow::create($data);
			self::itemSave($model,$product,$process);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }	
	
    public static function itemSave($model,$product,$process)
    {
        $add 				= [];
		$update 			= [];
		$delete 			= [];
		$ids 				= [];
		foreach($product as $vo){
			if(empty($vo['id'])){
				$add[] 		= ['type'=>1,'follow_id'=>$model['id'],'image'=>$vo['image'],'title'=>$vo['title'],'checked'=>$vo['checked'],'is_num'=>$vo['is_num']];
			}else{
				$update[] 	= ['id'=>$vo['id'],'image'=>$vo['image'],'title'=>$vo['title'],'checked'=>$vo['checked'],'is_num'=>$vo['is_num']];
				$ids[] 		= $vo['id'];
			}
		}
		foreach($process as $vo){
			if(empty($vo['id'])){
				$add[] 		= ['type'=>2,'follow_id'=>$model['id'],'image'=>$vo['image'],'title'=>$vo['title'],'checked'=>$vo['checked'],'is_num'=>$vo['is_num']];
			}else{
				$update[] 	= ['id'=>$vo['id'],'image'=>$vo['image'],'title'=>$vo['title'],'checked'=>$vo['checked'],'is_num'=>$vo['is_num']];
				$ids[] 		= $vo['id'];
			}
		}	
		$delete 			= array_diff((array)ErpFollowItem::where('follow_id',$model['id'])->column('id'),$ids);
		if($add){
			(new ErpFollowItem)->saveAll($add);
		}
		if($update){
			(new ErpFollowItem)->saveAll($update);
		}   
		if($delete){
		
			ErpFollowItem::destroy(array_values($delete));
		}
	}
	
	
    // 编辑
    public static function goEdit($data){
        //验证
        $validate 	= new ErpFollowValidate;
        if(!$validate->scene('edit')->check($data))
			return ['msg'=>$validate->getError(),'code'=>201];
		$product 	= empty($data['product'])?[]:$data['product'];
		$process 	= empty($data['process'])?[]:$data['process'];
		unset($data['product']);unset($data['process']);
        try {
            $model 	= self::getOne($data['id']);
			if ($model->isEmpty())  
				return ['msg'=>'数据不存在','code'=>201];

            $model->save($data); 
			self::itemSave($model,$product,$process);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 删除
    public static function goRemove($ids)
    {
        try{
			ErpFollow::destroy($ids);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }


    // 获取列表
    public static function getRecycle($query=[],$limit=10)
    {
        $list 		= ErpFollow::onlyTrashed()->withSearch(['query'],['query'=>$query])->field('id,name,code,status,cid,iso,address,according,remark,delete_time')->append(['status_desc','category_name'])->order(['id'=>'desc'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
        
    }

    // 获取列表
    public static function batchRecycle($ids,$type)
    {
		if (!is_array($ids)) 
			return ['msg'=>'参数错误','code'=>'201'];
		try{
			if($type){
				$data = ErpFollow::onlyTrashed()->whereIn('id', $ids)->select();
				foreach($data as $k){
					$k->restore();
				}
			}else{
				ErpFollow::destroy($ids,true);				
			}
		}catch (\Exception $e){
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
		}
    }
	
	
	// 获取所有
    public static function getAll()
    {
		return ErpFollow::field('id,name')->order(['id'=>'asc'])->select();
    }
	
}
