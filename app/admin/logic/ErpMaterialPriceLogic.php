<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\{ErpMaterialPrice,ErpSupplier,ErpMaterial};
use app\admin\validate\ErpMaterialPriceValidate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;


class ErpMaterialPriceLogic extends BaseLogic{

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$field = 'a.*,b.name,b.sn';
		$query['_alias']= 'a';
		$query['_material_alias']= 'b';
        $list = ErpMaterialPrice::alias('a')->join('erp_material b','a.material_id = b.id','LEFT')->withSearch(['query'],['query'=>$query])->with(['supplier'=>function($query){return $query->field('id,name');}])->field($field)->order('a.id','desc')->append(['status_desc'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

    // 添加
    public static function goAdd($data)
    {
        //验证
        $validate = new ErpMaterialPriceValidate;
        if(!$validate->scene('add')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
        try {
            ErpMaterialPrice::create($data);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
    
    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpMaterialPrice::where($map)->find();
		}else{
			return ErpMaterialPrice::find($map);
		}
    }
	
    // 编辑
    public static function goEdit($data)
    {
        //验证
        $validate 	= new ErpMaterialPriceValidate;
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
        $validate 	= new ErpMaterialPriceValidate;
        if(!$validate->scene('remove')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}	
        try{
			ErpMaterialPrice::destroy($data['ids']);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 导入
    public static function goImport($file)
    {
		if(empty($file) || !is_file('.'.$file)) {
			return ['msg'=>'excel文件不存在','code'=>201];
		}
		ini_set("memory_limit","-1");
		ini_set('max_execution_time', '30');
		try {
			$supplier	= ErpSupplier::column('id','name');
			$material	= ErpMaterial::column('id','sn');
			
			
			$reader 	= IOFactory::createReader('Xlsx');
			$spreadsheet= $reader->load('.'.$file);
			$sheet 		= $spreadsheet->getActiveSheet();
			$field 		= ['A'=>'material_id','B'=>'supplier_id','C'=>'last_price','D'=>'price','E'=>'effective_date'];
			
			$photo		= [];
			$floder		= '/upload/image/'.date('Ymd').'/';
			if(!file_exists('.'.$floder)) {
				mkdir('.'.$floder);
            }			
			
			foreach($sheet->getRowIterator(2) as $ii=>$row) {	
				$tmp 	= [];
				foreach ($row->getCellIterator() as $k=>$cell) {				
					if(empty($field[$k])){
						break;
					}
					$value 				= delete_html($cell->getFormattedValue());					
					if($k == 'A'){
						if(!$value || empty($material[$value])){
							$tmp 		= [];
							break;
						}
					}
					if($k == 'B'){
						if(!$value || empty($supplier[$value])){
							$tmp 		= [];
							break;
						}
					}					
					
					if($k == 'A'){
						$tmp[$field[$k]] = $material[$value];
					}else if($k == 'B'){
						$tmp[$field[$k]] = $supplier[$value];
					}else if($k == 'E'){
						$tmp[$field[$k]] = $value?date('Y-m-d',strtotime($value)):'2020-01-01';
					}else{
						$tmp[$field[$k]] = $value?$value:'';
					}
				}
				if($tmp){
					$res[] 	= $tmp;
				}
			}
			
			$tmp 		= ErpMaterialPrice::where('material_id','in',array_column($res,'material_id'))->where('supplier_id','in',array_column($res,'supplier_id'))->field('id,material_id,supplier_id')->select();
			$price 		= [];
			foreach($tmp as $vo){
				$price[$vo['material_id']][$vo['supplier_id']] = $vo['id'];
			}
			
			$add 		= [];
			$update 	= [];
			foreach($res as $k=>$vo){
				if(empty($price[$vo['material_id']][$vo['supplier_id']])){
					$add[] 		= $vo;
				}else{
					$vo['id'] 	= $price[$vo['material_id']][$vo['supplier_id']];	
					$update[] 	= $vo;
				}
			}
			if($add){
				(new ErpMaterialPrice)->saveAll($add);
			}
			if($update){
				(new ErpMaterialPrice)->saveAll($update);
			}			
			unlink('.'.$file);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

}
