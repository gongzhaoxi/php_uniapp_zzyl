<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\ErpWarehouse;
use app\admin\validate\ErpWarehouseValidate;

class ErpWarehouseLogic extends BaseLogic{

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$field = 'id,status,name,type,sn,remark';
        $list = ErpWarehouse::withSearch(['query'],['query'=>$query])->field($field)->order('id','desc')->append(['status_desc','type_desc'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

    // 添加
    public static function goAdd($data)
    {
        //验证
        $validate = new ErpWarehouseValidate;
        if(!$validate->scene('add')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
        try {
			$data['admin_id'] = self::$adminUser['id'];
            ErpWarehouse::create($data);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
    
    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpWarehouse::where($map)->find();
		}else{
			return ErpWarehouse::find($map);
		}
    }
	
    // 编辑
    public static function goEdit($data)
    {
        //验证
        $validate 	= new ErpWarehouseValidate;
        if(!$validate->scene('edit')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
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

    // 删除
    public static function goRemove($data)
    {
		//验证
        $validate 	= new ErpWarehouseValidate;
        if(!$validate->scene('remove')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}	
        try{
			ErpWarehouse::destroy($data['ids']);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 获取回收站
    public static function getRecycle($query=[],$limit=10)
	{
		$field = 'id,status,name,type,sn,remark,create_time,delete_time';
        $list = ErpWarehouse::onlyTrashed()->withSearch(['query'],['query'=>$query])->field($field)->append(['status_desc','type_desc'])->order('id','desc')->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

	//恢复/删除回收站
    public static function goRecycle($ids,$action)
    {
        $validate 		= new ErpWarehouseValidate;
        if(!$validate->scene('recycle')->check(['ids'=>$ids])){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		try{
			if($action){
				$data 	= ErpWarehouse::onlyTrashed()->whereIn('id', $ids)->select();
				foreach($data as $k){
					$k->restore();
				}				
			}else{				
				ErpWarehouse::destroy($ids,true);
			}
		}catch (\Exception $e){
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
		}
		return ['msg'=>'操作成功'];
    }

	// 获取所有
    public static function getAll($query=[])
    {
		$field 	= 'id,name,type,sn';
        return ErpWarehouse::withSearch(['query'],['query'=>$query])->field($field)->order('id','asc')->select();
    }

}
