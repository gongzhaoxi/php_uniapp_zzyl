<?php
namespace app\common\model;
use app\common\model\BaseModel;

class ErpMaterialEnterMaterialReport extends BaseModel
{
	
	//protected $json = ['instrument_number','ng_item','ng_num','inspection_items'];
	//protected $jsonAssoc = true;	
	

	public function enter(){
		return $this->belongsTo('app\common\model\ErpMaterialEnter','material_stock_id','id');
	}
	
	public function enterMaterial(){
		return $this->belongsTo('app\common\model\ErpMaterialEnterMaterial','material_enter_material_id','id');
	}	

	public function material(){
		return $this->belongsTo('app\common\model\ErpMaterial','material_id','id');
	}

	public function getCanFinishAttr($value, $data)
    {
		return $data['status'] == 1 && !empty($data['approval_date']);
    }		
	
	public function searchQueryAttr($query, $value, $data)
    {
		$alias 		= '';
		$m_alias	= '';
		if (!empty($value['_alias'])) {
			$alias 	= $value['_alias'].'.';
        }
		if (!empty($value['_material_alias'])) {
			$m_alias= $value['_material_alias'].'.';
        }		
		if (!empty($value['material_stock_id'])) {
			$query->where($alias.'material_stock_id', '=', $value['material_stock_id']);
        }
        if ($m_alias && !empty($value['keyword'])) {
			$query->where($m_alias.'sn|'.$m_alias.'name', 'like', '%' . $value['keyword'] . '%');
        }
        if ($m_alias && !empty($value['cid'])) {
			$query->where($m_alias.'cid', '=', $value['cid']);
        }	
        if (!empty($value['status'])) {
			$query->where($alias.'status', 'in', $value['status']);
        }		
		if (!empty($value['sort']) && !empty($value['order'])) {
			$query->order($alias.$value['sort'],$value['order']);
		}
    }
	
	public static function getReportCode(){
		$code = (int)ErpMaterialEnterMaterialReport::whereDay('create_time')->max('code');
		if($code){
			return $code + 1;;
		}else{
			return date('Ymd').'001';
		}
	}
	
	
}