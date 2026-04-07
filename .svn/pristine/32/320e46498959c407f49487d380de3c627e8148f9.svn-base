<?php
declare (strict_types = 1);
namespace app\index\logic;
use app\index\logic\BaseLogic;
use app\common\model\{ErpOrderProduceRework};
use think\facade\Db;

class ErpOrderProduceReworkLogic extends BaseLogic{

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$user_info 	= request()->userInfo;
		$map	 	= [];
		if(!empty($query['produce_status'])) {
			$map[]	= ['a.produce_status', '=', $query['produce_status']];
        }
		if(!empty($query['region_type'])) {
			$map[]	= ['b.region_type', '=', $query['region_type']];
        }		
		if(!empty($query['customer_name'])) {
			$map[]	= ['b.customer_name', 'like', '%' . $query['customer_name'] . '%'];
        }
        if(!empty($query['order_sn'])) {
			$map[]	= ['b.order_sn', 'like', '%' . $query['order_sn'] . '%'];
        }
		if(!empty($query['delivery_time'])) {
			$time 		= is_array($query['delivery_time'])?$query['delivery_time']:explode('至',$query['delivery_time']);
			if(!empty($time[0])){
				$map[]	= ['b.delivery_time', '>=', strtotime(trim($time[0]))];
			}
			if(!empty($time[1])){
				$map[]	= ['b.delivery_time', '<', strtotime(trim($time[1]))+24*3600];
			}
        }
		if(!empty($query['create_time'])) {
			$time 		= is_array($query['create_time'])?$query['create_time']:explode('至',$query['create_time']);
			if(!empty($time[0])){
				$map[]	= ['b.create_time', '>=', strtotime(trim($time[0]))];
			}
			if(!empty($time[1])){
				$map[]	= ['b.create_time', '<', strtotime(trim($time[1]))+24*3600];
			}
        }	
		if(!empty($query['produce_date'])) {
			$time 		= is_array($query['produce_date'])?$query['produce_date']:explode('至',$query['produce_date']);
			if(!empty($time[0])){
				$map[]	= ['a.produce_date', '>=', (trim($time[0]))];
			}
			if(!empty($time[1])){
				$map[]	= ['a.produce_date', '<=', (trim($time[1]))];
			}
        }		
		$field 			= 'a.*,b.order_sn,b.delivery_time,b.address,b.salesman_id,b.order_remark,b.shipping_type,b.order_type,c.product_name,c.product_model,c.product_specs,c.is_pause,d.qc_file,d.produce_file';		
		$list 			= ErpOrderProduceRework::alias('a')
		->join('erp_order b','a.order_id = b.id','LEFT')
		->join('erp_order_product c','a.order_product_id = c.id','LEFT')
		->join('erp_product d','a.product_id = d.id','LEFT')
		->where($map)->field($field)->order('a.id','asc')->append(['order_type_desc'])->paginate($limit);

        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpOrderProduceRework::where($map)->find();
		}else{
			return ErpOrderProduceRework::find($map);
		}
    }

    public static function goAdd($data)
    {
        try {
			ErpOrderProduceRework::create($data);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

	
    public static function goEdit($data)
    {
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
        try{
			ErpOrderProduceRework::destroy($data['ids']);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
	
	
}
