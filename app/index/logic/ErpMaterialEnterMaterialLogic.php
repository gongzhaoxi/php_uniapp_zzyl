<?php
declare (strict_types = 1);
namespace app\index\logic;
use app\index\logic\BaseLogic;
use app\common\model\{ErpMaterialEnterMaterial,ErpMaterialEnterMaterialReport,ErpMaterialCode,ErpMaterialScrap,ErpGuideBook};
use app\common\enum\{ErpMaterialEnterMaterialEnum,ErpMaterialStockEnum};
use think\facade\Db;

class ErpMaterialEnterMaterialLogic extends BaseLogic{

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$field 			= 'a.id,a.qualities_num as qualities_num_original,a.defective_num as defective_num_original,a.material_stock_id,a.material_id,a.stock_num,a.stocked_num,a.quality_num,a.status,a.stock_num-a.stocked_num-a.quality_num as max_num,a.qualities_num,defective_num,a.is_prior,b.name,b.sn,b.unit,c.order_sn,c.batch_number,c.type,d.inspector_sign,d.status as report_status';	
		$query['_alias']= 'a';
		$query['_material_alias']= 'b';
		$query['_stock_alias']= 'c';
		$query['_report_alias']= 'd';
		if(!empty($query['finish_status']) && $query['finish_status'] == 2){
			$order 		= ['d.finish_time'=>'desc','a.id'=>'desc'];
		}else{
			$order 		= ['a.is_prior'=>'desc','a.id'=>'desc'];
		}		
        $list 			= ErpMaterialEnterMaterial::alias('a')
		->join('erp_material b','a.material_id = b.id','LEFT')
		->join('erp_material_stock c','a.material_stock_id = c.id','LEFT')
		->join('erp_material_enter_material_report d','a.id = d.material_enter_material_id','LEFT')
		->withSearch(['query'],['query'=>$query])
		->field($field)->order($order)
		->where('need_check',1)
		->where('c.status','<>',ErpMaterialStockEnum::STATUS_CANCEL)
		//->where('a.status','<>',ErpMaterialEnterMaterialEnum::STATUS_CANCEL)
		->where('check_status','in',[ErpMaterialEnterMaterialEnum::CHECK_STATUS_NOTICED,ErpMaterialEnterMaterialEnum::CHECK_STATUS_PART,ErpMaterialEnterMaterialEnum::CHECK_STATUS_FINISH])->append(['can_check'])->paginate($limit);
        $data = $list->items();
		foreach($data as &$vo){
			$vo['num'] 			= '';
			$vo['num2'] 		= '';
			if($vo['type'] == ErpMaterialStockEnum::TYPE_ENTER_WORKSHOP && $vo['batch_number']){
				$vo['order_sn']	= $vo['batch_number'];
			}
		}
		return ['code'=>0,'data'=>$data,'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpMaterialEnterMaterial::where($map)->find();
		}else{
			return ErpMaterialEnterMaterial::find($map);
		}
    }

	public static function goCheck($ids,$num,$defective,$userInfo){
		$enter_material = ErpMaterialEnterMaterial::with(['material'=>function($query){return $query->field('id,name,sn');}])->where('id','in',$ids)->select();
		if($enter_material->isEmpty() || $enter_material->count() != count($ids)){
			return ['msg'=>'数据错误','code'=>201];
		}
		$update 		= [];
		
		foreach($enter_material as $key=>$vo){
			if(!isset($num[$vo['id']])){
				return ['msg'=>$vo['material']['sn'].'本次正品量不存在','code'=>201];
			}
			if(!$vo['can_check']){
				return ['msg'=>$vo['material']['sn'].'已全部品检或已作废','code'=>201];
			}
			$_num 			= $num[$vo['id']];
			if($_num != intval($_num) ){
				return ['msg'=>$vo['material']['sn'].'本次正品量必须为不等于0的整数','code'=>201];
			}
			$_defective 	= $defective[$vo['id']];
			if($_defective != intval($_defective) ){
				return ['msg'=>$vo['material']['sn'].'本次次品量必须为不等于0的整数','code'=>201];
			}			
			$check 			= $vo['stock_num']  - $vo['qualities_num'] - $vo['defective_num'] - $_num - $_defective;
			if($check < 0){
				return ['msg'=>$vo['material']['sn'].'已检正品量+已检次品量不可大于要求质检量','code'=>201];
			}
			
			$inspection		= $vo['inspection']? $vo['inspection']:[];
			$inspection[]	= ['create_time'=>time(),'quality_num'=>$_num,'defective_num'=>$_defective,'admin'=>$userInfo['name']];
			
			//if($check == 0){
				//$check_status	= ErpMaterialEnterMaterialEnum::CHECK_STATUS_FINISH;
			//}else{
				$check_status	= ErpMaterialEnterMaterialEnum::CHECK_STATUS_PART;
			//}
			if($vo['status'] == ErpMaterialEnterMaterialEnum::STATUS_RETURN){
				$status			= ErpMaterialEnterMaterialEnum::STATUS_PART;
			}else{
				$status 		= $vo['status'];
			}
			$update[] 			= ['id'=>$vo['id'],'check_username'=>$userInfo['name'],'check_date'=>date('Y-m-d'),'status'=>$status,'check_status'=>$check_status,'quality_num'=>$vo['quality_num']+$_num,'defective_num'=>$vo['defective_num']+$_defective,'qualities_num'=>$vo['qualities_num']+$_num,'inspection'=>$inspection];
		}
		try {
			(new ErpMaterialEnterMaterial)->saveAll($update);
		}catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}


    public static function goSetCode($id,$num=1)
    {
        $model 		= self::getOne($id);
        if(empty($model['id'])) {
			return ['msg'=>'数据不存在','code'=>201];
		}
        if(empty($model['report'])) {
			return ['msg'=>'报告不存在','code'=>201];
		}		
		$num		= $num>0?$num:1;
        try {
			$str 	= $model['material']['tag'].($model['material']['tag']?'-':'').date('ymd',strtotime($model['report']['inspection_date']));
			$record = ErpMaterialCode::where('code', 'like', $str . '%')->order('id desc')->find();
			
			if(empty($record['id'])){
				$count = 1;
			}else{
				$count = (int)ltrim(substr($record['code'],-4),'0') + 1;
			}

			$data 	= [];
			for($i=0;$i<$num;$i++){
				$code 	= $str.sprintf("%04d",$count+$i);
				$data[] = ['data_id'=>$model['id'],'data_type'=>'erp_material_enter_material','code'=>$code,'material_id'=>$model['material_id'],'purchase_order_id'=>$model['purchase_order_id'],'purchase_order_data_id'=>$model['purchase_order_data_id']];
			}
			(new ErpMaterialCode)->saveAll($data);
			
			return ['msg'=>'操作成功','code'=>200,'data'=>['code'=>array_column($data,'code')]];
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }


    public static function goCheckCode($code,$num=1)
    {
        $model 		= ErpMaterialCode::where('code',$code)->find();
        if(empty($model['id'])) {
			return ['msg'=>'输入的标签编号不存在！','code'=>201];
		}
        try {
			return ['msg'=>'操作成功','code'=>200,'data'=>['code'=>[$code]]];
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }



	public static function getReportCode(){
		return ErpMaterialEnterMaterialReport::getReportCode();
	}
	
    public static function goReport($data)
    {
        //验证
		$model 		= self::getOne($data['material_enter_material_id']);
        if(empty($model['id'])) {
			return ['msg'=>'数据不存在','code'=>201];
		}
		foreach($data as &$vo){
			if(is_array($vo)){
				$vo = implode(',',$vo);
			}
		}
		$report 	= ErpMaterialEnterMaterialReport::where('material_enter_material_id',$model['id'])->find();
		if(empty($report['id'])){
			$data['code'] 				= self::getReportCode();
			$data['material_stock_id']	= $model['material_stock_id'];
			$data['material_id']		= $model['material_id'];
			$report						= new ErpMaterialEnterMaterialReport;
		}
		unset($data['status']);
        try {
            $report->save($data);	
			return ['msg'=>'操作成功','code'=>200,'data'=>['code'=>$report['code']]];
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

	public static function goFinish($id){
		$model 						= self::getOne($id);
        if(empty($model['id'])) {
			return ['msg'=>'数据不存在','code'=>201];
		}
        if(empty($model->report)) {
			return ['msg'=>'请先保存报告','code'=>201];
		}	
		if(!$model->report['can_finish']){
			return ['msg'=>'状态错误','code'=>201];
		}
		try {
			$model->save(['check_status'=>ErpMaterialEnterMaterialEnum::CHECK_STATUS_FINISH,'defective_num'=>$model['stock_num'] - $model['qualities_num']]);
			$model->report->save(['status'=>2,'finish_time'=>time()]);
				
			if($model['defective_num'] > 0){
				ErpMaterialScrap::create(['stock_date'=>date('Y-m-d'),'stock_num'=>$model['defective_num'],'material_id'=>$model['material_id'],'enter_material_id'=>$model['id'],'remark'=>'检验不合格']);
			}
			
		}catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}
	
	public static function getGuideBook($query){
		$model = ErpGuideBook::withSearch(['query'],['query'=>$query])->find();
		return ['data'=>$model?$model->toArray():[]];
	}
	
	
	public static function goSetPrint($id){
		$model 						= self::getOne($id);
        if(empty($model['id'])) {
			return ['msg'=>'数据不存在','code'=>201];
		}
        if(empty($model->report)) {
			return ['msg'=>'请先保存报告','code'=>201];
		}	
		try {
			$model->report->save(['print'=>1]);
		}catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}	
	
	
	
}
