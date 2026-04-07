<?php
namespace app\common\model;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;
use app\common\enum\YesNoEnum;
use app\common\enum\ErpMaterialEnum;
use app\common\enum\ErpMaterialStockEnum;
use app\common\enum\ErpOrderProduceEnum;

class ErpMaterial extends BaseModel{
	use SoftDelete;
    protected $deleteTime = 'delete_time';

	protected $json = ['qc_file','produce_file','num_version'];
	protected $jsonAssoc = true;


    public function drawing(){
		return $this->hasMany('app\common\model\ErpDrawing', 'sn', 'sn')->where('status',1)->where('final_pic','<>','');
    }	

	public function bom(){
		return $this->hasMany('app\common\model\ErpMaterialBom','material_id','id');
	}

	public function category(){
		return $this->belongsTo('app\common\model\DictData','cid','id');
	}
	
	public function warehouse(){
		return $this->belongsTo('app\common\model\ErpWarehouse','warehouse_id','id');
	}	
	
 	public function getTypeDescAttr($value, $data){
		return ErpMaterialEnum::getTypeDesc($data['type']);
    }
	
 	public function getProcessingWayDescAttr($value, $data){
		return ErpMaterialEnum::getProcessingWayDesc($data['processing_way']);
    }		
	
	//可用库存
 	public function getStorageNumAttr($value, $data){
		return $data['stock'] - $data['freeze_stock'];
    }
	
    public function getCategoryNameAttr($value, $data)
    {
		$category 	= [];
		if($data['type'] == ErpMaterialEnum::PARTN){
			$category = get_dict_data('material_partn');
		}else if($data['type'] == ErpMaterialEnum::COMPONENT){
			$category = get_dict_data('material_component');
		}
		return $category&&!empty($category[$data['cid']])?$category[$data['cid']]['name']:'';
    }	
	
    public function getStatusDescAttr($value, $data)
    {
        return YesNoEnum::getIsOpenDesc($data['status']);
    }		
	
	public function getPhotoLinkAttr($value,$data)
    {
        return get_browse_url($data['photo']);
    }
	
	public function searchQueryAttr($query, $value, $data){
        if (!empty($value['name'])) {
            $query->where('name', 'like', '%' . $value['name'] . '%');
        }
        if (!empty($value['sn'])) {
            $query->where('sn', 'like', '%' . $value['sn'] . '%');
        }	
        if (!empty($value['sns'])) {
			$query->where('sn', 'in', $value['sns']);
        }		
        if (!empty($value['remark'])) {
            $query->where('remark', 'like', '%' . $value['remark'] . '%');
        }		
        if (isset($value['status']) && $value['status'] !== '') {
            $query->where('status', '=', $value['status']);
        }
        if (!empty($value['cid'])) {
			$query->where('cid', '=', $value['cid']);
        }	
        if (!empty($value['no_id'])) {
			$query->where('id', '<>', $value['no_id']);
        }		
        if (!empty($value['keyword'])) {
            $query->where('sn|name|remark', 'like', '%' . $value['keyword'] . '%');
        }
        if (!empty($value['type'])) {
			$query->where('type', '=', $value['type']);
        }
        if (!empty($value['ids'])) {
			$query->where('id', 'in', $value['ids']);
        }		
        if (!empty($value['tree_id'])) {
			$query->where('tree_id', 'in', ErpMaterialTree::where('path','find in set',$value['tree_id'])->column('id'));
        }		
        if (!empty($value['warehouse_id'])) {
			$query->where('warehouse_id', '=', $value['warehouse_id']);
        }	
        if (!empty($value['workstation_id'])) {
			$query->where('workstation_id', '=', $value['workstation_id']);
        }			
		if (!empty($value['processing_way'])) {
			$query->where('processing_way', '=', $value['processing_way']);
        }	
		if (!empty($value['material_category'])) {
			$query->where('material_category', '=', $value['material_category']);
        }		
		
        if (!empty($value['supplier_id'])) {
			$query->where('supplier_id', 'find in set', $value['supplier_id']);
        }		
        if (!empty($value['stock_search'])) {
			if($value['stock_search'] == 1){
				$query->whereRaw('stock < safety_stock');
			}else if($value['stock_search'] == 2){
				$query->whereRaw('stock < min_stock');
			}else if($value['stock_search'] == 3){
				$query->where('stock', '<=', 0);
			}
        }
		if (!empty($value['processing_type'])) {
            $query->where('processing_type', 'like', '%' . $value['processing_type'] . '%');
        }
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($value['sort'],$value['order']);
		}
    }
	
	public static function getActuallyStock($id,$stock,$orderProduceBomId=0)
    {
		//$map 		= [];
		//$map[] 		= ['a.material_id','=',$id];
		//if($orderProduceBomId){
			//$map[]	= ['a.id','not in',$orderProduceBomId];
		//}
		//$num 		= ErpOrderProduceBom::alias('a')->join('erp_order_produce b','a.order_produce_id = b.id','LEFT')->where($map)->sum('a.use_num');
		//return $stock - $num;	
		return $stock;	
    }
	
	public static function getEnterStockAttr($value,$data)
    {
		return $data['stock']- ErpMaterialEnterMaterial::where('freeze_out_stock','>',0)->where('material_id','=',$data['id'])->sum('freeze_out_stock');	
    }
	
	
	public function getQcFileLinkAttr($value,$data)
    {
		$data 						= $data['qc_file'];
		if(!empty($data['file'])){
			foreach($data['file'] as $key=>$vo){
				$data['file'][$key]	= get_browse_url($vo);
			}
		}
        return $data;
    }
	
	public function getProduceFileLinkAttr($value,$data)
    {
		$data 						= $data['produce_file'];
		if(!empty($data['file'])){
			foreach($data['file'] as $key=>$vo){
				$data['file'][$key]	= get_browse_url($vo);
			}
		}
        return $data;
    }	
 	public function getSupplierIdAttr($value, $data){
		return $value?explode(',',$value):[];
    }	
	
	
 	public function setSupplierIdAttr($value, $data){
		return is_array($value)?implode(',',$value):$value;
    }	

	public function getNumVersionLinkAttr($value,$data)
    {
		$data 						= $data['num_version'];
		if(!empty($data['file'])){
			$data['is_image']		= [];
			foreach($data['file'] as $key=>$vo){
				$data['file'][$key]	= get_browse_url($vo);
				$arr 				= explode('.',$vo);
				$data['is_image'][$key]	= in_array(strtolower($arr[count($arr)-1]),['png','jpg','jpeg','gif','ico','bmp']);
			}
		}
        return $data;
    }

 	public function getGuideBookAttr($value, $data){
		return ErpGuideBook::whereRaw("FIND_IN_SET('".$data['id']."',data_id) or FIND_IN_SET('0',data_id)")->where('data_type','in','1,2')->order('data_id asc,id desc')->select();
    }

	
	
}