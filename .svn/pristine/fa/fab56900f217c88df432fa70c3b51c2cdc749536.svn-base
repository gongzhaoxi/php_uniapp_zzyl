<?php
declare (strict_types = 1);
namespace app\supplier\controller;
use app\common\util\Form;
use app\supplier\logic\BaseLogic;


class Base extends \app\BaseController{
	
	protected $middleware = ['\app\supplier\middleware\SupplierCheck'];
	protected function initialize(){
		$this->assign(['form'=>new Form(),'_supplier'=>session('supplier')]);
		BaseLogic::setSupplier(session('supplier'));
	}

}
