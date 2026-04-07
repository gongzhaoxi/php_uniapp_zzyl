<?php
declare (strict_types = 1);
namespace app\admin\controller;
use think\captcha\facade\Captcha;
use app\admin\logic\AdminAdminLogic;

class Login extends Base{
	protected $middleware = [];
    //后台登录
    public function index(){
        //是否已经登录
        if(AdminAdminLogic::isLogin()){
			return redirect((string)url('index/index'));
        }
        if($this->request->isAjax()){
			return $this->getJson(AdminAdminLogic::login($this->request->param()));
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
        return $this->getJson(AdminAdminLogic::logout());
    }
}
