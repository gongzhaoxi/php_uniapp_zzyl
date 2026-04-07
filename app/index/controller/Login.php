<?php
declare (strict_types = 1);
namespace app\index\controller;
use app\index\logic\ErpUserLogic;
use think\captcha\facade\Captcha;
class Login extends Base
{
	protected $middleware = [];
	
    //后台登录
    public function index(){
        //是否已经登录
		//if(ErpUserLogic::isLogin()){
			//return redirect((string)url('index/index'));
		//}
        if($this->request->isAjax()){
			return $this->getJson(ErpUserLogic::goLogin($this->request->only(['mobile','sn','terminal'=>4])));
        }
        return $this->fetch();
    }	
	
    // 验证码
    public function verify(){
        ob_clean(); 
        return Captcha::create();
    }

    //退出登陆
    public function logout(){
        return $this->getJson(ErpUserLogic::goLogout());
    }	
	
}
