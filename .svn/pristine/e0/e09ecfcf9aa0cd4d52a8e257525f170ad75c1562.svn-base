<?php
namespace app\common\model;
use app\common\model\BaseModel;
use app\common\enum\YesNoEnum;


class ErpOrderProductBom extends BaseModel
{

	protected $json = ['material','replace_material'];
	protected $jsonAssoc = true;

  	public function order(){
		return $this->belongsTo('app\common\model\ErpOrder','order_id','id');
	}
	
  	public function orderProduct(){
		return $this->belongsTo('app\common\model\ErpOrderProduct','order_product_id','id');
	}	
	
  	public function productBom(){
		return $this->belongsTo('app\common\model\ErpProductBom','product_bom_id','id');
	}	
	
  	public function replaceProductBom(){
		return $this->belongsTo('app\common\model\ErpProductBom','replace_product_bom_id','id');
	}	
	
    public function getCanReplaceDescAttr($value, $data){
        return YesNoEnum::getIsOpenDesc($data['can_replace']);
    }   
   
	public function getBillTypeNameAttr($value, $data){
		$category = get_dict_data('product_bill_type');
		return $category&&!empty($category[$data['bill_type']])?$category[$data['bill_type']]['name']:'';
    } 
	
	public function getTypeDescAttr($value, $data){
		$type = [1=>'标配',2=>'换配',3=>'销售加配',4=>'技术加配'];
		return $type[$data['type']];
	}

  	public function actuallyMaterial(){
		return $this->belongsTo('app\common\model\ErpMaterial','material_id','id');
	}
	
}