<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\ErpSupplierProcess;
use app\admin\validate\ErpSupplierProcessValidate;

class ErpSupplierProcessLogic extends BaseLogic{

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$field = '*';
        $list = ErpSupplierProcess::withSearch(['query'],['query'=>$query])->with(['supplier'=>function($query){return $query->field('id,name');}])->field($field)->order('id','desc')->append(['status_desc'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

    // 添加
    public static function goAdd($data)
    {
        //验证
        $validate = new ErpSupplierProcessValidate;
        if(!$validate->scene('add')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
        try {
            ErpSupplierProcess::create($data);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
    
    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpSupplierProcess::where($map)->find();
		}else{
			return ErpSupplierProcess::find($map);
		}
    }
	
    // 编辑
    public static function goEdit($data)
    {
        //验证
        $validate 	= new ErpSupplierProcessValidate;
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
        $validate 	= new ErpSupplierProcessValidate;
        if(!$validate->scene('remove')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}	
        try{
			ErpSupplierProcess::destroy($data['ids']);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    public static function getAll()
    {
		return ErpSupplierProcess::field('id,name')->order(['id'=>'desc'])->select();
    }

}
