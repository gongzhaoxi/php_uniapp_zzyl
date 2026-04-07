<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\{ErpProductStockLogic};
use app\common\enum\ErpProductStockEnum;
use app\common\util\Excel;
use app\common\util\FileUtil;
use app\common\util\ZipUtils;

class ProductStock extends \app\admin\controller\Base
{

    // 列表
    public function list(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpProductStockLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['status'=>ErpProductStockEnum::getStatusDesc()]);
    }
	
    // 审核入库
    public function confirm(){
        return $this->getJson(ErpProductStockLogic::goConfirm($this->request->only(['ids'])));
    }	
	
    // 退货入库
    public function returned(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpProductStockLogic::goReturned($this->request->param()));
        }
        return $this->fetch('',[]);
    }
    
	//导出成品库存
    public function export($export_act=0){
		ini_set("memory_limit","-1");
		ini_set('max_execution_time', '60');
		$key 			= preg_replace( '/[\W]/', '', $this->request->param('key',''));
		if($export_act == 1){
			return $this->getJson(ErpProductStockLogic::getExportCount($this->request->param(),$this->request->param('limit',10000)));
		}else if($export_act == 2 && $key){
			$page 		= $this->request->param('page');
			$data 		= ErpProductStockLogic::getExport($this->request->param(),$this->request->param('limit',10000));
			$dir 		= './download/'.$key.'/';
			$fileUtil 	= new FileUtil();
			$fileUtil->createDir($dir);
			Excel::go("成品库存", $data['column'], $data['setWidh'], $data['list'], $data['keys'],$page,$data['image_fields'],$dir);
			return $this->getJson();
		}else if($export_act == 3 && $key && is_dir('./download/'.$key)){
			ZipUtils::zip('./download/'.$key,'./download/'.$key.'.zip');
			return json(['path'=>'/download/'.$key.'.zip']);
		}else{
			$data 		= ErpProductStockLogic::getExport($this->request->param(),$this->request->param('limit',10000));
			Excel::go('成品库存', $data['column'], $data['setWidh'], $data['list'], $data['keys'],"成品库存",$data['image_fields']);
			exit;
		}
    }	

	// 报表
    public function report(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpProductStockLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['status'=>ErpProductStockEnum::getStatusDesc()]);
    }
	
}
