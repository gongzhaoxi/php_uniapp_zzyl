<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\ErpProductBom;
use app\admin\validate\ErpProductBomValidate;
use app\common\model\{DictData,ErpMaterialTree};
use app\common\model\ErpMaterial;
use app\common\model\ErpProduct;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;



class ErpProductBomLogic extends BaseLogic{


	// 获取清单类型
    public static function getBillType()
    {
		$data = get_dict_data('product_bill_type');
		return $data;
    }
	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$field 						= 'a.id,a.material_id,a.product_id,a.color_follow,a.bill_type,a.can_replace,a.num,a.project_bom_id,b.type,b.name,b.sn,b.unit';
		$query['_alias']			= 'a';
		$query['_material_alias']	= 'b';
        $list 	= ErpProductBom::alias('a')->join('erp_material b','a.material_id = b.id','LEFT')->withSearch(['query'],['query'=>$query])->field($field)->order('a.id','desc')->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }	
	

    // 添加
    public static function goAdd($param)
    {
        //验证
        $validate = new ErpProductBomValidate;
        if(!$validate->scene('add')->check($param)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
        try {
			$material_id 	= explode(',',$param['material_id']);
			$data 			= [];
			foreach($material_id  as $vo){
				$data[]		= array_merge($param,['material_id'=>$vo]);
			}
			(new ErpProductBom)->saveAll($data);
			
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
    
    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpProductBom::where($map)->find();
		}else{
			return ErpProductBom::find($map);
		}
    }
	
    // 编辑
    public static function goEdit($data)
    {
        //验证
        $validate 	= new ErpProductBomValidate;
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
        $validate 	= new ErpProductBomValidate;
        if(!$validate->scene('remove')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}	
        try{
			ErpProductBom::destroy($data['ids']);
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
		$category 		= DictData::where('type_id','in','1,2')->column('id','name');
		$bill_type 		= DictData::where('type_id','=',4)->column('id','name');
		$default_bill 	= array_shift($bill_type);
		$tree_id		= ErpMaterialTree::column('id','title');
		$produce_type	= DictData::where('type_id','=',7)->column('id','name');
		
		try {
			$reader 	= IOFactory::createReader('Xlsx');
			$spreadsheet= $reader->load('.'.$file);
			
			$field 		= ['A'=>'sn','B'=>'name','C'=>'num','D'=>'unit','E'=>'bill_type','F'=>'material','G'=>'surface','H'=>'color','I'=>'remark','J'=>'type','K'=>'cid','L'=>'produce_type','M'=>'tree_id'];
			$sns 		= [];
			$products	= [];	
			$sheets 	= $spreadsheet->getAllSheets();
			
			foreach($sheets as $sheet_idx=>$sheet){
				$res 				= [];
				foreach ($sheet->getRowIterator(2) as $row) {
					$tmp 	= [];
					foreach ($row->getCellIterator() as $k=>$cell) {
						if(empty($field[$k])){
							break;
						}
						$value 				= delete_html($cell->getFormattedValue());					
						if($k == 'A'){
							if(!$value){
								$tmp 		= [];
								break;
							}
							if(!in_array($value,$sns)){
								$sns[] 		= $value;
							}
						}
						
						if($k == 'K'){
							if(!empty($category[$value])){
								$tmp[$field[$k]] = $category[$value];
							}else{
								$tmp[$field[$k]] = 0;
							}
						}else if($k == 'E'){
							if(!empty($bill_type[$value])){
								$tmp[$field[$k]] = $bill_type[$value];
							}else{
								$tmp[$field[$k]] = $default_bill ;
							}
						}else if($k == 'J'){
							if($value == '零件'){
								$tmp[$field[$k]] = 1;
							}else{
								$tmp[$field[$k]] = 2;
							}
						}else if($k == 'L'){
							if(!empty($produce_type[$value])){
								$tmp[$field[$k]] = $produce_type[$value];
							}
						}else if($k == 'M'){
							if(!empty($tree_id[$value])){
								$tmp[$field[$k]] = $tree_id[$value];
							}
						}else{
							$tmp[$field[$k]] 	= $value;
						}
						
					}
					if($tmp){
						$res[] 	= $tmp;
					}
				}			
				$products[]		= ['sn'=>trim($sheet->getTitle()),'data'=>$res];
			}
			$material 			= ErpMaterial::where('sn','in',$sns)->column('id','sn');
			$product 			= ErpProduct::where('sn','in',array_column($products,'sn'))->column('id','sn');

			$material_add 				= [];
			foreach($products as $k=>$res){
				foreach($res['data'] as $vo){
					if(empty($material[$vo['sn']]) && (!$material_add || ($material_add && !in_array($vo['sn'],array_column($material_add,'sn'))) ) ){
						$material_add[] 	= $vo;
					}
				}
				if(!empty($product[$res['sn']])){
					$products[$k]['bom'] 	= ErpProductBom::where('product_id','=',$product[$res['sn']])->where('data_type',1)->where('project_id',0)->column('id','material_id');
				}else{
					$products[$k]['bom'] 	= [];
				}	
			}
			
			if($material_add){
				(new ErpMaterial)->saveAll($material_add);
			}			
			
			$bom_add 		= [];
			$material 		= ErpMaterial::where('sn','in',$sns)->column('id','sn');
			foreach($products as $res){
				foreach($res['data'] as $vo){
					if(!empty($material[$vo['sn']]) && !empty($product[$res['sn']]) && empty($res['bom'][$material[$vo['sn']]])){
						$bom_add[] = ['product_id'=>$product[$res['sn']],'material_id'=>$material[$vo['sn']],'bill_type'=>$vo['bill_type'],'num'=>$vo['num']];
					}
				}
			}
			if($bom_add){
				(new ErpProductBom)->saveAll($bom_add);
			}			
			
			unlink('.'.$file);

        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}	













    // 获取回收站
    public static function getRecycle($query=[],$limit=10)
	{
        $list = ErpProductBom::onlyTrashed()->withSearch(['query'],['query'=>$query])->order('id','desc')->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

	//恢复/删除回收站
    public static function goRecycle($ids,$action)
    {
        $validate 		= new ErpProductBomValidate;
        if(!$validate->scene('recycle')->check(['ids'=>$ids])){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		try{
			if($action){
				$data 	= ErpProductBom::onlyTrashed()->whereIn('id', $ids)->select();
				foreach($data as $k){
					$k->restore();
				}				
			}else{				
				ErpProductBom::destroy($ids,true);
			}
		}catch (\Exception $e){
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
		}
		return ['msg'=>'操作成功'];
    }

}
