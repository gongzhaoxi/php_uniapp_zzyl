<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\ErpMaterialScrapOutLogic;
use app\admin\logic\ErpMaterialScrapLogic;
use app\admin\logic\ErpSupplierLogic;
use app\common\util\Excel;
use app\common\util\FileUtil;
use app\common\util\ZipUtils;
class MaterialScrapOut extends \app\admin\controller\Base
{

    //报废已出库记录
    public function list(){
		if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialScrapOutLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['supplier'=>ErpSupplierLogic::getAll(),'material_scrap_type'=>get_dict_data('material_scrap_type')]);
	}

	//导出报废明细
    public function export($export_act=0){
		ini_set("memory_limit","-1");
		ini_set('max_execution_time', '60');
		$key 			= preg_replace( '/[\W]/', '', $this->request->param('key',''));
		if($export_act == 1){
			return $this->getJson(['data'=>['count'=>ErpMaterialScrapOutLogic::getCount($this->request->param()),'key'=>rand_string()]]);
		}else if($export_act == 2 && $key){
			$page 		= $this->request->param('page');
			$data 		= ErpMaterialScrapOutLogic::getExport($this->request->param(),$this->request->param('limit',10000));
			$dir 		= './download/'.$key.'/';
			$fileUtil 	= new FileUtil();
			$fileUtil->createDir($dir);
			Excel::go("报废已出库记录", $data['column'], $data['setWidh'], $data['list'], $data['keys'],$page,$data['image_fields'],$dir);
			return $this->getJson();
		}else if($export_act == 3 && $key && is_dir('./download/'.$key)){
			ZipUtils::zip('./download/'.$key,'./download/'.$key.'.zip');
			return json(['path'=>'/download/'.$key.'.zip']);
		}else{
			$data 		= ErpMaterialScrapOutLogic::getExport($this->request->param(),$this->request->param('limit',10000));
			Excel::go('报废已出库记录', $data['column'], $data['setWidh'], $data['list'], $data['keys'],"报废已出库记录",$data['image_fields']);
			exit;
		}
    }

    // 添加
    public function add(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpMaterialScrapOutLogic::goAdd($this->request->only(['material','supplier_id','stock_date','remark'])));
        }
		$tmp 	= ErpMaterialScrapLogic::getList($this->request->only(['ids']),10000)['data'];
		$data 	= [];
		foreach($tmp as $vo){
			if(empty($data[$vo['material_id']])){
				$vo['ids'] 						= [$vo['id']];
				$data[$vo['material_id']] 		= $vo->toArray();
				$data[$vo['material_id']]['ids']= [];
				$data[$vo['material_id']]['ids'][] 		= $vo['id'];
			}else{
				$data[$vo['material_id']]['stock_num'] 	= $data[$vo['material_id']]['stock_num'] + $vo['stock_num'];
				$data[$vo['material_id']]['stocked_num']= $data[$vo['material_id']]['stocked_num'] + $vo['stocked_num'];
				$data[$vo['material_id']]['ids'][] 		= $vo['id'];
			}
		}
		return $this->fetch('',['data'=>$data,'supplier'=>ErpSupplierLogic::getOne($this->request->param('supplier_id'))]);
    }

    // 整单撤回出库
    public function remove(){
        return $this->getJson(ErpMaterialScrapOutLogic::goRemove($this->request->param('id')));
    }


    //报表-报废已出库记录
    public function report(){
		if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialScrapOutLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['supplier'=>ErpSupplierLogic::getAll(),'material_scrap_type'=>get_dict_data('material_scrap_type')]);
	}

}
