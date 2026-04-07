<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use think\facade\Db;
use app\common\model\DictType;
use app\common\model\DictData;
use app\admin\validate\DictTypeValidate;

class DictTypeLogic extends BaseLogic{

	
	// 获取列表
    public static function getList($query=[],$limit=10)
    {
        $list = DictType::order('id','desc')->append(['status_desc'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

    // 添加
    public static function goAdd($data)
    {
        //验证
        $validate = new DictTypeValidate;
        if(!$validate->scene('add')->check($data))
			return ['msg'=>$validate->getError(),'code'=>201];
        try {
            DictType::create($data);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
    
    public static function goFind($id)
    {
       return DictType::find($id);
    }
	
    // 编辑
    public static function goEdit($data)
    {
        //验证
        $validate 	= new DictTypeValidate;
        if(!$validate->scene('edit')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		$model 		= self::goFind($data['id']);
        if($model->isEmpty()) {
			return ['msg'=>'数据不存在','code'=>201];
		}
        try {
            $model->save($data);
			DictData::where('type_id',$model['id'])->update(['type_value'=>$model['type']]);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 删除
    public static function goRemove($id)
    {
        $model 		= self::goFind($id);
        if ($model->isEmpty()) 
			return ['msg'=>'数据不存在','code'=>201];
        try{
            $model->delete();
			DictData::destroy(function($query) use($id){
				$query->where('type_id','=',$id);
			});
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 批量删除
    public static function goBatchRemove($ids)
    {
        if (!is_array($ids)) 
			return ['msg'=>'参数错误','code'=>'201'];
        try{
            DictType::destroy($ids);
			DictData::destroy(function($query) use($ids){
				$query->whereIn('type_id',$ids);
			});
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 获取回收站
    public static function getRecycle($query=[],$limit=10)
	{
        $list = DictType::onlyTrashed()->append(['status_desc'])->order('id','desc')->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

	//恢复/删除回收站
    public static function goRecycle($ids,$action)
    {
		if (!is_array($ids)) 
			return ['msg'=>'参数错误','code'=>'201'];
		try{
			if($action){
				$type 	= DictType::onlyTrashed()->whereIn('id', $ids)->select();
				foreach($type as $k){
					$k->restore();
				}
				$data 	= DictData::onlyTrashed()->whereIn('type_id', $ids)->select();
				foreach($data as $k){
					$k->restore();
				}				
			}else{				
				DictType::destroy($ids,true);
				DictData::destroy(function($query) use($ids){
					$query->whereIn('type_id',$ids);
				},true);				
			}
		}catch (\Exception $e){
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
		}
		return ['msg'=>'操作成功'];
    }

}
