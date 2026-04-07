<?php
declare (strict_types = 1);
namespace app\supplier\logic;
use think\facade\Db;

class BaseLogic{

	public static $supplier;
   
	public static function setSupplier($supplier){
		self::$supplier = $supplier;
	}


}
