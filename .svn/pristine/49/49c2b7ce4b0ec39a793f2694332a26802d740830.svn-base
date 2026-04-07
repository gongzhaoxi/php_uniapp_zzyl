<?php
declare (strict_types = 1);
namespace app\supplier\logic;
use app\supplier\logic\BaseLogic;
use app\common\model\{ErpPurchaseOrder,ErpPurchaseOrderData,ErpPurchaseOrderLog,ErpPurchaseOrderFeedback};
use app\common\enum\{ErpPurchaseOrderLogEnum,ErpPurchaseOrderEnum,ErpPurchaseApplyEnum};
use think\facade\Db;
use app\supplier\validate\ErpPurchaseOrderFeedbackValidate;

class ErpPurchaseOrderLogic extends BaseLogic{

	// 物料采购单
    public static function getMaterial($query=[],$limit=10)
    {
		$field 				= 'a.*';
		$query['_alias']	= 'a';	
		$list 				= ErpPurchaseOrder::alias('a')
		->withSearch(['query'],['query'=>$query])->field($field)->where('a.type',ErpPurchaseApplyEnum::TYPE_MATERIAL)
		->where('a.status',ErpPurchaseOrderEnum::STATUS_YES)
		->where('a.supplier_status','<>',ErpPurchaseOrderEnum::SUPPLIER_STATUS_WAIT_SEND)
		->where('a.supplier_id','=',self::$supplier['id'])->order('a.id','desc')->append(['can_cancel','can_confirm','over_day','last_feedback','system_name'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

    public static function getMaterialData($query=[],$limit=10)
    {
		$field 				= 'a.*,b.cid,b.name,b.sn,b.unit,b.material,b.surface,b.color';	
		$query['_alias']	= 'a';
		$query['_material_alias']= 'b';
		$list 				= ErpPurchaseOrderData::alias('a')
		->join('erp_material b','a.data_id = b.id','LEFT')
		->withSearch(['query'],['query'=>$query])->field($field)->where('a.type',ErpPurchaseApplyEnum::TYPE_MATERIAL)->order('a.id','desc')->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

	public static function getLog($id){
		return ErpPurchaseOrderLog::where('order_id',$id)->where('data_type','in',[ErpPurchaseOrderLogEnum::ORDER_FILED_CHANGE,ErpPurchaseOrderLogEnum::ORDER_DATA_FILED_CHANGE])->order('id desc')->select();
	}

	public static function getFeedback($id){
		return ErpPurchaseOrderFeedback::where('order_id',$id)->order('id desc')->select();
	}

    // 反馈
    public static function goFeedback($data)
    {
		$validate 	= new ErpPurchaseOrderFeedbackValidate;
        if(!$validate->scene('add')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		$model = ErpPurchaseOrder::where('id',$data['order_id'])->where('supplier_id','=',self::$supplier['id'])->find();
		if(empty($model['id'])){
			return ['msg'=>'数据不存在','code'=>201];
		}
        try{
			$data['operator'] 	= self::$supplier['name'];
			$data['type']		= 2;
			ErpPurchaseOrderFeedback::create($data);
		}catch (\Exception $e){
			
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }	

	
    // 确认采购单
    public static function goConfirm($id)
    {
		$model = ErpPurchaseOrder::where('id',$id)->find();
		if(empty($model['id'])){
			return ['msg'=>'数据不存在','code'=>201];
		}
		if(!$model['can_confirm']) {
			return ['msg'=>'状态错误','code'=>201];
		}
		Db::startTrans();
        try{
			ErpPurchaseOrderLog::create(['log'=>'供应商确认采购单','data_type'=>ErpPurchaseOrderLogEnum::ORDER_CONFIRM,'order_id'=>$model['id'],'operator'=>self::$supplier['name']]);
			$model->save(['supplier_status'=>ErpPurchaseOrderEnum::SUPPLIER_STATUS_CONFIRMED]);
			Db::commit();
		}catch (\Exception $e){
			Db::rollback();
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }	
	
    // 撤销采购单
    public static function goCancel($id)
    {
		$model = ErpPurchaseOrder::where('id',$id)->find();
		if(empty($model['id'])){
			return ['msg'=>'数据不存在','code'=>201];
		}
		if(!$model['can_cancel']) {
			return ['msg'=>'状态错误','code'=>201];
		}
		Db::startTrans();
        try{
			ErpPurchaseOrderLog::create(['log'=>'供应商撤销采购单','data_type'=>ErpPurchaseOrderLogEnum::ORDER_CANCEL,'order_id'=>$model['id'],'operator'=>self::$supplier['name']]);
			$model->save(['supplier_status'=>ErpPurchaseOrderEnum::SUPPLIER_STATUS_CANCEL]);
			Db::commit();
		}catch (\Exception $e){
			Db::rollback();
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }	
	
	
	// 成品采购单
    public static function getProduct($query=[],$limit=10)
    {
		$field 				= 'a.*';
		$query['_alias']	= 'a';	
		$list 				= ErpPurchaseOrder::alias('a')
		->withSearch(['query'],['query'=>$query])->field($field)->where('a.type',ErpPurchaseApplyEnum::TYPE_PRODUCT)
		->where('a.status',ErpPurchaseOrderEnum::STATUS_YES)
		->where('a.supplier_status','<>',ErpPurchaseOrderEnum::SUPPLIER_STATUS_WAIT_SEND)
		->where('a.supplier_id','=',self::$supplier['id'])->order('a.id','desc')->append(['can_cancel','can_confirm','over_day','last_feedback','system_name'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }	
	
    public static function getProductData($query=[],$limit=10)
    {
		$field 					= 'a.*,b.produce_sn,b.order_product_id,b.queue_num,c.product_model,c.product_specs,c.product_num,c.remark as order_product_remark,d.order_sn,d.address,d.order_type,e.username';	
		$query['_alias']		= 'a';
		$query['_produce_alias']= 'b';
		$query['_product_alias']= 'c';
		$query['_order_alias']	= 'd';
		$list 	= ErpPurchaseOrderData::alias('a')
		->join('erp_order_produce b','a.data_id = b.id','LEFT')
		->join('erp_order_product c','b.order_product_id = c.id','LEFT')
		->join('erp_order d','b.order_id = d.id','LEFT')
		->join('admin_admin e','d.salesman_id = e.id','LEFT')
		->with(['order_product'])
		->withSearch(['query'],['query'=>$query])->field($field)->where('a.type',ErpPurchaseApplyEnum::TYPE_PRODUCT)->order('a.id','desc')->append(['status_desc','data_type_desc','can_warehous','order_type_desc'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }	
	
	
}
