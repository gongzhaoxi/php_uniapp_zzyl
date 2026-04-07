<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\ErpMaterial;
use app\common\model\ErpMaterialScrap;
use app\admin\validate\ErpMaterialScrapValidate;
use think\facade\Db;
use app\common\model\{ErpMaterialEnterMaterial,ErpSupplier};

class ErpMaterialScrapLogic extends BaseLogic{

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$field 		= 'a.id,a.enter_material_id,a.is_confirm,a.stock_date,a.material_id,a.stock_num,a.stocked_num,a.status,a.stock_num-a.stocked_num as num,a.photo,a.cid,a.remark,b.sn,b.name,b.supplier_id,c.unqualified_description';	
		$query['_alias']= 'a';
		$query['_material_alias']= 'b';
		$list 		= ErpMaterialScrap::alias('a')
		->join('erp_material b','a.material_id = b.id','LEFT')
		->join('erp_material_enter_material_report c','a.enter_material_id = c.material_enter_material_id','LEFT')
		->whereRaw('a.stock_num>a.stocked_num')
		->withSearch(['query'],['query'=>$query])->field($field)->order('a.stock_date','desc')->append(['type','photo_link'])->paginate($limit);
		$data 		= $list->items();
		$supplier 	= ErpSupplier::where('id','in',implode(',',array_column($data,'supplier_id')))->column('name','id');
		foreach($data as &$vo){
			$vo['supplier_id'] 	= $vo['supplier_id']?explode(',',$vo['supplier_id']):[];
			$supplier_name 		= [];
			foreach($vo['supplier_id'] as $v){
				$supplier_name[]= empty($supplier[$v])?'':$supplier[$v];
			}
			$vo['supplier_name']= implode(',',$supplier_name);
			$vo['remark'] = $vo['unqualified_description']?$vo['unqualified_description']:$vo['remark'];
		}
        return ['code'=>0,'data'=>$data,'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

	public static function getCount($query=[]){
		$query['_alias']= 'a';
		$query['_material_alias']= 'b';
        return ErpMaterialScrap::alias('a')
		->join('erp_material b','a.material_id = b.id','LEFT')->whereRaw('a.stock_num>a.stocked_num')
		->withSearch(['query'],['query'=>$query])->count();
	}
	
	public static function getExport($query=[],$limit=10000){
		$limit				= $limit>10000?10000:$limit;
		$data				= self::getList($query,$limit)['data'];
		$return				= ['column'=>[],'setWidh'=>[],'keys'=>[],'list'=>$data,'image_fields'=>['photo']];
		$field 				= ['type'=>'报废来源','stock_date'=>'报废日期','remark'=>'报废原因','supplier_name'=>'供应商','sn'=>'物料编码','name'=>'物料名称','stock_num'=>'总需报废量','stocked_num'=>'已出库量','num'=>'剩余需报废量','photo'=>'报废图片'];
		foreach($field as $key=>$vo){
			$return['column'][] 	= $vo;
			$return['setWidh'][] 	= 10;
			$return['keys'][] 		= $key;				
		}
        return $return;	
	}


    // 添加
    public static function goAdd($param)
    {
        //验证
        $validate 	= new ErpMaterialScrapValidate;
        if(!$validate->scene('add')->check($param)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		$material	= $param['material'];
		foreach($material as &$vo){
			if(empty($vo['enter_material_id'])){
				return ['msg'=>'请选择入库批次号','code'=>201];
			}
			$vo['create_admin'] = self::$adminUser['username'];
		}	

        try {
			(new ErpMaterialScrap)->saveAll($material);
			return ['msg'=>'创建成功','code'=>200];
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
	
	
	public static function goRemove($id){
		try {
			ErpMaterialScrap::where('id','in',$id)->where('cid','>',0)->where('stocked_num',0)->delete();
		}catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}
	
	
    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpMaterialScrap::where($map)->find();
		}else{
			return ErpMaterialScrap::find($map);
		}
    }
	
	// 编辑
    public static function goEdit($data)
    {
		$model 		= self::getOne($data['id']);
        if($model->isEmpty()) {
			return ['msg'=>'数据不存在','code'=>201];
		}
        try {
			if(!$model['receive_date']){
				$data['receive_by'] 	= self::$adminUser['username'];
				$data['receive_date'] 	= date('Y-m-d');
			}
            $model->save($data);

        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

}
