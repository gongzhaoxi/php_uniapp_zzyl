<?php
declare (strict_types = 1);
namespace app\index\logic;
use think\facade\Db;
use app\index\validate\ErpUserValidate;
use app\index\service\UserTokenService;
use app\common\enum\UserTerminalEnum;
use app\common\model\{ErpUser,ErpOrderProduceRework,ErpProcess,ErpMaterialWarehouseReturn,ErpOrderProduceFollow,ErpMaterialApproval};

class ErpUserLogic{

    // 登录
    public static function goLogin($data)
    {
        //验证
        $validate	= new ErpUserValidate;
        if(!$validate->scene('login')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		$user 		= ErpUser::where('sn',$data['sn'])->where('mobile',$data['mobile'])->find();
		if(empty($user['id'])) {
			//throw new \Exception('');
			return ['msg'=>'用户不存在或识别码错误','code'=>201];
		}
        try {
			return ['code'=>200,'data'=>self::loginSuccess($user,$data['terminal'])];
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
	
	public static function loginSuccess($user,$terminal){
		//更新登录信息
		$user->save(['login_time'=>time(),'login_ip'=>request()->ip()]);
		//设置token
		$userInfo 	= UserTokenService::setToken($user->id, $terminal);
		if($terminal == UserTerminalEnum::PC){
			cookie(config('project.user_token.cookie_key'),$userInfo['token']);
		}
		return ['user'=>['name' => $user['name'],'title' => $user['title'],'mobile' => $user['mobile']],'token' => $userInfo['token'],'permission'=>$user['permission']];
	}
	
	
    // 退出登陆
    public static function goLogout()
    {
		cookie(config('project.user_token.cookie_key'),null);
        return ['msg'=>'退出成功','code'=>200];
    }	
	
	
    // 待办信息数量
    public static function toDoCount($user)
    {
        $data 				= [];
		$data['count']		= 0;
		if(in_array(11,$user['permission']) || in_array(12,$user['permission'])){
			$data['count']	= $data['count'] + ErpOrderProduceRework::where(self::toDo1Map($user))->count();
		}
		if(in_array(14,$user['permission'])){
			$data['count']	= $data['count'] + ErpMaterialWarehouseReturn::alias('a')->where(self::toDo2Map($user))->count();
		}
		if(in_array(9,$user['permission'])){
			$data['count']	= $data['count'] + ErpOrderProduceFollow::alias('a')->join('erp_process b','a.process_id = b.id','LEFT')->where(self::toDo3Map($user))->count();
		}	
		if(in_array(14,$user['permission'])){
			$data['count']	= $data['count'] + ErpMaterialApproval::where(self::toDo4Map($user))->count();
		}

		
		return $data;
    }	
	
    public static function toDo1Map($user)
    {
		$process_id 	= ErpProcess::where('monitor','find in set',$user['user_id'])->column('id');
		$map 			= [];
		$map[]			= ['status','=',0];
		$map[]			= ['user_id','exp',Db::raw('='.$user['user_id'].($process_id?(' or process_id in ('.implode(',',$process_id).')'):''))];
		return $map;
    }	
	
    public static function toDo2Map($user)
    {
		$map 			= [];
		$map[]			= ['a.status','=',0];
		return $map;
    }	

    public static function toDo3Map($user)
    {
		$map 			= [];
		$map[]			= ['a.inspect_user_id','=',0];
		$map[]			= ['b.user_id','find in set',$user['user_id']];
		return $map;
    }
	
    public static function toDo4Map($user)
    {
		$map 			= [];
		$map[]			= ['status','=',0];
		return $map;
    }	
	

	// 待办信息
    public static function toDoList($user,$query=[],$limit=10)
    {
		$data 			= [];
		if(in_array(11,$user['permission']) || in_array(12,$user['permission'])){
			$list 		= ErpOrderProduceRework::where(self::toDo1Map($user))->field('*')->order('id','desc')->select();
			foreach($list as $vo){
				$data[] = ['from'=>1,'type'=>'不合格/返工评审处理单','id'=>$vo['id'],'remark'=>$vo['remark'],'username'=>$vo['username'],'create_time'=>$vo['create_time']];
			}
		}
		if(in_array(14,$user['permission'])){
			$list 		= ErpMaterialWarehouseReturn::alias('a')->join('erp_material b','a.material_id = b.id','LEFT')->join('erp_warehouse c','a.warehouse_id = c.id','LEFT')->where(self::toDo2Map($user))->field('a.*,b.sn,b.name,c.name as warehouse_name')->order('a.id','desc')->select();
			foreach($list as $vo){
				$data[] = ['from'=>2,'type'=>'零件退仓','id'=>$vo['id'],'remark'=>$vo['sn'].$vo['name'].'；退仓数量：'.$vo['stock_num'].'；工位仓位：'.$vo['warehouse_name'],'username'=>$vo['username'],'create_time'=>$vo['create_time']];
			}
		}
		if(in_array(9,$user['permission'])){
			$list 		= ErpOrderProduceFollow::alias('a')
			->join('erp_process b','a.process_id = b.id','LEFT')
			->join('erp_order_produce c','a.order_produce_id = c.id','LEFT')
			->join('erp_order d','c.order_id = d.id','LEFT')
			->join('erp_order_product e','c.order_product_id = e.id','LEFT')
			->where(self::toDo3Map($user))
			->field('a.*,b.name as process_name,c.produce_sn,c.order_sn as produce_order_sn,d.order_sn as sale_order_sn,d.customer_name,e.product_model')->order('a.id','desc')->select();
			foreach($list as $vo){
				$data[] = ['from'=>3,'type'=>'生产巡检','id'=>$vo['id'],'produce_sn'=>$vo['produce_sn'],'process_id'=>$vo['process_id'],'remark'=>'工艺:'.$vo['process_name'].':订单号:'.$vo['sale_order_sn'].':生产单号:'.$vo['produce_order_sn'].';型号:'.$vo['product_model'].';客户名称:'.$vo['customer_name'],'username'=>$vo['username'],'create_time'=>$vo['create_time']];
			}		
		}
		if(in_array(14,$user['permission'])){
			$list 		= ErpMaterialApproval::field('id,type,create_by,create_time')->where(self::toDo4Map($user))->select();
			foreach($list as $vo){
				$data[] = ['from'=>$vo['type']==1?4:5,'type'=>$vo['type']==1?'车间退回仓库':'物料报废','id'=>$vo['id'],'remark'=>$vo['type']==1?'物料退回零件仓库':'不良品退回仓库','username'=>$vo['create_by'],'create_time'=>$vo['create_time']];
			}
		}

        return ['code'=>0,'data'=>$data,'extend'=>['count' => count($data), 'limit' =>  count($data)]];
    }

     // 修改密码
     public static function goPass($user,$data)
     {      
		ErpUser::where('id',$user['user_id'])->update(['sn'=>$data['sn']]);
     }

}
