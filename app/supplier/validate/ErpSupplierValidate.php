<?php
declare (strict_types = 1);
namespace app\supplier\validate;
use think\Validate;

class ErpSupplierValidate extends Validate{

    protected $rule = [
        'code|组织码' 		=> 'require',
		'captcha|验证码' 	=> 'require|captcha',
    ];
    
    public function sceneLogin(){
        if(config('web.login_captcha')==1){
			return $this->only(['code','captcha']);
        }else{
			return $this->only(['code']);
        }
    }
}
