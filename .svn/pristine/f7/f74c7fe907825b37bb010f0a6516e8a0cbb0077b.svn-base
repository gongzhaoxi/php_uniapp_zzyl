<?php
namespace app\index\validate;
use think\Validate;

/**
 * 微信登录验证
 * Class WechatLoginValidate
 * @package app\index\validate
 */
class WechatLoginValidate extends Validate
{
    protected $rule = [
        'code' => 'require',
        'nickname' => 'require',
        'headimgurl' => 'require',
        'openid' => 'require',
        'access_token' => 'require',
        'terminal' => 'require',
    ];

    protected $message = [
        'code.require' => 'code缺少',
        'nickname.require' => '昵称缺少',
        'headimgurl.require' => '头像缺少',
        'openid.require' => 'opendid缺少',
        'access_token.require' => 'access_token缺少',
        'terminal.require' => '终端参数缺少',
    ];


    /**
     * @notes 公众号登录场景
     * @return WechatLoginValidate
     * @author 段誉
     * @date 2022/9/16 10:57
     */
    public function sceneOa()
    {
        return $this->only(['code']);
    }


    /**
     * @notes 小程序-授权登录场景
     * @return WechatLoginValidate
     * @author 段誉
     * @date 2022/9/16 11:15
     */
    public function sceneMnp()
    {
        return $this->only(['code']);
    }

}