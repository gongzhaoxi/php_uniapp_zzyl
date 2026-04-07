<?php
namespace app\common\model;
use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

class ErpOrderProduct extends BaseModel
{
	use \app\common\traits\OrderProductProject;
	protected $json = ['replace_info','project_price'];
	protected $jsonAssoc = true;
	
	public function order(){
		return $this->belongsTo('app\common\model\ErpOrder','order_id','id');
	}
	
	public function product(){
		return $this->belongsTo('app\common\model\ErpProduct','product_id','id');
	}	
	
	public function bom(){
		return $this->hasMany('app\common\model\ErpOrderProductBom', 'order_product_id', 'id');
    }
	
    public function produce(){
		return $this->hasMany('app\common\model\ErpOrderProduce', 'order_product_id', 'id');
    }		
	
	public function getBomFomatAttr($value,$data){
		$bom 							= [];
		foreach($this->bom as $vo){
			$bom[$vo['bill_type']][] 	= $vo->toArray();
		}
		$bill_type 						= get_dict_data('product_bill_type');
		$return							= [];
		foreach($bill_type as $vo){
			if(!empty($bom[$vo['id']])){
				$return[]				= ['name'=>$vo['name'],'data'=>$bom[$vo['id']]];
			}
		}
		return $return;
	}
	
	
	public function getBomHtmlAttr($value,$data){
		$html 					= '';
		$bom_fomat				= $this->getAttr('bom_fomat');
		foreach($bom_fomat as $k2=>$v2){
			$html 				.= '<div>';				
			$html 				.= '<span class="title">'.($k2+1).'.'.$v2['name'].'：</span>';	
			foreach($v2['data'] as $k3=>$v3){
				$html 			.= ($k3+1).'）';
				if($v3['type'] == 2 ){
					$html 		.= '改`'.$v3['material']['name'].'`';
				}else if($v3['type'] == 3){
					$html 		.= '加`'.$v3['material']['name'].'`';
				}else{
					$html 		.= '`'.$v3['material']['name'].'`';
				}
				$html 		.= '；';
			}
			$html 		.= '</div>';
		}
		$ids 			= array_merge($this->getAttr('add_project') , $this->getAttr('change_project'));
		if($ids){
			$project 	= ErpProductProject::where('id','in',$ids)->order(['sort'=>'desc','type'=>'asc'])->select();
			$category 	= get_dict_data('product_project_category');
			$html 		.= '<div>';		
			$html 		.= '<span class="title">'.(count($bom_fomat)+1).'.方案：</span>';	
			foreach($project as $k3=>$v3){
				$html 	.= ($k3+1).'）';
				$html 	.= ($category&&!empty($category[$v3['cid']])?$category[$v3['cid']]['name']:'').'：'.$v3['name'].'';
				$html 	.= '；';
			}
			$html 		.= '</div>';
		}
		
		return $html;
	}
	
	public function setAddProjectAttr($value,$data){
		return $value?(is_array($value)?implode(',',$value):''):'';
	}

	public function setChangeProjectAttr($value,$data){
		return $value?(is_array($value)?implode(',',$value):''):'';
	}
	
}