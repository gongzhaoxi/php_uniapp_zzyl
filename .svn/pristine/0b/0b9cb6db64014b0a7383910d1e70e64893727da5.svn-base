<?php
declare (strict_types = 1);
namespace app\admin\logic;
use app\common\model\AdminAdmin;
use think\facade\Db;

class BaseLogic{

	public static $adminUser;
   
	public static function setAdmin($adminUser){
		self::$adminUser = $adminUser;
	}

	public static function getAdmins($role_id=0){
		$map 		= [];
		$map[]		= ['status','=',1];	
		if($role_id){
			$map[]	= ['id','in',Db::name('admin_admin_role')->where('role_id',$role_id)->column('admin_id')];	
		}
		return  AdminAdmin::where($map)->column('id,username,nickname','id');
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
