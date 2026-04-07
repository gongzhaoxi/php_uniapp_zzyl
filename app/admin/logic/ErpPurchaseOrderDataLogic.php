<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\{ErpPurchaseOrderData,ErpPurchaseOrderOut};
use app\admin\validate\ErpPurchaseOrderDataValidate;
use app\common\enum\{ErpPurchaseApplyEnum};

class ErpPurchaseOrderDataLogic extends BaseLogic{

    public static function getMaterial($query=[],$limit=10)
    {
		$field 						= 'a.*,a.apply_num-a.warehous_num as no_warehous_num,b.cid,b.name,b.sn,b.unit,b.material,b.surface,b.color,c.order_sn,c.order_date,c.overdue_reason,c.check_admin,c.check_date,d.name as supplier_name,e.username as follow_admin_username';	
		$query['_alias']			= 'a';
		$query['_material_alias']	= 'b';
		$query['_order_alias']		= 'c';
		$list 				= ErpPurchaseOrderData::alias('a')
		->join('erp_material b','a.data_id = b.id','LEFT')
		->join('erp_purchase_order c','a.order_id = c.id','LEFT')
		->join('erp_supplier d','c.supplier_id = d.id','LEFT')
		->join('admin_admin e','c.follow_admin_id = e.id','LEFT')
		->withSearch(['query'],['query'=>$query])->field($field)->where('a.type',ErpPurchaseApplyEnum::TYPE_MATERIAL)->order(['a.id'=>'desc'])->append(['status_desc'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

	public static function getMaterialExportCount($query=[]){
		$field 						= 'a.id';	
		$query['_alias']			= 'a';
		$query['_material_alias']	= 'b';
		$query['_order_alias']		= 'c';
        $count 			= ErpPurchaseOrderData::alias('a')
		->join('erp_material b','a.data_id = b.id','LEFT')
		->join('erp_purchase_order c','a.order_id = c.id','LEFT')
		->withSearch(['query'],['query'=>$query])->field($field)->order('a.id','desc')->count();
		return ['data'=>['count'=>$count,'key'=>rand_string()]];
	}	
	
	
	public static function getMaterialExport($query=[],$limit=10000){
		$limit				= $limit>10000?10000:$limit;
		$data				= self::getMaterial($query,$limit)['data'];
		$return				= ['column'=>[],'setWidh'=>[],'keys'=>[],'list'=>$data,'image_fields'=>[]];
		$field 				= ['order_sn'=>'采购单编号','supplier_name'=>'供应商','order_date'=>'采购日期','overdue_reason'=>'超期原因','follow_admin_username'=>'采购跟进人','sn'=>'物料编码','name'=>'物料名称','apply_num'=>'采购量','warehous_num'=>'已回厂数','no_warehous_num'=>'未回厂数','warehous_date'=>'入库日期','unit'=>'单位','material'=>'材料','surface'=>'表面','color'=>'颜色','delivery_date'=>'要求交货日期','warehous_overdue'=>'入库时超期天数','status_desc'=>'状态'];
		foreach($field as $key=>$vo){
			$return['column'][] 	= $vo;
			$return['setWidh'][] 	= 10;
			$return['keys'][] 		= $key;				
		}
        return $return;	
	}	

	public static function getMaterialListExport($query=[],$limit=10000){
		$limit				= $limit>10000?10000:$limit;
		$data				= self::getMaterial($query,$limit)['data'];
		$return				= ['column'=>[],'setWidh'=>[],'keys'=>[],'list'=>$data,'image_fields'=>[]];
		$field 				= ['order_sn'=>'采购单编号','supplier_name'=>'供应商','order_date'=>'采购日期','overdue_reason'=>'超期原因','follow_admin_username'=>'采购跟进人','sn'=>'物料编码','name'=>'物料名称','apply_num'=>'采购量','unit'=>'单位','material'=>'材料','surface'=>'表面','color'=>'颜色','delivery_date'=>'要求交货日期','status_desc'=>'状态'];
		foreach($field as $key=>$vo){
			$return['column'][] 	= $vo;
			$return['setWidh'][] 	= 10;
			$return['keys'][] 		= $key;				
		}
        return $return;	
	}		
	
	
    public static function getMaterialStat($query=[],$limit=10,$order=['a.id'=>'desc'])
    {
		$field 						= 'sum(a.apply_num) as apply_num,sum(a.warehous_num) as warehous_num,b.cid,b.name,b.sn,b.unit,b.material,b.surface,b.color,d.name as supplier_name';	
		$query['_alias']			= 'a';
		$query['_material_alias']	= 'b';
		$query['_order_alias']		= 'c';
		$list 				= ErpPurchaseOrderData::alias('a')
		->join('erp_material b','a.data_id = b.id','LEFT')
		->join('erp_purchase_order c','a.order_id = c.id','LEFT')
		->join('erp_supplier d','c.supplier_id = d.id','LEFT')
		->withSearch(['query'],['query'=>$query])->field($field)->where('a.type',ErpPurchaseApplyEnum::TYPE_MATERIAL)->order(['c.supplier_id'=>'desc','a.data_id'=>'desc'])->group('c.supplier_id,a.data_id')->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }  
	
	
	public static function getMaterialStatExportCount($query=[]){
		$field 						= 'a.id';	
		$query['_alias']			= 'a';
		$query['_material_alias']	= 'b';
		$query['_order_alias']		= 'c';
        $count 			= ErpPurchaseOrderData::alias('a')
		->join('erp_material b','a.data_id = b.id','LEFT')
		->join('erp_purchase_order c','a.order_id = c.id','LEFT')
		->join('erp_supplier d','c.supplier_id = d.id','LEFT')
		->withSearch(['query'],['query'=>$query])->field($field)->where('a.type',ErpPurchaseApplyEnum::TYPE_MATERIAL)->order(['c.supplier_id'=>'desc','a.data_id'=>'desc'])->group('c.supplier_id,a.data_id')->count();
		return ['data'=>['count'=>$count,'key'=>rand_string()]];
	}		
	
	public static function getMaterialStatExport($query=[],$limit=10000){
		$limit				= $limit>10000?10000:$limit;
		$data				= self::getMaterialStat($query,$limit)['data'];
		$return				= ['column'=>[],'setWidh'=>[],'keys'=>[],'list'=>$data,'image_fields'=>[]];
		$field 				= ['supplier_name'=>'供应商','sn'=>'物料编码','name'=>'物料名称','apply_num'=>'采购量','warehous_num'=>'入库量','unit'=>'单位','material'=>'材料','surface'=>'表面','color'=>'颜色'];
		foreach($field as $key=>$vo){
			$return['column'][] 	= $vo;
			$return['setWidh'][] 	= 10;
			$return['keys'][] 		= $key;				
		}
        return $return;	
	}	
	
	
    public static function getProduct($query=[],$limit=10)
    {
		$field 					= 'a.*,b.produce_sn,b.order_product_id,b.queue_num,c.overdue_reason,c.order_date,c.order_sn as purchase_order_sn,c.follow_admin_id,d.order_sn,d.address,d.order_type,d.salesman_id';	
		$query['_alias']		= 'a';
		$query['_order_alias']	= 'c';
		$list 	= ErpPurchaseOrderData::alias('a')
		->join('erp_order_produce b','a.data_id = b.id','LEFT')
		->join('erp_purchase_order c','a.order_id = c.id','LEFT')
		->join('erp_order d','b.order_id = d.id','LEFT')
		->with(['order_product','supplier'=>function($query){return $query->field('id,code,name');},'salesman'=>function($query){return $query->field('id,username');},'follower'=>function($query){return $query->field('id,username');}])
		->withSearch(['query'],['query'=>$query])->field($field)->where('a.type',ErpPurchaseApplyEnum::TYPE_PRODUCT)->order('a.id','desc')->append(['status_desc','data_type_desc','over_day','order_type_desc'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

	public static function getProductStat($query=[],$limit=10,$order=['a.id'=>'desc'])
    {
		$field 						= 'sum(a.warehous_num) as warehous_num,e.product_name,d.name as supplier_name';	
		$query['_alias']			= 'a';
		$query['_order_alias']		= 'c';
		$list 						= ErpPurchaseOrderData::alias('a')
		->join('erp_order_produce b','a.data_id = b.id','LEFT')
		->join('erp_purchase_order c','a.order_id = c.id','LEFT')
		->join('erp_supplier d','c.supplier_id = d.id','LEFT')
		->join('erp_order_product e','a.data_id = b.id','LEFT')
		->withSearch(['query'],['query'=>$query])->field($field)->where('a.type',ErpPurchaseApplyEnum::TYPE_PRODUCT)->order(['c.supplier_id'=>'desc','b.product_id'=>'desc'])->group('c.supplier_id,b.product_id')->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    } 
	
	
	
	
    public static function getOutsourcing($query=[],$limit=10)
    {
		$field 						= 'a.*,b.cid,b.name,b.sn,b.unit,b.material,b.surface,b.color,c.process_name,c.order_sn,c.order_date,c.overdue_reason,c.follow_admin_username,d.name as supplier_name,e.id as enter_material_id,f.order_sn as enter_order_sn,f.create_admin as enter_create_admin,g.num';	
		$query['_alias']			= 'a';
		$query['_material_alias']	= 'b';
		$query['_order_alias']		= 'c';
		$query['_stock_alias']		= 'f';
		$list 				= ErpPurchaseOrderData::alias('a')
		->join('erp_material b','a.data_id = b.id','LEFT')
		->join('erp_purchase_order c','a.order_id = c.id','LEFT')
		->join('erp_supplier d','c.supplier_id = d.id','LEFT')
		->join('erp_material_enter_material e','a.id = e.purchase_order_data_id','LEFT')
		->join('erp_material_stock f','e.material_stock_id = f.id','LEFT')
		->join('erp_material_payout g','a.id = g.data_id')
		->withSearch(['query'],['query'=>$query])->field($field)->order(['a.id'=>'desc'])->group('g.id')->append(['status_desc','no_warehous_num'])->paginate($limit);
		
		$total 						= [];
		$total['apply_num']			= ErpPurchaseOrderData::alias('a')
		->join('erp_material b','a.data_id = b.id','LEFT')->join('erp_purchase_order c','a.order_id = c.id','LEFT')
		->join('erp_material_enter_material e','a.id = e.purchase_order_data_id','LEFT')
		->join('erp_material_stock f','e.material_stock_id = f.id','LEFT')
		->withSearch(['query'],['query'=>$query])->sum('apply_num');
		
		$total['warehous_num']		= ErpPurchaseOrderData::alias('a')
		->join('erp_material b','a.data_id = b.id','LEFT')->join('erp_purchase_order c','a.order_id = c.id','LEFT')
		->join('erp_material_enter_material e','a.id = e.purchase_order_data_id','LEFT')
		->join('erp_material_stock f','e.material_stock_id = f.id','LEFT')
		->withSearch(['query'],['query'=>$query])->sum('warehous_num');
		
        $total['no_warehous_num'] 	= $total['apply_num'] - $total['warehous_num'];
		return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit,'totalRow'=>$total]];
    }	
	
	public static function getOutsourcingExportCount($query=[]){
		$field 			= 'a.id';	
		$query['_alias']= 'a';
		$query['_material_alias']= 'b';
		$query['_order_alias']	= 'c';
		$query['_stock_alias']		= 'f';
        $count 			= ErpPurchaseOrderData::alias('a')
		->join('erp_material b','a.data_id = b.id','LEFT')->join('erp_purchase_order c','a.order_id = c.id','LEFT')
		->join('erp_material_enter_material e','a.id = e.purchase_order_data_id','LEFT')
		->join('erp_material_stock f','e.material_stock_id = f.id','LEFT')
		->join('erp_material_payout g','a.id = g.data_id')
		->withSearch(['query'],['query'=>$query])->field($field)->order('a.id','desc')->group('g.id')->count();
		return ['data'=>['count'=>$count,'key'=>rand_string()]];
	}	
	
	
	public static function getOutsourcingExport($query=[],$limit=10000){
		$limit				= $limit>10000?10000:$limit;
		$data				= self::getOutsourcing($query,$limit)['data'];
		$return				= ['column'=>[],'setWidh'=>[],'keys'=>[],'list'=>$data,'image_fields'=>[]];
		$field 				= ['enter_order_sn'=>'入库单号','warehous_date'=>'入库日期','supplier_name'=>'供应商','process_name'=>'委外工序','enter_create_admin'=>'入库单创建人','sn'=>'物料编码','name'=>'物料名称','warehous_num'=>'入库数量','unit'=>'单位'];
		foreach($field as $key=>$vo){
			$return['column'][] 	= $vo;
			$return['setWidh'][] 	= 10;
			$return['keys'][] 		= $key;				
		}
        return $return;	
	}	
	
	public static function getOutsourcingLoss($query=[],$limit=10)
    {
		$field 						= 'a.*,b.cid,b.name as order_material_name,b.sn as order_material_sn,c.name as out_material_name,d.warehous_num,d.apply_num,e.name as supplier_name,c.sn as out_material_sn,f.process_name,f.order_sn,f.order_date,f.follow_admin_username';	
		$query['_alias']			= 'a';
		$query['_order_alias']		= 'f';
		
		$list 				= ErpPurchaseOrderOut::alias('a')
		->join('erp_material b','a.order_material_id = b.id','LEFT')
		->join('erp_material c','a.out_material_id = c.id','LEFT')
		->join('erp_purchase_order_data d','a.order_data_id = d.id','LEFT')
		->join('erp_supplier e','d.supplier_id = e.id','LEFT')
		->join('erp_purchase_order f','a.order_id = f.id','LEFT')
		->withSearch(['query'],['query'=>$query])->field($field)->order(['a.id'=>'desc'])->append(['status_desc','no_warehous_num'])->paginate($limit);
		
		$total 						= [];
		$total['apply_num']			= ErpPurchaseOrderOut::alias('a')
		->join('erp_purchase_order_data d','a.order_data_id = d.id','LEFT')
		->join('erp_purchase_order f','a.order_id = f.id','LEFT')
		->withSearch(['query'],['query'=>$query])->group('a.order_data_id')->sum('d.apply_num');
		
		$total['warehous_num']		= ErpPurchaseOrderOut::alias('a')
		->join('erp_purchase_order_data d','a.order_data_id = d.id','LEFT')
		->join('erp_purchase_order f','a.order_id = f.id','LEFT')
		->withSearch(['query'],['query'=>$query])->group('a.order_data_id')->sum('d.warehous_num');
		
		$total['theory_num']		= ErpPurchaseOrderOut::alias('a')
		->join('erp_purchase_order_data d','a.order_data_id = d.id','LEFT')
		->join('erp_purchase_order f','a.order_id = f.id','LEFT')
		->withSearch(['query'],['query'=>$query])->sum('a.theory_num');		
		
		$total['stock_num']			= ErpPurchaseOrderOut::alias('a')
		->join('erp_purchase_order_data d','a.order_data_id = d.id','LEFT')
		->join('erp_purchase_order f','a.order_id = f.id','LEFT')
		->withSearch(['query'],['query'=>$query])->sum('a.stock_num');			
		
		$total['loss_rate']			= ErpPurchaseOrderOut::alias('a')
		->join('erp_purchase_order_data d','a.order_data_id = d.id','LEFT')
		->join('erp_purchase_order f','a.order_id = f.id','LEFT')
		->withSearch(['query'],['query'=>$query])->avg('a.loss_rate');	
		
        $total['no_warehous_num'] 	= $total['apply_num'] - $total['warehous_num'];
		return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit,'totalRow'=>$total]];
    }
	
	public static function getOutsourcingLossExportCount($query=[]){
		$field 						= 'a.id';	
		$query['_alias']			= 'a';
		$query['_order_alias']		= 'f';
        $count 			= ErpPurchaseOrderOut::alias('a')
		->join('erp_purchase_order_data d','a.order_data_id = d.id','LEFT')
		->join('erp_purchase_order f','a.order_id = f.id','LEFT')
		->withSearch(['query'],['query'=>$query])->field($field)->count();
		return ['data'=>['count'=>$count,'key'=>rand_string()]];
	}
	
	public static function getOutsourcingLossExport($query=[],$limit=10000){
		$limit				= $limit>10000?10000:$limit;
		$data				= self::getOutsourcingLoss($query,$limit)['data'];
		$return				= ['column'=>[],'setWidh'=>[],'keys'=>[],'list'=>$data,'image_fields'=>[]];
		$field 				= ['order_sn'=>'采购单编号','supplier_name'=>'供应商','process_name'=>'委外工序','order_date'=>'采购日期','follow_admin_username'=>'采购跟进人','order_material_sn'=>'物料编码','order_material_name'=>'物料名称','apply_num'=>'采购量','warehous_num'=>'总入库数量','order_material_unit'=>'单位','out_order_sn'=>'关联出库单号','out_material_sn'=>'出库物料编号','out_material_name'=>'出库物料名称','num'=>'用量','theory_num'=>'理论用量','stock_num'=>'出库数量','out_material_unit'=>'单位','loss_rate'=>'损耗率'];
		foreach($field as $key=>$vo){
			$return['column'][] 	= $vo;
			$return['setWidh'][] 	= 10;
			$return['keys'][] 		= $key;				
		}
        return $return;	
	}
}
