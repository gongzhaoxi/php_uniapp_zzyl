<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\ErpProduct;
use app\admin\validate\ErpProductValidate;
use app\common\model\{ErpProductBom,ErpMaterialBom};
use app\common\model\DictData;
use app\common\model\{ErpProductProject,ErpOrderShipping,ErpDrawing};
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;


class ErpProductLogic extends BaseLogic{

	// 获取分类
    public static function getCategory()
    {
		$data = get_dict_data('product_category');
		return $data;
    }

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$field = 'id,status,name,sn,cid,unit,specs,model,remark,photo,status';
        $list = ErpProduct::withSearch(['query'],['query'=>$query])->field($field)->order('id','desc')->append(['status_desc','category_name','photo_link'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

    // 添加
    public static function goAdd($data,$count=null)
    {
        //验证
        $validate 		= new ErpProductValidate;
        if(!$validate->scene('add')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		if(empty($data['sn'])){
			$data['sn']	= self::getSn($count);
		}
        try {
            ErpProduct::create($data);
			return ['msg'=>'创建成功','code'=>200];
        }catch (\Exception $e){
            return ['msg'=>'创建失败'.$e->getMessage(),'code'=>201];
        }
    }
    
	public static function getSn($count=null){
		if($count === null){
			$count 	= ErpProduct::withTrashed()->whereDay('create_time')->count() + 1;
		}
		return date('YmdH').sprintf("%05d",$count);
	}
	

    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpProduct::where($map)->find();
		}else{
			return ErpProduct::find($map);
		}
    }
	
    // 编辑
    public static function goEdit($data)
    {
        //验证
        $validate 	= new ErpProductValidate;
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
        $validate 	= new ErpProductValidate;
        if(!$validate->scene('remove')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}	
        try{
			ErpProduct::destroy($data['ids'],true);
			
			ErpProductBom::destroy(function($query) use($data){
				$query->where('product_id','in',$data['ids']);
			},true);
			
			ErpProductProject::destroy(function($query) use($data){
				$query->where('product_id','in',$data['ids']);
			},true);			
			
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 获取回收站
    public static function getRecycle($query=[],$limit=10)
	{
		$field = 'id,status,name,sn,cid,unit,specs,model,remark,photo,status';
        $list = ErpProduct::onlyTrashed()->withSearch(['query'],['query'=>$query])->field($field)->append(['status_desc','category_name','photo_link'])->order('id','desc')->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

	//恢复/删除回收站
    public static function goRecycle($ids,$action)
    {
        $validate 		= new ErpProductValidate;
        if(!$validate->scene('recycle')->check(['ids'=>$ids])){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		try{
			if($action){
				$data 	= ErpProduct::onlyTrashed()->whereIn('id', $ids)->select();
				foreach($data as $k){
					$k->restore();
				}				
			}else{				
				ErpProduct::destroy($ids,true);
			}
		}catch (\Exception $e){
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
		}
		return ['msg'=>'操作成功'];
    }


	
    public static function goCopy($id)
    {
        //验证
		$model 		= self::getOne($id);
        if($model->isEmpty()) {
			return ['msg'=>'数据不存在','code'=>201];
		}
        try {
			$bom_data 			= [];
			$product			= ErpProduct::create(['sn'=>self::getSn(),'name'=>$model['name'],'cid'=>$model['cid'],'specs'=>$model['specs'],'model'=>$model['model'],'unit'=>$model['unit'],'remark'=>$model['remark'],'photo'=>$model['photo']]);
			$projects 			= ErpProductProject::with(['bom'])->where('product_id',$id)->select();
			foreach($product->bom as $vo){
				$bom_data[] 	= ['product_id'=>$product['id'],'project_id'=>0,'material_id'=>$vo['material_id'],'color_follow'=>$vo['color_follow'],'bill_type'=>$vo['bill_type'],'can_replace'=>$vo['can_replace'],'num'=>$vo['num'],'data_type'=>$vo['data_type']];
			}
			foreach($projects as $v){
				$project 		= ErpProductProject::create(['product_id'=>$product->id,'name'=>$v['name'],'code'=>$v['code'].rand(1000,9999),'cid'=>$v['cid'],'type'=>$v['type']]);
				foreach($v->bom as $vo){
					$bom_data[] = ['product_id'=>$product['id'],'project_id'=>$project['id'],'material_id'=>$vo['material_id'],'color_follow'=>$vo['color_follow'],'bill_type'=>$vo['bill_type'],'can_replace'=>$vo['can_replace'],'num'=>$vo['num'],'data_type'=>$vo['data_type']];
				}
			}
			if($bom_data){
				(new ErpProductBom)->saveAll($bom_data);
			}
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }
	
	
	// 获取带bom的列表
    public static function getListWithBom($query=[],$limit=10)
    {
		$field = 'id,status,name,sn,cid,unit,specs,model,remark,photo,status';
        $list = ErpProduct::withSearch(['query'],['query'=>$query])->with(['bom.material','project'])->field($field)->order('id','desc')->append(['status_desc','category_name','photo_link','bom.bill_type_name'])->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }
	
	
	public static function goImport($file){
		if(empty($file) || !is_file('.'.$file)) {
			return ['msg'=>'excel文件不存在','code'=>201];
		}
		ini_set("memory_limit","-1");
		ini_set('max_execution_time', '30');		
		$product 	= ErpProduct::column('id','sn');
		$category 	= DictData::where('type_id',3)->column('id','name');
		
		try {
			$reader 	= IOFactory::createReader('Xlsx');
			$spreadsheet= $reader->load('.'.$file);
			$sheet 		= $spreadsheet->getActiveSheet();
			$res 		= [];
			$field 		= ['A'=>'sn','B'=>'name','C'=>'cid','D'=>'model','E'=>'specs','F'=>'unit','G'=>'remark','H'=>'region_type'];
			foreach ($sheet->getRowIterator(2) as $row) {
				$tmp 	= [];
				foreach ($row->getCellIterator() as $k=>$cell) {
					if(empty($field[$k])){
						break;
					}
					$value 				= trim($cell->getFormattedValue());					
					if($k == 'A' && (!empty($product[$value]) || !$value)){
						$tmp 			= [];
						break;
					}
					if($k == 'C'){
						if(!empty($category[$value])){
							$tmp[$field[$k]] = $category[$value];
						}else{
							$tmp[$field[$k]] = 0;
						}
					}else if($k == 'H'){
						$tmp[$field[$k]] = $value=='国外'?2:1;
					}else{
						$tmp[$field[$k]] = $value;
					}
				}
				if($tmp){
					$res[] 	= $tmp;
				}
			}
			if($res){
				(new ErpProduct)->saveAll($res);
			}
			unlink('.'.$file);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
	}
	
	public static function getShippingCount($query){
		$map 		= [];
		if($query['act'] == 1){
			$map[]	= ['a.out_warehouse_time','=',date('Y-m-d')];					
		}
		if($query['act'] == 2){
			$map[]	= ['a.out_warehouse_time','>=',date('Y-m-01')];
			$map[]	= ['a.out_warehouse_time','<=',date('Y-m-'.date("t"))];		
		}		
		if($query['act'] == 3){
			$map[]	= ['a.out_warehouse_time','>=',date('Y-01-01')];
			$map[]	= ['a.out_warehouse_time','<=',date('Y-12-31')];		
		}
		if($query['year'] && $query['month']){
			$map[]	= ['a.out_warehouse_time','>=',$query['year'].'-'.$query['month'].'-01'];
			$map[]	= ['a.out_warehouse_time','<=',date('Y-m-'.date("t",strtotime($query['year'].'-'.$query['month'])))];	
		}else if($query['year']){
			$map[]	= ['a.out_warehouse_time','>=',$query['year'].'-01-01'];
			$map[]	= ['a.out_warehouse_time','<=',$query['year'].'-12-31'];	
		}
		$tmp 		= ErpOrderShipping::alias('a')->join('erp_product b','a.product_id = b.id','LEFT')->field('a.product_id,sum(a.num) as num')->where($map)->group('a.product_id')->select();
		$count		= [];
		foreach($tmp as $vo){
			$count[$vo['product_id']] 	= $vo['num'];
		}
		$list 							= ErpProduct::field('id,model,specs')->select();
		foreach($list as &$vo){
			$vo['count'] 				= empty($count[$vo['id']])?0:$count[$vo['id']];
		}
		return $list;
	}
	
	
	public static function getBom($product_id){
		$tmp 		= ErpProductBom::alias('a')->join('erp_material b','a.material_id = b.id','LEFT')->field('a.*,b.type,b.stock,b.name,b.sn,b.material,b.surface,b.color,b.unit,b.remark,b.photo')->where('a.product_id',$product_id)->where('a.project_id',0)->select();
		$tmp2 		= ErpDrawing::where('sn','in',$tmp->column('sn'))->select();
		$drawing	= [];
		foreach($tmp2 as $vo){
			$drawing[$vo['sn']][] = $vo;
		}

		$data1 	= [];
		$data2 	= [];
		foreach($tmp as $vo){
			if($vo['type'] == 2){
				$data1[$vo['material_id']] 	= $vo->toArray();
				$data1[$vo['material_id']]['drawing']				= $drawing[$vo['sn']]??[];
			}else{
				$data2[$vo['material_id']] 	= $vo->toArray();
				$data2[$vo['material_id']]['drawing']				= $drawing[$vo['sn']]??[];
			}
			
		}
		if($data1){
			$tmp = ErpMaterialBom::alias('a')->join('erp_material b','a.related_material_id = b.id','LEFT')->field('a.*,b.type,b.stock,b.name,b.sn,b.material,b.surface,b.color,b.unit,b.remark,b.photo')
			->where('a.material_id','in',array_column($data1,'material_id'))->select();
			
			$tmp2 		= ErpDrawing::where('sn','in',$tmp->column('sn'))->select();
			$drawing	= [];
			foreach($tmp2 as $vo){
				$drawing[$vo['sn']][] = $vo;
			}			
			
			foreach($tmp as $vo){
				$d 				= $vo->toArray();
				$d['num']		= $vo['num']*$data1[$vo['material_id']]['num'];
				$d['drawing']	= $drawing[$vo['sn']]??[];
				if(!empty($data2[$vo['related_material_id']])){
					$data2[$vo['related_material_id']]['num'] = $data2[$vo['related_material_id']]['num'] + $vo['num'];
				}else{
					$data2[$vo['related_material_id']] = $d;
				}
				
			}
		}
		return ['data1'=>$data1,'data2'=>$data2,'product_id'=>$product_id];
	}
	
	public static function getBomExport($product_id,$type){
		$return					= ['column'=>[],'setWidh'=>[],'keys'=>[],'list'=>[]];
		$return['image_fields'] = ['photo'];
		if($type == 2){
			$return['column'] 	= ['物料编码','物料名称','现有库存','数量','单位','材料','表面','颜色','备注','图片'];
			$return['keys'] 	= ['sn','name','stock','num','unit','material','surface','color','remark','photo'];
			$return['setWidh']	= ['10','10','10','10','10','10','10','10','10','10','10','10','10','10','15'];
		}else{
			$return['column'] 	= ['物料编码','物料名称','现有库存','数量','单位','颜色','备注','图片'];
			$return['keys'] 	= ['sn','name','stock','num','unit','color','remark','photo'];
			$return['setWidh']	= ['10','10','10','10','10','10','10','15'];
		}
        $data 					= self::getBom($product_id);;
		$return['list']			= array_values($data['data'.$type]);
        return $return;	
	}
	
}
