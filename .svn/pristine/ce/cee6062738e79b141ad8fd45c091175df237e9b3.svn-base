<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\{ErpProductStat,ErpProductStock,ErpProduct,ErpOrderProduce};
use app\admin\validate\ErpProductStatValidate;

class ErpProductStatLogic extends BaseLogic{

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		
		$where 			= [];
		if(!empty($query['month'])){
			$where[] 	= ['a.month','=',$query['month']];
		}
		if(!empty($query['region_type']) && $query['region_type'] == 2){
			$field 		= 'a.id,a.product_id,a.month,a.num_foreign as num,a.last_num_foreign as last_num,a.warehouse_num_foreign as warehouse_num,a.sale_num_foreign as sale_num,b.sn';
		}else{
			$field 		= 'a.id,a.product_id,a.month,a.num,a.last_num,a.warehouse_num,a.sale_num,b.sn';
		}
        $list 			= ErpProductStat::alias('a')
		->join('erp_product b','a.product_id = b.id','LEFT')->field($field)->where($where)->order('a.id','desc')->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

    // 添加
    public static function goAdd()
    {
		$month		= date('Y-m');
		$add 		= [];
		$update 	= [];
		$product 	= ErpProduct::field('id,name')->select();
		$last_stat 	= ErpProductStat::where('month',date('Y-m',strtotime('-1 month')))->column('num,num_foreign','product_id');
		$now_stat 	= ErpProductStat::where('month',$month)->column('id','product_id');
	   
		foreach($product as $vo){
			$last_num			= empty($last_stat[$vo['product_id']])?0:$last_stat[$vo['product_id']]['num'];
			$last_num_foreign	= empty($last_stat[$vo['product_id']])?0:$last_stat[$vo['product_id']]['num_foreign'];
			$tmp				= [
				'product_id'		=> $vo['id'],
				'month'				=> $month,
				'last_num'			=> $last_num,
				'num'				=> ErpProductStock::alias('a')->join('erp_order b','a.order_id = b.id','LEFT')->where('a.product_id',$vo['id'])->where('b.region_type',1)->where('a.is_out_warehouse',0)->count(),
				'warehouse_num'		=> ErpProductStock::alias('a')->join('erp_order b','a.order_id = b.id','LEFT')->where('a.product_id',$vo['id'])->where('b.region_type',1)->where('a.stock_date','>=',$month.'-01')->where('a.stock_date','<=',$month.'-'.date('t'))->count(),
				'sale_num'			=> ErpOrderProduce::alias('a')->join('erp_order b','a.order_id = b.id','LEFT')->where('a.product_id',$vo['id'])->where('b.region_type',1)->where('a.produce_date','>=',$month.'-01')->where('a.produce_date','<=',$month.'-'.date('t'))->count(),
				'last_num_foreign'	=> $last_num_foreign,
				'num_foreign'			=> ErpProductStock::alias('a')->join('erp_order b','a.order_id = b.id','LEFT')->where('a.product_id',$vo['id'])->where('b.region_type',2)->where('a.is_out_warehouse',0)->count(),
				'warehouse_num_foreign'	=> ErpProductStock::alias('a')->join('erp_order b','a.order_id = b.id','LEFT')->where('a.product_id',$vo['id'])->where('b.region_type',2)->where('a.stock_date','>=',$month.'-01')->where('a.stock_date','<=',$month.'-'.date('t'))->count(),
				'sale_num_foreign'		=> ErpOrderProduce::alias('a')->join('erp_order b','a.order_id = b.id','LEFT')->where('a.product_id',$vo['id'])->where('b.region_type',2)->where('a.produce_date','>=',$month.'-01')->where('a.produce_date','<=',$month.'-'.date('t'))->count(),
			
			];
			if(empty($now_stat[$vo['id']])){
				$add[] 		= $tmp;
			}else{
				$tmp['id']	= $now_stat[$vo['id']];
				$update[] 	= $tmp;
			}
			
		}
		if($add){
			(new ErpProductStat)->saveAll($add);
		}
		if($update){
			(new ErpProductStat)->saveAll($update);
		}
    }
    
   

}
