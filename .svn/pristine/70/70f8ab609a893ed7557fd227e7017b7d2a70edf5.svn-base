<?php
namespace app\common\model;
use app\common\model\BaseModel;
use app\common\enum\ErpMaterialStockEnum;
use app\common\enum\ErpMaterialEnum;
use app\common\model\ErpMaterialOutMaterial;
/**
 * 物料库存变动记录模型
 * Class ErpMaterialChange
 * @package app\common\model;
 */
class ErpMaterialChange extends BaseModel
{

	public function stock(){
		return $this->belongsTo('app\common\model\ErpMaterialStock','material_stock_id','id');
	}
	
	public function material(){
		return $this->belongsTo('app\common\model\ErpMaterial','material_id','id');
	}	
	
	public function supplier(){
		return $this->belongsTo('app\common\model\ErpSupplier','supplier_id','id');
	}	

	public function getTypeDescAttr($value, $data)
    {
		if(empty($data['type'])){
			return '';
		}
        return ErpMaterialStockEnum::getTypeDesc($data['type']);
    }		
	
    public function getDataTypeDescAttr($value, $data)
    {
		if(empty($data['data_type'])){
			return '';
		}
        return ErpMaterialStockEnum::getDataTypeDesc($data['data_type']);
    }
	
    public function getMaterialTypeDescAttr($value, $data)
    {
		if(empty($data['material_type'])){
			return '';
		}
        return ErpMaterialEnum::getTypeDesc($data['material_type']);
    }	
	
	public function getCreateDateAttr($value, $data)
    {
		return date('Y-m-d',$this->getData('create_time'));
    }
	
	public function getPhotoAttr($value, $data)
    {
		if(empty($data['type']) ){
			return '';
		}
		return get_browse_url(ErpMaterialOutMaterial::where('material_stock_id',$data['material_stock_id'])->where('material_id',$data['material_id'])->value('photo'));
    }
}