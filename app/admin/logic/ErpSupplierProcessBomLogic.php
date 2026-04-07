<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\{ErpSupplierProcessBom,ErpSupplierProcess,ErpMaterial};
use app\admin\validate\ErpSupplierProcessBomValidate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;


class ErpSupplierProcessBomLogic extends BaseLogic{

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$field = 'a.*,b.name,b.sn';
		$query['_alias']= 'a';
		$query['_material_alias']= 'b';
		$query['_related_material_alias']= 'c';
		$query['_process_alias']= 'd';
		
        $list = ErpSupplierProcessBom::alias('a')
		->join('erp_material b','a.material_id = b.id','LEFT')
		->join('erp_material c','a.related_material_id = c.id','LEFT')
		->join('erp_supplier_process d','a.process_id = d.id','LEFT')
		->field('a.*,b.sn,b.name,c.sn as related_sn,c.name as related_name,d.name as process_name')
		->withSearch(['query'],['query'=>$query])->field($field)->order('a.id','desc')->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

    // 添加
    public static function goAdd($data)
    {
        //验证
        $validate = new ErpSupplierProcessBomValidate;
        if(!$validate->scene('add')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
        try {
            ErpSupplierProcessBom::create($data);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
    
    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpSupplierProcessBom::where($map)->find();
		}else{
			return ErpSupplierProcessBom::find($map);
		}
    }
	
    // 编辑
    public static function goEdit($data)
    {
        //验证
        $validate 	= new ErpSupplierProcessBomValidate;
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
        $validate 	= new ErpSupplierProcessBomValidate;
        if(!$validate->scene('remove')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}	
        try{
			ErpSupplierProcessBom::destroy($data['ids']);
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
		ini_set('max_execution_time', '300');
		try {
			$process	= ErpSupplierProcess::column('id','name');
			$material	= ErpMaterial::column('id','sn');
			
			$reader 	= IOFactory::createReader('Xlsx');
			$spreadsheet= $reader->load('.'.$file);
			$sheet 		= $spreadsheet->getActiveSheet();
			$field 		= ['A'=>'material_id','B'=>'material_unit','C'=>'process_id','D'=>'related_material_id','E'=>'related_material_unit','F'=>'num','G'=>'remark'];
			
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
							throw new \Exception('第'.$ii."行：委外物料编号不存在");
							break;
						}
					}
					if($k == 'C'){
						if(!$value || empty($process[$value])){
							$tmp 		= [];
							throw new \Exception('第'.$ii."行：委外工序不存在");
							break;
						}
					}	
					if($k == 'D'){
						if(!$value || empty($material[$value])){
							$tmp 		= [];
							throw new \Exception('第'.$ii."行：出库坯料编号不存在");
							break;
						}					
						if($material[$value] == $tmp['material_id']){
							$tmp 		= [];
							throw new \Exception('第'.$ii."行：委外物料编号不能和出库坯料编号一样");
							break;
						}
					}

					if($k == 'A' || $k == 'D'){
						$tmp[$field[$k]] = $material[$value];
					}else if($k == 'C'){
						$tmp[$field[$k]] = $process[$value];
					}else{
						$tmp[$field[$k]] = $value?$value:'';
					}
				}
				if($tmp){
					$res[] 	= $tmp;
				}
			}
			
			$tmp 		= ErpSupplierProcessBom::where('material_id','in',array_column($res,'material_id'))
			->where('process_id','in',array_column($res,'process_id'))
			->where('related_material_id','in',array_column($res,'related_material_id'))
			->field('id,material_id,process_id')->select();
			$bom 		= [];
			foreach($tmp as $vo){
				$bom[$vo['material_id']][$vo['process_id']][$vo['related_material_id']] = $vo['id'];
			}
			
			$add 		= [];
			$update 	= [];
			foreach($res as $k=>$vo){
				if(empty($bom[$vo['material_id']][$vo['process_id']][$vo['related_material_id']])){
					$add[] 		= $vo;
				}else{
					$vo['id'] 	= $bom[$vo['material_id']][$vo['process_id']];	
					$update[] 	= $vo;
				}
			}
			if($add){
				(new ErpSupplierProcessBom)->saveAll($add);
			}
			if($update){
				(new ErpSupplierProcessBom)->saveAll($update);
			}			
			unlink('.'.$file);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

}
