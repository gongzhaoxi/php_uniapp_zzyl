<?php
declare (strict_types = 1);
namespace app\index\logic;
use think\facade\Db;

class BaseLogic{

	public static $user;
   
	public static function setUser($user){
		self::$user = $user;
	}

	public static function checkAuth($rule){
		if(session('admin.id') == 1){
			return true;
		} 
        //验证权限
        $rule 	= strtolower(ltrim($rule,'/')); 
        $auth 	= array_column(session('admin.menu'), 'auth');
        if(!in_array($rule, $auth)) {
			return false;
		}
		return true;
	}
}
