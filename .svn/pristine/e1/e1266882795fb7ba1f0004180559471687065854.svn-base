<?php
declare (strict_types = 1);
namespace app\common\model;
use app\common\model\BaseModel;

class ErpMaterialTree extends BaseModel
{

	public static function onAfterUpdate($record){
		$record->updateDataCache();
		$record->updatePath();
	}

	public static function onAfterInsert($record){
		$record->updateDataCache();
		$record->updatePath();
	}

    public static function onAfterDelete($record){
		$record->updateDataCache();
    }
	
	/**
     * 跟新路径
     * @param $value
     * @return string
     */
	public function updatePath(){
		$id   		= $this->getAttr('id');
		$pid  		= $this->getAttr('pid');
		
		if($pid > 0) {
			$parent = ErpMaterialTree::removeOption('where')->where('id',$pid)->find();
			$path 	= $parent['path']. ',' . $id ;
		}else {
			$path 	= $id;
		}
		ErpMaterialTree::where('id',$id)->update(['path'=>$path]);
		$child 		= ErpMaterialTree::removeOption('where')->where('id','<>',$id)->where('path','find in set',$id)->select();
		if($child){
			foreach ($child as $value) {
				$arr 				= explode(",",$value['path']);
				$new 				= [];
				$key 				= null;
				foreach($arr as $k=>$vo){
					if($key !== null && $k > $key){
						$new[] 		= $vo;
					}
					if($vo == $id){
						$key		= $k;
						$new[] 		= $path;
					}
				}
				ErpMaterialTree::where('id',$value['id'])->update(['path'=>implode(',',$new)]);
			}
		}
	}
	
	public function updateDataCache(){
		cache('erp_material_tree',null);
	}	
	
}
