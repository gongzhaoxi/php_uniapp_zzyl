<?php
namespace app\common\model;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

class Material extends BaseModel{
	use SoftDelete;
    protected $deleteTime = 'delete_time';

	public function category(){
		return $this->belongsTo('app\common\model\MaterialCategory','category_id','id');
	}
	
	public function setBomAttr($value,$data){
		return $value?json_encode($value):'';
	}
	
	public function getBomAttr($value,$data){
		$value 		= $value?json_decode($value,true):[];
		if($value){
			$bom 	= Material::where('id','in',array_column($value,'id'))->column('name,code,spec,description,ex_factory_price,ex_factory_unit,ex_factory_compute,retail_unit,retail_price,retail_compute,stock_unit ','id');
			foreach($value as &$vo){
				$vo	= array_merge($vo,$bom[$vo['id']]);
			}
		}
		return $value;
	}
}