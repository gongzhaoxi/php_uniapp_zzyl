<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\ErpMaterialScrapLogic;
use app\admin\logic\ErpSupplierLogic;
use app\common\util\Excel;
use app\common\util\FileUtil;
use app\common\util\ZipUtils;
class MaterialScrap extends \app\admin\controller\Base
{

    //报废明细
    public function list(){
		if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialScrapLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['supplier'=>ErpSupplierLogic::getAll(),'material_scrap_type'=>get_dict_data('material_scrap_type')]);
	}

	//导出报废明细
    public function export($export_act=0){
		ini_set("memory_limit","-1");
		ini_set('max_execution_time', '60');
		$key 			= preg_replace( '/[\W]/', '', $this->request->param('key',''));
		if($export_act == 1){
			return $this->getJson(['data'=>['count'=>ErpMaterialScrapLogic::getCount($this->request->param()),'key'=>rand_string()]]);
		}else if($export_act == 2 && $key){
			$page 		= $this->request->param('page');
			$data 		= ErpMaterialScrapLogic::getExport($this->request->param(),$this->request->param('limit',10000));
			$dir 		= './download/'.$key.'/';
			$fileUtil 	= new FileUtil();
			$fileUtil->createDir($dir);
			Excel::go("报废明细", $data['column'], $data['setWidh'], $data['list'], $data['keys'],$page,$data['image_fields'],$dir);
			return $this->getJson();
		}else if($export_act == 3 && $key && is_dir('./download/'.$key)){
			ZipUtils::zip('./download/'.$key,'./download/'.$key.'.zip');
			return json(['path'=>'/download/'.$key.'.zip']);
		}else{
			$data 		= ErpMaterialScrapLogic::getExport($this->request->param(),$this->request->param('limit',10000));
			Excel::go('报废明细', $data['column'], $data['setWidh'], $data['list'], $data['keys'],"报废明细",$data['image_fields']);
			exit;
		}
    }

    // 添加
    public function add(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpMaterialScrapLogic::goAdd($this->request->only(['material'])));
        }
		return $this->fetch('',['supplier'=>ErpSupplierLogic::getAll(),'material_scrap_type'=>get_dict_data('material_scrap_type')]);
    }
	
	// 删除
    public function remove(){
        return $this->getJson(ErpMaterialScrapLogic::goRemove($this->request->param('id')));
    }
	
	// 编辑
    public function edit(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpMaterialScrapLogic::goEdit($this->request->only(['id','stock_num','is_confirm']))); 
        }
	}

}
