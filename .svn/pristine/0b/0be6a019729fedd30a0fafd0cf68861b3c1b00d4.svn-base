<?php
declare (strict_types = 1);
namespace app\supplier\controller;
use think\facade\Db;
use think\facade\Cookie;

class Common extends Base{
	protected $middleware = [];
   
	public function setColWidth(){
		$field 	= $this->request->param('field');
		$width 	= $this->request->param('width/d');
		$path 	= $this->request->param('path');
		$key 	= md5($path.$field);
		Cookie::forever($key, $width);
		return $this->getJson(['code'=>200]);
	}
	
	public function printAdd($data){
		$id = Db::name('erp_print')->insertGetId(['data'=>is_array($data)?json_decode($data):$data]);
		return json(['id'=>$id]);
	}
	

}
