<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\DictData;
use app\admin\validate\DictDataValidate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use app\common\model\ErpProductProject;

/**
 * 字典数据逻辑
 * Class DictDataLogic
 * @package app\adminapi\logic\DictData
 */
class DictDataLogic extends BaseLogic
{

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$map 		= [];
		if(!empty($query['type_id'])){
			$map[]	= ['type_id','=',$query['type_id']];
		}
        $list 		= DictData::where($map)->order(['sort'=>'desc','id'=>'asc'])->append(['status_desc'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

    // 添加
    public static function goAdd($data)
    {
        //验证
        $validate = new DictDataValidate;
        if(!$validate->scene('add')->check($data))
			return ['msg'=>$validate->getError(),'code'=>201];
        try {
            DictData::create($data);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
    
    public static function goFind($id)
    {
       return DictData::find($id);
    }
	
    // 编辑
    public static function goEdit($data)
    {
        //验证
        $validate 	= new DictDataValidate;
        if(!$validate->scene('edit')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		$model 		= self::goFind($data['id']);
        if($model->isEmpty()) {
			return ['msg'=>'数据不存在','code'=>201];
		}
        try {
            $model->save($data);
			if($model['type_id'] == 6){
				ErpProductProject::where('cid',$model['id'])->update(['sort'=>$data['sort']]);
			}
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 删除
    public static function goRemove($id)
    {
        $model 		= self::goFind($id);
        if ($model->isEmpty()) 
			return ['msg'=>'数据不存在','code'=>201];
        try{
            $model->delete();
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 批量删除
    public static function goBatchRemove($ids)
    {
        if (!is_array($ids)) 
			return ['msg'=>'参数错误','code'=>'201'];
        try{
            DictData::destroy($ids);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 获取回收站
    public static function getRecycle($query=[],$limit=10)
	{
		$map 		= [];
		if(!empty($query['type_id'])){
			$map[]	= ['type_id','=',$query['type_id']];
		}
        $list 		= DictData::onlyTrashed()->where($map)->append(['status_desc'])->order('id','desc')->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

	//恢复/删除回收站
    public static function goRecycle($ids,$action)
    {
		if (!is_array($ids)) 
			return ['msg'=>'参数错误','code'=>'201'];
		try{
			if($action){
				$data 	= DictData::onlyTrashed()->whereIn('id', $ids)->select();
				foreach($data as $k){
					$k->restore();
				}
			}else{
				DictData::destroy($ids,true);
			}
		}catch (\Exception $e){
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
		}
		return ['msg'=>'操作成功'];
    }
	
		// 导入
    public static function goImport($type_id,$file)
    {
		if(empty($file) || !is_file('.'.$file)) {
			return ['msg'=>'excel文件不存在','code'=>201];
		}
		ini_set("memory_limit","-1");
		ini_set('max_execution_time', '30');
		try {
			$reader 	= IOFactory::createReader('Xlsx');
			$spreadsheet= $reader->load('.'.$file);
			$sheet 		= $spreadsheet->getActiveSheet();
			$field 		= ['A'=>'name','B'=>'sort'];
			$name 		= [];
			$res 		= [];			
				
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
						if(!in_array($value,$name)){
							$name[] 		= $value;
						}
					}
					$tmp[$field[$k]] 	= $value;	
				}
				if($tmp){
					$res[] 	= $tmp;
				}
			}
			
			$dict 		= DictData::where('name','in',$name)->column('id','name');
			$add 		= [];
			$update 	= [];
			foreach($res as $k=>$vo){
				if(empty($dict[$vo['name']])){
					$vo['value']	= $vo['name'];
					$vo['type_id']	= $type_id;
					$add[] 			= $vo;
				}else{
					$vo['id'] 		= $dict[$vo['name']];	
					$update[] 		= $vo;
				}
			}
			if($add){
				(new DictData)->saveAll($add);
			}
			if($update){
				(new DictData)->saveAll($update);
			}			
			unlink('.'.$file);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

}