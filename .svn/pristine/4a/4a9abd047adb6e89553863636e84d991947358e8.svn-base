<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\{ErpProjectLogic,ErpProductBomLogic};
use app\common\enum\ErpProductProjectEnum;
use app\common\util\Excel;
use app\common\util\FileUtil;
use app\common\util\ZipUtils;

class Project extends \app\admin\controller\Base
{
    // 列表
    public function list(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpProjectLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['bill_type'=>ErpProductBomLogic::getBillType()]);
    }

    // 添加
    public function add(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpProjectLogic::goAdd($this->request->param()));
        }
        return $this->fetch('',['category'=>get_dict_data('product_project_category'),'type'=>ErpProductProjectEnum::getTypeDesc()]);
    }

    // 编辑
    public function edit(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpProjectLogic::goEdit($this->request->param())); 
        }
        return $this->fetch('',['category'=>get_dict_data('product_project_category'),'type'=>ErpProductProjectEnum::getTypeDesc(),'model' => ErpProjectLogic::getOne($this->request->param('id/d'))]);
    }

    // 删除
    public function remove(){
        return $this->getJson(ErpProjectLogic::goRemove($this->request->only(['ids'])));
    }	

	// 复制
    public function copy(){
        return $this->getJson(ErpProjectLogic::goCopy($this->request->param('id')));
    }	
    
	// 导入
    public function import(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpProjectLogic::goImport($this->request->param('excel')));
        }
        return $this->fetch();
    }	
	
	
	public function export($export_act=0){
		ini_set("memory_limit","-1");
		ini_set('max_execution_time', '60');
		$key 			= preg_replace( '/[\W]/', '', $this->request->param('key',''));
		if($export_act == 1){
			return $this->getJson(ErpProjectLogic::getExportCount($this->request->param()));
		}else if($export_act == 2 && $key){
			$page 		= $this->request->param('page');
			$data 		= ErpProjectLogic::getExport($this->request->param(),$this->request->param('limit',10000));
			$dir 		= './download/'.$key.'/';
			$fileUtil 	= new FileUtil();
			$fileUtil->createDir($dir);
			Excel::go("方案", $data['column'], $data['setWidh'], $data['list'], $data['keys'],$page,$data['image_fields'],$dir);
			return $this->getJson();
		}else if($export_act == 3 && $key && is_dir('./download/'.$key)){
			ZipUtils::zip('./download/'.$key,'./download/'.$key.'.zip');
			return json(['path'=>'/download/'.$key.'.zip']);
		}else{
			$data 		= ErpProjectLogic::getExport($this->request->param(),$this->request->param('limit',10000));
			Excel::go('方案', $data['column'], $data['setWidh'], $data['list'], $data['keys'],"方案",$data['image_fields']);
			exit;
		}
    }	
	
	
}
