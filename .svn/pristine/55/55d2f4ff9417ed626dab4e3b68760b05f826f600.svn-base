<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\ErpOrderProduceError;

class ErpOrderProduceErrorLogic extends BaseLogic{


	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$map	 			= [];
		if(!empty($query['type'])) {
			$map[]			= ['a.type', '=', $query['type']];
        }			
		if(isset($query['status']) && $query['status'] !== '') {
			$map[]			= ['a.status', '=', $query['status']];
        }
		if(!empty($query['create_time'])) {
			$time 			= is_array($query['create_time'])?$query['create_time']:explode('至',$query['create_time']);
			if(!empty($time[0])){
				$map[]		= ['a.create_time', '>=', (trim($time[0]))];
			}
			if(!empty($time[1])){
				$map[]		= ['a.create_time', '<=', (trim($time[1]))];
			}
        }
		$field 				= 'a.*,b.produce_sn,c.order_sn';	
		$list 				= ErpOrderProduceError::alias('a')->join('erp_order_produce b','a.order_produce_id = b.id','LEFT')->join('erp_order c','b.order_id = c.id','LEFT')->field($field)->where($map)->order('a.id','desc')->paginate($limit);
		return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

    // 确认已处理
    public static function goConfirm($id)
    {
        try{
			ErpOrderProduceError::where('id',$id)->where('status',0)->update(['status'=>1,'confirm_date'=>date('Y-m-d')]);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
	
	public static function getErrorCount(){
		return ErpOrderProduceError::where('status',0)->count();
	}	

	public static function getExport($query=[],$limit=10){
		$limit					= $limit>10000?10000:$limit;
		$return					= ['column'=>[],'setWidh'=>[],'keys'=>[],'list'=>[]];
		$return['image_fields'] = ['photo'];
		$return['column'] 		= ['上报日期','类型','联系人(上报人)','产品编码','销售合同号','状态','内容','图'];
		$return['keys'] 		= ['create_time','type','username','produce_sn','order_sn','status','content','photo'];
		$return['setWidh']		= ['20','20','20','20','20','20','30','30'];
		
        $data 					= self::getList($query,$limit)['data'];
		$list					= [];
		foreach($data as $vo){
			$vo					= $vo->toArray();
			if($vo['type'] == '缺料中'){
				$vo['content']	= '物料编码：'.$vo['material_sn'].'|物料名称：'.$vo['material_name'].'|报缺数量：'.$vo['lack_num'].'
报缺原因：'.$vo['error'];
			}else{
				$vo['content']	= '检验类型：'.$vo['check_type'].'
不良说明：'.$vo['error'];
			}
			$vo['photo']		= $vo['photo']?$vo['photo'][0]:'';
			$vo['status']		= $vo['status']?'已处理':'未处理';
			$list[] 			= $vo;
		}
		$return['list']			= $list;
        return $return;	
	}


}
