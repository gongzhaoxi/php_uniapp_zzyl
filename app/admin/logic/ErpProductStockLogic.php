<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\{ErpProductStock,ErpOrderProduce,ErpOrder,ErpOrderProduceFollow};
use app\admin\validate\ErpProductStockValidate;
use app\common\enum\{ErpProductStockEnum,ErpOrderProduceEnum,ErpOrderEnum};

class ErpProductStockLogic extends BaseLogic{

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$query['_alias'] 			= 'a';
		$query['_produce_alias'] 	= 'b';
		$query['_order_alias'] 		= 'c';
		$query['_product_alias'] 	= 'd';
		if(!empty($query['follow'])){
			$ids 						= ErpOrderProduceFollow::where('num|after_num','=',$query['follow'])->group('order_produce_id')->column('order_produce_id');
			$query['order_produce_ids'] = $ids?$ids:[0];
		}
		
		$field = 'a.*,b.produce_sn,b.produce_finish_sn,b.queue_num,c.order_sn as sale_order_sn,c.customer_name,c.region_type,c.address,c.contacts,c.salesman_id,c.shipping_type,c.order_remark,d.product_name,d.product_model,d.product_specs,d.product_num,d.product_unit,d.add_project,d.change_project,d.color';
        $list = ErpProductStock::alias('a')
		->join('erp_order_produce b','a.order_produce_id = b.id','LEFT')
		->join('erp_order c','a.order_id = c.id','LEFT')
		->join('erp_order_product d','a.order_product_id = d.id','LEFT')
		->withSearch(['query'],['query'=>$query])->with(['salesman'])
		->field($field)->order('a.id','desc')->append(['status_desc','type_desc','project_html'])->paginate($limit);
		
		$data = $list->items();
		foreach($data as &$vo){
			$vo['product_num'] 	= $vo['queue_num'].'/'.$vo['product_num'];
			$vo['salesman'] 	= empty($vo['salesman'])?'':$vo['salesman']['username'];
		}
		
        return ['code'=>0,'data'=>$data,'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }


    public static function goConfirm($data)
    {
		//验证
        $validate 	= new ErpProductStockValidate;
        if(!$validate->scene('confirm')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}	
        try{
			$models 				= ErpProductStock::where('id','in',$data['ids'])->where('status',ErpProductStockEnum::STATUS_NO)->select();
			$produce_ids 			= [];
			$order_ids 				= [];
			foreach($models as $vo){
				if($vo['type'] == ErpProductStockEnum::TYPE_CANCEL_PRODUCE || $vo['type'] == ErpProductStockEnum::TYPE_PRODUCE){
					$produce_ids[] 	= $vo['order_produce_id'];
					$order_ids[] 	= $vo['order_id'];
				}
			}
			if($produce_ids){
				ErpOrderProduce::where('id','in',$produce_ids)->update(['produce_status'=>ErpOrderProduceEnum::PRODUCE_STATUS_FINISH]);
			}
			if($order_ids){
				$order_ids				= array_unique($order_ids);
				$update_order 			= [];
				foreach($order_ids as $order_id){
					if(ErpOrderProduce::where('order_id',$order_id)->where('produce_status','<>',ErpOrderProduceEnum::PRODUCE_STATUS_FINISH)->count() == 0){
						$update_order[]	= $order_id;
					}
				}
				if($update_order){
					ErpOrder::where('id','in',$update_order)->update(['order_status'=>ErpOrderEnum::ORDER_STATUS_WAIT_SHIPPING,'produce_status'=>ErpOrderEnum::PRODUCE_STATUS_FINISH]);
				}
			}
			ErpProductStock::where('id','in',$data['ids'])->where('status',ErpProductStockEnum::STATUS_NO)->update(['order_sn'=>'CR'.date('Ymd'),'status'=>ErpProductStockEnum::STATUS_YES]);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }


    public static function goReturned($param)
    {
		//验证
        $validate 	= new ErpProductStockValidate;
        if(!$validate->scene('confirm')->check($param)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}	
        try{
			$models 				= ErpProductStock::where('id','in',$param['ids'])->where('is_out_warehouse',1)->where('is_returned',0)->select();
			$ids 					= [];
			$data 					= [];
			$count 					= ErpProductStock::whereDay('create_time')->group('order_sn')->count() + 1;
			$order_sn				= 'TH'.date('Ymd').sprintf("%03d",$count);
			
			foreach($models as $vo){
				$data[] 			= ['type'=>ErpProductStockEnum::TYPE_RETURN,'order_sn'=>$order_sn,'stock_date'=>$param['stock_date'],'remark'=>$param['remark'],'from_product_stock_id'=>$vo['id'],'product_id'=>$vo['product_id'],'order_id'=>$vo['order_id'],'order_product_id'=>$vo['order_product_id'],'order_produce_id'=>$vo['order_produce_id'],'supplier_id'=>$vo['supplier_id'],'warehouse_id'=>$vo['warehouse_id'],'purchase_date'=>$vo['purchase_date'],'username'=>$vo['username']];
				$ids[] 				= $vo['id'];
			}
			ErpProductStock::where('id','in',$ids)->update(['is_returned'=>1]);
			(new ErpProductStock)->saveAll($data);
			
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
    
    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpProductStock::where($map)->find();
		}else{
			return ErpProductStock::find($map);
		}
    }
	
    // 编辑
    public static function goEdit($data)
    {
        //验证
        $validate 	= new ErpProductStockValidate;
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

	public static function getExportCount($query=[]){
        $count 	= $query['_alias'] 	= 'a';
		$query['_produce_alias'] 	= 'b';
		$query['_order_alias'] 		= 'c';
		$query['_product_alias'] 	= 'd';
		if(!empty($query['follow'])){
			$ids 						= ErpOrderProduceFollow::where('num|after_num','=',$query['follow'])->group('order_produce_id')->column('order_produce_id');
			$query['order_produce_ids'] = $ids?$ids:[0];
		}

		$list = ErpProductStock::alias('a')
		->join('erp_order_produce b','a.order_produce_id = b.id','LEFT')
		->join('erp_order c','a.order_id = c.id','LEFT')
		->join('erp_order_product d','a.order_product_id = d.id','LEFT')
		->withSearch(['query'],['query'=>$query])
		->field('a.id')->count();
		return ['data'=>['count'=>$count,'key'=>rand_string()]];
	}
	
	public static function getExport($query=[],$limit=10000){
		$limit				= $limit>10000?10000:$limit;
		$data				= self::getList($query,$limit)['data'];
		$return				= ['column'=>[],'setWidh'=>[],'keys'=>[],'list'=>$data,'image_fields'=>[]];
		$field 				= ['region_type'=>'订单性质','stock_date'=>'完工/入库日期','username'=>'完工/入库人','produce_sn'=>'成品编码','product_model'=>'型号','product_specs'=>'款式','product_num'=>'数量','product_unit'=>'单位','sale_order_sn'=>'销售合同号','address'=>'收货地址','contacts'=>'联系人','salesman'=>'业务员','shipping_type'=>'发货类型','order_remark'=>'备注','type_desc'=>'数据来源','order_sn'=>'入库单号','status_desc'=>'状态'];
		foreach($field as $key=>$vo){
			$return['column'][] 	= $vo;
			$return['setWidh'][] 	= 10;
			$return['keys'][] 		= $key;				
		}
        return $return;	
	}

}
