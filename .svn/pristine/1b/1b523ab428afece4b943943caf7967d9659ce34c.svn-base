<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use think\facade\Db;
use app\common\model\{ErpProcess,ErpProcessWage,ErpProcessMaterial};
use app\admin\validate\ErpProcessValidate;

class ErpProcessLogic extends BaseLogic{
    	
	// 获取列表
    public static function getList($query=[],$limit=10)
    {
        $list 	= ErpProcess::withSearch(['query'],['query'=>$query])->append(['status_desc','user_name','type_desc'])->order(['type'=>'asc','sort'=>'asc','id'=>'asc'])->paginate($limit);
		$data 	= $list->items();
		if(!empty($query['product_id'])){
			foreach($data as $vo){
				$vo['price'] = (int)ErpProcessWage::where('process_id',$vo['id'])->where('product_id',$query['product_id'])->value('price');
			}
		}

        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpProcess::where($map)->find();
		}else{
			return ErpProcess::find($map);
		}
    }
	
    // 添加
    public static function goAdd($data,$wage,$material)
    {
        //验证
        $validate = new ErpProcessValidate;
        if(!$validate->scene('add')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
        try {
			if(!empty($data['is_end'])){
				ErpProcess::where('is_end',1)->update(['is_end'=>0]);
			}
            $model = ErpProcess::create($data);
			self::goSaveWage($model,$wage);
			self::goSaveMaterial($model,$material);			
			
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }	
	
	public static function goSaveWage($model,$wage){
		$wage			= $wage?$wage:[];
		$old 			= ErpProcessWage::where('process_id',$model['id'])->column('id','product_id');
		$data 			= [];
		foreach($wage as $vo){
			if(empty($old[$vo['product_id']])){
				$data[] = ['process_id'=>$model['id'],'product_id'=>$vo['product_id'],'price'=>$vo['price']];
			}else{
				$data[] = ['id'=>$old[$vo['product_id']],'product_id'=>$vo['product_id'],'price'=>$vo['price']];
			}
		}
		if($data){
			(new ErpProcessWage)->saveAll($data);
		}
		if($old){
			$delete 	= array_diff(array_keys($old),array_column($data,'product_id'));
			if($delete){
				ErpProcessWage::where('process_id',$model['id'])->where('product_id','in',$delete)->delete();
			}
		}	
	}
	
	public static function goSaveMaterial($model,$material){
		$material		= $material?$material:[];
		$old 			= ErpProcessMaterial::where('process_id',$model['id'])->column('id','material_id');
		$data 			= [];
		foreach($material as $vo){
			if(empty($old[$vo['material_id']])){
				$data[] = ['process_id'=>$model['id'],'material_id'=>$vo['material_id']];
			}else{
				$data[] = ['id'=>$old[$vo['material_id']],'material_id'=>$vo['material_id']];
			}
		}
		if($data){
			(new ErpProcessMaterial)->saveAll($data);
		}
		if($old){
			$delete 	= array_diff(array_keys($old),array_column($data,'material_id'));
			if($delete){
				ErpProcessMaterial::where('process_id',$model['id'])->where('material_id','in',$delete)->delete();
			}
		}	
	}	
	
	
    // 编辑
    public static function goEdit($data,$wage,$material){
        //验证
        $validate 	= new ErpProcessValidate;
        if(!$validate->scene('edit')->check($data))
			return ['msg'=>$validate->getError(),'code'=>201];
        try {
            $model 	= self::getOne($data['id']);
			if ($model->isEmpty())  
				return ['msg'=>'数据不存在','code'=>201];
			if(!empty($data['is_end'])){
				ErpProcess::where('is_end',1)->update(['is_end'=>0]);
			}
            $model->force()->save($data); 
			self::goSaveWage($model,$wage);
			self::goSaveMaterial($model,$material);	
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    public static function getWage($id)
    {
		return ErpProcessWage::with(['product'=>function($query){return $query->field('id,sn,name,model,specs');}])->where('process_id',$id)->select();
    }

    public static function getMaterial($id)
    {
		return ErpProcessMaterial::with(['material'=>function($query){return $query->field('id,sn,name');}])->where('process_id',$id)->select();
    }


    // 删除
    public static function goRemove($ids)
    {
        try{
			ErpProcess::destroy($ids);
			
			ErpProcessWage::destroy(function($query) use($ids){
				$query->where('process_id','in',$ids);
			});	

			ErpProcessMaterial::destroy(function($query) use($ids){
				$query->where('process_id','in',$ids);
			});				
			
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }


    // 获取列表
    public static function getRecycle($query=[],$limit=10)
    {
        $list 		= ErpProcess::onlyTrashed()->withSearch(['query'],['query'=>$query])->append(['status_desc'])->order('id','desc')->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
        
    }

    // 获取列表
    public static function batchRecycle($ids,$type)
    {
		if (!is_array($ids)) 
			return ['msg'=>'参数错误','code'=>'201'];
		try{
			if($type){
				$data = ErpProcess::onlyTrashed()->whereIn('id', $ids)->select();
				foreach($data as $k){
					$k->restore();
				}
			}else{
				ErpProcess::destroy($ids,true);				
			}
		}catch (\Exception $e){
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
		}
    }
	
}
