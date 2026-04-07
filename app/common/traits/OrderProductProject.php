<?php
namespace app\common\traits;
use app\common\model\ErpProductProject;

trait OrderProductProject
{
	public function getProjectHtmlAttr($value,$data){
		$html 			= '';
		$ids 			= array_merge($this->getAttr('add_project') , $this->getAttr('change_project'));
		if($ids){
			$project 	= ErpProductProject::where('id','in',$ids)->order(['sort'=>'desc','type'=>'asc'])->select();
			$category 	= get_dict_data('product_project_category');
			foreach($project as $k3=>$v3){
				$html 		.= '<div>';	
				$html 	.= ($k3+1).'）';
				$html 	.= ($category&&!empty($category[$v3['cid']])?$category[$v3['cid']]['name']:'').'：'.$v3['name'].'';
				$html 	.= '；';
				$html 		.= '</div>';
			}
		}
		return $html;
	}	
	
	public function getAddProjectAttr($value,$data){
		return $value?explode(',',$value):[];
	}	
	
	public function getChangeProjectAttr($value,$data){
		return $value?explode(',',$value):[];
	}	
    
}