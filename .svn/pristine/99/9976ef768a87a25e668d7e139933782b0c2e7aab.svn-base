<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\admin\logic\BaseLogic;
use app\common\model\ErpMaterial;
use app\common\model\ErpWarehouse;
use app\common\model\DictData;
use app\common\model\{ErpMaterialScrap,ErpSupplier,ErpMaterialChange,ErpPurchaseApply,ErpPurchaseOrderData,ErpMaterialEnterMaterial,ErpMaterialEnter,ErpMaterialOut,ErpMaterialOutMaterial,ErpMaterialCheckMaterial,ErpProductBom,ErpMaterialBom,ErpMaterialTree,ErpMaterialWarehouse,ErpMaterialDiscardMaterial};
use app\admin\validate\ErpMaterialValidate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use app\common\enum\{ErpPurchaseApplyEnum,ErpPurchaseOrderDataEnum,ErpMaterialEnum,ErpMaterialStockEnum,ErpMaterialEnterMaterialEnum,ErpMaterialOutMaterialEnum,ErpMaterialCheckMaterialEnum,ErpMaterialDiscardMaterialEnum};

class ErpMaterialLogic extends BaseLogic{


	// 获取仓库
    public static function getWarehouse($type)
    {
		$data = ErpWarehouse::field('id,name')->where('status',1)->where('type',$type)->select();
		return $data;
    }

	// 获取分类
    public static function getCategory($type)
    {
		$data = get_dict_data($type);
		return $data;
    }

	// 获取列表
    public static function getList($query=[],$limit=10)
    {
		$field = 'id,status,type,name,sn,cid,safety_stock,min_stock,max_stock,unit,processing_type,material,surface,color,remark,workstation_id,photo,warehouse_id,status,stock,processing_way,supplier_id as supplier_ids,material_category';
        $list = ErpMaterial::withSearch(['query'],['query'=>$query])->with(['drawing'=>function($query){$query->field('id,sn,final_pic')->where('status',1)->where('final_pic','<>','');}])->field($field)->order('id','desc')->append(['status_desc','type_desc','category_name','photo_link','processing_way_desc'])->paginate($limit);
        $data = $list->items();
		$supplier = ErpSupplier::where('id','in',implode(',',array_column($data,'warehouse_id')))->column('name','id');
		$warehouse = ErpWarehouse::where('id','in',array_merge(array_column($data,'warehouse_id'),array_column($data,'workstation_id')))->column('name','id');
		$supplier 	= ErpSupplier::where('id','in',implode(',',array_column($data,'supplier_ids')))->column('name','id');
		
		foreach($data as &$vo){
			$vo['supplier_id'] 	= $vo['supplier_ids']?explode(',',$vo['supplier_ids']):[];
			$name 				= [];
			foreach($vo['supplier_id'] as $v){
				if(!empty($supplier[$v])){
					$name[] 	= $supplier[$v];
				}
			}
			$vo['supplier_name']= implode(',',$name);
			$vo['warehouse_name'] = !empty($warehouse[$vo['warehouse_id']])?$warehouse[$vo['warehouse_id']]:'';
			$vo['workstation_name'] = !empty($warehouse[$vo['workstation_id']])?$warehouse[$vo['workstation_id']]:'';
			$vo['final_pic']		= [];
			if($vo['drawing']){
				$vo['final_pic']	= $vo['drawing']->column('final_pic');
			}			
		}
		return ['code'=>0,'data'=>$data,'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

    // 添加
    public static function goAdd($data,$count=null)
    {
        //验证
        $validate 		= new ErpMaterialValidate;
        if(!$validate->scene('add')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		if(empty($data['sn'])){
			$data['sn']	= self::getSn($count);
		}
        try {
            ErpMaterial::create($data);
			return ['msg'=>'创建成功','code'=>200];
        }catch (\Exception $e){
            return ['msg'=>'创建失败'.$e->getMessage(),'code'=>201];
        }
    }
    
	public static function getSn($count=null){
		if($count === null){
			$count 	= ErpMaterial::withTrashed()->whereDay('create_time')->count() + 1;
		}
		return date('YmdH').sprintf("%05d",$count);
	}
	
    // 导入
    public static function goImport($type,$file)
    {
		if(empty($file) || !is_file('.'.$file)) {
			return ['msg'=>'excel文件不存在','code'=>201];
		}
		ini_set("memory_limit","-1");
		ini_set('max_execution_time', '300');
		try {
			$tree_id	= ErpMaterialTree::where('type','=',$type)->column('id','title');
			$produce_type=DictData::where('type_id','=',7)->column('id','name');
			$category 	= DictData::where('type_id','=',$type)->column('id','name');
			$warehouse	= ErpWarehouse::column('id','name');
			$supplier		= ErpSupplier::column('id','name');
			$processing_way 		= ErpMaterialEnum::getProcessingWayDesc();
			$quality_testing_area 	= DictData::where('type_id','=',12)->column('id','name');
			
			
			$reader 	= IOFactory::createReader('Xlsx');
			$spreadsheet= $reader->load('.'.$file);
			$sheet 		= $spreadsheet->getActiveSheet();
			
			if($type == 1){
				$field 	= ['A'=>'sn','B'=>'name','C'=>'unit','D'=>'cid','E'=>'material','F'=>'surface','G'=>'color','H'=>'remark','I'=>'photo','J'=>'processing_type','K'=>'produce_type','L'=>'tree_id','M'=>'safety_stock','N'=>'min_stock','O'=>'max_stock','P'=>'warehouse_id','Q'=>'generality','R'=>'supplier_id','S'=>'processing_way','T'=>'quality_testing_area','U'=>'qc_code','V'=>'supplier_code','W'=>'material_category','X'=>'workstation_id','Y'=>'tag'];
			}else{
				$field 	= ['A'=>'sn','B'=>'name','C'=>'unit','D'=>'material','E'=>'surface','F'=>'color','G'=>'remark','H'=>'cid','I'=>'stock','J'=>'safety_stock','K'=>'min_stock','L'=>'warehouse_id','M'=>'photo','N'=>'produce_type','O'=>'tree_id','P'=>'material_category'];
			}

			$sns 		= [];
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
							$tmp[$field[$k]] = $photo[$k][$ii];
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
			
			$material 	= ErpMaterial::where('sn','in',$sns)->column('id','sn');
			$add 		= [];
			$update 	= [];
			foreach($res as $k=>$vo){
				if(empty($material[$vo['sn']])){
					$vo['type'] = $type;
					$add[] 		= $vo;
				}else{
					$vo['id'] 	= $material[$vo['sn']];	
					$update[] 	= $vo;
				}
			}
			if($add){
				(new ErpMaterial)->saveAll($add);
			}
			if($update){
				(new ErpMaterial)->saveAll($update);
			}			
			unlink('.'.$file);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }	
	
	
    public static function getOne($map)
    {
		if(is_array($map)){
			return ErpMaterial::where($map)->find();
		}else{
			return ErpMaterial::find($map);
		}
    }
	
    // 编辑
    public static function goEdit($data)
    {
        //验证
        $validate 	= new ErpMaterialValidate;
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
        $validate 	= new ErpMaterialValidate;
        if(!$validate->scene('remove')->check($data)){
			return ['msg'=>$validate->getError(),'code'=>201];
		}	
        try{
			ErpMaterial::destroy($data['ids'],true);
			ErpMaterialBom::destroy(function($query) use($data){
				$query->where('material_id|related_material_id','in',$data['ids']);
			},true);
			ErpProductBom::destroy(function($query) use($data){
				$query->where('material_id','in',$data['ids']);
			},true);
			
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

    // 获取回收站
    public static function getRecycle($query=[],$limit=10)
	{
		$field = 'id,status,type,name,sn,cid,safety_stock,min_stock,max_stock,unit,processing_type,material,surface,color,remark,photo,warehouse_id,status';
        $list = ErpMaterial::onlyTrashed()->withSearch(['query'],['query'=>$query])->field($field)->append(['status_desc','type_desc','category_name','photo_link'])->order('id','desc')->paginate($limit);
        return ['code'=>0,'data'=>$list->items(),'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

	//恢复/删除回收站
    public static function goRecycle($ids,$action)
    {
        $validate 		= new ErpMaterialValidate;
        if(!$validate->scene('recycle')->check(['ids'=>$ids])){
			return ['msg'=>$validate->getError(),'code'=>201];
		}
		try{
			if($action){
				$data 	= ErpMaterial::onlyTrashed()->whereIn('id', $ids)->select();
				foreach($data as $k){
					$k->restore();
				}				
			}else{				
				ErpMaterial::destroy($ids,true);
			}
		}catch (\Exception $e){
			return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
		}
		return ['msg'=>'操作成功'];
    }


	// 获取分类
    public static function getStockCount($query)
    {
		return ErpMaterial::withSearch(['query'],['query'=>$query])->count();
    }

	public static function getExport($query=[],$limit=10){
		$limit					= $limit>10000?10000:$limit;
		$return					= ['column'=>[],'setWidh'=>[],'keys'=>[],'list'=>[]];
		$return['image_fields'] = ['photo'];
		
		$tree_id				= ErpMaterialTree::where('type','=',$query['type'])->column('title','id');
		$produce_type			= DictData::where('type_id','=',7)->column('name','id');
		$supplier				= ErpSupplier::column('name','id');
		
		if($query['type'] == ErpMaterialEnum::PARTN){
			$return['column'] 	= ['物料编码','物料名称','物料分类','现有库存','安全库存','最低库存','最高库存','仓位','单位','加工类型','材料','表面','颜色','备注','图片','生产物料类别','目录树','通用性','供应商'];
			$return['keys'] 	= ['sn','name','category_name','stock','safety_stock','min_stock','max_stock','warehouse_name','unit','processing_type','material','surface','color','remark','photo','produce_type','tree_id','generality','supplier_id'];
			$return['setWidh']	= ['10','10','10','10','10','10','10','10','10','10','10','10','10','10','15','10','10','10','10'];
		}else{
			$return['column'] 	= ['物料编码','物料名称','物料分类','现有库存','安全库存','最低库存','最高库存','仓位','单位','备注','图片','生产物料类别','目录树','通用性'];
			$return['keys'] 	= ['sn','name','category_name','stock','safety_stock','min_stock','max_stock','warehouse_name','unit','remark','photo','produce_type','tree_id','generality'];
			$return['setWidh']	= ['10','10','10','10','10','10','10','10','10','10','15','10','10','10'];
		}
		
		$field 					= 'id,status,type,name,sn,cid,safety_stock,min_stock,max_stock,unit,processing_type,material,surface,color,remark,photo,warehouse_id,status,stock,produce_type,tree_id,generality,supplier_id';
        $list 					= ErpMaterial::withSearch(['query'],['query'=>$query])->with(['warehouse','category'])->field($field)->order('id','desc')->limit(0,$limit)->select()->toArray();
		foreach($list as &$vo){
			$vo['warehouse_name'] 	= empty($vo['warehouse'])?'':$vo['warehouse']['name'];
			$vo['category_name'] 	= empty($vo['category'])?'':$vo['category']['name'];
			$vo['produce_type'] 	= empty($produce_type[$vo['produce_type']])?'':$produce_type[$vo['produce_type']];
			$vo['tree_id'] 			= empty($tree_id[$vo['tree_id']])?'':$tree_id[$vo['tree_id']];
			$vo['generality'] 		= empty($vo['generality'])?'不通用':'通用';
			$tmp 					= [];
			foreach($vo['supplier_id'] as $v){
				if(!empty($supplier[$v])){
					$tmp[]			= $supplier[$v];
				}
			}
			$vo['supplier_id'] 		= implode(',',$tmp);
		}
		$return['list']		= $list;
        return $return;	
	}
	
	
	//获取选取物料数据
    public static function getSelect($query=[],$limit=10){
		$field 		= 'id,status,name,sn,stock,max_stock,unit,cid,type,processing_way,safety_stock,supplier_id as supplier_id2,warehouse_id';
		
        $list 		= ErpMaterial::withSearch(['query'],['query'=>$query])->field($field)->append(['category_name','type_desc'])->order('id','desc')->paginate($limit);
        $data		= $list->items();
		$supplier 	= ErpSupplier::where('id','in',implode(',',array_unique(array_column($data,'supplier_id2'))))->column('name','id');
		foreach($data as &$vo){
			$vo['supplier_id'] 	= $vo['supplier_id2']?explode(',',$vo['supplier_id2']):[];
			$supplier_name 	 	= [];
			foreach($vo['supplier_id'] as $item){
				$supplier_name[]= $supplier[$item];
			}
			$vo['supplier_name']= implode(',',$supplier_name);
		}
		return ['code'=>0,'data'=> $data,'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }
	
	//获取物料库存
    public static function getMaterialStock($query=[],$limit=10)
    {
		$field 					= 'id,status,type,name,sn,cid,unit,max_stock,warehouse_id,workstation_id';
        $list 					= ErpMaterial::withSearch(['query'],['query'=>$query])->field($field)->order('id','desc')->paginate($limit);
		$data 					 = $list->items();
		
		foreach($data as &$item){
			
			$map 				= [];
			$map[] 				= ['a.material_id','=',$item['id']];
			if(!empty($query['warehouse_type'])){
				$map[] 			= ['c.type','in',$query['warehouse_type']];
			}
			$enter 				= ErpMaterialEnterMaterial::alias('a')
			->join('erp_material_stock b','a.material_stock_id = b.id','LEFT')
			->join('erp_warehouse c','a.warehouse_id = c.id','LEFT')
			->fieldRaw('a.id,a.warehouse_id,a.can_out_num,a.freeze_out_stock,a.can_out_num - a.freeze_out_stock as num,b.order_sn')
			->whereRaw('a.can_out_num>a.freeze_out_stock')->where('a.can_out_num','>',0)->where($map)->select();
			$freeze_out_stock	= ErpMaterialEnterMaterial::alias('a')->join('erp_warehouse c','a.warehouse_id = c.id','LEFT')->where('a.freeze_out_stock','>',0)->where($map)->sum('a.freeze_out_stock');
			$can_out_num		= ErpMaterialEnterMaterial::alias('a')->join('erp_warehouse c','a.warehouse_id = c.id','LEFT')->where('a.can_out_num','>',0)->where($map)->sum('a.can_out_num');
			$item['enter']		= $enter;
			$item['enter_sn'] 	= implode(',', $enter->column('order_sn'));
			$item['stock']		= $can_out_num - $freeze_out_stock;
			$item['freeze_out_stock']		= $freeze_out_stock;
			$d1				= [];
			$d2				= [];
			$warehouse		= ErpMaterialWarehouse::where('material_id','=',$item['id'])->select();

			foreach($warehouse as &$vo){
				$d1[$vo['warehouse_id']] = $vo['stock'];
				$d2[$vo['warehouse_id']] = $vo['max_stock'];
			}
			$item['warehouse']		= $d1;
			$item['warehouse_max']		= $d2;
			
			//$item['warehouse']	= ErpMaterialWarehouse::where('material_id','=',$item['material_id'])->column('stock','warehouse_id');
		}
		
		
		
		
		
		
        return ['code'=>0,'data'=>$data,'extend'=>['count' => $list->total(), 'limit' => $limit],'count' => $list->total(), 'limit' => $limit];
    }

	public static function checkResetMaterialStock($material_ids){
		if(ErpMaterialOutMaterial::where('material_id','in',$material_ids)->where('status','in',[ErpMaterialOutMaterialEnum::STATUS_HANDLE,ErpMaterialOutMaterialEnum::STATUS_PART])->count() || ErpMaterialDiscardMaterial::where('material_id','in',$material_ids)->where('status','in',[ErpMaterialDiscardMaterialEnum::STATUS_HANDLE,ErpMaterialDiscardMaterialEnum::STATUS_PART])->count()){
			return false;
		}else{
			return true;
		}
	}

	public static function checkOutMaterialStock($material_ids){
		if(ErpMaterialCheckMaterial::where('material_id','in',$material_ids)->where('status','in',[ErpMaterialCheckMaterialEnum::STATUS_HANDLE])->count()){
			return false;
		}else{
			return true;
		}
	}


	public static function checkEnterMaterialStock($material_ids){
		if(ErpMaterialOutMaterial::where('material_id','in',$material_ids)->where('status','in',[ErpMaterialOutMaterialEnum::STATUS_HANDLE,ErpMaterialOutMaterialEnum::STATUS_PART])->count() || ErpMaterialDiscardMaterial::where('material_id','in',$material_ids)->where('status','in',[ErpMaterialDiscardMaterialEnum::STATUS_HANDLE,ErpMaterialDiscardMaterialEnum::STATUS_PART])->count()){
			return false;
		}else{
			return true;
		}
	}	

	public static function resetMaterialStock($type=0,$ids=[],$from='correct'){
		$result 				= self::fomatMaterialStock($type,$ids);
		$data 					= $result['data'];
		$enter 					= $result['enter'];

		if(!ErpMaterialLogic::checkResetMaterialStock($data->column('material_id'))){
			return ['msg'=>'请先处理物料出库单','code'=>201];
		}		
		$update 				= [];
		$material_id 			= [];
		foreach($data as $vo){
			$update[] 			= ['id'=>$vo['id'],'stock'=>!empty($enter[$vo['material_id']][$vo['warehouse_id']])?array_sum(array_column($enter[$vo['material_id']][$vo['warehouse_id']],'num')):0];
			if(!in_array($vo['material_id'],$material_id)){
				$material_id[]	= $vo['material_id'];
			}
		}
		(new ErpMaterialWarehouse)->saveAll($update);
		$material_update 		= [];
		foreach($material_id as $vo){
			$material_update[] 	= ['id'=>$vo,'stock'=>ErpMaterialWarehouse::where('material_id',$vo)->sum('stock')];
		}
		(new ErpMaterial)->saveAll($material_update);
	}
	
	public static function fomatMaterialStock($type=0,$ids=[]){
		$map 			= [];
		if($ids){
			$map[]		= ['a.id','in',$ids];
		}
		if($type){
			//$map[]		= ['c.type','=',$type];
		}
		$data 			= ErpMaterialWarehouse::alias('a')
		->join('erp_warehouse b','a.warehouse_id = b.id','LEFT')->join('erp_material c','a.material_id = c.id','LEFT')
		->field('a.*,b.name as warehouse_name,c.type,c.type as material_type,c.name,c.sn')->where($map)->order('a.id asc')->select();
		$enter_data 	= ErpMaterialEnterMaterial::alias('a')->join('erp_material_stock b','a.material_stock_id = b.id','LEFT')->
		fieldRaw('a.id,a.warehouse_id,a.material_id,a.can_out_num,a.freeze_out_stock,a.can_out_num - a.freeze_out_stock as num,b.order_sn,b.material_type')
		->whereRaw('a.can_out_num>a.freeze_out_stock')->where('a.can_out_num','>',0)->where('a.material_id','in',$data->column('material_id'))->where('a.warehouse_id','in',$data->column('warehouse_id'))->order('a.id asc')->select()->toArray();

		$enter			= [];
		foreach($enter_data as $vo){
			$enter[$vo['material_id']][$vo['warehouse_id']][] = $vo;
		}
		return ['data'=>$data,'enter'=>$enter];	
	}
	
	public static function getErrorStock($type=0){
		$result 	= self::fomatMaterialStock($type);
		$data 		= $result['data'];
		$enter 		= $result['enter'];
		$return 	= [];
		foreach($data as $vo){
			$num 		= 0;
			if(!empty($enter[$vo['material_id']][$vo['warehouse_id']])){
				$num 	= array_sum(array_column($enter[$vo['material_id']][$vo['warehouse_id']],'num'));
			}
			if($vo['stock'] > 0 && $num != $vo['stock']){
				$tmp 		= $vo->toArray();
				$tmp['sum'] = $num;
				$return[] 	= $tmp;
			}
		}
		return $return;
	}
	
	public static function goTransfer($from_id,$to_id)
    {
		if(!$from_id){
			return ['msg'=>'请选择数据','code'=>201];
		}
		if(!$to_id){
			return ['msg'=>'请选择调拨哪','code'=>201];
		}
        try{
            ErpMaterial::where('tree_id','=',$from_id)->update(['tree_id'=>$to_id]);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
    }

	public static function getWarehouseStock($material_id){
		$material_id	= is_array($material_id)?$material_id:explode(',',$material_id);
		$stock 			= ErpMaterialWarehouse::where('material_id','in',$material_id)->select();
		$return 		= [];
		foreach($stock as $vo){
			$return[$vo['material_id']][$vo['warehouse_id']] =  $vo['stock'];
		}
		return ['code'=>200,'data'=>$return];
	}

	// 获取列表
    public static function getStockStat($query=[],$limit=10)
    {
		$field 	= 'id,status,type,name,sn,unit,stock,supplier_id as supplier_ids';
        $list 	= ErpMaterial::withSearch(['query'],['query'=>$query])->with(['warehouse'])->field($field)->order('supplier_id','asc')->paginate($limit);
		$data 	= $list->items();
		$time 	= explode('至',$query['create_time']);
		$start 	= (trim($time[0]));
		$end 	= (trim($time[1]));
		$supplier = ErpSupplier::where('id','in',implode(',',array_column($data,'supplier_ids')))->column('name','id');
		
		$ids 	= array_column($data,'id');
		$num1 	= [];
		$tmp	= ErpMaterialWarehouse::alias('a')->join('erp_warehouse b','a.warehouse_id = b.id','LEFT')
		->where('a.material_id','in',$ids)->where('b.type','in','1,2')->group('a.material_id')->field('a.material_id,sum(a.stock) as stock')->select();
		foreach($tmp as $vo){
			$num1[$vo['material_id']]	= $vo['stock'];
		}
		
		$num2 	= [];
		$tmp	= ErpMaterialWarehouse::alias('a')->join('erp_warehouse b','a.warehouse_id = b.id','LEFT')
		->where('a.material_id','in',$ids)->where('b.type','=',3)->group('a.material_id')->field('a.material_id,sum(a.stock) as stock')->select();
		foreach($tmp as $vo){
			$num2[$vo['material_id']]	= $vo['stock'];
		}		
		
		$num3 	= [];
		$tmp	= ErpMaterialEnterMaterial::alias('a')
			->join('erp_material_enter_material_report d','a.id = d.material_enter_material_id','LEFT')
			->where('d.id is null or d.status = 1')->where('a.need_check = 1')
			->where('a.status','<>',ErpMaterialEnterMaterialEnum::STATUS_CANCEL)
			->where('a.status','<>',ErpMaterialEnterMaterialEnum::STATUS_FINISH)
			->where('a.material_id','in',$ids)
			->where('a.check_status','in',[ErpMaterialEnterMaterialEnum::CHECK_STATUS_NOTICED,ErpMaterialEnterMaterialEnum::CHECK_STATUS_PART,ErpMaterialEnterMaterialEnum::CHECK_STATUS_FINISH])
			->group('a.material_id')->field('a.material_id,sum(a.stock_num) as stock_num')->select();
		foreach($tmp as $vo){
			$num3[$vo['material_id']]	= $vo['stock_num'];
		}			
		
		$num4 	= [];
		$tmp	= ErpPurchaseOrderData::alias('a')->join('erp_purchase_order b','a.order_id = b.id','LEFT')
		->where('b.order_date','>=',$start)->where('b.order_date','<=',$end)->where('a.data_id','in',$ids)->where('a.type',1)->group('a.data_id')->field('a.data_id,sum(a.apply_num) as apply_num')->select();
		foreach($tmp as $vo){
			$num4[$vo['data_id']]	= $vo['apply_num'];
		}	
		
		$num5 	= [];
		$tmp	= ErpPurchaseOrderData::where('data_id','in',$ids)->where('type',1)
		->where('status','in',[ErpPurchaseOrderDataEnum::STATUS_NO,ErpPurchaseOrderDataEnum::STATUS_YES])->group('data_id')->field('data_id,sum(apply_num) as apply_num,sum(warehous_num) as warehous_num')->select();
		foreach($tmp as $vo){
			$num5[$vo['data_id']]= $vo['apply_num'] - $vo['warehous_num'];
		}	
		
		
		$num8 	= [];
		$tmp	= ErpMaterialChange::alias('a')->join('erp_material_stock b','a.material_stock_id = b.id','LEFT')
		->where('b.stock_date','>=',$start)->where('b.stock_date','<=',$end)->where('a.material_id','in',$ids)->where('a.stock_num','>',0)->group('a.material_id')->field('a.material_id,sum(a.stock_num) as stock_num')->select();
		foreach($tmp as $vo){
			$num8[$vo['material_id']]= $vo['stock_num'];
		}			
		
		$num9 	= [];
		$tmp	= ErpMaterialChange::alias('a')->join('erp_material_stock b','a.material_stock_id = b.id','LEFT')
		->where('b.stock_date','>=',$start)->where('b.stock_date','<=',$end)->where('a.material_id','in',$ids)->where('a.stock_num','<',0)->group('a.material_id')->field('a.material_id,sum(a.stock_num) as stock_num')->select();
		foreach($tmp as $vo){
			$num9[$vo['material_id']]= abs((float)$vo['stock_num']);
		}		
		
		$num10 	= [];
		$tmp	= ErpMaterialScrap::alias('a')->where('a.material_id','in',$ids)->group('material_id')->field('material_id,sum(stocked_num) as stocked_num')->select();
		foreach($tmp as $vo){
			$num10[$vo['material_id']]= $vo['stocked_num'];
		}		
		
		foreach($data as &$vo){
			$vo['supplier_id'] 	= $vo['supplier_ids']?explode(',',$vo['supplier_ids']):[];
			$name 				= [];
			foreach($vo['supplier_id'] as $v){
				if(!empty($supplier[$v])){
					$name[] 	= $supplier[$v];
				}
			}
			$vo['supplier_name']= implode(',',$name);			
			
			//$vo['num1'] 	= ErpMaterialWarehouse::alias('a')->join('erp_warehouse b','a.warehouse_id = b.id','LEFT')->where('a.material_id',$vo['id'])->where('b.type','in','1,2')->sum('stock');
			$vo['num1'] 	= empty($num1[$vo['id']])?0:$num1[$vo['id']];
			//$vo['num2'] 	= ErpMaterialWarehouse::alias('a')->join('erp_warehouse b','a.warehouse_id = b.id','LEFT')->where('a.material_id',$vo['id'])->where('b.type','=',3)->sum('stock');
			$vo['num2'] 	= empty($num2[$vo['id']])?0:$num2[$vo['id']];
			//$vo['num3'] 	= ErpMaterialEnterMaterial::where('material_id',$vo['id'])->where('status','in',[ErpMaterialEnterMaterialEnum::STATUS_HANDLE,ErpMaterialEnterMaterialEnum::STATUS_PART])->sum('stock_num');
			/*
			$vo['num3'] 	= ErpMaterialEnterMaterial::alias('a')
			->join('erp_material_enter_material_report d','a.id = d.material_enter_material_id','LEFT')
			->where('d.id is null or d.status = 1')->where('a.need_check = 1')
			->where('a.status','<>',ErpMaterialEnterMaterialEnum::STATUS_CANCEL)
			->where('a.status','<>',ErpMaterialEnterMaterialEnum::STATUS_FINISH)
			->where('a.material_id',$vo['id'])
			->where('a.check_status','in',[ErpMaterialEnterMaterialEnum::CHECK_STATUS_NOTICED,ErpMaterialEnterMaterialEnum::CHECK_STATUS_PART,ErpMaterialEnterMaterialEnum::CHECK_STATUS_FINISH])
			->sum('stock_num');*/
			$vo['num3'] 	= empty($num3[$vo['id']])?0:$num3[$vo['id']];
			
			//$vo['num4'] 	= ErpPurchaseOrderData::alias('a')->join('erp_purchase_order b','a.order_id = b.id','LEFT')->where('b.order_date','>=',$start)->where('b.order_date','<=',$end)->where('a.data_id',$vo['id'])->where('a.type',1)->sum('a.apply_num');
			$vo['num4'] 	= empty($num4[$vo['id']])?0:$num4[$vo['id']];
			//$vo['num5'] 	= ErpPurchaseOrderData::where('data_id',$vo['id'])->where('type',1)->where('status','in',[ErpPurchaseOrderDataEnum::STATUS_NO,ErpPurchaseOrderDataEnum::STATUS_YES])->sum('apply_num') - ErpPurchaseOrderData::where('data_id',$vo['id'])->where('type',1)->where('status','in',[ErpPurchaseOrderDataEnum::STATUS_NO,ErpPurchaseOrderDataEnum::STATUS_YES])->sum('warehous_num');
			$vo['num5'] 	= empty($num5[$vo['id']])?0:$num5[$vo['id']];
			$vo['num6'] 	= $vo['num1']+$vo['num2']+$vo['num3']+$vo['num5'];
			
			$vo['num7'] 	= (int)ErpMaterialChange::alias('a')->join('erp_material_stock b','a.material_stock_id = b.id','LEFT')->where('a.material_id',$vo['id'])->where('b.stock_date','<',$start)->order('a.id desc')->value('after_num');
			//$vo['num8'] 	= ErpMaterialEnterMaterial::alias('a')->join('erp_material_stock b','a.material_stock_id = b.id','LEFT')->where('a.material_id',$vo['id'])->where('b.type','in',[ErpMaterialStockEnum::TYPE_ENTER_PURCHASE])->sum('a.stocked_num');
			//$vo['num8'] 	= ErpMaterialChange::alias('a')->join('erp_material_stock b','a.material_stock_id = b.id','LEFT')->where('b.stock_date','>=',$start)->where('b.stock_date','<=',$end)->where('a.material_id',$vo['id'])->where('a.stock_num','>',0)->sum('a.stock_num');
			$vo['num8'] 	= empty($num8[$vo['id']])?0:$num8[$vo['id']];
			//$vo['num9'] 	= ErpMaterialOutMaterial::alias('a')->join('erp_material_stock b','a.material_stock_id = b.id','LEFT')->where('a.material_id',$vo['id'])->where('b.type','not in',[ErpMaterialStockEnum::TYPE_OUT_BACK_WAREHOUSE,ErpMaterialStockEnum::TYPE_OUT_ALLOCATE])->sum('a.stocked_num');
			//$vo['num9'] 	= ErpMaterialChange::alias('a')->join('erp_material_stock b','a.material_stock_id = b.id','LEFT')->where('b.stock_date','>=',$start)->where('b.stock_date','<=',$end)->where('a.material_id',$vo['id'])->where('a.stock_num','<',0)->sum('a.stock_num');
			//$vo['num9'] 	= abs($vo['num9']);
			$vo['num9'] 	= empty($num9[$vo['id']])?0:$num9[$vo['id']];
			//$vo['num10'] 	= ErpMaterialScrap::alias('a')->where('a.material_id',$vo['id'])->sum('a.stocked_num');
			$vo['num10'] 	= empty($num10[$vo['id']])?0:$num10[$vo['id']];
			$vo['num11'] 	= $vo['num7'] + $vo['num8'] - $vo['num9'] - $vo['num10'];
		}
		return ['code'=>0,'data'=>$data,'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }

	public static function goSafetyStock($data)
    {
		//验证
		$model 		= self::getOne($data['id']);
        if($model->isEmpty()) {
			return ['msg'=>'数据不存在','code'=>201];
		}
        try {
            $model->save($data);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
		/*
		$data = array_filter($data);
		if(empty($data)){
			return ['msg'=>'没数据变化','code'=>201];
		}
        try{
            ErpMaterial::where('id','in',$ids)->update($data);
        }catch (\Exception $e){
            return ['msg'=>'操作失败'.$e->getMessage(),'code'=>201];
        }
		*/
    }

	// 获取列表
    public static function getStockList($query=[],$limit=10)
    {
		$field 		= 'id,status,type,name,sn,cid,safety_stock,min_stock,max_stock,unit,processing_type,material,surface,color,remark,photo,warehouse_id,stock,processing_way,supplier_id as supplier_ids,material_category,generality,tree_id,produce_type';
        $list 		= ErpMaterial::withSearch(['query'],['query'=>$query])->with(['warehouse','drawing'=>function($query){$query->field('id,sn,final_pic')->where('status',1)->where('final_pic','<>','');}])->field($field)->order('id','desc')->append(['status_desc','type_desc','category_name','photo_link','processing_way_desc'])->paginate($limit);
        $data 		= $list->items();
		$tree_id	= ErpMaterialTree::where('type','=',$query['type'])->column('title','id');
		$produce_type= DictData::where('type_id','=',7)->column('name','id');
		$supplier 	= ErpSupplier::where('id','in',implode(',',array_column($data,'supplier_ids')))->column('name','id');
		
		$tmp		= ErpMaterialWarehouse::alias('a')
		->join('erp_warehouse b','a.warehouse_id = b.id','LEFT')
		->where('a.material_id','in',implode(',',array_column($data,'id')))
		->where('b.type','in','1,2')->field('a.material_id,sum(a.stock) as stock')->group('material_id')->select();
		$stock1		= [];
		foreach($tmp as $vo){
			$stock1[$vo['material_id']] = $vo['stock'];
		}

		$tmp		= ErpMaterialWarehouse::alias('a')->join('erp_warehouse b','a.warehouse_id = b.id','LEFT')->where('a.material_id','in',implode(',',array_column($data,'id')))->where('b.type','=',3)->field('a.material_id,sum(a.stock) as stock')->group('material_id')->select();
		$stock2		= [];
		foreach($tmp as $vo){
			$stock2[$vo['material_id']] = $vo['stock'];
		}		
		
		$tmp		= ErpMaterialEnterMaterial::alias('a')->join('erp_material_stock b','a.material_stock_id = b.id','LEFT')->where('a.material_id','in',implode(',',array_column($data,'id')))->where('b.status','=',1)->field('a.material_id,sum(a.stock_num - a.stocked_num - a.defective_num) as stock_num')->group('a.material_id')->select();
		$stock3		= [];
		foreach($tmp as $vo){
			$stock3[$vo['material_id']] = $vo['stock_num'];
		}		

		foreach($data as &$vo){
			$vo['supplier_id'] 	= $vo['supplier_ids']?explode(',',$vo['supplier_ids']):[];
			$name 				= [];
			foreach($vo['supplier_id'] as $v){
				if(!empty($supplier[$v])){
					$name[] 	= $supplier[$v];
				}
			}
			$vo['supplier_name']= implode(',',$name);
			$vo['stock1']		= isset($stock1[$vo['id']])?$stock1[$vo['id']]:0;
			$vo['stock2']		= isset($stock2[$vo['id']])?$stock2[$vo['id']]:0;
			$vo['stock3']		= isset($stock3[$vo['id']])?$stock3[$vo['id']]:0;
			$vo['stock4']		= $vo['stock1'] + $vo['stock2'] + $vo['stock3'] + $vo['stock4'];
			
			$vo['warehouse_name'] 	= empty($vo['warehouse'])?'':$vo['warehouse']['name'];
			$vo['category_name'] 	= empty($vo['category'])?'':$vo['category']['name'];
			$vo['produce_type'] 	= empty($produce_type[$vo['produce_type']])?'':$produce_type[$vo['produce_type']];
			$vo['tree_id'] 			= empty($tree_id[$vo['tree_id']])?'':$tree_id[$vo['tree_id']];
			$vo['generality'] 		= empty($vo['generality'])?'不通用':'通用';
			
			$vo['final_pic']		= [];
			if($vo['drawing']){
				$vo['final_pic']	= $vo['drawing']->column('final_pic');
			}
			
			
		}
		return ['code'=>0,'data'=>$data,'extend'=>['count' => $list->total(), 'limit' => $limit]];
    }


	public static function getStockExport($query=[],$limit=10){
		$limit					= $limit>10000?10000:$limit;
		$return					= ['column'=>[],'setWidh'=>[],'keys'=>[],'list'=>[]];
		$return['image_fields'] = ['final_pic'];
		// if($query['type'] == ErpMaterialEnum::PARTN){
		// 	$return['column'] 	= ['物料编码','物料名称','物料分类','仓库库存','车间库存','质检中库存','合计库存','安全库存','最低库存','最高库存','仓位','单位','加工类型','材料','表面','颜色','备注','图片','生产物料类别','目录树','通用性','供应商'];
		// 	$return['keys'] 	= ['sn','name','category_name','stock1','stock2','stock3','stock4','safety_stock','min_stock','max_stock','warehouse_name','unit','processing_type','material','surface','color','remark','final_pic','produce_type','tree_id','generality','supplier_name'];
		// 	$return['setWidh']	= ['10','10','10','10','10','10','10','10','10','10','10','10','10','10','10','10','10','15','10','10','10','10'];
		// }else{
		// 	$return['column'] 	= ['物料编码','物料名称','物料分类','现有库存','安全库存','最低库存','最高库存','仓位','单位','备注','图片','生产物料类别','目录树','通用性'];
		// 	$return['keys'] 	= ['sn','name','category_name','stock','safety_stock','min_stock','max_stock','warehouse_name','unit','remark','final_pic','produce_type','tree_id','generality'];
		// 	$return['setWidh']	= ['10','10','10','10','10','10','10','10','10','10','15','10','10','10'];
		// }
		if($query['type'] == ErpMaterialEnum::PARTN){
			$return['column'] 	= ['物料编码','物料名称','物料分类','仓库库存','车间库存','质检中库存','合计库存','安全库存','最低库存','最高库存','仓位','单位','加工类型','材料','表面','颜色','备注','生产物料类别','目录树','通用性','供应商'];
			$return['keys'] 	= ['sn','name','category_name','stock1','stock2','stock3','stock4','safety_stock','min_stock','max_stock','warehouse_name','unit','processing_type','material','surface','color','remark','produce_type','tree_id','generality','supplier_name'];
			$return['setWidh']	= ['10','10','10','10','10','10','10','10','10','10','10','10','10','10','10','10','10','15','10','10','10','10'];
		}else{
			$return['column'] 	= ['物料编码','物料名称','物料分类','现有库存','安全库存','最低库存','最高库存','仓位','单位','备注','生产物料类别','目录树','通用性'];
			$return['keys'] 	= ['sn','name','category_name','stock','safety_stock','min_stock','max_stock','warehouse_name','unit','remark','produce_type','tree_id','generality'];
			$return['setWidh']	= ['10','10','10','10','10','10','10','10','10','10','15','10','10','10'];
		}
		$data 					= self::getStockList($query,$limit);
        $list 					= $data['data'];
		$return['list']			= $list;
        return $return;	
	}
	
	
	
	//获取物料库存
    public static function getCheckMaterialStock($query=[],$limit=10)
    {
		$field 				= 'id,status,type,name,sn,cid,unit,max_stock,warehouse_id,stock,supplier_id as supplier_ids';
        $list 				= ErpMaterial::withSearch(['query'],['query'=>$query])->field($field)->order('id','desc')->paginate($limit);
		$data 				= $list->items();
		$supplier 			= ErpSupplier::where('id','in',implode(',',array_column($data,'supplier_ids')))->column('name','id');
		foreach($data as &$item){
			$map 			= [];
			$map[] 			= ['a.material_id','=',$item['id']];
			$enter 			= ErpMaterialEnterMaterial::alias('a')
			->join('erp_material_stock b','a.material_stock_id = b.id','LEFT')
			->fieldRaw('a.id,a.warehouse_id,a.can_out_num,a.freeze_out_stock,a.can_out_num - a.freeze_out_stock as num,b.order_sn')
			->whereRaw('a.can_out_num>=a.freeze_out_stock')->where($map)->order('a.id desc')->limit(5)->select();
			$item['enter']	= $enter;
			
			$warehouse_id		= $enter->column('warehouse_id');
			$warehouse_id[]		= $item['warehouse_id'];
			$item['warehouse']	= ErpWarehouse::where('id','in',$warehouse_id)->select();
			
			$item['supplier_id'] 	= $item['supplier_ids']?explode(',',$item['supplier_ids']):[];
			$name 				= [];
			foreach($item['supplier_id'] as $v){
				if(!empty($supplier[$v])){
					$name[] 	= $supplier[$v];
				}
			}
			$item['supplier_name']= implode(',',$name);
			
		}
        return ['code'=>0,'data'=>$data,'extend'=>['count' => $list->total(), 'limit' => $limit],'count' => $list->total(), 'limit' => $limit];
    }
	
	
	
    public static function getStockDetail($id)
    {
		$data	= ErpMaterialEnterMaterial::alias('a')
		->join('erp_purchase_order b','a.purchase_order_id = b.id','LEFT')
		->join('erp_material_stock c','a.material_stock_id = c.id','LEFT')
		->join('erp_warehouse d','a.warehouse_id = d.id','LEFT')
		->where('a.material_id',$id)
		->where('a.can_out_num','>',0)
		->where('d.type','in','1,2')
		->field('a.can_out_num,c.order_sn,b.order_sn as purchase_order_sn')->select();
		return $data;
    }	
	
	

}
