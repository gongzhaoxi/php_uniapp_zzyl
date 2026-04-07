<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use think\facade\Db;
use app\common\model\ErpOrderProduce;
use app\common\model\ErpProcess;
use app\common\model\{ErpOrderProduceProcess,ErpOrderProduceError};
use app\common\enum\{ErpOrderProduceProcessEnum,ErpMaterialStockEnum};
use app\common\model\ErpUser;
use app\common\model\ErpOrder;
use app\common\model\ErpProduct;
use app\common\model\{ErpOrderProduct,ErpCustomer,ErpMaterialChange};


class ErpStatLogic extends BaseLogic{

	
	// 获取列表
    public static function capacity($date1='',$date2='')
    {
		$data				= [];
		$data['date1']		= $date1?$date1:date('Y-m-d', strtotime('-1 days'));
		$data['date2']		= $date2?$date2:date('Y-m-d', strtotime('-1 days'));
		$data['date']		= date('Y年m月d日');
		$data['num']		= ErpOrderProduce::where('produce_date',date('Y-m-d'))->count();
		$data['total']		= ErpOrderProduce::count();
		$data['process1']	= [];
		$data['process2']	= [];
		$data['x2']				= [];
		$time1				= strtotime(date('Y-m-d', strtotime('-7 days')));
		$time2				= strtotime(date('Y-m-d', strtotime('-30 days')));
		$process 			= ErpProcess::field('id,name,user_id')->where('status',1)->order(['sort'=>'asc','id'=>'asc'])->select()->toArray();
		$user_num 			= ErpUser::count();
		$user_num			= $user_num?$user_num:1;
		foreach($process as $vo){
			//$user_num 			= $vo['user_id']?count($vo['user_id']):1;
			$count1 			= ErpOrderProduceProcess::where('create_time','>=',$time1)->where('process_id',$vo['id'])->count();
			$count2 			= ErpOrderProduceProcess::where('create_time','>=',$time2)->where('process_id',$vo['id'])->count();
			$data['process1'][] = ['name'=>$vo['name'],'count'=>$count1,'average'=>round($count1/$user_num,1)];
			$data['process2'][] = ['name'=>$vo['name'],'count'=>$count2,'average'=>round($count2/$user_num,1)];
			$data['x2'][]		= $vo['name'];
		}
		
		$data['x1']				= [];
		$data['y1_data1']		= [];
		$data['y1_data2']		= [];
		for($i=0;$i<=6;$i++){
			$time 				= strtotime('-'.(6-$i).' days',strtotime($data['date1']));
			$data['x1'][]		= date('d日',$time);
			$data['y1_data1'][]	= ErpOrderProduce::where('produce_date',date('Y-m-d',$time))->count();
			$data['y1_data2'][]	= ErpOrderProduce::where('finish_date',date('Y-m-d',$time))->count();
		}
	
		$data['series2']		= [['name'=>$data['date2'],'data'=>[],'type'=>'bar'],['name'=>date('Y-m-d'),'data'=>[],'type'=>'bar']] ;
		foreach($data['series2'] as $k1=>$v1){
			foreach($process as $v2){
				$data['series2'][$k1]['data'][] = ErpOrderProduceProcess::where('create_time','>=',strtotime($v1['name']))->where('create_time','<',strtotime($v1['name'])+24*3600)->where('process_id',$v2['id'])->count();
			}
		}
		
		
		
		//dump($data);exit;
		
        return $data;
    }

    public static function errors($month1='',$month2='')
    {
		$data				= [];
		$data['month1']		= $month1;
		$data['month2']		= $month2?$month2:date('Y-m');
		$data['series1']	= [];
		$data['series1'][]	= ['name'=>'正常产品量','value'=>0];
		$data['series1'][]	= ['name'=>'异常产品量','value'=>0];
		$map1 				= [];
		if($data['month1']){
			$firstday 		= date('Y-m-01', strtotime($data['month1']));
			$lastday 		= date('Y-m-d', strtotime("$firstday +1 month -1 day"));
			$data['series1'][0]['value']	= ErpOrderProduce::where('error_time','=',0)->where('produce_date','>=',$firstday)->where('produce_date','<=',$lastday)->count();
			$data['series1'][1]['value']	= ErpOrderProduce::where('error_time','>',0)->where('produce_date','>=',$firstday)->where('produce_date','<=',$lastday)->count();
		}else{
			$data['series1'][0]['value']	= ErpOrderProduce::where('error_time','=',0)->count();
			$data['series1'][1]['value']	= ErpOrderProduce::where('error_time','>',0)->count();
		}
		
		$data['x2']				= [];
		$data['series2']		= [['name'=>'上报异常次数','data'=>[],'type'=>'line']] ;
		for($i=1;$i<=12;$i++){
			$time 							= strtotime('-'.(12-$i).' months',strtotime(date('Y-m-01')));
			$data['x2'][]					= date('y-m',$time);
			$data['series2'][0]['data'][] 	= ErpOrderProduceError::where('create_time','>=',date('Y-m-d H:i:s',$time))->where('create_time','<=',date('Y-m-d H:i:s',strtotime(date('Y-m-01',$time)." +1 month ")-1))->count();
		}
		
		$data['x3']				= [];
		$process 				= ErpProcess::field('id,name,user_id')->where('status',1)->order(['sort'=>'asc','id'=>'asc'])->select()->toArray();
		foreach($process as $vo){
			$data['x3'][]		= $vo['name'];
		}
		$data['series3']		= [['name'=>$data['month2'],'data'=>[],'type'=>'bar'],['name'=>date('Y-m', strtotime($data['month2']) - 1),'data'=>[],'type'=>'bar']] ;
		foreach($data['series3'] as $k1=>$v1){
			foreach($process as $v2){
				$data['series3'][$k1]['data'][] = ErpOrderProduceError::where('create_time','>=',date('Y-m-d H:i:s',strtotime($v1['name'])))->where('create_time','<',date('Y-m-d H:i:s',strtotime($v1['name'])+date('t',strtotime($v1['name']))*24*3600))->where('process_id',$v2['id'])->count();
			}
		}
		
		$data['list'] 			= ErpOrderProduceError::where('create_time','>=',date('Y-m-d',strtotime('-7 day')))->order('id asc')->select();

		return $data;
	}

	public static function sale($create_time1='',$create_time2='',$year='',$create_time4=''){
		$data					= [];
		$data['year']			= ($year?$year:date('Y'));
		$data['create_time1'] 	= $create_time1;
		$data['create_time2'] 	= $create_time2;
		$data['create_time4'] 	= $create_time4;
		$data['series1']		= [];
		$data['series1'][]		= ['name'=>'国内合同','value'=>0];
		$data['series1'][]		= ['name'=>'国外合同','value'=>0];
		$map1 					= [];
		if($data['create_time1']){
			$create_time 		= explode('至',$data['create_time1']);
			$map1[] 			= ['create_time', '>', strtotime(trim($create_time[0]))];
			$map1[] 			= ['create_time', '>', strtotime(trim($create_time[1]))+24*3600];	
		}
		$data['series1'][0]['value']	= ErpOrder::where($map1)->where('region_type',1)->count();
		$data['series1'][1]['value']	= ErpOrder::where($map1)->where('region_type',2)->count();
		
		$product 			= ErpProduct::field('id,name,model,specs')->select();
		$data['x2']			= [];
		$data['series2']	= [['data'=>[],'type'=>'bar']];
		$map2 					= [];
		if($data['create_time2']){
			$create_time 		= explode('至',$data['create_time2']);
			$map2[] 			= ['create_time', '>', strtotime(trim($create_time[0]))];
			$map2[] 			= ['create_time', '>', strtotime(trim($create_time[1]))+24*3600];	
		}
		foreach($product as $vo){
			$data['x2'][]					= $vo['model'].$vo['specs'];
			$data['series2'][0]['data'][] 	= ErpOrderProduct::where('product_id',$vo['id'])->where($map2)->sum('product_num');
		}
		
		$data['x3']				= [];
		$data['series3']		= [['name'=>$data['year'],'data'=>[],'type'=>'line'],['name'=>(string)($data['year']-1),'data'=>[],'type'=>'line']] ;
		for($i=1;$i<=12;$i++){
			$data['x3'][]		= $i.'月';
			foreach($data['series3'] as $k=>$v){
				$time 							= strtotime('-'.(12-$i).' months',strtotime(date($v['name'].'-m-01')));
				$data['series3'][$k]['data'][] 	= ErpOrder::where('create_time','>=',$time)->where('create_time','<=',strtotime(date('Y-m-01',$time)." +1 month ")-1)->sum('order_amount');
			}
		}
		
		$map4 					= [];
		if($data['create_time4']){
			$create_time 		= explode('至',$data['create_time4']);
			$map4[] 			= ['a.create_time', '>', strtotime(trim($create_time[0]))];
			$map4[] 			= ['a.create_time', '>', strtotime(trim($create_time[1]))+24*3600];	
		}
		$data['list1'] 			= ErpCustomer::alias('a')->join('erp_order b','a.id = b.customer_id','LEFT')->fieldRaw('a.id,a.name,sum(b.order_product_num) as num')->where($map4)->where('a.region_type',1)->group('a.id')->order('num desc')->limit(10)->select();
		$data['list2'] 			= ErpCustomer::alias('a')->join('erp_order b','a.id = b.customer_id','LEFT')->fieldRaw('a.id,a.name,sum(b.order_product_num) as num')->where($map4)->where('a.region_type',2)->group('a.id')->order('num desc')->limit(10)->select();
		return $data;
	}
	
	
	
	// 工位用量统计
    public static function getUseMaterial($query=[],$limit=10,$sort='',$order='')
    {
		$map	 	= [];
		$map[]		= ['d.type', '=',3];
		$map[]		= ['b.status', '=',1];
		$map[]		= ['c.type', 'in', [ErpMaterialStockEnum::TYPE_OUT_PULL,ErpMaterialStockEnum::TYPE_OUT_BACK_WAREHOUSE]];
		if(!empty($query['warehouse_id'])) {
			$map[]	= ['a.warehouse_id', '=', $query['warehouse_id']];
        }
		if(!empty($query['type'])) {
			$map[]	= ['c.type', '=', $query['type']];
        }	
        if(!empty($query['keyword'])) {
			$map[]	= ['b.sn|b.name', 'like', '%' . $query['keyword'] . '%'];
        }
		if(!empty($query['stock_date'])) {
			$time 		= is_array($query['stock_date'])?$query['stock_date']:explode('至',$query['stock_date']);
			if(!empty($time[0])){
				$map[]	= ['c.stock_date', '>=', trim($time[0])];
			}
			if(!empty($time[1])){
				$map[]	= ['c.stock_date', '<=', trim($time[1])];
			}
        }
		$orderby 		= 'a.id desc';	
		$field 			= 'a.*,b.sn,b.name,b.unit,c.material_type,c.data_type,c.type,d.name as warehouse_name,sum(ABS(stock_num)) as count';		
        $list 			= ErpMaterialChange::alias('a')
		->join('erp_material b','a.material_id = b.id','LEFT')
		->join('erp_material_stock c','a.material_stock_id = c.id','LEFT')
		->join('erp_warehouse d','a.warehouse_id = d.id','LEFT')->where($map)->field($field)
		->group('c.type,a.warehouse_id,a.material_id')
		->order($orderby)->append(['material_type_desc','type_desc'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }
	
	
	
	
	
	
	
	
	
	
}
