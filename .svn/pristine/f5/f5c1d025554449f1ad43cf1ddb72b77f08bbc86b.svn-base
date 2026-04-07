<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\{ErpProductProject,ErpProject,ErpProjectBom};
use app\common\model\ErpProductBom;
use app\common\model\DictData;
use app\common\model\ErpMaterial;
use app\common\model\ErpProduct;
use app\admin\validate\ErpProductProjectValidate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ErpProductProjectLogic extends BaseLogic{

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$field = '*';
        $list = ErpProductProject::withSearch(['query'],['query'=>$query])->field($field)->order('id','desc')->append(['type_desc','category','status_desc','is_default_desc'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

    // 添加
    public static function goAdd($data,$count=null)
    {
		//验证
		if(empty($data['erp_project_id'])){
			$validate 		= new ErpProductProjectValidate;
			if(!$validate->scene('add')->check($data)){
				return ['msg'=>$validate->getError(),'code'=>201];
			}
		}else{
			$validate 		= new ErpProductProjectValidate;
			if(!$validate->scene('addFromProject')->check($data)){
				return ['msg'=>$validate->getError(),'code'=>201];
			}
			$projects 		= ErpProject::where('id','in',$data['erp_project_id'])->select()->toArray();
			if(empty($projects)){
				return ['msg'=>'方案不存在','code'=>201];
			}	
			foreach($projects as $k=>$project){
				$project_bom 		= ErpProjectBom::where('project_id',$project['id'])->select();
				if($project['type'] == 1){
					$contain_bom 	= (array)ErpProductBom::where('data_type',1)->where('project_id',0)->where('product_id',$data['product_id'])->column('material_id');
					foreach($project_bom as $vo){
						if($vo['data_type'] == 4 && !in_array($vo['material_id'],$contain_bom)){
							return ['msg'=>'产品不包含物料：'.$vo['material']['name'],'code'=>201];
						}
					}
				}
				$projects[$k]['bom']= $project_bom;
			}	
		}
		
        try {
			if(empty($data['erp_project_id'])){
				$model = ErpProductProject::create($data);
				if(!empty($model['is_default'])){
					ErpProductProject::where('product_id',$model['product_id'])->where('id','<>',$model['id'])->where('is_default',1)->update(['is_default'=>0]);
				}
			}else{
				$bom 				= [];
				foreach($projects as $project){
					$data['erp_project_id'] = $project['id'];
					$data['type'] 			= $project['type'];
					$data['cid'] 			= $project['cid'];
					$data['name'] 			= $project['name'];
					$data['code'] 			= $project['code'].'-'.(ErpProductProject::withTrashed()->where('erp_project_id',$project['id'])->count()+1);
					$model 					= ErpProductProject::create($data);
					
					foreach($project['bom'] as $vo){
						$bom[]		= ['product_id'=>$model['product_id'],'project_id'=>$model['id'],'project_bom_id'=>$vo['id'],'erp_project_id'=>$project['id'],'material_id'=>$vo['material_id'],'color_follow'=>$vo['color_follow'],'bill_type'=>$vo['bill_type'],'can_replace'=>$vo['can_replace'],'num'=>$vo['num'],'data_type'=>$vo['data_type']];
					}
				}
				
				if($bom){
					(new ErpProductBom)->saveAll($bom);
				}
			}
			return ['msg'=>'创建成功','code'=>200];
        }catch (\Exception $e){
            return ['msg'=>'创建失败'.$e->getMessage(),'code'=>201];
        }
    }
    
    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpProductProject::where($map)->find();
		}else{
			return ErpProductProject::find($map);
		}
    }
	
    // 编辑
    public static function goEdit($data)
    {
        //验证
        $validate 	= new ErpProductProjectValidate;
        if(!$validate->scene('edit')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		$model 		= self::getOne($data['id']);
        if($model->isEmpty()) {
			return ['msg'=>'数据不存在','code'=>201];
		}
        try {
            $model->save($data);
			
			$category = DictData::where('id',$data['cid'])->find();
			if($category['multiple'] == 0){
				if(!empty($model['is_default'])){
					ErpProductProject::where('product_id',$model['product_id'])->where('cid',$model['cid'])->where('id','<>',$model['id'])->where('is_default',1)->update(['is_default'=>0]);
				}
			}
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 删除
    public static function goRemove($data)
    {
		//验证
        $validate 	= new ErpProductProjectValidate;
        if(!$validate->scene('remove')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}	
        try{
			ErpProductProject::destroy($data['ids']);
			ErpProductBom::destroy(function($query) use($data){
				$query->where('project_id','in',$data['ids']);
			});
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }


    public static function goCopy($id)
    {
        //验证
		$model 		= self::getOne($id);
        if($model->isEmpty()) {
			return ['msg'=>'数据不存在','code'=>201];
		}
        try {
			$bom 				= $model->bom;
			$bom_data 			= [];
			if($model['erp_project_id'] == 0){
				$code 			= $model['code'].'-'.ErpProductProject::where('code',$model['code'])->count();
			}else{
				$code 			= ErpProject::where('id',$model['erp_project_id'])->value('code').'-'.(ErpProductProject::withTrashed()->where('erp_project_id',$model['erp_project_id'])->count()+1);
			}
			$project			= ErpProductProject::create(['erp_project_id'=>$model['erp_project_id'],'name'=>$model['name'],'cid'=>$model['cid'],'product_id'=>$model['product_id'],'code'=>$code,'type'=>$model['type']]);
			if($bom){
				foreach($bom as $vo){
					$bom_data[] = ['project_id'=>$project['id'],'project_bom_id'=>$vo['project_bom_id'],'erp_project_id'=>$vo['erp_project_id'],'product_id'=>$vo['product_id'],'material_id'=>$vo['material_id'],'color_follow'=>$vo['color_follow'],'bill_type'=>$vo['bill_type'],'can_replace'=>$vo['can_replace'],'num'=>$vo['num'],'data_type'=>$vo['data_type']];
				}
				(new ErpProductBom)->saveAll($bom_data);
			}
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
	
	
	public static function goImport($file){
		if(empty($file) || !is_file('.'.$file)) {
			return ['msg'=>'excel文件不存在','code'=>201];
		}
		ini_set("memory_limit","-1");
		ini_set('max_execution_time', '30');		
		try {
			$bill_type 		= DictData::where('type_id','=',4)->column('id','name');
			$default_bill 	= array_shift($bill_type);
			$category 		= DictData::where('type_id','in','1,2')->column('id','name');
			$category2		= DictData::where('type_id','=',6)->column('id','name');
			$reader 	= IOFactory::createReader('Xlsx');
			$spreadsheet= $reader->load('.'.$file);
			//$sheets 	= $spreadsheet->getAllSheets();
			//$sheetNames = $spreadSheet->getSheetNames();//获取当前表格中所有工作表名
			
			//$sheet1 	= $spreadsheet->getSheetByName('改配');//根据工作表名，获取工作表
			$sheet 		= $spreadsheet->getActiveSheet();//获取当前活跃的工作表
			$field 		= ['A'=>'product_sn','B'=>'code','C'=>'name','D'=>'type','E'=>'cid','F'=>'material_sn','G'=>'material_name','H'=>'num','I'=>'unit','J'=>'data_type','K'=>'bill_type','L'=>'material_type','M'=>'material_cid'];
			$res 			= [];
			$projects 		= [];
			$codes			= [];
			$product_sns	= [];
			$material_sns	= [];
			
			foreach($sheet->getRowIterator(2) as $row) {
				foreach ($row->getCellIterator() as $k=>$cell) {
					if(empty($field[$k])){
						break;
					}
					$value 		= delete_html($cell->getFormattedValue());						
					if($k == 'A'){
						if(!$value){
							$tmp 			= [];
							break;
						}
						if(!in_array($value,$product_sns)){
							$product_sns[] 	= $value;
						}
					}
					if($k == 'B'){
						if(!$value){
							$tmp 		= [];
							break;
						}
						if(!in_array($value,$codes)){
							$codes[] 	= $value;
						}
					}
					if($k == 'F'){
						if(!$value){
							$tmp 		= [];
							break;
						}
						if(!in_array($value,$material_sns)){
							$material_sns[] 	= $value;
						}
					}
					if($k == 'D'){
						if($value == '改配'){
							$tmp[$field[$k]] = 1;
						}else{
							$tmp[$field[$k]] = 2;
						}
					}else if($k == 'E'){
						if(!empty($category2[$value])){
							$tmp[$field[$k]] = $category2[$value];
						}else{
							$tmp[$field[$k]] = 0;
						}
					}else if($k == 'J'){
						if($value == '取消'){
							$tmp[$field[$k]] = 4;
						}else if($value == '加配'){
							$tmp[$field[$k]] = 3;
						}else{
							$tmp[$field[$k]] = 2;
						}
					}else if($k == 'K'){
						if(!empty($bill_type[$value])){
							$tmp[$field[$k]] = $bill_type[$value];
						}else{
							$tmp[$field[$k]] = $default_bill ;
						}
					}else if($k == 'L'){
						if($value == '零件'){
							$tmp[$field[$k]] = 1;
						}else{
							$tmp[$field[$k]] = 2;
						}
					}else if($k == 'M'){
						if(!empty($category[$value])){
							$tmp[$field[$k]] = $category[$value];
						}else{
							$tmp[$field[$k]] = 0;
						}
					}else{
						$tmp[$field[$k]] 	= $value;
					}
				}
				if($tmp){
					if(!isset($res[$tmp['product_sn']])){
						$res[$tmp['product_sn']] = ['sn'=>$tmp['product_sn'],'project'=>[]];
					}
					if(!isset($res[$tmp['product_sn']]['project'][$tmp['code']])){
						$res[$tmp['product_sn']]['project'][$tmp['code']] = ['code'=>$tmp['code'],'name'=>$tmp['name'],'type'=>$tmp['type'],'cid'=>$tmp['cid'],'data'=>[]];
					}
					$res[$tmp['product_sn']]['project'][$tmp['code']]['data'][] = $tmp;
				}
			}
			
			$material 			= ErpMaterial::where('sn','in',$material_sns)->column('id','sn');
			$product 			= ErpProduct::where('sn','in',$product_sns)->column('id','sn');
			$project 			= ErpProductProject::where('code','in',$codes)->column('id','code');
			
			$material_add 				= [];
			$project_add 				= [];
			$project_update 			= [];
			$project_bom 				= [];
			
			
			foreach($res as $k1=>$v1){
				foreach($v1['project'] as $k2=>$v2){
					if(empty($project[$v2['code']]) && !empty($product[$v1['sn']])){
						$project_add[] 						= ['product_id'=>$product[$v1['sn']],'code'=>$v2['code'],'name'=>$v2['name'],'type'=>$v2['type'],'cid'=>$v2['cid']];
						
					}else{
						$project_update[] 					= ['id'=>$project[$v2['code']],'product_id'=>$product[$v1['sn']],'code'=>$v2['code'],'name'=>$v2['name'],'type'=>$v2['type'],'cid'=>$v2['cid']];
						$project_bom[$project[$v2['code']]] = ErpProductBom::where('project_id',$project[$v2['code']])->column('id','material_id');
					}	
					foreach($v2['data'] as $k3=>$v3){
						if(empty($material[$v3['material_sn']]) && (!$material_add || ($material_add && !in_array($v3['material_sn'],array_column($material_add,'sn'))) ) ){
							$material_add[] 				= ['name'=>$v3['material_name'],'sn'=>$v3['material_sn'],'unit'=>$v3['unit'],'cid'=>$v3['material_cid'],'type'=>$v3['material_type']];
						}
					}	
				}
			}
			
			if($material_add){
				(new ErpMaterial)->saveAll($material_add);
			}
			if($project_add){
				(new ErpProductProject)->saveAll($project_add);
			}
			if($project_update){
				(new ErpProductProject)->saveAll($project_update);
			}			
			
			$material 	= ErpMaterial::where('sn','in',$material_sns)->column('id','sn');
			$project 	= ErpProductProject::where('code','in',$codes)->column('id','code');
			$bom_add 	= [];
			$bom_update = [];
			
			foreach($res as $k1=>$v1){
				foreach($v1['project'] as $k2=>$v2){
					foreach($v2['data'] as $k3=>$v3){
						if(!empty($project[$v2['code']]) && !empty($product[$v1['sn']]) && !empty($material[$v3['material_sn']])){
							if(empty($project_bom[$project[$v2['code']]][$material[$v3['material_sn']]])){
								$bom_add[] 		= ['project_id'=>$project[$v2['code']],'product_id'=>$product[$v1['sn']],'material_id'=>$material[$v3['material_sn']],'bill_type'=>$v3['bill_type'],'num'=>$v3['num'],'data_type'=>$v3['data_type']];
							}else{
								$bom_update[] 	= ['id'=>$project_bom[$project[$v2['code']]][$material[$v3['material_sn']]],'project_id'=>$project[$v2['code']],'product_id'=>$product[$v1['sn']],'material_id'=>$material[$v3['material_sn']],'bill_type'=>$v3['bill_type'],'num'=>$v3['num'],'data_type'=>$v3['data_type']];
							}	
						}	
					}	
				}
			}
			if($bom_add){
				(new ErpProductBom)->saveAll($bom_add);
			}			
			if($bom_update){
				(new ErpProductBom)->saveAll($bom_update);
			}					
			unlink('.'.$file);

        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}	
	
	
}
