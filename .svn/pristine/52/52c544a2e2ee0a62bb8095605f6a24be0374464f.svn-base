<?php
declare (strict_types = 1);
namespace app\index\controller;
use app\common\util\Form;
use app\index\logic\BaseLogic;


class Base extends \app\BaseController{
	
	protected $middleware = ['\app\index\middleware\LoginCheck'];

	protected function initialize(){
		$this->assign(['form'=>new Form()]);
	}

}
