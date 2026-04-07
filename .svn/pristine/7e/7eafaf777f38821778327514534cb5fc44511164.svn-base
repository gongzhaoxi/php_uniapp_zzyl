<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\{ErpPurchaseApply,ErpSupplier};
use app\admin\validate\ErpPurchaseApplyValidate;
use app\common\enum\ErpPurchaseApplyEnum;
use think\facade\Db;

class ErpPurchaseApplyLogic extends BaseLogic{

	// 获取列表
    public static function getMaterial($query=[],$limit=10)
    {
		$field 				= 'a.*,b.cid,b.name,b.sn,b.unit,b.material,b.surface,b.color,b.supplier_id,c.name as category';	
		$query['_alias']	= 'a';
		$query['_material_alias']= 'b';
		$list 				= ErpPurchaseApply::alias('a')
		->join('erp_material b','a.data_id = b.id','LEFT')
		->join('dict_data c','b.cid = c.id','LEFT')
		->withSearch(['query'],['query'=>$query])->field($field)->where('a.status',ErpPurchaseApplyEnum::STATUS_NO)->order('b.supplier_id asc,a.id desc')->append(['status_desc','data_type_desc'])->paginate($limit);
        $data 				= $list->items();
		$supplier 			= ErpSupplier::where('id','in',implode(',',array_column($data,'supplier_id')))->where('status',1)->order(['id'=>'desc'])->column('name','id');
		foreach($data as &$vo){
			$vo['supplier_id']	= $vo['supplier_id']?explode(',',$vo['supplier_id']):[];
			$supplier_name		= [];
			foreach($vo['supplier_id'] as $v){
				$supplier_name[]= $supplier[$v];
			}
			$vo['supplier_name']= implode(',',$supplier_name);
		}
		
		return ['code'=>0,'data'=>$data,'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

    // 添加
    public static function goMaterialAdd($param)
    {
        //验证
        $validate 	= new ErpPurchaseApplyValidate;
        if(!$validate->scene('materialAdd')->check($param)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		$material	= $param['material'];
        try {
			$data 	= [];
			foreach($material as $vo){
				$data[] = ['data_id'=>$vo['id'],'delivery_date'=>$vo['delivery_date'],'apply_num'=>$vo['apply_num'],'process_id'=>empty($param['process_id'])?0:$param['process_id'],'type'=>empty($vo['type'])?$param['type']:$vo['type'],'supplier_id'=>$param['supplier_id'],'remark'=>$param['remark'],'apply_date'=>$param['apply_date'],'data_type'=>$param['data_type'],'username'=>$param['username']];
			}
			$res 	= (new ErpPurchaseApply)->saveAll($data);
			return ['msg'=>'创建成功','code'=>200,'data'=>['ids'=>implode(',',$res->column('id'))]];
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    
    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpPurchaseApply::where($map)->find();
		}else{
			return ErpPurchaseApply::find($map);
		}
    }
	

    // 删除
    public static function goRemove($data)
    {
		//验证
        $validate 	= new ErpPurchaseApplyValidate;
        if(!$validate->scene('remove')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}	
        try{
			ErpPurchaseApply::destroy($data['ids']);
        }catch (\Exception $e){
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
	

	// 获取产品申购
    public static function getProduct($query=[],$limit=10)
    {
		$field 					= 'a.*,b.produce_sn,b.order_product_id,b.queue_num,c.product_model,c.product_specs,c.product_num,c.remark as order_product_remark,d.order_sn,d.address,d.order_type,e.username';	
		$query['_alias']		= 'a';
		$query['_produce_alias']= 'b';
		$query['_product_alias']= 'c';
		$query['_order_alias']	= 'd';
		$list 	= ErpPurchaseApply::alias('a')
		->join('erp_order_produce b','a.data_id = b.id','LEFT')
		->join('erp_order_product c','b.order_product_id = c.id','LEFT')
		->join('erp_order d','b.order_id = d.id','LEFT')
		->join('admin_admin e','d.salesman_id = e.id','LEFT')
		->with(['order_product'])
		->withSearch(['query'],['query'=>$query])->field($field)->where('a.type',ErpPurchaseApplyEnum::TYPE_PRODUCT)->where('a.status',ErpPurchaseApplyEnum::STATUS_NO)->order('a.id','desc')->append(['status_desc','data_type_desc'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }
	
}
