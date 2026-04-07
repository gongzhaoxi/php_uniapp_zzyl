<?php
declare (strict_types = 1);
namespace app\supplier\logic;
use app\supplier\logic\BaseLogic;
use app\common\model\ErpSupplier;
use app\supplier\validate\ErpSupplierValidate;
use think\facade\Session;
use think\facade\Cookie;
use think\facade\Db;

class ErpSupplierLogic extends BaseLogic{
    

    // 用户登录验证
    public static function login(array $data)
    {
        $validate = new ErpSupplierValidate;
        if(!$validate->scene('login')->check($data)) 
			return ['msg'=>$validate->getError(),'code'=>201];
        //验证用户
        $supplier = ErpSupplier::where(['code' => trim($data['code']),'status' => 1 ])->find();
        if(!$supplier) return ['msg'=>'组织码错误','code'=>201];
        $supplier->token = rand_string().$supplier->id.microtime(true);
        $supplier->save();
        //是否记住密码
        $time = 3600;
        if (isset($data['remember'])) $time = 30 * 86400;
        //缓存登录信息
        $info = [
            'id' 		=> $supplier->id,
            'token'		=> $supplier->token,
			'name'	=> $supplier->name,
        ];
        Session::set('supplier', $info);
        Cookie::set('supplier_token',$supplier->token, $time);
        return ['msg'=>'登录成功'];
    }
    
    // 判断是否登录
    public static function isLogin()
    {
        if(Session::get('supplier')) return true; 
        if(Cookie::has('supplier_token')){
            $supplier = ErpSupplier::where(['token'=>Cookie::get('supplier_token'),'status'=>1])->find();
            if(!$supplier) return false;
            return Session::set('supplier',[
                'id' 		=> $supplier->id,
                'token' 	=> $supplier->token,
				'name'		=> $supplier->name,
            ]); 
        }
        return false;
    }
    
    // 退出登陆
    public static function logout()
    {
        Session::delete('supplier');
		Cookie::delete('supplier_token');
        return ['msg'=>'退出成功'];
    }
}
