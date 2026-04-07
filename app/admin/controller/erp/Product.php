<?php
declare (strict_types = 1);
namespace app\admin\controller\erp;
use app\admin\logic\ErpProductLogic;
use app\common\enum\RegionTypeEnum;
use app\admin\logic\{ErpProductBomLogic,ErpMaterialLogic};
use app\common\util\Excel;
class Product extends \app\admin\controller\Base
{

    // 列表
    public function list(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpProductLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',['bill_type'=>ErpProductBomLogic::getBillType()]);
    }

    // 添加
    public function add(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpProductLogic::goAdd($this->request->param()));
        }
        return $this->fetch('',['region_type'=>RegionTypeEnum::getDesc(),'category'=>ErpProductLogic::getCategory()]);
    }

    // 编辑
    public function edit(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpProductLogic::goEdit($this->request->param())); 
        }
        return $this->fetch('',['region_type'=>RegionTypeEnum::getDesc(),'category'=>ErpProductLogic::getCategory(),'model' => ErpProductLogic::getOne($this->request->param('id/d'))]);
    }

    // 删除
    public function remove(){
        return $this->getJson(ErpProductLogic::goRemove($this->request->only(['ids'])));
    }	
	
	// 导入
    public function import(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpProductLogic::goImport($this->request->param('excel')));
        }
        return $this->fetch();
    }

    // 回收站
    public function recycle(){
        if ($this->request->isAjax()) {
            return $this->getJson(ErpProductLogic::getRecycle($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch();
    }
	
	// 恢复/删除回收站
	public function batchRecycle(){
		return $this->getJson(ErpProductLogic::goRecycle($this->request->param('ids'),$this->request->param('type')));
    }
	
	// 复制
    public function copy(){
        return $this->getJson(ErpProductLogic::goCopy($this->request->param('id')));
    }	
	
	//累计发货统计
    public function shippingCount(){
		$query = $this->request->only(['act'=>2,'year'=>'','month'=>'']);
		return $this->fetch('',['query'=>$query,'list'=>ErpProductLogic::getShippingCount($query)]);
    }
	
	// 仓库产品列表
    public function list2(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpProductLogic::getList($this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',[]);
    }
	
    // 产品bom
    public function bom(){
        return $this->fetch('',ErpProductLogic::getBom($this->request->param('product_id')));
    }
	
	// 导出产品bom
    public function bomExport(){
		ini_set("memory_limit","-1");
		ini_set('max_execution_time', '30');
		$data = ErpProductLogic::getBomExport($this->request->param('product_id'),$this->request->param('type'));
		Excel::go('产品bom', $data['column'], $data['setWidh'], $data['list'], $data['keys'],"产品bom", $data['image_fields']);
		exit;
    }
}
