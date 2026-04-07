<?php
declare (strict_types = 1);
namespace app\admin\validate;
use think\Validate;

class AdminAdminValidate extends Validate{

    protected $rule = [
        'username|用户名' 	=> 'require|unique:admin_admin',
        'password|密码' 	=> 'require',
        'nickname|昵称' 	=> 'require',
		'id|管理员' 		=> 'require',
		'captcha|验证码' 	=> 'require|captcha',
    ];
    
    /**
     * 定义错误信息
     * 格式：'字段名.规则名'	=>	'错误信息'
     *
     * @var array
     */	
    protected $message = [
        'username.unique' => '用户名已存在'
    ];

    /**
     * 登录
     */
    public function sceneLogin(){
        if(config('web.login_captcha')==1){
			return $this->only(['username','password','captcha'])->remove('username', 'unique');
        }else{
			return $this->only(['username','password'])->remove('username', 'unique');
        }
    }

    /**
     * 添加
     */
    public function sceneAdd(){
        return $this->only(['username','password','nickname']);
    }

    /**
     * 编辑
     */
    public function sceneEdit()
    {
        return $this->only(['id','username','nickname']);
    }

    /**
     * 修改密码
     */
    public function scenePass(){
		return $this->only(['password']);
    }
}
