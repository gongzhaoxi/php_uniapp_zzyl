<?php
declare (strict_types = 1);
namespace app\index\logic;
use think\facade\Db;
use app\index\validate\ErpOrderShippingValidate;
use app\common\model\{ErpOrderShipping,ErpOrderProduce};
use app\common\service\ErpOrderShippingService;
use app\common\enum\{ErpOrderShippingEnum,ErpOrderEnum,RegionTypeEnum};


class ErpOrderShippingLogic{

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$map	 			= [];
		$map[]				= ['c.shipping_status', '<>', ErpOrderShippingEnum::SHIPPING_STATUS_FINISH];
		$map[]				= ['b.data_type', '=', ErpOrderEnum::DATA_TYPE_1];
		$map[]				= ['a.order_shipping_id', '>', 0];
		$map[]				= ['c.shipping_sn', '<>', ''];
		if(!empty($query['customer_name'])) {
			$map[]			= ['b.customer_name', 'like', '%' . $query['customer_name'] . '%'];
        }
		if(!empty($query['order_sn'])) {
			$map[]			= ['b.order_sn', 'like', '%' . $query['order_sn'] . '%'];
        }
		if(!empty($query['produce_sn'])) {
			$map[]			= ['a.produce_sn', 'like', '%' . $query['produce_sn'] . '%'];
        }
		if(!empty($query['shipping_sn'])) {
			$map[]			= ['c.shipping_sn', 'like', '%' . $query['shipping_sn'] . '%'];
        }		
		if(isset($query['region_type']) && $query['region_type'] !== '') {
			$map[]			= ['b.region_type', '=', $query['region_type']];
        }
		$field 				= 'a.id,a.order_product_id,a.produce_sn,a.order_shipping_id,a.queue_num,b.order_sn,b.region_type,b.customer_name,b.address,b.contacts,c.shipping_sn';	
		$list 				= ErpOrderProduce::alias('a')
		->join('erp_order b','a.order_id = b.id','LEFT')
		->join('erp_order_shipping c','a.order_shipping_id = c.id','left')
		->with(['order_product.bom'])->field($field)->where($map)->append(['order_product.project_html'])->order('a.id','desc')->paginate($limit);
		$data 				= $list->items();
		foreach($data as $k=>$vo){
			$data[$k]['region_type_desc']	= RegionTypeEnum::getDesc($vo['region_type']);
		}
		return ['code'=>0,'data'=>$data,'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }


	public static function getProduce($shipping_sn,$user_id){
		$field		= 'a.*';
		$data 		= ErpOrderShipping::alias('a')
		->with(['order_produce','order'])
		->where('a.shipping_sn',$shipping_sn)->select()->toArray();
	
		$return		= [];
		foreach($data as $vo){
			if(empty($return[$vo['order_id']])){
				$return[$vo['order_id']] 			= $vo;
				$return[$vo['order_id']]['list'] 	= [];
			}
			foreach($vo['order_produce'] as $v){
				$return[$vo['order_id']]['list'][] 	= $v;
			}
		}
		return array_values($return);
	}

    // 添加
    public static function goConfirm($data,$user_id)
    {
        //验证
        $validate	= new ErpOrderShippingValidate;
        if(!$validate->scene('confirm')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		$model 		= ErpOrderShipping::where('shipping_sn','=',$data['shipping_sn'])->select();
		if($model->isEmpty()){
			return ['msg'=>'产品出仓（发货）单号不存在','code'=>201];
		}
        return ErpOrderShippingService::goConfirm($data,$model);
    }
	

}
