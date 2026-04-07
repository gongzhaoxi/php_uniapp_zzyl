<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\{ErpCustomer,Region};
use app\admin\validate\ErpCustomerValidate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ErpCustomerLogic extends BaseLogic{

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$field = 'id,status,region_type,name,contacts,phone,address,address_en,region_type,sn,region';
        $list = ErpCustomer::withSearch(['query'],['query'=>$query])->field($field)->order('id','desc')->append(['status_desc','region_type_desc','region_name'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

    // 添加
    public static function goAdd($data)
    {
        //验证
        $validate = new ErpCustomerValidate;
        if(!$validate->scene('add')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
        try {
			$data['admin_id'] = self::$adminUser['id'];
            ErpCustomer::create($data);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
    
    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpCustomer::where($map)->find();
		}else{
			return ErpCustomer::find($map);
		}
    }
	
    // 编辑
    public static function goEdit($data)
    {
        //验证
        $validate 	= new ErpCustomerValidate;
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
        $validate 	= new ErpCustomerValidate;
        if(!$validate->scene('remove')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}	
        try{
			ErpCustomer::destroy($data['ids']);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 获取回收站
    public static function getRecycle($query=[],$limit=10)
	{
        $list = ErpCustomer::onlyTrashed()->withSearch(['query'],['query'=>$query])->append(['status_desc','region_type_desc'])->order('id','desc')->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

	//恢复/删除回收站
    public static function goRecycle($ids,$action)
    {
        $validate 		= new ErpCustomerValidate;
        if(!$validate->scene('recycle')->check(['ids'=>$ids])){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		try{
			if($action){
				$data 	= ErpCustomer::onlyTrashed()->whereIn('id', $ids)->select();
				foreach($data as $k){
					$k->restore();
				}				
			}else{				
				ErpCustomer::destroy($ids,true);
			}
		}catch (\Exception $e){
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
		}
		return ['msg'=>'操作成功'];
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
			$reader 	= IOFactory::createReader('Xlsx');
			$spreadsheet= $reader->load('.'.$file);
			$sheet 		= $spreadsheet->getActiveSheet();
			$field 		= ['A'=>'name','B'=>'contacts','C'=>'phone','D'=>'region_name','E'=>'address','F'=>'region_type','G'=>'invoice_title','H'=>'invoice_code'];
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
					if($k == 'F'){
						$tmp[$field[$k]] 	= $value!='国内'?2:1;
					}else{
						$tmp[$field[$k]] 	= $value;
					}
				}
				if($tmp){
					$res[] 	= $tmp;
				}
			}
			
			$customer 	= ErpCustomer::where('name','in',$name)->column('id','name');
			$region		= Region::where('name','in',array_column($res,'region_name'))->column('id','name');
			$count 		= ErpCustomer::count() + 1;
			
			$add 		= [];
			$update 	= [];
			foreach($res as $k=>$vo){
				if(!empty($region[$vo['region_name']])){
					$arr 			= [];
					$region_id		= (string)$region[$vo['region_name']];
					if(substr($region_id,4,2) != '00'){
						$arr[] 		= substr($region_id,0,2).'0000';
						$arr[] 		= substr($region_id,0,4).'00';
					}else if(substr($region_id,2,2) != '00'){
						$arr[] 		= substr($region_id,0,2).'0000';
					}
					$arr[] 			= $region_id;
					$vo['region'] 	= $arr;	
				}
				if(empty($customer[$vo['name']])){
					$vo['sn'] 	= sprintf("%04d",$count);
					$count++;
					$add[] 		= $vo;
				}else{
					$vo['id'] 	= $customer[$vo['name']];	
					$update[] 	= $vo;
				}
			}
			if($add){
				(new ErpCustomer)->saveAll($add);
			}
			if($update){
				(new ErpCustomer)->saveAll($update);
			}			
			unlink('.'.$file);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
    public static function getAll($query=[])
    {
		return ErpCustomer::withSearch(['query'],['query'=>$query])->field('id,name')->where('status',1)->order(['id'=>'desc'])->select();
    }
}
