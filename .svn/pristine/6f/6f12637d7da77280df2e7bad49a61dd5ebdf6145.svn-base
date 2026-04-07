<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\{ErpOrderAccessory,ErpOrderLog};
use app\admin\validate\ErpOrderAccessoryValidate;
use app\common\enum\ErpOrderLogEnum;

class ErpOrderAccessoryLogic extends BaseLogic{

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$field = '*';
        $list = ErpOrderAccessory::withSearch(['query'],['query'=>$query])->field($field)->order('id','desc')->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

    // 添加
    public static function goAdd($data,$order_id)
    {
        //验证
        try {
            $res = (new ErpOrderAccessory)->saveAll($data);
			ErpOrderLogic::updateAmount($order_id);
			foreach($res as $model){
				$log	= ['log'=>'添加配件：'.$model['product_sn'].'  '.$model['product_name'],'data_type'=>ErpOrderLogEnum::ORDER_ACCESSORY_ADD,'data_id'=>$model['id'],'order_id'=>$model['order_id'],'operator'=>self::$adminUser['username']];
				ErpOrderLog::create($log);	
			}
			
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
    
    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpOrderAccessory::where($map)->find();
		}else{
			return ErpOrderAccessory::find($map);
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
			
			$log 			= [];
			$filed_check   	= ['product_num'=>'数量','product_model'=>'型号','product_name'=>'产品名称','shipping_time'=>'发运时间','remark'=>'备注','order_product_id'=>'递属产品','product_price'=>'单价','total_price'=>'总价'];
			foreach($filed_check as $k=>$vo){
				if($data[$k] != $model[$k]){
					$log[]	= $vo.'从`'.$model[$k].'`到`'.$data[$k].'`';
				}
			}
            $model->save($data);
			if($log){
				ErpOrderLog::create(['log'=>'配件`'.$data['product_name'].'`的'.implode('，',$log).'','data_type'=>ErpOrderLogEnum::ORDER_ACCESSORY_FILED_CHANGE,'order_id'=>$model['order_id'],'operator'=>self::$adminUser['username']]);
			}
			
            $model->save($data);
			ErpOrderLogic::updateAmount($model->order_id);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 删除
    public static function goRemove($id)
    {
        try{
			$model 		= self::getOne($id);
			if($model->isEmpty()) {
				return ['msg'=>'数据不存在','code'=>201];
			}
			
			$log	= ['log'=>'删除配件：'.$model['product_name'].'  '.$model['product_model'],'data_type'=>ErpOrderLogEnum::ORDER_ACCESSORY_DELETE,'data_id'=>$model['id'],'order_id'=>$model['order_id'],'operator'=>self::$adminUser['username']];
			ErpOrderLog::create($log);
			
			$model->delete();
			ErpOrderLogic::updateAmount($model->order_id);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
}
