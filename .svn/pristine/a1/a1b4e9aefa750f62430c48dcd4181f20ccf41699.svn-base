<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use think\facade\Db;
use app\common\model\ErpSupplier;
use app\admin\validate\ErpSupplierValidate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ErpSupplierLogic extends BaseLogic{
    	
	// 获取列表
    public static function getList($query=[],$limit=10)
    {
        $list 	= ErpSupplier::withSearch(['query'],['query'=>$query])->append(['status_desc'])->order(['id'=>'desc'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpSupplier::where($map)->find();
		}else{
			return ErpSupplier::find($map);
		}
    }
	
    // 添加
    public static function goAdd($data)
    {
        //验证
        $validate = new ErpSupplierValidate;
        if(!$validate->scene('add')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
        try {
            ErpSupplier::create($data);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }	
	
    // 编辑
    public static function goEdit($data){
        //验证
        $validate 	= new ErpSupplierValidate;
        if(!$validate->scene('edit')->check($data))
			return ['msg'=>$validate->getError(),'code'=>201];
        try {
            $model 	= self::getOne($data['id']);
			if ($model->isEmpty())  
				return ['msg'=>'数据不存在','code'=>201];

            $model->save($data); 
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 删除
    public static function goRemove($ids)
    {
        try{
			ErpSupplier::destroy($ids);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }


    // 获取列表
    public static function getRecycle($query=[],$limit=10)
    {
        $list 		= ErpSupplier::onlyTrashed()->withSearch(['query'],['query'=>$query])->append(['status_desc'])->order('id','desc')->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
        
    }

    // 获取列表
    public static function batchRecycle($ids,$type)
    {
		if (!is_array($ids)) 
			return ['msg'=>'参数错误','code'=>'201'];
		try{
			if($type){
				$data = ErpSupplier::onlyTrashed()->whereIn('id', $ids)->select();
				foreach($data as $k){
					$k->restore();
				}
			}else{
				ErpSupplier::destroy($ids,true);				
			}
		}catch (\Exception $e){
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
		}
    }
	
	
	// 获取所有供应商
    public static function getAll($query=[])
    {
		return ErpSupplier::withSearch(['query'],['query'=>$query])->field('id,name')->where('status',1)->order(['id'=>'desc'])->select();
    }
	
	
	
    // 导入
    public static function goImport($file)
    {
		if(empty($file) || !is_file('.'.$file)) {
			return ['msg'=>'excel文件不存在','code'=>201];
		}
		ini_set("memory_limit","-1");
		ini_set('max_execution_time', '90');
		try {
			$reader 	= IOFactory::createReader('Xlsx');
			$spreadsheet= $reader->load('.'.$file);
			$sheet 		= $spreadsheet->getActiveSheet();
			$field 		= ['A'=>'code','B'=>'name','C'=>'tel','D'=>'contact','E'=>'address','F'=>'remark','G'=>'file','H'=>'is_survey','I'=>'quality_date','J'=>'contract_date','K'=>'certificate','L'=>'score'];
			$codes 		= [];
			$res 		= [];
			$photo		= [];
			$floder		= '/upload/image/'.date('Ymd').'/';
			if(!file_exists('.'.$floder)) {
				mkdir('.'.$floder);
            }			
			
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
				
			foreach($sheet->getRowIterator(2) as $ii=>$row) {	
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
						if(!in_array($value,$codes)){
							$codes[] 		= $value;
						}
					}
					if($k == 'G'){
						if(!empty($photo[$k][$ii])){
							$tmp[$field[$k]]= ['file'=>[$photo[$k][$ii]],'name'=>['']];
						}
					}else{
						$tmp[$field[$k]] 	= $value;
					}
				}
				if($tmp){
					$res[] 	= $tmp;
				}
			}
			
			$supplier 	= ErpSupplier::where('code','in',$codes)->column('id','code');
			$add 		= [];
			$update 	= [];
			foreach($res as $k=>$vo){
				if(empty($supplier[$vo['code']])){
					$add[] 		= $vo;
				}else{
					$vo['id'] 	= $supplier[$vo['code']];	
					$update[] 	= $vo;
				}
			}
			
			
			if($add){
				(new ErpSupplier)->saveAll($add);
			}
			if($update){
				(new ErpSupplier)->saveAll($update);
			}			
			unlink('.'.$file);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }	
	
	
	
	
}
