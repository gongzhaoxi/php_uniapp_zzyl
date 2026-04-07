<?php
namespace app\common\model;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;
use app\common\enum\ErpMaterialStockEnum;
/**
 * 物料出库/入库模型
 * Class ErpMaterialStock
 * @package app\common\model;
 */
class ErpMaterialStock extends BaseModel
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';

	public function supplier(){
		return $this->belongsTo('app\common\model\ErpSupplier','supplier_id','id');
	}

	public function order(){
		return $this->belongsTo('app\common\model\ErpOrder','order_id','id');
	}

    public function setProduceSnAttr($value, $data)
    {
        return is_array($value)?implode(',',$value):$value;
    }

    public function getStatusDescAttr($value, $data)
    {
        return ErpMaterialStockEnum::getStatusDesc($data['status']);
    }	
	
    public function getCanEditAttr($value, $data)
    {
        return $data['status']===ErpMaterialStockEnum::STATUS_HANDLE?true:false;
    }
	
    public function getTypeDescAttr($value, $data)
    {
        return ErpMaterialStockEnum::getTypeDesc($data['type']);
    }		
	
    public function getDataTypeDescAttr($value, $data)
    {
        return ErpMaterialStockEnum::getDataTypeDesc($data['data_type']);
    }	
	
	public function getCanCancelAttr($value, $data)
    {
		return $data['status']==ErpMaterialStockEnum::STATUS_CANCEL||$data['status']==ErpMaterialStockEnum::STATUS_FINISH||$data['status']==ErpMaterialStockEnum::STATUS_SETTLEMENT?false:true;
    }		
	
	public function getCanSettleAttr($value, $data)
    {
		return $data['status']==ErpMaterialStockEnum::STATUS_CANCEL||$data['status']==ErpMaterialStockEnum::STATUS_SETTLEMENT?false:true;
    }	
	
	public function getCanSendAttr($value, $data)
    {
		return $data['supplier_status'] == ErpMaterialStockEnum::SUPPLIER_STATUS_NO && $data['type'] != ErpMaterialStockEnum::DISCARD_SCRAP;
    }
	
	public function getCanConfirmAttr($value, $data)
    {
		return $data['supplier_status'] == ErpMaterialStockEnum::SUPPLIER_STATUS_YES && $data['type'] != ErpMaterialStockEnum::DISCARD_SCRAP;
    }	
	
	public function searchQueryAttr($query, $value, $data)
    {
        if (!empty($value['type'])) {
			$query->where('type', '=', $value['type']);
        }
        if (!empty($value['no_type'])) {
			$query->where('type', '<>', $value['no_type']);
        }
        if (!empty($value['has_department'])) {
			$query->where('department', '<>', '');
        }		
        if (!empty($value['supplier_id'])) {
			$query->where('supplier_id', '=', $value['supplier_id']);
        }		
        if (!empty($value['material_type'])) {
			$query->where('material_type', '=', $value['material_type']);
        }		
        if (!empty($value['data_type'])) {
			$query->where('data_type', '=', $value['data_type']);
        }		
        if (!empty($value['status'])) {
            $query->where('status', '=', $value['status']);
        }
        if (!empty($value['order_sn'])) {
			$query->where('order_sn', 'like', '%' . $value['order_sn'] . '%');
        }	
		if (!empty($value['stock_date'])) {
			$date = is_array($value['stock_date'])?$value['stock_date']:explode('至',$value['stock_date']);
			if(!empty($date[0])){
				$query->where('stock_date', '>=', trim($date[0]));
			}
			if(!empty($date[1])){
				$query->where('stock_date', '<=', trim($date[1]));
			}
		}	
        if (!empty($value['enter_material'])) {
			$query->where('id', 'in', function ($que) use ($value) {
				$que->name('erp_material_enter_material')->alias('a')->join('erp_material b','a.material_id = b.id','LEFT')->where('sn|name', 'like', '%' . $value['enter_material'] . '%')->group('a.material_stock_id')->field(['a.material_stock_id'])->select();
			});
        }		
        if (!empty($value['out_material'])) {
			$query->where('id', 'in', function ($que) use ($value) {
				$que->name('erp_material_out_material')->alias('a')->join('erp_material b','a.material_id = b.id','LEFT')->where('sn|name', 'like', '%' . $value['out_material'] . '%')->group('a.material_stock_id')->field(['a.material_stock_id'])->select();
			});
        }		
        if (!empty($value['check_material'])) {
			$query->where('id', 'in', function ($que) use ($value) {
				$que->name('erp_material_check_material')->alias('a')->join('erp_material b','a.material_id = b.id','LEFT')->where('sn|name', 'like', '%' . $value['check_material'] . '%')->group('a.material_stock_id')->field(['a.material_stock_id'])->select();
			});
        }		
		
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($value['sort'],$value['order']);
		}
    }

}