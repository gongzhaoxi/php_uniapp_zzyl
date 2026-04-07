<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\{ErpMaterialWarehouse,ErpMaterial};
use app\admin\validate\ErpMaterialWarehouseValidate;
use think\facade\Db;


class ErpMaterialWarehouseLogic extends BaseLogic{

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$field 			= 'a.id,a.stock,a.safety_stock,a.min_stock,a.max_stock,a.material_id,a.warehouse_id,b.type,b.name,b.sn,b.unit,b.material,b.surface,b.color,b.remark,b.processing_type,c.name as warehouse_name,d.name as category_name';	
		$query['_alias']= 'a';
		$query['_material_alias']= 'b';
		$query['_warehouse_alias']= 'c';
        $list 			= ErpMaterialWarehouse::alias('a')
		->join('erp_material b','a.material_id = b.id','LEFT')->withSearch(['query'],['query'=>$query])
		->join('erp_warehouse c','a.warehouse_id = c.id','LEFT')
		->join('dict_data d','b.cid = d.id','LEFT')
		->whereNotNull('b.id')
		->field($field)->order('a.id','desc')->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

	public static function getExportCount($query=[]){
		$field 			= 'a.id';	
		$query['_alias']= 'a';
		$query['_material_alias']= 'b';
        $count 			= ErpMaterialWarehouse::alias('a')->join('erp_material b','a.material_id = b.id','LEFT')->withSearch(['query'],['query'=>$query])->field($field)->order('a.id','desc')->count();
		return ['data'=>['count'=>$count,'key'=>rand_string()]];
	}
	
	public static function getExport($query=[],$limit=10000){
		$limit				= $limit>10000?10000:$limit;
		$data				= self::getList($query,$limit)['data'];
		$return				= ['column'=>[],'setWidh'=>[],'keys'=>[],'list'=>$data,'image_fields'=>[]];
		$field 				= ['sn'=>'物料编码','name'=>'物料名称','type'=>'物料类型','safety_stock'=>'安全库存','min_stock'=>'最低库存','max_stock'=>'最高库存','stock'=>'现有库存量','warehouse_name'=>'仓位','unit'=>'单位','processing_type'=>'加工类型','material'=>'材料','surface'=>'表面','color'=>'颜色','remark'=>'备注'];
		foreach($field as $key=>$vo){
			$return['column'][] 	= $vo;
			$return['setWidh'][] 	= 10;
			$return['keys'][] 		= $key;				
		}
        return $return;	
	}


    public static function goUpdateStock($data,$stock_type)
    {
		$tmp					= ErpMaterialWarehouse::where('material_id','in',array_column($data,'material_id'))->where('warehouse_id','in',array_column($data,'warehouse_id'))->field('id,stock,freeze_stock,material_id,warehouse_id')->select();
		$warehouse				= [];
		foreach($tmp as $vo){
			$warehouse[$vo['material_id'].'-'.$vo['warehouse_id']] = $vo->toArray();
		}
		$material 				= ErpMaterial::where('id','in',array_column($data,'material_id'))->column('id,stock,freeze_stock','id');
		$material_change		= [];
		
		if($stock_type == 2){
			$stock_key 			= 'freeze_stock';
		}else{
			$stock_key 			= 'stock';
		}
		
		foreach($data as $vo){
			$material_change[]							= ['stock_type'=>$stock_type,'enter_order_sn'=>$vo['enter_order_sn'],'enter_batch_number'=>$vo['enter_batch_number'],'num'=>$vo['num'],'material'=>$material[$vo['material_id']],'material_id'=>$vo['material_id'],'warehouse_id'=>$vo['warehouse_id'],'material_stock_id'=>$vo['material_stock_id'],'supplier_id'=>$vo['supplier_id']];
			
			$key 										= $vo['material_id'].'-'.$vo['warehouse_id'];
			if(empty($warehouse[$key])){
				$warehouse[$key] 						= ['stock'=>0,'freeze_stock'=>0,'material_id'=>$vo['material_id'],'warehouse_id'=>$vo['warehouse_id']];
			}
			
			$warehouse[$key][$stock_key] 				= $warehouse[$key][$stock_key] + $vo['num'];

			$material[$vo['material_id']][$stock_key] 	= $material[$vo['material_id']][$stock_key] + $vo['num'];
		}
		
		(new ErpMaterialWarehouse)->saveAll(array_values($warehouse));
		(new ErpMaterial)->saveAll(array_values($material));	
		
		ErpMaterialChangeLogic::goAdd($material_change);
    }


	public static function goEditSafetyStock($data){
		ErpMaterialWarehouse::where('id',$data['id'])->update($data);
	}


	//获取物料库存
    public static function getMaterialStock($warehouse_type=1,$query=[],$limit=10)
    {
		$field 					= 'id,status,type,name,sn,cid,unit';
        $list 					= ErpMaterial::withSearch(['query'],['query'=>$query])->field($field)->order('id','desc')->paginate($limit)->each(function($item,$index){
			$enter 				= ErpMaterialEnterMaterial::alias('a')
			->join('erp_material_stock b','a.material_stock_id = b.id','LEFT')
			->join('erp_warehouse c','a.warehouse_id = c.id','LEFT')
			->fieldRaw('a.id,a.can_out_num,a.freeze_out_stock,a.can_out_num - a.freeze_out_stock as num,b.order_sn')
			->whereRaw('a.can_out_num>a.freeze_out_stock')->where('a.can_out_num','>',0)->where('c.type','=',$warehouse_type)->where('a.material_id','=',$item['id'])->select();
			$freeze_out_stock	= ErpMaterialEnterMaterial::alias('a')->join('erp_warehouse c','a.warehouse_id = c.id','LEFT')->where('a.freeze_out_stock','>',0)->where('a.material_id','=',$item['id'])->sum('a.freeze_out_stock');
			$can_out_num		= ErpMaterialEnterMaterial::alias('a')->join('erp_warehouse c','a.warehouse_id = c.id','LEFT')->where('a.can_out_num','>',0)->where('a.material_id','=',$item['id'])->sum('a.can_out_num');
			$item['enter']		= $enter;
			$item['enter_sn'] 	= implode(',', $enter->column('order_sn'));
			$item['stock']		= $can_out_num - $freeze_out_stock;
			$item['freeze_out_stock']	= $freeze_out_stock;
		});
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit],'count' => $list->total(), 'limit' => $limit];
    }


    // 删除
    public static function goShow($is_show,$ids)
    {
        try{
			ErpMaterialWarehouse::where('id','in',$ids)->update(['is_show'=>$is_show]);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpMaterialWarehouse::where($map)->find();
		}else{
			return ErpMaterialWarehouse::find($map);
		}
    }




    // 添加
    public static function goAdd($data)
    {
        //验证
        $validate = new MaterialWarehouseValidate;
        if(!$validate->scene('add')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
        try {
			$data['admin_id'] = self::$adminUser['id'];
            MaterialWarehouse::create($data);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
    

	
    // 编辑
    public static function goEdit($data)
    {
        //验证
        $validate 	= new MaterialWarehouseValidate;
        if(!$validate->scene('edit')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		$model 		= self::getOne($data['id']);
        if($model->isEmpty()) {
			return ['msg'=>'数据不存在','code'=>201];
		}
        try {
            $model->save($data);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }






}
