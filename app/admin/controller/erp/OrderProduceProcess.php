<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\ErpOrderProduceProcessLogic;
use app\common\enum\RegionTypeEnum;
use app\common\model\ErpProcess;
use app\common\model\ErpUser;
use app\common\util\Excel;
use app\common\util\FileUtil;
use app\common\util\ZipUtils;

class OrderProduceProcess extends \app\admin\controller\Base
{

	// 计划-添加
    public function add(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpOrderProduceProcessLogic::goAdd($this->request->param('order_produce_id'),$this->request->param('process_id')));
        }
    }

    // 计划-编辑
    public function edit(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpOrderProduceProcessLogic::goEdit($this->request->param('order_produce_id'),$this->request->param('process_id'),$this->request->param('price'))); 
        }
    }

    // 计划-删除
    public function remove(){
        return $this->getJson(ErpOrderProduceProcessLogic::goRemove($this->request->param('order_produce_id'),$this->request->param('process_id')));
    }	
	
    // 生产进度
    public function index(){
        return $this->fetch('',ErpOrderProduceProcessLogic::getIndex($this->request->only(['customer_name','order_sn','produce_sn','produce_date','delivery_time','date'])));
    }

    // 车间异常
    public function errorList(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderProduceProcessLogic::getErrorList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['count'=>ErpOrderProduceProcessLogic::getErrorCount(),'status'=>$this->request->param('status','')]);
    }

    // 确认处理异常
    public function confirm(){
        return $this->getJson(ErpOrderProduceProcessLogic::goConfirm($this->request->only(['ids'])));
    }	
    
	//计件统计
    public function list($type=1){
        if ($this->request->isAjax()) {
			if($type == 2){
				return $this->getJson(ErpOrderProduceProcessLogic::getWageGroupUser($this->request->param(),$this->request->param('limit')));
			}else{
				return $this->getJson(ErpOrderProduceProcessLogic::getWage($this->request->param(),$this->request->param('limit')));
			}
        }
		return $this->fetch('',['users'=>ErpUser::field('id,name')->order(['id'=>'desc'])->select(),'process'=>ErpProcess::field('id,name')->where('status',1)->order(['sort'=>'asc','id'=>'asc'])->select()]);
    }
	
	
	//导出计件统计
    public function export($type=1,$export_act=0){
		ini_set("memory_limit","-1");
		ini_set('max_execution_time', '60');
		$key 			= preg_replace( '/[\W]/', '', $this->request->param('key',''));
		$title 			= $type == 1?'报工明细':'员工汇总';
		
		if($export_act == 1){
			return $this->getJson($type == 1?ErpOrderProduceProcessLogic::getWageCount($this->request->param(),$this->request->param('limit',10000)):ErpOrderProduceProcessLogic::getWageGroupUserCount($this->request->param(),$this->request->param('limit',10000)));
		}else if($export_act == 2 && $key){
			$page 		= $this->request->param('page');
			$data 		= $type == 1?ErpOrderProduceProcessLogic::getWageExport($this->request->param(),$this->request->param('limit',10000)):ErpOrderProduceProcessLogic::getWageGroupUserExport($this->request->param(),$this->request->param('limit',10000));
			$dir 		= './download/'.$key.'/';
			$fileUtil 	= new FileUtil();
			$fileUtil->createDir($dir);
			Excel::go($title, $data['column'], $data['setWidh'], $data['list'], $data['keys'],$page,$data['image_fields'],$dir);
			return $this->getJson();
		}else if($export_act == 3 && $key && is_dir('./download/'.$key)){
			ZipUtils::zip('./download/'.$key,'./download/'.$key.'.zip');
			return json(['path'=>'/download/'.$key.'.zip']);
		}else{
			$data 		= $type == 1?ErpOrderProduceProcessLogic::getWageExport($this->request->param(),$this->request->param('limit',10000)):ErpOrderProduceProcessLogic::getWageGroupUserExport($this->request->param(),$this->request->param('limit',10000));
			Excel::go($title, $data['column'], $data['setWidh'], $data['list'], $data['keys'],$title,$data['image_fields']);
			exit;
		}
    }	
	
	
	//车间工资
    public function wage(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderProduceProcessLogic::getWage(array_merge($this->request->param(),['has_price'=>1]),$this->request->param('limit')));
        }
		return $this->fetch('',['users'=>ErpUser::field('id,name')->order(['id'=>'desc'])->select(),'process'=>ErpProcess::field('id,name')->where('status',1)->order(['sort'=>'asc','id'=>'asc'])->select()]);
    }	
	
    // 审核车间工资
    public function approve(){
        return $this->getJson(ErpOrderProduceProcessLogic::goApprove($this->request->only(['ids'])));
    }		
	
	//导出车间工资
    public function wageExport($export_act=0){
		ini_set("memory_limit","-1");
		ini_set('max_execution_time', '60');
		$key 			= preg_replace( '/[\W]/', '', $this->request->param('key',''));
		if($export_act == 1){
			return $this->getJson(ErpOrderProduceProcessLogic::getWageCount($this->request->param(),$this->request->param('limit',10000)));
		}else if($export_act == 2 && $key){
			$page 		= $this->request->param('page');
			$data 		= ErpOrderProduceProcessLogic::getWageExport($this->request->param(),$this->request->param('limit',10000));
			$dir 		= './download/'.$key.'/';
			$fileUtil 	= new FileUtil();
			$fileUtil->createDir($dir);
			Excel::go("工资管理", $data['column'], $data['setWidh'], $data['list'], $data['keys'],$page,$data['image_fields'],$dir);
			return $this->getJson();
		}else if($export_act == 3 && $key && is_dir('./download/'.$key)){
			ZipUtils::zip('./download/'.$key,'./download/'.$key.'.zip');
			return json(['path'=>'/download/'.$key.'.zip']);
		}else{
			$data 		= ErpOrderProduceProcessLogic::getWageExport($this->request->param(),$this->request->param('limit',10000));
			Excel::go('工资管理', $data['column'], $data['setWidh'], $data['list'], $data['keys'],"工资管理",$data['image_fields']);
			exit;
		}
    }
	
	// 编辑车间工资
    public function wageEdit(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpOrderProduceProcessLogic::goWageEdit($this->request->param())); 
        }
        return $this->fetch('',['users'=>ErpUser::field('id,name')->order(['id'=>'desc'])->select(),'model' => ErpOrderProduceProcessLogic::getOne($this->request->param('id/d'))]);
    }
	
	// 变更记录
    public function log(){
        return $this->fetch('',['data' => ErpOrderProduceProcessLogic::getLog($this->request->param('order_produce_id/d'))]);
    }	
	
	
    // 总生产情况
    public function produce(){
		if ($this->request->isAjax()) {
			return $this->getJson(ErpOrderProduceProcessLogic::getProduce($this->request->param(),$this->request->param('limit')));
        }
		return $this->fetch('',['process'=>ErpProcess::field('id,name')->where('status',1)->order(['sort'=>'asc','id'=>'asc'])->select()]);
    }	
	
	// 导出总生产情况
    public function produceExport($export_act=0){
		ini_set("memory_limit","-1");
		ini_set('max_execution_time', '60');
		$key 			= preg_replace( '/[\W]/', '', $this->request->param('key',''));
		if($export_act == 1){
			return $this->getJson(ErpOrderProduceProcessLogic::getProduceCount($this->request->param(),$this->request->param('limit',10000)));
		}else if($export_act == 2 && $key){
			$page 		= $this->request->param('page');
			$data 		= ErpOrderProduceProcessLogic::getProduceExport($this->request->param(),$this->request->param('limit',10000));
			$dir 		= './download/'.$key.'/';
			$fileUtil 	= new FileUtil();
			$fileUtil->createDir($dir);
			Excel::go("生产进度", $data['column'], $data['setWidh'], $data['list'], $data['keys'],$page,$data['image_fields'],$dir);
			return $this->getJson();
		}else if($export_act == 3 && $key && is_dir('./download/'.$key)){
			ZipUtils::zip('./download/'.$key,'./download/'.$key.'.zip');
			return json(['path'=>'/download/'.$key.'.zip']);
		}else{
			$data 		= ErpOrderProduceProcessLogic::getProduceExport($this->request->param(),$this->request->param('limit',10000));
			Excel::go('生产进度', $data['column'], $data['setWidh'], $data['list'], $data['keys'],"生产进度",$data['image_fields']);
			exit;
		}
    }	
	
}
