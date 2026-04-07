<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\ErpMaterialBom;
use app\admin\validate\ErpMaterialBomValidate;
use app\common\model\{DictData,ErpMaterialTree,ErpSupplier,ErpWarehouse};
use app\common\model\ErpMaterial;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use app\common\enum\ErpMaterialEnum;


class ErpMaterialBomLogic extends BaseLogic{

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$field 	= 'id,material_id,related_material_id,color_follow,num';
        $list 	= ErpMaterialBom::withSearch(['query'],['query'=>$query])->with(['relatedMaterial'=>function($query){return $query->field('id,status,stock,type,name,sn,cid,safety_stock,min_stock,max_stock,unit,processing_type,material,surface,color,remark,photo,warehouse_id,status');}])->field($field)->append(['relatedMaterial.category_name'])->order('id','desc')->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }	
	

    // 添加
    public static function goAdd($param)
    {
        //验证
        $validate = new ErpMaterialBomValidate;
        if(!$validate->scene('add')->check($param)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
        try {
			$related_material_id 	= explode(',',$param['related_material_id']);
			$data 					= [];
			foreach($related_material_id as $vo){
				$data[] 			= array_merge($param,['related_material_id'=>$vo]);
			}
			(new ErpMaterialBom)->saveAll($data);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
    
    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpMaterialBom::where($map)->find();
		}else{
			return ErpMaterialBom::find($map);
		}
    }
	
    // 编辑
    public static function goEdit($data)
    {
        //验证
        $validate 	= new ErpMaterialBomValidate;
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

    // 删除
    public static function goRemove($data)
    {
		//验证
        $validate 	= new ErpMaterialBomValidate;
        if(!$validate->scene('remove')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}	
        try{
			ErpMaterialBom::destroy($data['ids']);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }



	public static function goImport($file){
		if(empty($file) || !is_file('.'.$file)) {
			return ['msg'=>'excel文件不存在','code'=>201];
		}
		ini_set("memory_limit","-1");
		ini_set('max_execution_time', '300');
		
		$tree_id	= ErpMaterialTree::where('type','=',1)->column('id','title');
		$produce_type=DictData::where('type_id','=',7)->column('id','name');
		$category 	= DictData::where('type_id','=',1)->column('id','name');
		$warehouse	= ErpWarehouse::column('id','name');
		$supplier	= ErpSupplier::column('id','name');
		$processing_way 		= ErpMaterialEnum::getProcessingWayDesc();
		$quality_testing_area 	= DictData::where('type_id','=',12)->column('id','name');		
		
		try {
			
			$reader 	= IOFactory::createReader('Xlsx');
			$spreadsheet= $reader->load('.'.$file);
	
			$field 		= ['A'=>'sn','B'=>'name','C'=>'num','D'=>'unit','E'=>'cid','F'=>'material','G'=>'surface','H'=>'color','I'=>'remark','J'=>'photo','K'=>'processing_type','L'=>'produce_type','M'=>'tree_id','N'=>'safety_stock','O'=>'min_stock','P'=>'max_stock','Q'=>'warehouse_id','R'=>'generality','S'=>'supplier_id','T'=>'processing_way','U'=>'quality_testing_area','V'=>'qc_code','W'=>'supplier_code','X'=>'material_category','Y'=>'workstation_id','Z'=>'tag'];

			$sns 		= [];
			$components	= [];	
			$sheets 	= $spreadsheet->getAllSheets();
			$floder		= '/upload/image/'.date('Ymd').'/';
			if(!file_exists('.'.$floder)) {
				mkdir('.'.$floder);
            }
			
			/*
			foreach($sheets as $sheet_idx=>$sheet){	
				dump($sheet->getTitle());
			}
			unlink('.'.$file);exit;*/
			
			foreach($sheets as $sheet_idx=>$sheet){				
				$photo	= [];
				foreach ($sheet->getDrawingCollection() as $drawing) {
					list($startColumn, $startRow) = Coordinate::coordinateFromString($drawing->getCoordinates());					
					$imageFileName 			= md5($drawing->getCoordinates() . mt_rand(1000, 9999).'_'.time());
					switch ($drawing->getExtension()) {
						case 'jpg':
						case 'jpeg':
							$imageFileName .= '.jpg';
							$source 		= imagecreatefromjpeg($drawing->getPath());
							imagejpeg($source, '.'.$floder . $imageFileName);
							break;
						case 'gif':
							$imageFileName 	.= '_'.time().'.gif';
							$source 		= imagecreatefromgif($drawing->getPath());
							imagegif($source, '.'.$floder . $imageFileName);
						break;
						case 'png':
							$imageFileName .= '_'.time().'.png';
							$source 		= imagecreatefrompng($drawing->getPath());
							imagepng($source, '.'.$floder. $imageFileName);
							break;
					}
					$photo[$startColumn][$startRow] =  $floder . $imageFileName;
				}
				$_sn 				= strtoupper(trim($sheet->getTitle()));
				$res 				= [];
				foreach ($sheet->getRowIterator(2) as $ii=>$row) {
					$tmp 	= [];
					foreach ($row->getCellIterator() as $k=>$cell) {
						if(empty($field[$k])){
							break;
						}
						$value 				= (delete_html($cell->getFormattedValue()));					
						if($field[$k] == 'sn'){
							if(!$value){
								$tmp 		= [];
								break;
							}
							if(!in_array($value,$sns)){
								$sns[] 		= $value;
							}
						}
						if($field[$k] == 'cid'){
							if(!empty($category[$value])){
								$tmp[$field[$k]] = $category[$value];
							}else{
								$tmp[$field[$k]] = 0;
							}
						}else if($field[$k] == 'safety_stock' || $field[$k] == 'min_stock' || $field[$k] == 'max_stock' || $field[$k] == 'tag'){
							if($value !== ''){
								$tmp[$field[$k]] = $value;
							}
						}else if($field[$k] == 'warehouse_id'){
							if(!empty($warehouse[$value])){
								$tmp[$field[$k]] = $warehouse[$value];
							}
						}else if($field[$k] == 'photo'){
							if(!empty($photo[$k][$ii])){
								$tmp[$field[$k]]	= $photo[$k][$ii];
							}
						}else if($field[$k] == 'produce_type'){
							if(!empty($produce_type[$value])){
								$tmp[$field[$k]] = $produce_type[$value];
							}
						}else if($field[$k] == 'tree_id'){
							if(!empty($tree_id[$value])){
								$tmp[$field[$k]] = $tree_id[$value];
							}
						}else if($field[$k] == 'generality'){
							$tmp[$field[$k]] 	= $value == '不通用'?0:1;
						}else if($field[$k] == 'supplier_id'){
							if($value){
								$arr 			= explode(',',str_replace('，',',',$value));
								$value 			= [];
								foreach($arr as $vo){
									if(!empty($supplier[$vo])){
										$value[]= $supplier[$vo];
									}
								}
								$tmp[$field[$k]]= $value;
							}
						}else if($field[$k] == 'processing_way'){
							if($value && in_array($value,$processing_way)){
								$tmp[$field[$k]] = array_search($value, $processing_way);
							}
						}else if($field[$k] == 'quality_testing_area'){
							if(!empty($quality_testing_area[$value])){
								$tmp[$field[$k]] = $quality_testing_area[$value];
							}
						}else if($field[$k] == 'workstation_id'){
							if(!empty($warehouse[$value])){
								$tmp[$field[$k]] = $warehouse[$value];
							}
						}else{
							$tmp[$field[$k]] 	= $value;
						}						
					}
					if($tmp){
						$res[] 	= $tmp;
					}
				}			
				$components[]	= ['sn'=>$_sn,'data'=>$res];
			}
			
			$temp 			= ErpMaterial::where('sn','in',$sns)->field('id,sn')->select();
			$material		= [];
			foreach($temp as $vv){
				$material[strtoupper($vv['sn'])] = $vv['id'];
			}
			$component 			= ErpMaterial::where('sn','in',array_column($components,'sn'))->column('id','sn');

			$material_add 		= [];
			$material_edit		= [];
			foreach($components as $k=>$res){
				foreach($res['data'] as $vo){
					if(empty($material[$vo['sn']]) && (!$material_add || ($material_add && !in_array($vo['sn'],array_column($material_add,'sn'))) ) ){
						$material_add[] 	= array_merge($vo,['type'=>1]);
					}else if(!empty($material[$vo['sn']])){
						$material_edit[$material[$vo['sn']]] 	= array_merge($vo,['type'=>1,'id'=>$material[$vo['sn']]]);
					}
				}
				//if(!empty($component[$res['sn']])){
					//$components[$k]['bom'] 	= ErpMaterialBom::where('material_id','=',$component[$res['sn']])->column('id','related_material_id');
				//}else{
					//$components[$k]['bom'] 	= [];
				//}	
			}
			
			ErpMaterialBom::destroy(function($query) use($component){
				$query->whereIn('material_id',array_values($component));
			});	

			if($material_add){
				(new ErpMaterial)->saveAll($material_add);
			}	
			if($material_edit){
				(new ErpMaterial)->saveAll($material_edit);
			}				
			
			$bom_add 		= [];
			$material 		= ErpMaterial::where('sn','in',$sns)->column('id','sn');
			foreach($components as $res){
				foreach($res['data'] as $vo){
					if(!empty($material[$vo['sn']]) && !empty($component[$res['sn']]) ){
						$bom_add[] = ['material_id'=>$component[$res['sn']],'related_material_id'=>$material[$vo['sn']],'num'=>$vo['num']];
					}
				}
			}
			if($bom_add){
				(new ErpMaterialBom)->saveAll($bom_add);
			}	

			unlink('.'.$file);

        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}



    // 获取回收站
    public static function getRecycle($query=[],$limit=10)
	{
        $list = ErpMaterialBom::onlyTrashed()->withSearch(['query'],['query'=>$query])->order('id','desc')->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

	//恢复/删除回收站
    public static function goRecycle($ids,$action)
    {
        $validate 		= new ErpMaterialBomValidate;
        if(!$validate->scene('recycle')->check(['ids'=>$ids])){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		try{
			if($action){
				$data 	= ErpMaterialBom::onlyTrashed()->whereIn('id', $ids)->select();
				foreach($data as $k){
					$k->restore();
				}				
			}else{				
				ErpMaterialBom::destroy($ids,true);
			}
		}catch (\Exception $e){
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
		}
		return ['msg'=>'操作成功'];
    }


	public static function getExportCount($query=[]){
		$field 			= 'id';	
        $count 			= ErpMaterialBom::withSearch(['query'],['query'=>$query])->field($field)->order('id','desc')->count();
		return ['data'=>['count'=>$count,'key'=>rand_string()]];
	}
	
	public static function getExport($query=[],$limit=10000){
		$limit				= $limit>10000?10000:$limit;
		$data				= self::getList($query,$limit)['data'];
		foreach($data as &$vo){
			$vo['sn']		= $vo['relatedMaterial']['sn'];
			$vo['name']		= $vo['relatedMaterial']['name'];
			$vo['category_name']		= $vo['relatedMaterial']['category_name'];
			$vo['unit']		= $vo['relatedMaterial']['unit'];
			$vo['processing_type']		= $vo['relatedMaterial']['processing_type'];
			$vo['material']		= $vo['relatedMaterial']['material'];
			$vo['surface']		= $vo['relatedMaterial']['surface'];
			$vo['color']		= $vo['relatedMaterial']['color'];
			$vo['remark']		= $vo['relatedMaterial']['remark'];
			$vo['color_follow']	= $vo['color_follow']?'是':'否';
		}
		$return				= ['column'=>[],'setWidh'=>[],'keys'=>[],'list'=>$data,'image_fields'=>[]];
		$field 				= ['sn'=>'物料编码','name'=>'物料名称','category_name'=>'物料分类','unit'=>'单位','processing_type'=>'加工类型','material'=>'材料','surface'=>'表面','color'=>'颜色','remark'=>'备注','num'=>'数量','color_follow'=>'颜色是否跟随产品'];
		foreach($field as $key=>$vo){
			$return['column'][] 	= $vo;
			$return['setWidh'][] 	= 10;
			$return['keys'][] 		= $key;				
		}
        return $return;	
	}


}
