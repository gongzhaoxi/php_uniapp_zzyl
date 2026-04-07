<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\{ErpMaterialChange,ErpMaterialStock,ErpMaterialTree,ErpMaterialEnterMaterial,ErpDrawing};

class ErpMaterialChangeLogic extends BaseLogic{

	// 获取物料
    public static function getList($query=[],$limit=10,$sort='',$order='')
    {
		$map	 	= [];
		if(!empty($query['material_type'])) {
			$map[]	= ['c.material_type', '=', $query['material_type']];
        }
		if(!empty($query['data_type'])) {
			$map[]	= ['c.data_type', '=', $query['data_type']];
        }
		if(!empty($query['type'])) {
			$map[]	= ['c.type', '=', $query['type']];
        }	
        if(!empty($query['keyword'])) {
			$map[]	= ['b.sn|b.name|c.order_sn', 'like', '%' . $query['keyword'] . '%'];
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
        if(!empty($query['stock_create_admin'])) {
			$map[]		= ['c.create_admin', 'like', '%' . $query['stock_create_admin'] . '%'];
        }		
        if(!empty($query['create_admin'])) {
			$map[]		= ['a.create_admin', 'like', '%' . $query['create_admin'] . '%'];
        }	
        if(!empty($query['tree_id'])) {
			$map[]		= ['b.tree_id', 'in', ErpMaterialTree::where('path','find in set',$query['tree_id'])->column('id')];
        }	
		
        if(!empty($query['department'])) {
			$map[]	= ['c.department', 'like', '%' . $query['department'] . '%'];
        }		
		$orderby 		= 'a.id desc';
		if($sort == 'stock_date'){
			$orderby 	= 'c.stock_date '.($order=='asc'?'asc':'desc');
		}
		$field 			= 'a.*,b.sn,b.name,b.unit,c.material_type,c.data_type,c.type,c.order_sn,c.stock_date,c.create_admin as stock_create_admin,d.order_sn as sale_order_sn,c.department';		
        $list 			= ErpMaterialChange::alias('a')->with(['supplier'=>function($query){return $query->field('id,name');}])
		->join('erp_material b','a.material_id = b.id','LEFT')->join('erp_material_stock c','a.material_stock_id = c.id','LEFT')
		->join('erp_order d','c.order_id = d.id','LEFT')->where($map)->field($field)->order($orderby)->append(['material_type_desc','data_type_desc','type_desc','photo'])->paginate($limit);
        
		$data 	 		= $list->items();
		$tmp			= ErpMaterialEnterMaterial::alias('a')
		->join('erp_material_stock b','a.material_stock_id = b.id','LEFT')
		->join('erp_purchase_order c','a.purchase_order_id = c.id','LEFT')
		->where('b.order_sn','in',array_column($data,'enter_order_sn'))
		->where('a.purchase_order_id','>',0)->field('b.order_sn,c.order_sn as purchase_order_sn')
		->group('a.material_stock_id,a.purchase_order_id')->select();
		$purchase_order	= [];
		foreach($tmp as $item){
			$purchase_order[$item['order_sn']][] = $item['purchase_order_sn'];
		}
		
		$tmp 			= ErpDrawing::where('sn','in',$data?array_column($data,'sn'):'')->where('status',1)->where('final_pic','<>','')->field('id,sn,final_pic')->select();
		$drawing 		= [];
		foreach($tmp as $item){
			$drawing[$item['sn']][] = $item['final_pic'];
		}
		
		foreach($data as &$vo){
			if(empty($purchase_order[$vo['enter_order_sn']])){
				$vo['purchase_order'] = '';
			}else{
				$vo['purchase_order'] = implode(',',$purchase_order[$vo['enter_order_sn']]);
			}
			if(empty($drawing[$vo['sn']])){
				$vo['final_pic'] = [];
			}else{
				$vo['final_pic'] = $drawing[$vo['sn']];
			}			
		}	
		
		return ['code'=>0,'data'=>$data ,'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

	public static function goAdd($list){
		$add 					= [];
		foreach($list as $vo){
			$data 						= $vo;
			$num 						= $vo['num'];
			$material					= empty($vo['material'])?[]:$vo['material'];
			$data['stock_num'] 			= $num;
			$data['before_num'] 		= $material['stock'];
			$data['before_freeze_num'] 	= $material['freeze_stock'];
			$data['after_num'] 			= $material['stock'];
			$data['after_freeze_num'] 	= $material['freeze_stock'];
			$data['create_admin'] 		= empty(self::$adminUser['username'])?'':self::$adminUser['username'];
			$data['warehouse_id'] 		= empty($vo['warehouse_id'])?0:$vo['warehouse_id'];
			if($data['stock_type'] == 1){
				$data['after_num'] 			= $material['stock']+$num;
			}else{
				$data['after_freeze_num'] 	= $material['freeze_stock']+$num;
			}
			unset($data['material']);
			$add[] 				= $data;
		}
		
		(new ErpMaterialChange)->saveAll($add);
	}

	public static function getStockCreateAdmin(){
		return ErpMaterialStock::distinct(true)->field('create_admin')->select();
	}

	public static function getCreateAdmin(){
		return ErpMaterialChange::distinct(true)->field('create_admin')->select();
	}

}
