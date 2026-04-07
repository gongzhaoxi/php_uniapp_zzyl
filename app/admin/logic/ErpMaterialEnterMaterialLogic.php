<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\{ErpMaterialEnterMaterial,ErpMaterialEnterMaterialReport};

class ErpMaterialEnterMaterialLogic extends BaseLogic{

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$field = 'id,status,region_type,name,contacts,phone,address,address_en,region_type,sn,region';
        $list = ErpMaterialEnterMaterial::withSearch(['query'],['query'=>$query])->field($field)->order('id','desc')->append(['status_desc','region_type_desc','region_name'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }


    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpMaterialEnterMaterial::where($map)->find();
		}else{
			return ErpMaterialEnterMaterial::find($map);
		}
    }
	
	
	// 编辑
    public static function goReportEdit($data)
    {
		$model 		= ErpMaterialEnterMaterialReport::where('id',$data['id'])->find();
        if($model->isEmpty()) {
			return ['msg'=>'数据不存在','code'=>201];
		}
        try {
			unset($data['status']);
            $model->save($data);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }


}
