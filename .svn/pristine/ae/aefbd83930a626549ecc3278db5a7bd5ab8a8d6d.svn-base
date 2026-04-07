<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\ErpMaterialScrapOut;
use app\common\model\ErpMaterialScrap;
use app\admin\validate\ErpMaterialScrapValidate;
use think\facade\Db;
use app\common\model\{ErpSupplier};

class ErpMaterialScrapOutLogic extends BaseLogic{

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$field 		= 'a.id,a.stock_date,a.material_id,a.stock_num,a.order_sn,a.remark,a.supplier_id,b.sn,b.name';	
		$query['_alias']= 'a';
		$query['_material_alias']= 'b';
		$list 		= ErpMaterialScrapOut::alias('a')
		->join('erp_material b','a.material_id = b.id','LEFT')
		->withSearch(['query'],['query'=>$query])->field($field)->order('a.id','desc')->paginate($limit);
		$data 		= $list->items();
		$supplier 	= ErpSupplier::where('id','in',implode(',',array_column($data,'supplier_id')))->column('name','id');
		foreach($data as &$vo){
			$vo['supplier_name']= empty($supplier[$vo['supplier_id']])?'':$supplier[$vo['supplier_id']];
		}
        return ['code'=>0,'data'=>$data,'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

	public static function getCount($query=[]){
		$query['_alias']= 'a';
		$query['_material_alias']= 'b';
        return ErpMaterialScrapOut::alias('a')
		->join('erp_material b','a.material_id = b.id','LEFT')
		->withSearch(['query'],['query'=>$query])->count();
	}
	
	public static function getExport($query=[],$limit=10000){
		$limit				= $limit>10000?10000:$limit;
		$data				= self::getList($query,$limit)['data'];
		$return				= ['column'=>[],'setWidh'=>[],'keys'=>[],'list'=>$data,'image_fields'=>[]];
		$field 				= ['order_sn'=>'退货单编号','stock_date'=>'退货日期','remark'=>'报废原因','supplier_name'=>'供应商','sn'=>'物料编码','name'=>'物料名称','stock_num'=>'出库数量'];
		foreach($field as $key=>$vo){
			$return['column'][] 	= $vo;
			$return['setWidh'][] 	= 10;
			$return['keys'][] 		= $key;				
		}
        return $return;	
	}


    // 添加
    public static function goAdd($param)
    {
        //验证
        $validate 	= new ErpMaterialScrapValidate;
        if(!$validate->scene('add')->check($param)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		$material	= $param['material'];
		$scrap		= ErpMaterialScrap::where('id','in',implode(',',array_column($param['material'],'scrap_id')))->whereRaw('stock_num>stocked_num')->column('id,stock_num,stocked_num,stock_num-stocked_num as num','id');
		$data 		= [];
		$update 	= [];
		
		$count 		= ErpMaterialScrapOut::whereDay('create_time')->count() + 1;
		$order_sn 	= 'TH'.date('Ymd').sprintf("%03d",$count);

		foreach($material as $vo){
			if(empty($vo['scrap_id'])){
				return ['msg'=>'请选择报废明细','code'=>201];
			}
			$stock_num			= $vo['stock_num']	;
			$scrap_id			= explode(',',$vo['scrap_id']);
			foreach($scrap_id as $v){
				if(empty($scrap[$v])){
					return ['msg'=>'报废明细错误','code'=>201];
				}
				if($scrap[$v]['num'] >=  $stock_num){							
					$update[] 			= ['id'=>$scrap[$v]['id'],'stocked_num'=>Db::raw('stocked_num+'.$stock_num)];
					$data[] 			= ['scrap_id'=>$scrap[$v]['id'],'order_sn'=>$order_sn,'create_admin'=>self::$adminUser['username'],'material_id'=>$vo['material_id'],'stock_num'=>$stock_num,'remark'=>$vo['remark']??$param['remark'],'stock_date'=>$param['stock_date'],'supplier_id'=>$param['supplier_id']];
					$stock_num			= 0;
					$scrap[$v]['num']	= $scrap[$v]['num'] - $stock_num; 
					break;
				}else{
					$update[] 			= ['id'=>$scrap[$v]['id'],'stocked_num'=>Db::raw('stocked_num+'.$scrap[$v]['num'])];
					$data[] 			= ['scrap_id'=>$scrap[$v]['id'],'order_sn'=>$order_sn,'create_admin'=>self::$adminUser['username'],'material_id'=>$vo['material_id'],'stock_num'=>$scrap[$v]['num'],'remark'=>$vo['remark']??$param['remark'],'stock_date'=>$param['stock_date'],'supplier_id'=>$param['supplier_id']];
					$stock_num			= $stock_num - $scrap[$v]['num'];
					$scrap[$v]['num']	= 0; 
				}
			}
			
			if($stock_num > 0){
				return ['msg'=>'出库数量不足','code'=>201];
			}
		}	
		
        try {
			(new ErpMaterialScrapOut)->saveAll($data);
			(new ErpMaterialScrap)->saveAll($update);
			
			return ['msg'=>'创建成功','code'=>200,'data'=>['order_sn'=>$order_sn]];
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
	
	
	public static function goRemove($id){
		try {
			$data 			= ErpMaterialScrapOut::where('id','in',$id)->select();
			$update 		= [];
			foreach($data as $vo){
				$update[] 	= ['id'=>$vo['scrap_id'],'stocked_num'=>Db::raw('stocked_num-'.$vo['stock_num'])];
			}
			
			(new ErpMaterialScrap)->saveAll($update);
			
			ErpMaterialScrapOut::where('id','in',$id)->delete();
			
		}catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}
	
	
    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpMaterialScrapOut::where($map)->find();
		}else{
			return ErpMaterialScrapOut::find($map);
		}
    }

}
