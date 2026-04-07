<?php
declare (strict_types = 1);
namespace app\index\logic;
use think\facade\Db;
use app\common\model\{ErpOrderProduce,ErpOrderProduceProcess,ErpOrderProduceError,ErpProduct};
use app\common\enum\{ErpOrderProduceEnum,ErpPurchaseApplyEnum,ErpProductStockEnum};


use app\common\model\{ErpProcess,ErpMaterialEnterMaterialReport,ErpProductStock};

class BoardLogic{

	public static function getList($query=[]){
		$map	 		= [];
		$map[]			= ['a.finish_date', '=', ''];
		$map[]			= ['a.produce_date', '<>', ''];
		$field 			= 'a.id,a.product_id,a.pass_down_time,a.create_time,a.produce_date,a.produce_sn,b.order_sn,b.address,c.product_model,c.product_specs';		
		if(isset($query['process_id']) && $query['process_id'] !== ''){
			$map[]		= ['a.process_id', '=', $query['process_id']];
		}
		$list 			= ErpOrderProduce::alias('a')->join('erp_order b','a.order_id = b.id','LEFT')->join('erp_order_product c','a.order_product_id = c.id','LEFT')->where($map)->field($field)->order(['a.create_time'=>'asc','b.order_sn'=>'asc','a.product_id'=>'asc','a.produce_sn'=>'asc'])->select();
		$data 			= [];
		$keys 			= [];
		$tmp	 		= ErpOrderProduceProcess::field('id,create_time,process_id,order_produce_id')->where('process_id','in','1,2,3')->where('order_produce_id','in',$list->column('id'))->order('id asc')->select();
		$process		= [];
		foreach($tmp as $vo){
			$process[$vo['order_produce_id']][$vo['process_id']] = $vo->toArray();
		}
		$tmp	 		= ErpOrderProduceError::field('id,check_type,process_id,order_produce_id')->where('status',0)->where('type','检验不良')->where('check_type','<>','')->where('order_produce_id','in',$list->column('id'))->order('id asc')->select();
		$error			= [];
		foreach($tmp as $vo){
			$error[$vo['order_produce_id']][$vo['process_id']] = $vo->toArray();
		}
		
		foreach($list as $k=>$vo){
			if(!empty($process[$vo['id']][3])){
				continue;
			}
			$data[$k]					= $vo->toArray();
			$data[$k]['create_time']	= substr($vo['create_time'],0,10);
			$data[$k]['numbers']		= $k+1;
			$key 						= $data[$k]['create_time'].$data[$k]['order_sn'].$data[$k]['product_id'];
			if(in_array($key,$keys)){
				$data[$k]['address']		= '';
				$data[$k]['create_time']	= '';
				$data[$k]['order_sn']		= '';
				$data[$k]['product_model']	= '';
				$data[$k]['product_specs']	= '';
			}else{
				$keys[] 					= $key;
			}
			if(!empty($process[$vo['id']][1])){
				$data[$k]['process1']		= '→';
			}else if($vo['pass_down_time']){
				$data[$k]['process1']		= date('H:i:s',$vo['pass_down_time']);
			}else{
				$data[$k]['process1']		= '';
			}
			
			if(!empty($process[$vo['id']][2])){
				$data[$k]['process2']		= '→';
			}else if(!empty($process[$vo['id']][1])){
				$data[$k]['process2']		= date('H:i:s',strtotime($process[$vo['id']][1]['create_time']));
			}else{
				$data[$k]['process2']		= '';
			}
			
			if(!empty($process[$vo['id']][2])){
				$data[$k]['process3']		= date('H:i:s',strtotime($process[$vo['id']][2]['create_time']));
			}else if(!empty($error[$vo['id']][2])){
				$data[$k]['process3']		= $error[$vo['id']][2]['check_type'];
			}else{
				$data[$k]['process3']		= '';
			}
		}
		return ['code'=>0,'data'=>$data,'extend'=>['count' => $list->count(), 'limit' => 10000]];
    }
	
	
	public static function getBoardMonthStat(){
		$month 		= date('Y-m');
		$data 		= [];
		for($i=1;$i<=7;$i++){
			$count	= ErpOrderProduce::where('finish_date','>=',$month.'-01')->where('finish_date','<=',$month.'-'.date('t'))->count();
			$days 	= $i == 1?date('j'):date('t');
			$data[]	= ['month'=>$month,'count'=>round($count/$days,1)];
			$month	= date('Y-m',strtotime('-'.$i.' month'));
		}
		return array_reverse($data);
	}
	
	public static function getBoardPass(){
		$map 	= [];
		$map[]	= ['a.process_id','=',3];
		$map[]	= ['b.process_id','<>',5];
		$field 	= 'b.produce_sn,c.address';
		return ErpOrderProduceProcess::alias('a')
		->join('erp_order_produce b','a.order_produce_id = b.id','LEFT')
		->join('erp_order c','b.order_id = c.id','LEFT')->where($map)->whereDay('a.create_time')
		->order(['b.create_time'=>'asc','c.order_sn'=>'asc','b.product_id'=>'asc','b.produce_sn'=>'asc'])->select();
	}
	
	
	public static function getBoardError1(){
		return ErpOrderProduceError::field('id,error,username,material_sn,material_name,lack_num,create_time,order_produce_id')->where('status',0)->where('type','缺料中')->order('id asc')->select();
	}
	
	public static function getBoardError2(){
		$list 	= ErpOrderProduceError::alias('a')->join('erp_order_produce b','a.order_produce_id = b.id','LEFT')->field('a.id,a.error,a.username,a.check_type,a.create_time,a.order_produce_id,b.produce_sn')->where('status',0)->where('type','检验不良')->order('id asc')->select();
		$data 	= [];
		foreach($list as $vo){
			if(empty($data[$vo['order_produce_id']])){
				$data[$vo['order_produce_id']] 				= $vo->toArray();
				$data[$vo['order_produce_id']]['error']		= [$vo['error']];
			}else{
				$data[$vo['order_produce_id']]['error'][]	= $vo['error'];
			}
		}
		return $data;
	}
		
	public static function getBoardCount(){
		$data 	= [];
		$data[]	= ErpOrderProduceProcess::where('process_id',1)->whereDay('create_time')->count();
		$data[]	= ErpOrderProduceProcess::where('process_id',2)->whereDay('create_time')->count();
		$data[]	= ErpOrderProduceProcess::where('process_id',3)->whereDay('create_time')->count();
		$data[]	= ErpOrderProduceProcess::where('process_id',5)->whereDay('create_time')->count();
		$data[]	= ErpOrderProduce::where('produce_date',date('Y-m-d'))->count();
		$data[]	= ErpOrderProduce::where('produce_status','=',10)->count();
		$data[]	= ErpOrderProduce::alias('a')
							->join('erp_order_produce_process b','a.id = b.order_produce_id and b.process_id = 2 ','LEFT')
							->where('a.produce_date','<>','')
							->where('a.finish_date','')
							->whereNull('b.id')
							->field('a.id')
							->count();
		return $data;
	}
	
	public static function getBoardWait(){
		$tmp  	= ErpOrderProduce::field('id,product_id')->with(['product'=>function($query){return $query->field('id,model,specs');}])->where('produce_status','=',10)->select();
		$data 	= [];
		foreach($tmp as $vo){
			if(empty($data[$vo['product_id']])){
				$data[$vo['product_id']] 			= ['model'=>$vo['product']['model'],'specs'=>$vo['product']['specs'],'count'=>1];
			}else{
				$data[$vo['product_id']]['count'] 	= $data[$vo['product_id']]['count'] + 1;
			}
		}
		array_multisort(array_column($data,'count'),SORT_DESC,$data);
		return $data;
	}
	
	public static function getProduct($act){
		$limit 		= 20;
		$map 		= [];
		if($act == 1){
			$map[] 	= ['a.create_time','>=',strtotime(date('Y-m-d'))];
		}else if($act == 2){
			$map[] 	= ['a.create_time','>=',strtotime(date('Y-m-01'))];
			$map[] 	= ['a.create_time','<=',strtotime(date('Y-m-').date('t').' 23:59:59')];
		}else if($act == 3){
			$map[] 	= ['a.create_time','>=',strtotime(date('Y-01-01'))];
			$map[] 	= ['a.create_time','<=',strtotime(date('Y-12-31 23:59:59'))];
		}
		$list 		= ErpOrderProduce::alias('a')->join('erp_product b','a.product_id = b.id','LEFT')->field('a.product_id,count(a.product_id) as num,b.specs,b.model')->where($map)->group('a.product_id')->order('num desc')->limit($limit)->select()->toArray();
		$count 		= $limit - count($list);
		if($count > 0){
			$tmp 		= ErpProduct::field('model,specs')->where('id','not in',array_column($list,'id'))->order('id asc')->limit($count)->select();
			foreach($tmp as $vo){
				$list[]	= ['num'=>0,'specs'=>$vo['specs'],'model'=>$vo['model']];
			}
		}
		$series 	= [];
		$other 		= 0;
		foreach($list as $k=>$vo){
			if($k <= 4){
				$series[] 	= ['name'=>$vo['model'].$vo['specs'],'value'=>$vo['num']];
			}else{
				$other 		= $other + $vo['num'];
			}
		}
		if($other){
			$series[] 		= ['name'=>'其它型号','value'=>$other];
		}
		return ['list'=>$list,'series'=>$series];
	}
	
	public static function getSummaryCount($act){
		$count 		= [];
		$map 		= [];
		$map2 		= [];
		$xAxis 		= [];
		$series 	= [['name'=>'接单量','type'=>'line','stack'=>'Total','data'=>[]],['name'=>'出货量','type'=>'line','stack'=> 'Total','data'=>[]]];
		if($act == 1){
			$today 					= date('Y-m-d');
			$map[] 					= ['a.shipping_time','>=',strtotime($today)];
			$map2[] 				= ['c.out_warehouse_time','=',$today];
			$xAxis					= [$today];
			$series[0]['data'][] 	= ErpOrderProduce::whereDay('create_time')->count();
			$series[1]['data'][] 	= ErpOrderProduce::alias('a')->join('erp_order_shipping b','a.order_shipping_id = b.id','LEFT')->where('b.out_warehouse_time',$today)->count();
		}else if($act == 2){
			$map[] 						= ['a.shipping_time','>=',strtotime(date('Y-m-01'))];
			$map[] 						= ['a.shipping_time','<=',strtotime(date('Y-m-').date('t').' 23:59:59')];
			$map2[] 					= ['c.out_warehouse_time','>=',(date('Y-m-01'))];
			$map2[] 					= ['c.out_warehouse_time','<=',(date('Y-m-').date('t'))];
			for($i=1;$i<=date('t');$i++){
				$xAxis[] 				= $i;
				$date 					= date('Y-m-').($i<10?('0'.$i):$i);
				$series[0]['data'][] 	= ErpOrderProduce::whereDay('create_time', $date)->count();
				$series[1]['data'][] 	= ErpOrderProduce::alias('a')->join('erp_order_shipping b','a.order_shipping_id = b.id','LEFT')->where('b.out_warehouse_time',$date)->count();
			}
		}else if($act == 3){
			$map[] 						= ['a.shipping_time','>=',strtotime(date('Y-01-01'))];
			$map[] 						= ['a.shipping_time','<=',strtotime(date('Y-12-31 23:59:59'))];
			$map2[] 					= ['c.out_warehouse_time','>=',(date('Y-01-01'))];
			$map2[] 					= ['c.out_warehouse_time','<=',(date('Y-12-31'))];
			for($i=1;$i<=12;$i++){
				$xAxis[] 				= $i;
				$month 					= date('Y-').($i<10?('0'.$i):$i);
				$series[0]['data'][] 	= ErpOrderProduce::whereMonth('create_time',$month)->count();
				$series[1]['data'][] 	= ErpOrderProduce::alias('a')->join('erp_order_shipping b','a.order_shipping_id = b.id','LEFT')->where('b.out_warehouse_time','>=',$month.'-01')->where('b.out_warehouse_time','<=',$month.'-31')->count();
			}
		}
		$cid 				= '42,43,44';
		$order_amount 		= ErpOrderProduce::alias('a')->join('erp_order_product b','a.order_product_id = b.id','LEFT')->join('erp_order_shipping c','a.order_shipping_id = c.id','LEFT')->join('erp_product d','a.product_id = d.id','LEFT')->where('d.cid','in',$cid)->where($map2)->where('c.shipping_status','=',30)->sum('b.product_price');
		$order_amount 		= $order_amount + ErpOrderProduce::alias('a')->join('erp_order_accessory b','a.order_product_id = b.order_product_id','LEFT')->join('erp_order_shipping c','a.order_shipping_id = c.id','LEFT')->join('erp_product d','a.product_id = d.id','LEFT')->where('d.cid','in',$cid)->where('c.shipping_status','=',30)->where($map2)->sum('b.product_price');
		$order_amount 		= $order_amount + Db::table('erp_order')->alias('a')->join('erp_order_accessory b','a.id = b.order_id','LEFT')->where($map)->where('b.order_product_id','=',0)->where('a.shipping_status','=',30)->sum('total_price');
		
		if($order_amount>10000){
			$order_amount 	= sprintf('%.2f', $order_amount/10000);
			$count[] 		= num_format($order_amount).'万';
		}else{
			$count[] 		= $order_amount;
		}
		$product_count 		= ErpOrderProduce::alias('a')->join('erp_order_shipping c','a.order_shipping_id = c.id','LEFT')->join('erp_product d','a.product_id = d.id','LEFT')->where('d.cid','in',$cid)->where($map2)->where('c.shipping_status','=',30)->count();
		if($product_count>10000){
			$product_count 	= sprintf('%.2f', $product_count/10000);
			$count[] 		= num_format($product_count).'万';
		}else{
			$count[] 		= $product_count;
		}
		return ['count'=>$count,'xAxis'=>$xAxis,'series'=>$series];
	}
	

	public static function getShipping($act){
		$limit 		= 40;
		$map 		= [];
		$map[] 		= ['c.shipping_status','=',30];
		if($act == 1){
			$map[] 	= ['c.out_warehouse_time','=',date('Y-m-d')];
		}else if($act == 2){
			$map[] 	= ['c.out_warehouse_time','>=',(date('Y-m-01'))];
			$map[] 	= ['c.out_warehouse_time','<=',(date('Y-m-').date('t'))];
		}else if($act == 3){
			$map[] 	= ['c.out_warehouse_time','>=',(date('Y-01-01'))];
			$map[] 	= ['c.out_warehouse_time','<=',(date('Y-12-31'))];
		}
		$list 		= ErpOrderProduce::alias('a')
							->join('erp_product b','a.product_id = b.id','LEFT')
							->join('erp_order_shipping c','a.order_shipping_id = c.id','LEFT')
							->field('a.product_id,count(a.product_id) as num,b.specs,b.model')
							->where($map)->group('a.product_id')
							->order('num desc')->limit($limit)->select()->toArray();
		$count 		= $limit - count($list);
		if($count > 0){
			$tmp 		= ErpProduct::field('id,model,specs')
							->where('id','not in',array_column($list,'product_id'))
							->order('id asc')->limit($count)->select();
			foreach($tmp as $vo){
				$list[]	= ['product_id'=>$vo['id'],'num'=>0,'specs'=>$vo['specs'],'model'=>$vo['model']];
			}
		}
		return $list ;
	}
	
	
	public static function getIndex(){
		$data = [];
		$data['title'] = '置安数字化车间管理平台';
		$data['wait_order_produce_count'] = ErpOrderProduce::where('produce_status',ErpOrderProduceEnum::PRODUCE_STATUS_NO)->count();
		$data['wait_order_produce_top10'] = ErpOrderProduce::alias('a')->join('erp_order_product d','a.order_product_id = d.id','LEFT')
		->where('produce_status',ErpOrderProduceEnum::PRODUCE_STATUS_NO)
		->fieldRaw('d.product_model,d.product_specs,count(a.id) as count')->group('a.product_id')
		->orderRaw('count desc')->limit(10)->select();
		
		$data['process'] = ErpProcess::field('id,name')->where('status',1)->order('sort','asc')->order('id','asc')->select();
		$data['week_materia_quality'] = ['xAxis'=>[],'series'=>[]];
		$data['week_product_stock'] = ['xAxis'=>[],'series'=>[]];
		
		$week_materia_quality 		= ErpMaterialEnterMaterialReport::alias('a')
		->join('erp_material_enter_material b','a.material_enter_material_id = b.id','LEFT')
		->join('erp_material_stock f','b.material_stock_id = f.id','LEFT')
		->fieldRaw('AVG(a.pass_rate) AS pass_rate,f.stock_date')
		->where('a.status', '=', 2)
		->where('f.stock_date','>=',date('Y-m-d', strtotime('-6 days')))
		->order('f.stock_date','desc')->group('f.stock_date')->select()->toArray();
		$week_materia_quality  = array_column($week_materia_quality,'pass_rate','stock_date');
		
		
		$week_product_stock 		= ErpProductStock::alias('a')
		->fieldRaw('count(a.id) AS count,a.stock_date')
		->where('a.type',ErpProductStockEnum::TYPE_PRODUCE)
		->where('a.stock_date','>=',date('Y-m-d', strtotime('-6 days')))
		->order('a.stock_date','desc')->group('a.stock_date')->select()->toArray();
		$week_product_stock  = array_column($week_product_stock,'count','stock_date');
		
		
		$time = strtotime('-6 days');
		for ($i = 0; $i < 7; $i++) {
			$date = date('Y-m-d');
			$data['week_materia_quality']['xAxis'][] = date('d日', strtotime("+$i days", $time));
			if(isset($week_materia_quality[$date])){
				$data['week_materia_quality']['series'][] = round($week_materia_quality[$date],2);
			}else{
				$data['week_materia_quality']['series'][] = 100;
			}
			
			$data['week_product_stock']['xAxis'][] = date('d日', strtotime("+$i days", $time));
			if(isset($week_product_stock[$date])){
				$data['week_product_stock']['series'][] = round($week_product_stock[$date],2);
			}else{
				$data['week_product_stock']['series'][] = 0;
			}
		}
		
		$today = date('Y-m-d');
		$data['today_produce_no_finish'] = ErpOrderProduce::alias('a')
		->join('erp_order b','a.order_id = b.id','LEFT')
		->join('erp_order_product c','a.order_product_id = c.id','LEFT')
		->where('a.produce_status','<>',ErpOrderProduceEnum::PRODUCE_STATUS_FINISH)
		->where('a.produce_date',$today)
		->field('b.customer_name,c.product_model,c.product_specs')->order('a.id asc')->paginate(10);
		
		
		$data['today_finish_process'] = ErpOrderProduceProcess::alias('a')->join('erp_order b','a.order_id = b.id','LEFT')
		->where('a.status',1)
		->where('a.confirm_date','>=',$today.' 00:00:00')
		->field('a.confirm_date,a.process_name,b.customer_name')->order('a.confirm_date desc')->paginate(10);
		
		
		$data['week_order_produce_error_count'] = ErpOrderProduceError::where('create_time','>=',date('Y-m-d 00:00:00', strtotime('-6 days')))->count();
		$data['week_order_produce_process_count'] = ErpOrderProduceProcess::where('create_time','>=',strtotime(date('Y-m-d 00:00:00', strtotime('-6 days'))))->count();
		$data['week_order_produce_process_rate'] = $data['week_order_produce_process_count']?round(($data['week_order_produce_process_count'] - $data['week_order_produce_error_count'])/$data['week_order_produce_process_count'],2)*100:100;


		$data['month_order_produce_error_count'] = ErpOrderProduceError::where('create_time','>=',date('Y-m-01 00:00:00'))->count();
		$data['month_order_produce_process_count'] = ErpOrderProduceProcess::where('create_time','>=',strtotime(date('Y-m-01')))->count();
		$data['month_order_produce_process_rate'] = $data['month_order_produce_process_count']?round(($data['month_order_produce_process_count'] - $data['month_order_produce_error_count'])/$data['month_order_produce_process_count'],2)*100:100;

		return $data ;
	}
	
	
	public static function getWaitOrderProduce($query=[],$limit=10)
    {
		$limit 		= 2000;
		$map	 	= [];
		$map[]		= ['a.produce_status', '<>', ErpOrderProduceEnum::PRODUCE_STATUS_NO];
		if(!empty($query['keyword'])) {
			$map[]	= ['c.product_model|c.product_specs', 'like', '%' . $query['keyword'] . '%'];
        }		
		if(!empty($query['customer_name'])) {
			$map[]	= ['b.customer_name', 'like', '%' . $query['customer_name'] . '%'];
        }
        if(!empty($query['order_sn'])) {
			$map[]	= ['b.order_sn', 'like', '%' . $query['order_sn'] . '%'];
        }
		
		$field 			= 'a.id,b.create_time as order_create_time,b.order_sn as sale_order_sn,b.delivery_time,b.customer_name,c.product_name,c.product_model,c.product_specs,count(a.id) as count';		
		$list 			= ErpOrderProduce::alias('a')
		->join('erp_order b','a.order_id = b.id','LEFT')
		->join('erp_order_product c','a.order_product_id = c.id','LEFT')
		->where($map)->field($field)->order('a.order_id asc')->group('a.order_id')->paginate($limit);
		
		$data 			= $list->items();
		foreach($data as &$vo){
			$vo['order_create_time'] = date('Y-m-d',$vo['order_create_time']);
		}

        return ['code'=>0,'data'=>$data,'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }
	

	public static function getTodayOrderProduce($query=[],$limit=10)
    {
		$limit 		= 2000;
		$map	 	= [];
		$map[]		= ['a.produce_status', '<>', ErpOrderProduceEnum::PRODUCE_STATUS_NO];
		$map[]		= ['a.produce_date', '=', date('Y-m-d')];
		if(!empty($query['keyword'])) {
			$map[]	= ['c.product_model|c.product_specs', 'like', '%' . $query['keyword'] . '%'];
        }		
		if(!empty($query['customer_name'])) {
			$map[]	= ['b.customer_name', 'like', '%' . $query['customer_name'] . '%'];
        }
        if(!empty($query['produce_sn'])) {
			$map[]	= ['a.produce_sn', 'like', '%' . $query['produce_sn'] . '%'];
        }
		$field 		= 'a.id,a.produce_sn,b.create_time as order_create_time,b.order_sn as sale_order_sn,b.delivery_time,b.customer_name,c.product_name,c.product_model,c.product_specs';		
		$list 		= ErpOrderProduce::alias('a')
		->join('erp_order b','a.order_id = b.id','LEFT')
		->join('erp_order_product c','a.order_product_id = c.id','LEFT')->with(['process'=>function($query){$query->where('status',1);}])
		->where($map)->field($field)->order('a.produce_date desc')->paginate($limit);
		$data 		= $list->items();
		foreach($data as &$vo){
			$process_content = [];
			$i=1;
			foreach($vo['process'] as $k=>$item){
				if($item['process_name']){
					$process_content[] = $i.'.'.$item['process_name'].'('.$item['username'].')';
					$i++;
				}
			}
			$vo['process_content'] = implode(';',$process_content);
		}

        return ['code'=>0,'data'=>$data,'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }


	public static function getMateriaQuality($query=[],$limit=10)
    {
		$limit 		= 2000;
		$map	 	= [];
		$map[]		= ['a.status', '=', 2];
		if(!empty($query['material_name'])) {
			$map[]	= ['e.name', 'like', '%' . $query['material_name'] . '%'];
        }		
		if(!empty($query['supplier_name'])) {
			$map[]	= ['g.name', 'like', '%' . $query['supplier_name'] . '%'];
        }
        if(!empty($query['order_sn'])) {
			$map[]	= ['c.order_sn', 'like', '%' . $query['order_sn'] . '%'];
        }
		if(!empty($query['enter_order_sn'])) {
			$map[]	= ['f.order_sn', 'like', '%' . $query['enter_order_sn'] . '%'];
        }
		if(count($map) == 1){
			$map[]	= ['f.stock_date', '=', date('Y-m-d')];
		}else{
			$map[]	= ['f.stock_date', '>=',date('Y-m-d', strtotime('-1 year'))];
		}
		$field 		= 'a.pass_rate,a.unqualified_description,c.order_sn,c.order_date,c.delivery_date,d.apply_num,e.sn,e.name,f.order_sn as enter_order_sn,f.stock_date,g.name as supplier_name';	
		$list 		= ErpMaterialEnterMaterialReport::alias('a')
		->join('erp_material_enter_material b','a.material_enter_material_id = b.id','LEFT')
		->join('erp_purchase_order c','b.purchase_order_id = c.id','LEFT')
		->join('erp_purchase_order_data d','b.purchase_order_data_id = d.id','LEFT')
		->join('erp_material e','a.material_id = e.id','LEFT')
		->join('erp_material_stock f','b.material_stock_id = f.id','LEFT')
		->join('erp_supplier g','c.supplier_id = g.id','LEFT')
		->field($field)->where($map)->order('f.stock_date','desc')->paginate($limit);
		
		$data 			= $list->items();
        return ['code'=>0,'data'=>$data,'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }
	
	
	public static function getTodayProductStock($query=[],$limit=10)
    {
		$limit 		= 2000;
		$map	 	= [];
		$map[]		= ['a.type','=',ErpProductStockEnum::TYPE_PRODUCE];
		if(!empty($query['keyword'])) {
			$map[]	= ['d.product_model|d.product_specs', 'like', '%' . $query['keyword'] . '%'];
        }		
        if(!empty($query['sale_order_sn'])) {
			$map[]	= ['c.order_sn', 'like', '%' . $query['sale_order_sn'] . '%'];
        }
		if(!empty($query['produce_sn'])) {
			$map[]	= ['b.produce_sn', 'like', '%' . $query['produce_sn'] . '%'];
        }
		if(count($map) == 1){
			$map[]	= ['a.stock_date', '=', date('Y-m-d')];
		}else{
			$map[]	= ['a.stock_date', '>=',date('Y-m-d', strtotime('-1 year'))];
		}
		$field 		= 'a.stock_date,b.produce_sn,b.queue_num,c.order_sn as sale_order_sn,d.product_name,d.product_model,d.product_specs,d.product_num';	
		$list 		= ErpProductStock::alias('a')
		->join('erp_order_produce b','a.order_produce_id = b.id','LEFT')
		->join('erp_order c','a.order_id = c.id','LEFT')
		->join('erp_order_product d','a.order_product_id = d.id','LEFT')
		->field($field)->where($map)->order('a.stock_date','desc')->paginate($limit);
		
		$data 			= $list->items();
		$product_num 	= 0;
		foreach($data as &$vo){
			$vo['product_num'] 	= $vo['queue_num'].'/'.$vo['product_num'];
			$product_num  = $product_num + (int)$vo['product_num'];
		}
        return ['code'=>0,'data'=>$data,'extend'=>['count' => $list->total(), 'limit' => $limit, 'totalRow' => ['product_num'=>$product_num]]];
    }
	
	
	public static function getTodayProduceNoFinish($query=[],$limit=10)
    {
		$limit 		= 2000;
		$map	 	= [];
		$map[]		= ['a.produce_status','<>',ErpOrderProduceEnum::PRODUCE_STATUS_FINISH];
		if(!empty($query['keyword'])) {
			$map[]	= ['d.product_model|d.product_specs', 'like', '%' . $query['keyword'] . '%'];
        }		
		if(!empty($query['customer_name'])) {
			$map[]	= ['b.customer_name', 'like', '%' . $query['customer_name'] . '%'];
        }
        if(!empty($query['order_sn'])) {
			$map[]	= ['b.order_sn', 'like', '%' . $query['order_sn'] . '%'];
        }
		if(count($map) == 1){
			$map[]	= ['a.produce_date', '=', date('Y-m-d')];
		}else{
			$map[]	= ['a.produce_date', '>=',date('Y-m-d', strtotime('-1 year'))];
		}
		$field 		= 'a.produce_date,b.customer_name,b.order_sn,b.delivery_time,d.product_model,d.product_specs,d.product_num,d.add_project,d.change_project,d.replace_info,d.color';		
        $list 		= ErpOrderProduce::alias('a')
		->join('erp_order b','a.order_id = b.id','LEFT')
		->join('erp_order_product d','a.order_product_id = d.id','LEFT')
		->where($map)->field($field)->order('a.id','asc')
		->append(['project_html'])->paginate($limit);
		
		$data 			= $list->items();
        return ['code'=>0,'data'=>$data,'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }



	public static function getTodayProduceProcess($query=[],$limit=10)
    {
		$limit 		= 2000;
		$map	 	= [];
		if(!empty($query['keyword'])) {
			$map[]	= ['c.product_model|c.product_specs', 'like', '%' . $query['keyword'] . '%'];
        }		
		if(!empty($query['customer_name'])) {
			$map[]	= ['b.customer_name', 'like', '%' . $query['customer_name'] . '%'];
        }
        if(!empty($query['produce_sn'])) {
			$map[]	= ['a.produce_sn', 'like', '%' . $query['produce_sn'] . '%'];
        }
		if(count($map) == 0){
			$map[]	= ['a.produce_date', '=', date('Y-m-d')];
		}else{
			$map[]	= ['a.produce_date', '>=',date('Y-m-d', strtotime('-1 year'))];
		}
		$field 		= 'a.id,a.produce_sn,b.create_time as order_create_time,b.order_sn as sale_order_sn,b.delivery_time,b.customer_name,c.product_name,c.product_model,c.product_specs';		
		$list 		= ErpOrderProduce::alias('a')
		->join('erp_order b','a.order_id = b.id','LEFT')
		->join('erp_order_product c','a.order_product_id = c.id','LEFT')->with(['process'=>function($query){$query->where('status',1);}])
		->where($map)->field($field)->order('a.produce_date desc')->paginate($limit);
		$data 		= $list->items();
		foreach($data as &$vo){
			$process_content = [];
			$i=1;
			foreach($vo['process'] as $k=>$item){
				if($item['process_name']){
					$process_content[] = $i.'.'.$item['process_name'].'('.$item['username'].')';
					$i++;
				}
			}
			$vo['process_content'] = implode(';',$process_content);
		}

        return ['code'=>0,'data'=>$data,'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }


	public static function getProduceProcessPassRate($query=[],$limit=10){
		$limit 		= 2000;
		$where = [];
		if(!empty($query['start_date'])){
			$where[] = ['create_time','>=',strtotime($query['start_date'])];
		}
		if(!empty($query['end_date'])){
			$where[] = ['create_time','<=',strtotime($query['end_date'])+24*3600];
		}		
		$error_count = array_column(ErpOrderProduceError::where($where)->fieldRaw('count(id) as count,process_id')->group('process_id')->select()->toArray(),'count','process_id');
		$process_count = array_column(ErpOrderProduceProcess::where($where)->where('status',1)->fieldRaw('count(id) as count,process_id')->group('process_id')->select()->toArray(),'count','process_id');
	
		$list =  ErpProcess::field('id,name')->where('status',1)->order('sort','asc')->order('id','asc')->select()->toArray();
		$pass_rate_total = 0;
		$error_count_total = 0;
		$process_count_total = 0;
		foreach($list as &$vo){
			$vo['error_count'] = $error_count[$vo['id']]??0;
			$vo['process_count'] = $process_count[$vo['id']]??0;
			$pass_rate = $vo['process_count']?round(($vo['process_count'] - $vo['error_count'])/$vo['process_count'],2)*100:100;
			$vo['pass_rate'] = $pass_rate.'%';
			$process_count_total = $process_count_total + $vo['process_count'];
			$error_count_total = $error_count_total + $vo['error_count'];
			$pass_rate_total = $pass_rate_total + $pass_rate;
		}
		return ['code'=>0,'data'=>$list,'extend'=>['count' => count($list), 'limit' => $limit, 'totalRow' => ['error_count'=>$error_count_total,'process_count'=>$process_count_total,'pass_rate'=>round(($pass_rate_total/count($list)),2).'%']]];
	}


	public static function getWeekProduceStock($query=[],$limit=10){
		$limit 		= 2000;
		$where = [];
		if(!empty($query['start_date'])){
			$where[] = ['stock_date','>=',($query['start_date'])];
		}
		if(!empty($query['end_date'])){
			$where[] = ['stock_date','<=',($query['end_date'])];
		}		

		$field 		= 'a.stock_date,d.product_name,d.product_model,d.product_specs,d.product_num,count(a.id) as count';	
		$list 		= ErpProductStock::alias('a')
		->join('erp_order_product d','a.order_product_id = d.id','LEFT')
		->fieldRaw($field)->where($where)->group('a.stock_date')->group('a.product_id')->order('a.stock_date','desc')->paginate($limit);
		
		$data 		= $list->items();
		$total 		= 0;
		foreach($data as &$vo){
			$total  = $total + (int)$vo['count'];
		}
        return ['code'=>0,'data'=>$data,'extend'=>['count' => $list->total(), 'limit' => $limit, 'totalRow' => ['count'=>$total]]];
	}


}
