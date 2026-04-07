<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\ErpMaterialBomLogic;
use app\admin\logic\ErpMaterialLogic;
use app\common\enum\ErpMaterialEnum;
use app\common\util\Excel;
use app\common\util\FileUtil;
use app\common\util\ZipUtils;

class MaterialBom extends \app\admin\controller\Base
{

    // 列表
    public function list(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialBomLogic::getList($this->request->only(['material_id']),$this->request->param('limit')));
        }
        return $this->fetch('',['material_id'=>$this->request->param('material_id/d')]);
    }

    // 添加
    public function add($material_id){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpMaterialBomLogic::goAdd($this->request->only(['related_material_id','material_id','color_follow','num'])));
        }
        return $this->fetch('',['category'=>ErpMaterialLogic::getCategory('material_partn'),'material_id'=>$this->request->param('material_id',''),'table'=>$this->request->param('table','')]);
    }

    // 零件列表
    public function partn(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialLogic::getList(array_merge($this->request->param(),['type'=>ErpMaterialEnum::PARTN]),$this->request->param('limit')));
        }
    }

    // 编辑
    public function edit(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpMaterialBomLogic::goEdit($this->request->only(['id','color_follow','num']))); 
        }
    }

    // 删除
    public function remove(){
        return $this->getJson(ErpMaterialBomLogic::goRemove($this->request->only(['ids'])));
    }	

    // 回收站
    public function recycle(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpMaterialBomLogic::getRecycle($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch();
    }
	
	// 恢复/删除回收站
	public function batchRecycle(){
		return $this->getJson(ErpMaterialBomLogic::goRecycle($this->request->param('ids'),$this->request->param('type')));
    }
    	
	// 导入
    public function import(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpMaterialBomLogic::goImport($this->request->param('excel')));
        }
        return $this->fetch();
    }
	
	// 导出
	public function export($export_act=0){
		ini_set("memory_limit","-1");
		ini_set('max_execution_time', '60');
		$key 			= preg_replace( '/[\W]/', '', $this->request->param('key',''));
		if($export_act == 1){
			return $this->getJson(ErpMaterialBomLogic::getExportCount($this->request->param()));
		}else if($export_act == 2 && $key){
			$page 		= $this->request->param('page');
			$data 		= ErpMaterialBomLogic::getExport($this->request->param(),$this->request->param('limit',10000));
			$dir 		= './download/'.$key.'/';
			$fileUtil 	= new FileUtil();
			$fileUtil->createDir($dir);
			Excel::go("仓位库存", $data['column'], $data['setWidh'], $data['list'], $data['keys'],$page,$data['image_fields'],$dir);
			return $this->getJson();
		}else if($export_act == 3 && $key && is_dir('./download/'.$key)){
			ZipUtils::zip('./download/'.$key,'./download/'.$key.'.zip');
			return json(['path'=>'/download/'.$key.'.zip']);
		}else{
			$data 		= ErpMaterialBomLogic::getExport($this->request->param(),$this->request->param('limit',10000));
			Excel::go('部件bom', $data['column'], $data['setWidh'], $data['list'], $data['keys'],"部件bom",$data['image_fields']);
			exit;
		}
    }
	
}
