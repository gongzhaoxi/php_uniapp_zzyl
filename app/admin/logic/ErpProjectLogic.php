<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\{ErpProject,ErpProductProject,ErpProductBom};
use app\common\model\ErpProjectBom;
use app\common\model\DictData;
use app\common\model\ErpMaterial;
use app\common\model\ErpProduct;
use app\admin\validate\ErpProjectValidate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ErpProjectLogic extends BaseLogic{

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$field = '*';
        $list = ErpProject::withSearch(['query'],['query'=>$query])->field($field)->order('id','desc')->append(['type_desc','category','status_desc'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

    // 添加
    public static function goAdd($data,$count=null)
    {
        //验证
        $validate 		= new ErpProjectValidate;
        if(!$validate->scene('add')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
        try {
            ErpProject::create($data);
			return ['msg'=>'创建成功','code'=>200];
        }catch (\Exception $e){
            return ['msg'=>'创建失败'.$e->getMessage(),'code'=>201];
        }
    }
    
    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpProject::where($map)->find();
		}else{
			return ErpProject::find($map);
		}
    }
	
    // 编辑
    public static function goEdit($data)
    {
        //验证
        $validate 	= new ErpProjectValidate;
        if(!$validate->scene('edit')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		$model 		= self::getOne($data['id']);
        if($model->isEmpty()) {
			return ['msg'=>'数据不存在','code'=>201];
		}
        try {
            $model->save($data);
			ErpProductProject::where('erp_project_id',$model['id'])->update(['name'=>$data['name'],'cid'=>$data['cid']]);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 删除
    public static function goRemove($data)
    {
		//验证
        $validate 	= new ErpProjectValidate;
        if(!$validate->scene('remove')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}	
        try{
			ErpProject::destroy($data['ids']);
			ErpProjectBom::destroy(function($query) use($data){
				$query->where('project_id','in',$data['ids']);
			});
			ErpProductProject::destroy(function($query) use($data){
				$query->where('erp_project_id','in',$data['ids']);
			});
			ErpProductBom::destroy(function($query) use($data){
				$query->where('erp_project_id','in',$data['ids']);
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
			$project			= ErpProject::create(['name'=>$model['name'],'cid'=>$model['cid'],'product_id'=>$model['product_id'],'code'=>$model['code'].'-'.ErpProject::where('code',$model['code'])->count(),'type'=>$model['type']]);
			if($bom){
				foreach($bom as $vo){
					$bom_data[] = ['project_id'=>$project['id'],'product_id'=>$vo['product_id'],'material_id'=>$vo['material_id'],'color_follow'=>$vo['color_follow'],'bill_type'=>$vo['bill_type'],'can_replace'=>$vo['can_replace'],'num'=>$vo['num'],'data_type'=>$vo['data_type']];
				}
				(new ErpProjectBom)->saveAll($bom_data);
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
			$sheet 			= $spreadsheet->getActiveSheet();//获取当前活跃的工作表
			$field 			= ['A'=>'code','B'=>'name','C'=>'type','D'=>'cid','E'=>'material_sn','F'=>'material_name','G'=>'num','H'=>'unit','I'=>'data_type','J'=>'bill_type','K'=>'material_type','L'=>'material_cid'];
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
							$tmp 		= [];
							break;
						}
						if(!in_array($value,$codes)){
							$codes[] 	= $value;
						}
					}
					if($k == 'E'){
						if(!$value){
							$tmp 		= [];
							break;
						}
						if(!in_array($value,$material_sns)){
							$material_sns[] 	= $value;
						}
					}
					if($k == 'C'){
						if($value == '改配'){
							$tmp[$field[$k]] = 1;
						}else{
							$tmp[$field[$k]] = 2;
						}
					}else if($k == 'D'){
						if(!empty($category2[$value])){
							$tmp[$field[$k]] = $category2[$value];
						}else{
							$tmp[$field[$k]] = 0;
						}
					}else if($k == 'I'){
						if($value == '取消'){
							$tmp[$field[$k]] = 4;
						}else if($value == '加配'){
							$tmp[$field[$k]] = 3;
						}else{
							$tmp[$field[$k]] = 2;
						}
					}else if($k == 'J'){
						if(!empty($bill_type[$value])){
							$tmp[$field[$k]] = $bill_type[$value];
						}else{
							$tmp[$field[$k]] = $default_bill ;
						}
					}else if($k == 'K'){
						if($value == '零件'){
							$tmp[$field[$k]] = 1;
						}else{
							$tmp[$field[$k]] = 2;
						}
					}else if($k == 'L'){
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
					if(!isset($res[$tmp['code']])){
						$res[$tmp['code']] 		= ['code'=>$tmp['code'],'name'=>$tmp['name'],'type'=>$tmp['type'],'cid'=>$tmp['cid'],'data'=>[]];
					}
					$res[$tmp['code']]['data'][]= $tmp;
				}
			}
			
			$material 			= ErpMaterial::where('sn','in',$material_sns)->column('id','sn');
			$project 			= ErpProject::where('code','in',$codes)->column('id','code');
			
			$material_add 				= [];
			$project_add 				= [];
			$project_update 			= [];
			$project_bom 				= [];
			
			foreach($res as $k1=>$v1){
				if(empty($project[$v1['code']])){
					$project_add[] 		= ['code'=>$v1['code'],'name'=>$v1['name'],'type'=>$v1['type'],'cid'=>$v1['cid']];					
				}else{
					$project_update[] 	= ['id'=>$project[$v1['code']],'code'=>$v1['code'],'name'=>$v1['name'],'type'=>$v1['type'],'cid'=>$v1['cid']];
					$project_bom[$project[$v1['code']]] = ErpProjectBom::where('project_id',$project[$v1['code']])->column('id','material_id');
				}	
				foreach($v1['data'] as $k2=>$v2){
					if(empty($material[$v2['material_sn']]) && (!$material_add || ($material_add && !in_array($v2['material_sn'],array_column($material_add,'sn'))) ) ){
						$material_add[] 				= ['name'=>$v2['material_name'],'sn'=>$v2['material_sn'],'unit'=>$v2['unit'],'cid'=>$v2['material_cid'],'type'=>$v2['material_type']];
					}
				}	
				
			}
			
			if($material_add){
				(new ErpMaterial)->saveAll($material_add);
			}
			if($project_add){
				(new ErpProject)->saveAll($project_add);
			}
			if($project_update){
				(new ErpProject)->saveAll($project_update);
			}			
			
			$material 	= ErpMaterial::where('sn','in',$material_sns)->column('id','sn');
			$project 	= ErpProject::where('code','in',$codes)->column('id','code');
			$bom_add 	= [];
			$bom_update = [];
			
			foreach($res as $k1=>$v1){
				foreach($v1['data'] as $k3=>$v2){
					if(!empty($project[$v1['code']])  && !empty($material[$v2['material_sn']])){
						if(empty($project_bom[$project[$v1['code']]][$material[$v2['material_sn']]])){
							$bom_add[] 		= ['project_id'=>$project[$v1['code']],'material_id'=>$material[$v2['material_sn']],'bill_type'=>$v2['bill_type'],'num'=>$v2['num'],'data_type'=>$v2['data_type']];
						}else{
							$bom_update[] 	= ['id'=>$project_bom[$project[$v1['code']]][$material[$v2['material_sn']]],'project_id'=>$project[$v1['code']],'material_id'=>$material[$v2['material_sn']],'bill_type'=>$v2['bill_type'],'num'=>$v3['num'],'data_type'=>$v3['data_type']];
						}	
					}	
				}
			}
			if($bom_add){
				(new ErpProjectBom)->saveAll($bom_add);
			}			
			if($bom_update){
				(new ErpProjectBom)->saveAll($bom_update);
			}					
			unlink('.'.$file);

        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}



	public static function getExportCount($query=[]){
		$field 			= 'a.id';	
        $count 			= ErpProjectBom::alias('a')->field($field)->order('a.id','desc')->count();
		return ['data'=>['count'=>$count,'key'=>rand_string()]];
	}
	
	public static function getExport($query=[],$limit=10000){
		$limit				= $limit>10000?10000:$limit;
		$bill_type 			= DictData::where('type_id','=',4)->column('name','id');
		$category 			= DictData::where('type_id','in','1,2')->column('name','id');
		$category2			= DictData::where('type_id','=',6)->column('name','id');
		$field 				= 'c.code,c.name,c.type,c.cid,b.sn as material_sn,b.name as material_name,a.num,b.unit,a.data_type,a.bill_type,b.type as material_type,b.cid as material_cid';	
		$data				= ErpProjectBom::alias('a')
		->join('erp_material b','a.material_id = b.id','LEFT')
		->join('erp_project c','a.project_id = c.id','LEFT')->field($field)->order('a.id','desc')->select()->toArray();
		foreach($data as &$vo){
			$vo['type'] 		= $vo['type']==1?'改配':'加配';
			$vo['cid'] 			= $category2[$vo['cid']]??'';
			$vo['bill_type'] 	= $bill_type[$vo['bill_type']]??'';
			if($vo['data_type'] == 4){
				$vo['data_type']= '取消';
			}else if($vo['data_type'] == 3){
				$vo['data_type']= '加配';
			}else{
				$vo['data_type']= '新增';
			}
			$vo['material_type']= $vo['material_type']==1?'零件':'部件';
			$vo['material_cid']	= $category[$vo['material_cid']]??'';	
		}
		$return				= ['column'=>[],'setWidh'=>[],'keys'=>[],'list'=>$data,'image_fields'=>[]];
		$field 				= ['code'=>'方案编号','name'=>'方案名称','type'=>'方案功能','cid'=>'方案分类','material_sn'=>'物料编号','material_name'=>'物料名称','num'=>'数量','unit'=>'单位','data_type'=>'功能','bill_type'=>'类型','material_type'=>'属性','material_cid'=>'物料分类'];
		foreach($field as $key=>$vo){
			$return['column'][] 	= $vo;
			$return['setWidh'][] 	= 10;
			$return['keys'][] 		= $key;				
		}
        return $return;	
	}



	
	
}
