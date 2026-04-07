<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\ErpOrderProduce;
use app\common\model\ErpProcess;
use app\common\model\ErpOrderProduceProcess;
use app\admin\validate\ErpOrderProduceProcessValidate;
use app\common\enum\ErpOrderProduceProcessEnum;
use app\common\enum\RegionTypeEnum;
use app\common\model\AdminAdmin;
use app\common\model\{ErpUser,ErpOrderProduceProcessLog};

class ErpOrderProduceProcessLogic extends BaseLogic{


    // 添加
    public static function goAdd($order_produce_id,$process_id)
    {
        try {
			$data 		= [];
			$produce 	= ErpOrderProduce::where('id',$order_produce_id)->find();
			$process 	= ErpProcess::alias('a')
			->join('erp_process_wage b','a.id = b.process_id and b.product_id='.$produce['product_id'],'LEFT')
			->with(['follow'])
			->field('a.*,b.price')->where('a.id','in',$process_id)->order('a.sort asc,a.id asc')->select();
			$old 		= ErpOrderProduceProcess::where('process_id','in',$process_id)->where('order_produce_id','=',$order_produce_id)->column('id','process_id');
			foreach($process as $vo){
				if(empty($old[$vo['id']])){
					$data[] = ['process_id'=>$vo['id'],'process_name'=>$vo['name'],'follow_name'=>empty($vo['follow'])?'':$vo['follow']['name'],'price'=>$vo['price']?$vo['price']:0,'product_id'=>$produce['product_id'],'order_produce_id'=>$produce['id'],'order_id'=>$produce['order_id']];
				}
			}
			(new ErpOrderProduceProcess)->saveAll($data);
			
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpOrderProduceProcess::where($map)->find();
		}else{
			return ErpOrderProduceProcess::find($map);
		}
    }

    // 编辑
    public static function goEdit($order_produce_id,$process_id,$price)
    {
		$model = self::getOne([['order_produce_id','=',$order_produce_id],['process_id','=',$process_id]]);
		if(empty($model['id'])){
			return ['msg'=>'数据不存在','code'=>201];
		}
		if($model['status'] == 1){
			return ['msg'=>'工序已报工完成','code'=>201];
		}
        try {
            $model->save(['price'=>$price]);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 删除
    public static function goRemove($order_produce_id,$process_id)
    {
		$model = self::getOne([['order_produce_id','=',$order_produce_id],['process_id','=',$process_id]]);
		if(empty($model['id'])){
			return ['msg'=>'数据不存在','code'=>201];
		}
		if($model['status'] == 1){
			return ['msg'=>'工序已报工完成','code'=>201];
		}		
        try{
			$model->delete();
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

	// 获取列表
    public static function getIndex($query=[])
    {
		$dates 				= [];
		if(empty($query['date'])){
			$query['date']	= date('Y-m-d');
		}
		$today 				= date('Y-m-d');
		
		for($i=-3;$i<=1;$i++){
			$date 			= date("Y-m-d",strtotime($today.($i<0?'':' +').$i." day"));
			$dates[] 		= ['date'=>$date,'count'=>ErpOrderProduce::where('produce_date',$date)->count()];
		}
		
		$produce1			= ErpOrderProduce::alias('a')->join('erp_order b','a.order_id = b.id','LEFT')->field('a.produce_sn,b.address_short')->where('a.process_id',3)->where('produce_date',$query['date'])->select();
		$map	 			= [];
		
		if(!empty($query['customer_name'])) {
			$map[]			= ['b.customer_name', 'like', '%' . $query['customer_name'] . '%'];
        }
		if(!empty($query['order_sn'])) {
			$map[]			= ['b.order_sn', 'like', '%' . $query['order_sn'] . '%'];
        }
		if(!empty($query['produce_sn'])) {
			$map[]			= ['a.produce_sn', 'like', '%' . $query['produce_sn'] . '%'];
        }
		if(!empty($query['delivery_time'])) {
			$time 			= is_array($query['delivery_time'])?$query['delivery_time']:explode('至',$query['delivery_time']);
			if(!empty($time[0])){
				$map[]		= ['b.delivery_time', '>=', strtotime(trim($time[0]))];
			}
			if(!empty($time[1])){
				$map[]		= ['b.delivery_time', '<=', strtotime(trim($time[1]))];
			}
        }
		if(!empty($query['date'])) {
			$map[]			= ['a.produce_date', '=', $query['date']];
		}
		$process 			= ErpProcess::order(['sort'=>'asc','id'=>'asc'])->select()->toArray();
		$user 				= ErpUser::column('name','id');
		$produce2			= ErpOrderProduce::alias('a')->join('erp_order b','a.order_id = b.id','LEFT')->join('erp_product c','a.product_id = c.id','LEFT')
		->field('a.id,a.order_id,a.produce_date,a.produce_sn,b.address_short,b.order_sn,b.customer_name,c.specs,c.model')->order(['a.id'=>'asc'])->where($map)->select()->toArray();
		$ids 				= [];
		
		foreach($produce2 as $k=>$vo){
			$ids[] 			= $vo['id'];
			foreach($process as $key=>$v){
				$produce2[$k]['process'][] = ErpOrderProduceProcess::where('process_id',$v['id'])->where('order_produce_id',$vo['id'])->where('user_id','>',0)->find();
			}
		}
		
		foreach($process as $key=>$vo){
			//->where('create_time','>',strtotime($query['date']))->where('create_time','<',strtotime($query['date'].' 23:59:59'))
			$process[$key]['count'] 	= $ids?ErpOrderProduceProcess::where('process_id',$vo['id'])->where('order_produce_id','in',$ids)->where('user_id','>',0)->count():0;
		}

        return ['today'=>$today,'query'=>$query,'dates'=>$dates,'produce1'=>$produce1,'produce2'=>$produce2,'process'=>$process,'user'=>$user];
    }

	// 获取列表
    public static function getErrorList($query=[],$limit=10)
    {
		$map	 			= [];
		$map[]				= ['a.error_time', '>', 0];
		$map[]				= ['c.type', 'in', [ErpOrderProduceProcessEnum::TYPE_LACK,ErpOrderProduceProcessEnum::TYPE_ERROR]];
		if(!empty($query['customer_name'])) {
			$map[]			= ['b.customer_name', 'like', '%' . $query['customer_name'] . '%'];
        }
		if(!empty($query['order_sn'])) {
			$map[]			= ['b.order_sn', 'like', '%' . $query['order_sn'] . '%'];
        }
		if(!empty($query['produce_sn'])) {
			$map[]			= ['a.produce_sn', 'like', '%' . $query['produce_sn'] . '%'];
        }
		if(!empty($query['delivery_time'])) {
			$time 			= is_array($query['delivery_time'])?$query['delivery_time']:explode('至',$query['delivery_time']);
			if(!empty($time[0])){
				$map[]		= ['b.delivery_time', '>=', strtotime(trim($time[0]))];
			}
			if(!empty($time[1])){
				$map[]		= ['b.delivery_time', '<=', strtotime(trim($time[1]))];
			}
        }
		if(!empty($query['produce_date'])) {
			$time 			= is_array($query['produce_date'])?$query['produce_date']:explode('至',$query['produce_date']);
			if(!empty($time[0])){
				$map[]		= ['a.produce_date', '>=', (trim($time[0]))];
			}
			if(!empty($time[1])){
				$map[]		= ['a.produce_date', '<=', (trim($time[1]))];
			}
        }
		if(isset($query['status']) && $query['status'] !== '') {
			$map[]			= ['c.status', '=', $query['status']];
        }		
		
		
		$field 				= 'a.id,a.order_product_id,a.produce_date,a.produce_sn,b.address_short,b.order_sn,b.salesman_id,b.delivery_time';	
		$list 				= ErpOrderProduce::alias('a')->join('erp_order b','a.order_id = b.id','LEFT')->join('erp_order_produce_process c','a.id = c.order_produce_id','right')->with(['order_product.bom'])->field($field)->where($map)->append(['order_product.bom_html'])->order('a.error_time','desc')->group(['a.id'])->paginate($limit);
        
		$data 				= $list->items();
		$admin 				= AdminAdmin::where('id','in',array_column($data,'salesman_id'))->column('id,username','id');
		foreach($data as $k=>$vo){
			$data[$k]['salesman'] 	= empty($admin[$vo['salesman_id']])?'':$admin[$vo['salesman_id']]['username'];
			$data[$k]['process'] 	= ErpOrderProduceProcess::where('order_produce_id',$vo['id'])->where('type', 'in', [ErpOrderProduceProcessEnum::TYPE_LACK,ErpOrderProduceProcessEnum::TYPE_ERROR])->order('id desc')->select()->toArray();
			if($data[$k]['process']){
				$data[$k]['error'] 	= date('m-d H:i',strtotime($data[$k]['process'][0]['create_time'])).'：'.$data[$k]['process'][0]['error'];
			}else{
				$data[$k]['error']  = '';
			}
		}
		return ['code'=>0,'data'=>$data,'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }


    // 删除
    public static function goConfirm($data)
    {
		//验证
        $validate 	= new ErpOrderProduceProcessValidate;
        if(!$validate->scene('remove')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}	
        try{
			ErpOrderProduceProcess::where('order_produce_id','in',$data['ids'])->where('status',0)->where('type', 'in', [ErpOrderProduceProcessEnum::TYPE_LACK,ErpOrderProduceProcessEnum::TYPE_ERROR])->update(['status'=>1,'confirm_date'=>date('Y-m-d')]);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
	
	public static function getErrorCount(){
		return ErpOrderProduceProcess::where('status',0)->where('type', 'in', [ErpOrderProduceProcessEnum::TYPE_LACK,ErpOrderProduceProcessEnum::TYPE_ERROR])->count();
	}
	

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$map	 			= [];
	
		if(!empty($query['process_id'])) {
			$map[]			= ['a.process_id', '=', $query['process_id']];
        }			
		if(!empty($query['produce_sn'])) {
			$map[]			= ['b.produce_sn', 'like', '%' . $query['produce_sn'] . '%'];
        }
		if(!empty($query['create_time'])) {
			$time 			= is_array($query['create_time'])?$query['create_time']:explode('至',$query['create_time']);
			if(!empty($time[0])){
				$map[]		= ['a.create_time', '>=', strtotime(trim($time[0]))];
			}
			if(!empty($time[1])){
				$map[]		= ['a.create_time', '<=', strtotime(trim($time[1]))];
			}
        }
		if(!empty($query['produce_date'])) {
			$time 			= is_array($query['produce_date'])?$query['produce_date']:explode('至',$query['produce_date']);
			if(!empty($time[0])){
				$map[]		= ['b.produce_date', '>=', (trim($time[0]))];
			}
			if(!empty($time[1])){
				$map[]		= ['b.produce_date', '<=', (trim($time[1]))];
			}
        }
		if(!empty($query['finish_date'])) {
			$time 			= is_array($query['finish_date'])?$query['finish_date']:explode('至',$query['finish_date']);
			if(!empty($time[0])){
				$map[]		= ['b.finish_date', '>=', (trim($time[0]))];
			}
			if(!empty($time[1])){
				$map[]		= ['b.finish_date', '<=', (trim($time[1]))];
			}
        }
		$field 				= 'a.*,b.produce_sn,b.produce_date,b.finish_date,c.order_sn,c.address,c.contacts,c.salesman_id,c.region_type';	
		$list 				= ErpOrderProduceProcess::alias('a')->join('erp_order_produce b','a.order_produce_id = b.id','LEFT')->join('erp_order c','b.order_id = c.id','LEFT')->with(['order_product.bom','user'])->field($field)->where($map)->append(['order_product.bom_html'])->order('a.id','desc')->paginate($limit);
		$data 				= $list->items();
		$admin 				= self::getAdmins();
		foreach($data as $k=>$vo){
			$data[$k]['salesman'] 						= empty($admin[$vo['salesman_id']])?'':$admin[$vo['salesman_id']]['username'];
			
			$data[$k]['region_type_desc']				= RegionTypeEnum::getDesc($vo['region_type']);
		}
		return ['code'=>0,'data'=>$data,'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

	// 获取列表
    public static function getWage($query=[],$limit=10)
    {
		$map 		= self::getWageWhere($query);
		$field 		= 'a.*,b.produce_sn,b.produce_date,b.finish_date,c.order_sn,c.address,c.contacts,c.salesman_id,c.region_type,c.delivery_time,d.product_model,d.product_specs';	
		$list 		= ErpOrderProduceProcess::alias('a')
		->join('erp_order_produce b','a.order_produce_id = b.id','LEFT')
		->join('erp_order c','b.order_id = c.id','LEFT')
		->join('erp_order_product d','b.order_product_id = d.id','LEFT')
		->field($field)->where($map)->order('a.id','desc')->paginate($limit);
		$data 				= $list->items();
		$admin 				= self::getAdmins();
		foreach($data as $k=>$vo){
			$data[$k]['salesman'] 			= empty($admin[$vo['salesman_id']])?'':$admin[$vo['salesman_id']]['username'];
			$data[$k]['delivery_time'] 		= date('Y-m-d',$vo['delivery_time']);
			$data[$k]['approve_status_desc']= $vo['approve_status']==1?'已审核':'未审核';
		}
		return ['code'=>0,'data'=>$data,'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }
	
	
	public static function getWageWhere($query=[])
    {
		$map	 			= [];
		$map[]				= ['a.status', '=', 1];
		if(!empty($query['process_id'])) {
			$map[]			= ['a.process_id', '=', $query['process_id']];
        }			
		if(!empty($query['produce_sn'])) {
			$map[]			= ['b.produce_sn', 'like', '%' . $query['produce_sn'] . '%'];
        }
		if(!empty($query['confirm_date'])) {
			$time 			= is_array($query['confirm_date'])?$query['confirm_date']:explode('至',$query['confirm_date']);
			if(!empty($time[0])){
				$map[]		= ['a.confirm_date', '>=', (trim($time[0]))];
			}
			if(!empty($time[1])){
				$map[]		= ['a.confirm_date', '<=', (trim($time[1]))];
			}
        }
		if(!empty($query['order_sn'])) {
			$map[]			= ['c.order_sn', 'like', '%' . $query['order_sn'] . '%'];
        }	
		if(!empty($query['customer_name'])) {
			$map[]			= ['c.customer_name', 'like', '%' . $query['customer_name'] . '%'];
        }	
		if(!empty($query['follow_name'])) {
			$map[]			= ['a.follow_name', 'like', '%' . $query['follow_name'] . '%'];
        }
		if(isset($query['approve_status']) && $query['approve_status'] !== '') {
			$map[]			= ['a.approve_status', '=', $query['approve_status']];
        }		
		if(!empty($query['has_price'])) {
			$map[]			= ['a.price', '>', 0];
        }			
		return $map;
		
	}
    public static function goApprove($data)
    {
		//验证
        $validate 	= new ErpOrderProduceProcessValidate;
        if(!$validate->scene('remove')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}	
        try{
			ErpOrderProduceProcess::where('id','in',$data['ids'])->update(['approve_status'=>1]);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }	
	
	public static function getWageCount($query=[]){
        $count 	= ErpOrderProduceProcess::alias('a')
		->join('erp_order_produce b','a.order_produce_id = b.id','LEFT')
		->join('erp_order c','b.order_id = c.id','LEFT')
		->join('erp_order_product d','b.order_product_id = d.id','LEFT')
		->field('a.id')->where(self::getWageWhere($query))->count();
		return ['data'=>['count'=>$count,'key'=>rand_string()]];
	}
	
	public static function getWageExport($query=[],$limit=10000){
		$limit				= $limit>10000?10000:$limit;
		$data				= self::getWage($query,$limit)['data'];
		$return				= ['column'=>[],'setWidh'=>[],'keys'=>[],'list'=>$data,'image_fields'=>[]];
		$field 				= ['produce_sn'=>'产品编码','product_model'=>'型号','product_specs'=>'款式','order_sn'=>'销售合同号','delivery_time'=>'交货日期','finish_date'=>'入库日期','salesman'=>'业务员','process_name'=>'报工工序','follow_name'=>'所属随工单','confirm_date'=>'完成时间','username'=>'完成人','price'=>'报工单价','approve_status_desc'=>'状态'];
		foreach($field as $key=>$vo){
			$return['column'][] 	= $vo;
			$return['setWidh'][] 	= 10;
			$return['keys'][] 		= $key;				
		}
        return $return;	
	}
	
    public static function goWageEdit($data)
    {
        //验证
        $validate 	= new ErpOrderProduceProcessValidate;
        if(!$validate->scene('edit')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		$model 		= ErpOrderProduceProcess::where('id',$data['id'])->find();
        if(empty($model)) {
			return ['msg'=>'数据不存在','code'=>201];
		}
        try {
			$log 			= [];
			$item			= ['username'=>'完成人','confirm_date'=>'随工单完成时间','price'=>'报工单价'];
			foreach($item as $field=>$vo){
				if($model[$field] != $data[$field]){
					$log[] 	= ['remark'=>'`'.$vo.'`从`'.$model[$field].'`到`'.$data[$field].'`;','process_id'=>$model['process_id'],'order_produce_id'=>$model['order_produce_id'],'user_id'=>0,'username'=>self::$adminUser['username']];
				}
			}
			if($log){
				(new ErpOrderProduceProcessLog)->saveAll($log);
			}
			$model->save($data);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
	
	public static function getLog($order_produce_id){
		return ErpOrderProduceProcessLog::where('order_produce_id',$order_produce_id)->order('id desc')->select();
	}
	
	
	public static function getWageGroupUser($query=[],$limit=10)
    {
		$map 		= self::getWageWhere($query);
		$field 		= 'a.*,count(a.id) as count,sum(a.price) as amount';	
		$list 		= ErpOrderProduceProcess::alias('a')
		->join('erp_order_produce b','a.order_produce_id = b.id','LEFT')
		->join('erp_order c','b.order_id = c.id','LEFT')
		->field($field)->where($map)->order('a.id','desc')->group('a.user_id,a.process_id')->paginate($limit);

		$totalRow 			= [];
		$totalRow['count'] 	= ErpOrderProduceProcess::alias('a')->join('erp_order_produce b','a.order_produce_id = b.id','LEFT')->join('erp_order c','b.order_id = c.id','LEFT')->field($field)->where($map)->count('a.id');
		$totalRow['amount'] = ErpOrderProduceProcess::alias('a')->join('erp_order_produce b','a.order_produce_id = b.id','LEFT')->join('erp_order c','b.order_id = c.id','LEFT')->field($field)->where($map)->sum('a.price');

		return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit, 'totalRow'=>$totalRow]];
    }
	
	public static function getWageGroupUserCount($query=[]){
        $count 	= ErpOrderProduceProcess::alias('a')
		->join('erp_order_produce b','a.order_produce_id = b.id','LEFT')
		->join('erp_order c','b.order_id = c.id','LEFT')
		->field('a.id')->where(self::getWageWhere($query))->group('a.user_id,a.process_id')->count();
		return ['data'=>['count'=>$count,'key'=>rand_string()]];
	}
	
	public static function getWageGroupUserExport($query=[],$limit=10000){
		$limit				= $limit>10000?10000:$limit;
		$data				= self::getWageGroupUser($query,$limit)['data'];
		$return				= ['column'=>[],'setWidh'=>[],'keys'=>[],'list'=>$data,'image_fields'=>[]];
		$field 				= ['username'=>'完成人','process_name'=>'报工工序','follow_name'=>'所属随工单','price'=>'工序单价','count'=>'报工次数','amount'=>'报工金额'];
		foreach($field as $key=>$vo){
			$return['column'][] 	= $vo;
			$return['setWidh'][] 	= 10;
			$return['keys'][] 		= $key;				
		}
        return $return;	
	}
	

	public static function getProduce($query=[],$limit=10)
    {
		$map	 			= self::getProduceWhere($query);
		$list				= ErpOrderProduce::alias('a')->join('erp_order b','a.order_id = b.id','LEFT')->join('erp_product c','a.product_id = c.id','LEFT')
		->field('a.id,a.order_id,a.produce_date,a.produce_sn,b.address_short,b.order_sn,b.customer_name,c.specs,c.model')->order(['a.id'=>'asc'])->where($map)->paginate($limit);
		$data 				= $list->items();
		
		$tmp 				= ErpOrderProduceProcess::field('id,order_produce_id,process_id,username,confirm_date')->where('order_produce_id','in',array_column($data,'id'))->where('user_id','>',0)->select();
		$produce_process	= [];
		foreach($tmp as $k=>$vo){
			$produce_process[$vo['order_produce_id']][] = $vo->toArray();
		}	
	
		foreach($data as $k=>$vo){
			if(!empty($produce_process[$vo['id']])){
				foreach($produce_process[$vo['id']] as $key=>$v){
					$data[$k]['process'.$v['process_id']]  = $v['username'].'：'.date('m-d H:i',strtotime($v['confirm_date']));
				}
			}
		}
		return ['code'=>0,'data'=>$data,'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }
	
	public static function getProduceWhere($query=[])
    {
		$map	 			= [];
		$map[] 				= ['approve_status','=',1];
		if(!empty($query['customer_name'])) {
			$map[]			= ['b.customer_name', 'like', '%' . $query['customer_name'] . '%'];
        }
		if(!empty($query['order_sn'])) {
			$map[]			= ['b.order_sn', 'like', '%' . $query['order_sn'] . '%'];
        }
		if(!empty($query['produce_sn'])) {
			$map[]			= ['a.produce_sn', 'like', '%' . $query['produce_sn'] . '%'];
        }
		if(!empty($query['delivery_time'])) {
			$time 			= is_array($query['delivery_time'])?$query['delivery_time']:explode('至',$query['delivery_time']);
			if(!empty($time[0])){
				$map[]		= ['b.delivery_time', '>=', strtotime(trim($time[0]))];
			}
			if(!empty($time[1])){
				$map[]		= ['b.delivery_time', '<=', strtotime(trim($time[1]))];
			}
        }	
		return $map;
    }	
	
	
	public static function getProduceCount($query=[]){
        $count 	= ErpOrderProduce::alias('a')->join('erp_order b','a.order_id = b.id','LEFT')
		->field('a.id')->where(self::getProduceWhere($query))->count();
		return ['data'=>['count'=>$count,'key'=>rand_string()]];
	}
	
	public static function getProduceExport($query=[],$limit=10000){
		$limit				= $limit>10000?10000:$limit;
		$data				= self::getProduce($query,$limit)['data'];
		$return				= ['column'=>[],'setWidh'=>[],'keys'=>[],'list'=>$data,'image_fields'=>[]];
		$field 				= ['customer_name'=>'客户名称','order_sn'=>'销售合同','produce_date'=>'上线日期','model'=>'型号','specs'=>'款式','produce_sn'=>'产品编码'];
		$process			= ErpProcess::field('id,name')->where('status',1)->order(['sort'=>'asc','id'=>'asc'])->select();
		foreach($process as $vo){
			$field['process'.$vo['id']] = $vo['name'];
		}
		foreach($field as $key=>$vo){
			$return['column'][] 	= $vo;
			$return['setWidh'][] 	= 10;
			$return['keys'][] 		= $key;				
		}
        return $return;	
	}	
	
	
}
