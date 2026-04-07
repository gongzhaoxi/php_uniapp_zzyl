<?php
namespace app\index\validate;
use think\Validate;

/**
 * 车间员工验证
 * Class CustomerValidate
 * @package app\index\validate
 */
class ErpUserValidate extends Validate{

    protected $rule = [
        'id' 					=> 'require',
        'name|姓名' 			=> 'require|max:50',
		'title|职称' 			=> 'require|max:50',
		'mobile|手机号码' 		=> 'require|mobile',
		'sn|手机端身份识别码' 	=> 'require|max:50',
        'terminal|客户端' 		=> 'require',
		
    ];

    protected $message = [
        'id.require' 			=> '参数缺失',
    ];

    public function sceneLogin(){
		return $this->only(['mobile','sn','terminal']);
    }




}