<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\ErpOrderProductBom;
use app\common\model\ErpOrderProduct;
use app\common\model\ErpProductBom;
use app\common\model\ErpProduct;
use app\common\model\ErpOrder;
use app\common\model\ErpOrderLog;
use app\common\model\ErpMaterial;
use app\common\model\{ErpProductProject,ErpOrderAccessory,ErpOrderRemark,ErpProductStock};
use app\admin\validate\ErpOrderProductValidate;
use app\admin\validate\ErpOrderProductBomValidate;
use think\facade\Db;
use app\common\enum\ErpOrderLogEnum;

class ErpOrderProductLogic extends BaseLogic{

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$field = 'id,status,region_type,name,contacts,phone,address,address_en,region_type,sn';
        $list = ErpOrderProduct::withSearch(['query'],['query'=>$query])->field($field)->order('id','desc')->append(['status_desc','region_type_desc'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

	 public static function getBomData($product_id,$replace,$change,$add){ 
		$project_info	= [];
		$change_id 		= [];
		$add_id 		= [];
		if($change){
			foreach($change['id'] as $k=>$vo){
				if(!in_array($vo,$change_id)){
					$change_id[]		= $vo;
					$project_info[$vo]	= ['price'=>$change['price'][$k],'num'=>$change['num'][$k]];
				}
			}
		}

		if($add){
			foreach($add['id'] as $k=>$vo){
				if(!in_array($vo,$add_id)){
					$add_id[]			= $vo;
					$project_info[$vo]	= ['price'=>$add['price'][$k],'num'=>$add['num'][$k]];
				}
			}
		}
 
		$bom_data 					= [];
		$bom_delete					= [];
		$accessory					= [];
		
		if($change_id){
			$change_project 		= ErpProductProject::with(['bom.material'])->where('id','in',$change_id)->select();
			foreach($change_project as $v){
				$num 				= empty($project_info[$v['cid']]['num'])?1:$project_info[$v['cid']]['num'];
				foreach($v['bom'] as $vo){				
					if(($vo['data_type'] == 2 || $vo['data_type'] == 4) && $vo['product_id'] == $product_id && $vo['material'] && $vo['material']['status']){
						if($vo['data_type'] == 4){
							$bom_delete[$vo['material_id']] = $vo['num']*$num;
						}else{
							$bom_data[] = ['type'=>2,'order_product_id'=>0,'order_id'=>0,'product_bom_id'=>$vo['id'],'material_id'=>$vo['material_id'],'material'=>$vo['material'],'num'=>$vo['num']*$num,'bill_type'=>$vo['bill_type']];
						}
					}
				}
				$accessory[]		= ['product_num'=>$num,'product_name'=>$v['name'],'product_price'=>empty($project_info[$v['cid']]['price'])?0:$project_info[$v['cid']]['price']];
			}
		}
		
		$ids 					= [];
		if(!empty($replace['replace_bom_id']) && !empty($replace['replace_bom_value'])) {
			$replace_data 		= ErpProductBom::with(['material'])->alias('a')->join('erp_material b','a.material_id = b.id','LEFT')->field('a.*,b.status,b.sn,b.name,b.type as material_type')->where('a.product_id',$product_id)->where('a.data_type',1)->where('a.can_replace',1)->where('b.status',1)->where('a.id','in',$replace['replace_bom_id'])->select()->toArray();
			$bom_replace_tmp 	= ErpProductBom::with(['material'])->alias('a')->join('erp_material b','a.material_id = b.id','LEFT')->where('a.product_id',$product_id)->where('a.data_type',2)->where('a.id','in',$replace['replace_bom_value'])->field('a.*,b.status,b.sn,b.name,b.type as material_type')->select()->toArray();
			$bom_replace		= [];
			foreach($bom_replace_tmp as $vvv){
				$bom_replace[$vvv['id']]=  $vvv;
				$accessory[]	= ['product_name'=>$vvv['material']['name']];
			}
			
			foreach($replace_data as $vo){
				if(!$vo['status']){
					return ['msg'=>$vo['sn'].'已被禁用','code'=>201];
				}				
				if(!$vo['can_replace']){
					return ['msg'=>$vo['sn'].'不能替换物料','code'=>201];
				}
				if(empty($replace['replace_bom_value'][$vo['id']])){
					return ['msg'=>$vo['sn'].'请选择要替换物料','code'=>201];
				}
				if(empty($replace['replace_bom_bill_type'][$vo['id']])){
					return ['msg'=>$vo['sn'].'请选择类型','code'=>201];
				}	
				if(empty($bom_replace[$replace['replace_bom_value'][$vo['id']]])){
					return ['msg'=>'替换物料不存在','code'=>201];
				}
				$ids[]			= $vo['id'];
				$tmp 			= $bom_replace[$replace['replace_bom_value'][$vo['id']]];				
				$bom_data[]		= ['type'=>2,'order_product_id'=>0,'order_id'=>0,'product_bom_id'=>$tmp['id'],'material_id'=>$tmp['material_id'],'material'=>$tmp['material'],'num'=>$tmp['num'],'bill_type'=>$replace['replace_bom_bill_type'][$vo['id']]];
			}
		}
		if($ids){
			$list 				= ErpProductBom::with(['material'])->alias('a')->join('erp_material b','a.material_id = b.id','LEFT')->field('a.*')->where('a.product_id',$product_id)->where('a.data_type',1)->where('b.status',1)->where('a.id','not in',$ids)->select()->toArray();
		}else{
			$list 				= ErpProductBom::with(['material'])->alias('a')->join('erp_material b','a.material_id = b.id','LEFT')->field('a.*')->where('a.product_id',$product_id)->where('a.data_type',1)->where('b.status',1)->select()->toArray();
		}
		
		foreach($list as $vo){
			if(!empty($bom_delete[$vo['material_id']]) && $bom_delete[$vo['material_id']] > 0){
				$num 			= $vo['num'] - $bom_delete[$vo['material_id']];
			}else{
				$num 			= $vo['num'];
			}
			if($num && $num > 0){
				$bom_data[]		= ['type'=>1,'order_product_id'=>0,'order_id'=>0,'product_bom_id'=>$vo['id'],'material'=>$vo['material'],'material_id'=>$vo['material_id'],'num'=>$num,'bill_type'=>$vo['bill_type']];
			}
		}

		if($add_id){
			$add_project 		= ErpProductProject::with(['bom.material'])->where('id','in',$add_id)->select();
			foreach($add_project as $v){
				$num 			= empty($project_info[$v['cid']]['num'])?1:$project_info[$v['cid']]['num'];
				foreach($v['bom'] as $vo){
					if($vo['data_type'] == 3 && $vo['product_id'] == $product_id && $vo['material'] && $vo['material']['status']){
						$bom_data[] 	= ['type'=>3,'order_product_id'=>0,'order_id'=>0,'product_bom_id'=>$vo['id'],'material_id'=>$vo['material']['id'],'material'=>$vo['material'],'num'=>$vo['num']*$num,'bill_type'=>$vo['bill_type']];
					}
				}
				$accessory[]	= ['product_num'=>$num,'product_name'=>$v['name'],'product_price'=>empty($project_info[$v['cid']]['price'])?0:$project_info[$v['cid']]['price']];
			}
		}

		
		return ['code'=>200,'data'=>$bom_data,'accessory'=>$accessory,'change_id'=>$change_id,'add_id'=>$add_id,'project_price'=>$project_info];
	 }


    // 添加
    public static function goAdd($data,$replace,$change,$add)
    {
        //验证
        $validate 		= new ErpOrderProductValidate;
        if(!$validate->scene('add')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		$product 		= ErpProduct::where('id',$data['product_id'])->find();
		if(empty($product['id'])) {
			return ['msg'=>'产品不存在','code'=>201];
		}
		$order 			= ErpOrder::where('id',$data['order_id'])->find();
		if(empty($order['id'])) {
			return ['msg'=>'订单不存在','code'=>201];
		}
		if(!$order->can_save_product){
			return ['msg'=>'该订单不能添加/修改产品','code'=>201];
		}
		$data['product_unit'] 	= $product['unit'];
		$data['replace_info'] 	= $replace;


		$check 					= self::getBomData($data['product_id'],$replace,$change,$add);
		if($check['code'] != 200){
			return $check;
		}
		$data['add_project'] 	= $check['add_id'];
		$data['change_project'] = $check['change_id'];
		$data['project_price'] 	= $check['project_price'];
		
		$bom_data				= $check['data'];
		$accessory				= $check['accessory'];

		
		Db::startTrans();
        try {
			$model 									= ErpOrderProduct::create($data);
			foreach($bom_data as $key=>$vo){
				$bom_data[$key]['order_product_id']	= $model->id;
				$bom_data[$key]['order_id']			= $order->id;
			}
			(new ErpOrderProductBom)->saveAll($bom_data);

			foreach($accessory as $key=>$vo){
				$accessory[$key]['order_product_id']	= $model->id;
				$accessory[$key]['order_id']			= $order->id;
			}
			(new ErpOrderAccessory)->saveAll($accessory);

			ErpOrderLogic::updateAmount($order->id);

			$log	= ['log'=>'添加产品：'.$model['product_sn'].'  '.$model['product_name'],'data_type'=>ErpOrderLogEnum::ORDER_PRODUCT_ADD,'data_id'=>$model['id'],'order_id'=>$model['order_id'],'operator'=>self::$adminUser['username']];
			ErpOrderLog::create($log);

			Db::commit();
        }catch (\Exception $e){
			Db::rollback();
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
    
    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpOrderProduct::where($map)->find();
		}else{
			return ErpOrderProduct::find($map);
		}
    }
	
    // 编辑
    public static function goEdit($data)
    {
        //验证
        $validate 	= new ErpOrderProductValidate;
        if(!$validate->scene('edit')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		$model 		= self::getOne($data['id']);
        if(empty($model['id'])) {
			return ['msg'=>'数据不存在','code'=>201];
		}
		//$order 		= $model->order;
		//if(!$order['can_save_product']){
			//return ['msg'=>'当前订单不能修改产品','code'=>201];
		//}		
		
		Db::startTrans();
        try {
			$log 			= [];
			$filed_check   	= ['product_num'=>'数量','product_model'=>'型号','color'=>'颜色','product_specs'=>'款式','currency'=>'币种','exchange_rates'=>'汇率','tax_rate'=>'税率','product_price'=>'单价','total_price'=>'总价'];
			foreach($filed_check as $k=>$vo){
				if($data[$k] != $model[$k]){
					$log[]	= ['log'=>$vo.'从`'.$model[$k].'`到`'.$data[$k].'`','data_type'=>'order_product_filed_change','order_product_id'=>$model['id'],'order_id'=>$model['order_id'],'operator'=>self::$adminUser['username']];
				}
			}
            $model->save($data);
			if($log){
				(new ErpOrderLog)->saveAll($log);
			}
			
			ErpOrderLogic::updateAmount($model['order_id']);

			Db::commit();
        }catch (\Exception $e){
			Db::rollback();
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
	


	
	public static function goSaveBom($id,$replace,$change,$add)
    {
		$model 		= self::getOne($id);
        if(empty($model['id'])) {
			return ['msg'=>'数据不存在','code'=>201];
		}
		$order 		= $model->order;
		if(!$order['can_save_product']){
			return ['msg'=>'当前订单不能修改产品','code'=>201];
		}
		$check 			= self::getBomData($model['product_id'],$replace,$change,$add);
		if($check['code'] != 200){
			return $check;
		}
	
		$change_old 	= ErpProductProject::alias('a')->join('dict_data b','a.cid = b.id','LEFT')->where('a.id','in',$model['change_project'])->column('a.id,a.cid,a.name,b.name as category','a.cid');
		$change_new 	= ErpProductProject::alias('a')->join('dict_data b','a.cid = b.id','LEFT')->where('a.id','in',$check['change_id'])->column('a.id,a.cid,a.name,b.name as category','a.cid');
		$add_old 		= ErpProductProject::alias('a')->join('dict_data b','a.cid = b.id','LEFT')->where('a.id','in',$model['add_project'])->column('a.id,a.cid,a.name,b.name as category','a.cid');
		$add_new 		= ErpProductProject::alias('a')->join('dict_data b','a.cid = b.id','LEFT')->where('a.id','in',$check['add_id'])->column('a.id,a.cid,a.name,b.name as category','a.cid');
		$log 			= [];
		foreach($change_old as $vo){
			if(!in_array($vo['id'],$check['change_id'])){
				if(empty($change_new[$vo['cid']])){
					//删除
					$log[]	= '删除选配置：'.$vo['category'].'('.$vo['name'].')';
				}else{
					//修改
					$log[]	= '修改选配置：'.$vo['category'].'从`'.$vo['name'].'`到`'.$change_new[$vo['cid']]['name'].'`';
				}
			}
		}
		foreach($change_new as $vo){
			if(!in_array($vo['id'],$model['change_project']) && empty($change_old[$vo['cid']])){
				//新增
				$log[]		= '新增选配置：'.$vo['category'].'('.$vo['name'].')';
			}
		}
		
		foreach($add_old as $vo){
			if(!in_array($vo['id'],$check['add_id'])){
				if(empty($add_new[$vo['cid']])){
					//删除
					$log[]	= '删除加配置：'.$vo['category'].'('.$vo['name'].')';
				}else{
					//修改
					$log[]	= '修改加配置：'.$vo['category'].'从`'.$vo['name'].'`到`'.$add_new[$vo['cid']]['name'].'`';
				}
			}
		}
		foreach($add_new as $vo){
			if(!in_array($vo['id'],$model['add_project']) && empty($add_old[$vo['cid']])){
				//新增
				$log[]		= '新增加配置：'.$vo['category'].'('.$vo['name'].')';
			}
		}
		
		$bom_data		= $check['data'];
		Db::startTrans();
        try {
			ErpOrderProductBom::destroy(function($query) use($id){
				$query->where('order_product_id','=',$id);
			});
			
			foreach($bom_data as $key=>$vo){
				$bom_data[$key]['order_product_id']	= $model->id;
				$bom_data[$key]['order_id']			= $order->id;
			}
			(new ErpOrderProductBom)->saveAll($bom_data);

			if($log){
				ErpOrderLog::create(['log'=>implode('；',$log),'data_type'=>ErpOrderLogEnum::ORDER_PRODUCT_BOM_CHANGE,'order_product_id'=>$model['id'],'order_id'=>$model['order_id'],'operator'=>self::$adminUser['username']]);
			}
			
			$model->save(['add_project'=>$check['add_id'],'change_project'=>$check['change_id'],'replace_info'=>$replace,'project_price'=>$check['project_price']]);
			
			Db::commit();
        }catch (\Exception $e){
			Db::rollback();
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }		
		/*
		$log 			= [];
        //验证
        $validate 		= new ErpOrderProductBomValidate;
        if(!$validate->scene('add')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		$order_product 	= self::getOne($data['order_product_id']);
		if(empty($order_product['id'])){
			return ['msg'=>'订单商品不存在','code'=>201];
		}	
		$order 			= $order_product->order;
		if(!$order['can_save_product']){
			return ['msg'=>'当前订单不能修改产品','code'=>201];
		}	

		if($data['type'] == 2){
			$bom1 		= ErpProductBom::alias('a')->join('erp_material b','a.material_id = b.id','LEFT')->field('a.*,b.status,b.sn,b.name,b.type as material_type')->where('a.id',$data['product_bom_id'])->where('a.data_type',1)->where('b.status',1)->find();	
		}else if($data['type'] == 1){
			$bom1 		= ErpProductBom::alias('a')->join('erp_material b','a.material_id = b.id','LEFT')->field('a.*,b.status,b.sn,b.name,b.type as material_type')->where('a.id',$data['product_bom_id'])->where('a.data_type',1)->where('b.status',1)->find();	
		}else{
			$bom1 		= ErpProductBom::alias('a')->join('erp_material b','a.material_id = b.id','LEFT')->field('a.*,b.status,b.sn,b.name,b.type as material_type')->where('a.id',$data['product_bom_id'])->where('a.data_type',3)->where('b.status',1)->find();	
		}
		
		if(empty($bom1['id'])){
			return ['msg'=>'数据错误1','code'=>201];
		}			
		$data['material_id'] 			= $bom1['material_id'];
		$data['material'] 				= $bom1->toArray();
		$data['actually_product_bom_id']= $bom1['id'];
		$data['actually_material_id'] 	= $bom1['material_id'];

		if($data['type'] == 2){
			if(!$bom1['can_replace']){
				return ['msg'=>'该物料不能替换其他物料','code'=>201];
			}
			$bom2 						= ErpProductBom::alias('a')->join('erp_material b','a.material_id = b.id','LEFT')->field('a.*,b.status,b.sn,b.name,b.type as material_type')->where('a.id',$data['replace_product_bom_id'])->where('a.data_type',2)->where('b.status',1)->find();
			if(empty($bom2['id'])){
				return ['msg'=>'数据错误3','code'=>201];
			}
			$data['replace_material_id']	= $bom2['material_id'];
			$data['replace_material'] 		= $bom2->toArray();
			$data['actually_product_bom_id']= $bom2['id'];
			$data['num']					= empty($data['num'])?$bom2['num']:$data['num'];
			$data['actually_material_id'] 	= $bom2['material_id'];
		}
		
		if(empty($data['id'])){
			if($data['type'] == 2){
				$model	= ErpOrderProductBom::where('type','in','1,2')->where('order_product_id',$data['order_product_id'])->where('product_bom_id',$data['product_bom_id'])->find();
			}else if($data['type'] == 1){
				
				
			}else{
				$model	= ErpOrderProductBom::where('type',$data['type'])->where('bill_type',$data['bill_type'])->where('order_product_id',$data['order_product_id'])->where('product_bom_id',$data['product_bom_id'])->find();
			}
			if(empty($model['id'])) {
				$model	= new ErpOrderProductBom;
			}
		}else{
			$model		= ErpOrderProductBom::where('id',$data['id'])->where('type',$data['type'])->where('order_product_id',$data['order_product_id'])->where('product_bom_id',$data['product_bom_id'])->find();
			if(empty($model['id'])) {
				return ['msg'=>'数据不存在','code'=>201];
			}
		}
		unset($data['id']);
		$product_bill_type = get_dict_data('product_bill_type');
		
		if($data['type'] == 2){			
			$log		= ['log'=>'加 换配置：'.$data['material']['sn'].'  '.$data['material']['name'].'  换成  '.$data['replace_material']['sn'].'  '.$data['replace_material']['name'].'','data_type'=>ErpOrderLogEnum::ORDER_PRODUCT_BOM_REPLACE,'order_product_id'=>$order_product['id'],'order_id'=>$order_product['order_id'],'operator'=>self::$adminUser['username']];		
		}
		if($data['type'] == 3){
			$log		= ['log'=>'加 加配置：'.$data['material']['sn'].'  '.$data['material']['name'].'  数量：'.$data['num'],'data_type'=>ErpOrderLogEnum::ORDER_PRODUCT_BOM_ADD,'order_product_id'=>$order_product['id'],'order_id'=>$order_product['order_id'],'operator'=>self::$adminUser['username']];
		}

		Db::startTrans();
        try {
			if($model->save($data) && $log){
				$log['data_id'] = $model->id;
				ErpOrderLog::create($log);
			}
			Db::commit();
        }catch (\Exception $e){
			Db::rollback();
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
		*/
    }
	

    public static function goRemoveBom($id)
    {
		//验证
        $validate 		= new ErpOrderProductBomValidate;
        if(!$validate->scene('remove')->check(['id'=>$id])){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
        try{
			$model		= ErpOrderProductBom::where('id',$id)->find();
			if(empty($model['id'])) {
				return ['msg'=>'数据不存在','code'=>201];
			}
			$order 		= $model->order;
			if(!$order['can_save_product']){
				return ['msg'=>'当前订单不能删除产品物料','code'=>201];
			}
			$log	= ['log'=>'删 配置：'.$model['material']['sn'].'  '.$model['material']['name'].'  数量：'.$model['num'],'data_type'=>ErpOrderLogEnum::ORDER_PRODUCT_BOM_ADD_DELETE,'data_id'=>$model['id'],'order_product_id'=>$model['order_product_id'],'order_id'=>$model['order_id'],'operator'=>self::$adminUser['username']];
			$model->delete();
			
			/*
			if($model['type'] == 3 || $model['type'] == 4){
				$log	= ['log'=>'删 加配置：'.$model['material']['sn'].'  '.$model['material']['name'].'  数量：'.$model['num'],'data_type'=>ErpOrderLogEnum::ORDER_PRODUCT_BOM_ADD_DELETE,'data_id'=>$model['id'],'order_product_id'=>$model['order_product_id'],'order_id'=>$model['order_id'],'operator'=>self::$adminUser['username']];
				$model->delete();
			}else if($model['type'] == 2){
				$log	= ['log'=>'删 换配置：'.$model['replace_material']['sn'].'  '.$model['replace_material']['name'],'data_type'=>ErpOrderLogEnum::ORDER_PRODUCT_BOM_REPLACE_DELETE,'data_id'=>$model['id'],'order_product_id'=>$model['order_product_id'],'order_id'=>$model['order_id'],'operator'=>self::$adminUser['username']];
				$model->save(['type'=>1,'replace_product_bom_id'=>0,'replace_material'=>[],'replace_material_id'=>0,'num'=>$model['material']['num'],'bill_type'=>$model['material']['bill_type'],'actually_product_bom_id'=>$model['product_bom_id'],'actually_product_bom_id'=>$model['material']['material_id']]);
			}else{
				$log	= ['log'=>'删 标配：'.$model['replace_material']['sn'].'  '.$model['replace_material']['name'],'data_type'=>ErpOrderLogEnum::ORDER_PRODUCT_BOM_REPLACE_DELETE,'data_id'=>$model['id'],'order_product_id'=>$model['order_product_id'],'order_id'=>$model['order_id'],'operator'=>self::$adminUser['username']];
				$model->save(['replace_product_bom_id'=>0,'replace_material'=>[],'replace_material_id'=>0,'num'=>$model['material']['num'],'bill_type'=>$model['material']['bill_type'],'actually_product_bom_id'=>$model['product_bom_id'],'actually_product_bom_id'=>$model['material']['material_id']]);
			}*/
			ErpOrderLog::create($log);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }	
	

    // 删除
    public static function goRemove($id)
    {
		$model 		= self::getOne($id);
        if(empty($model['id'])) {
			return ['msg'=>'数据不存在','code'=>201];
		}
		$order 		= $model->order;
		if(!$order['can_save_product']){
			return ['msg'=>'当前订单不能删除产品','code'=>201];
		}
        try{
			if($model['product_stock_id']){
				ErpProductStock::where('id','=',$model['product_stock_id'])->update(['is_re_produce'=>0,'is_out_warehouse'=>0]);
			}
			
			ErpOrderProduct::destroy($id);
			ErpOrderProductBom::destroy(function($query) use($id){
				$query->where('order_product_id','=',$id);
			});

			$log	= ['log'=>'删除产品：'.$model['product_sn'].'  '.$model['product_name'],'data_type'=>ErpOrderLogEnum::ORDER_PRODUCT_DELETE,'data_id'=>$model['id'],'order_id'=>$model['order_id'],'operator'=>self::$adminUser['username']];
			ErpOrderLog::create($log);

			ErpOrderLogic::updateAmount($order->id);

		}catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
	
    // 复制
    public static function goCopy($id)
    {
		$model 		= self::getOne($id);
        if(empty($model['id'])) {
			return ['msg'=>'数据不存在','code'=>201];
		}
		$order 		= $model->order;
		if(!$order['can_save_product']){
			return ['msg'=>'当前订单不能添加产品','code'=>201];
		}
        try{
			self::doCopy($order->id,$model);
			ErpOrderLogic::updateAmount($order->id);
		}catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }	
	
	public static function doCopy($order_id,$model,$data=[]){
		$data 				= array_merge(['order_id'=>$order_id,'color'=>$model['color'],'product_sn'=>$model['product_sn'],'product_id'=>$model['product_id'],'product_num'=>$model['product_num'],'product_name'=>$model['product_name'],'product_model'=>$model['product_model'],'product_specs'=>$model['product_specs'],'product_unit'=>$model['product_unit'],'currency'=>$model['currency'],'exchange_rates'=>$model['exchange_rates'],'tax_rate'=>$model['tax_rate'],'product_price'=>$model['product_price'],'total_price'=>$model['total_price'],'add_project'=>$model['add_project'],'change_project'=>$model['change_project'],'replace_info'=>$model['replace_info']],$data);
		$product 			= ErpOrderProduct::create($data);
		$bom 				= ErpOrderProductBom::where('order_product_id',$model['id'])->select();
		$bom_data			= [];
		foreach($bom as $vo){
			$bom_data[] 	= ['order_id'=>$order_id,'order_product_id'=>$product['id'],'product_bom_id'=>$vo['product_bom_id'],'material_id'=>$vo['material_id'],'material'=>$vo['material'],'bill_type'=>$vo['bill_type'],'num'=>$vo['num'],'type'=>$vo['type']];
		}
		(new ErpOrderProductBom)->saveAll($bom_data);
		
		$accessory				= ErpOrderAccessory::where('order_product_id',$model['id'])->select();
		$accessory_data			= [];
		foreach($accessory as $vo){
			$accessory_data[] 	= ['order_id'=>$order_id,'order_product_id'=>$product['id'],'product_num'=>$vo['product_num'],'product_name'=>$vo['product_name'],'product_model'=>$vo['product_model'],'product_price'=>$vo['product_price'],'total_price'=>$vo['total_price'],'remark'=>$vo['remark'],'shipping_time'=>$vo['shipping_time']];
		}
		if($accessory_data){
			(new ErpOrderAccessory)->saveAll($accessory_data);
		}
		
		$remark2				= ErpOrderRemark::where('order_product_id',$model['id'])->select();
		$remark2_data			= [];
		foreach($remark2 as $vo){
			$remark2_data[] 	= ['order_id'=>$order_id,'order_product_id'=>$product['id'],'remark'=>$vo['remark']];
		}
		if($remark2_data){
			(new ErpOrderRemark)->saveAll($remark2_data);
		}	
		return 	$product;
	}

	
	public static function goTechnicianEditBom($data){
		$model	= ErpOrderProductBom::where('id',$data['id'])->find();
		try{
			$model->save($data);
		}catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}
	
	public static function technicianEdit($data){
		if($data['type'] == 4){
			if(!empty($data['id'])) {
				$model		= ErpOrderProductBom::where('id',$data['id'])->find();
				if(empty($model['id'])){
					return ['msg'=>'数据不存在','code'=>201];
				}
			}else{
				$model		= ErpOrderProductBom::where('material_id',$data['material_id'])->where('type',$data['type'])->find();
				if(empty($model['id'])){
					$model 	= new ErpOrderProductBom;
				}
			}
			
			$material 		= ErpMaterial::field('*')->where('id',$data['material_id'])->where('status',1)->find();
			if(empty($material['id'])){
				return ['msg'=>'物料不存在','code'=>201];
			}
			$order_product 	= self::getOne($data['order_product_id']);
			if(empty($order_product['id'])){
				return ['msg'=>'订单商品不存在','code'=>201];
			}	
			$order 			= $order_product->order;
			if(!$order['can_save_product']){
				return ['msg'=>'当前订单不能修改产品','code'=>201];
			}

			$data['material_id'] 			= $material['id'];
			$data['material'] 				= $material->toArray();
			$data['actually_material_id'] 	= $material['id'];			
			unset($data['id']);
			
			if(empty($model['id'])){
				$log		= ['log'=>'加 加配置：'.$data['material']['sn'].'  '.$data['material']['name'].'  数量：'.$data['num'],'data_type'=>ErpOrderLogEnum::ORDER_PRODUCT_BOM_ADD,'order_product_id'=>$order_product['id'],'order_id'=>$order_product['order_id'],'operator'=>self::$adminUser['username']];
			}else{
				$log		= ['log'=>'改 加配置：'.$data['material']['sn'].'  '.$data['material']['name'].'  数量：'.$data['num'],'data_type'=>ErpOrderLogEnum::ORDER_PRODUCT_BOM_ADD,'order_product_id'=>$order_product['id'],'order_id'=>$order_product['order_id'],'operator'=>self::$adminUser['username']];
			}
			$model->save($data);
			ErpOrderLog::create($log);
		}else{
			return self::goSaveBom($data);
		}
	}
	
	public static function goAddFromReturned($order_id,$ids){
		$order			= ErpOrder::where('id',$order_id)->find();
		if(empty($order['id'])){
			return ['msg'=>'订单不存在','code'=>201];
		}
		if(!$order['can_save_product']){
			return ['msg'=>'当前订单不能添加产品','code'=>201];
		}		
		$stock			= ErpProductStock::with(['order_product','order.salesman','order_produce'])->where('id','in',$ids)->where('is_out_warehouse',0)->where('is_returned',0)->where('is_re_produce',0)->where('type','in','12,30')->select();
		if($stock->isEmpty()){
			return ['msg'=>'请选择仓库产品','code'=>201];
		}
		
		try{
			$ids 		= [];
			$log		= [];
			foreach($stock as $vo){
				$ids[]	= $vo['id'];
				$model 	= self::doCopy($order->id,$vo['order_product'],['product_num'=>1,'product_stock_id'=>$vo['id']]);
				$log[]	= ['log'=>'原产品编码：'.$vo['order_produce']['produce_sn']."；\n\r".'原客户名称：'.$vo['order']['customer_name']."；\n\r".'原业务员：'.$vo['order']['salesman']['username'],'data_type'=>ErpOrderLogEnum::ORDER_PRODUCT_ADD_FROM_RETURNED,'data_id'=>$model['id'],'order_product_id'=>$model['order_id'],'operator'=>self::$adminUser['username']];
			}
			
			(new ErpOrderLog)->saveAll($log);
			
			ErpOrderLogic::updateAmount($order->id);
			ErpProductStock::where('id','in',$ids)->update(['is_re_produce'=>1]);
		}catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}

    public static function goPause($id)
    {
		$model 		= self::getOne($id);
        if(empty($model['id'])) {
			return ['msg'=>'数据不存在','code'=>201];
		}
		//if(!$model['can_pause']){
			//return ['msg'=>'当前订单不能暂停','code'=>201];
		//}
        try{
			$model->save(['is_pause'=>1]);
		}catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }	
	
	public static function goCancelPause($ids)
    {
        try{
			ErpOrderProduct::where('id','in',$ids)->where('is_pause',1)->update(['is_pause'=>0]);
		}catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
	
}
