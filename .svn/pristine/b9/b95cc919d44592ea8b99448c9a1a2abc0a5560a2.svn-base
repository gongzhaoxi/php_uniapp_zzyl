<?php
namespace app\common\service\wechat;

use app\common\service\ConfigService;

/**
 * 微信配置类
 * Class WeChatConfigService
 * @package app\common\service
 */
class WeChatConfigService
{
    /**
     * @notes 获取小程序配置
     * @return array
     * @date 2022/9/6 19:49
     */
    public static function getMnpConfig()
    {
        $config = [
            'app_id' => ConfigService::get('mnp_setting', 'app_id'),
            'secret' => ConfigService::get('mnp_setting', 'app_secret'),
            'mch_id' => ConfigService::get('mnp_setting', 'mch_id'),
            'key' => ConfigService::get('mnp_setting', 'key'),
            'response_type' => 'array',
            'log' => [
                'level' => 'debug',
                'file' => '../runtime/log/wechat.log'
            ],
        ];
        return $config;
    }


    /**
     * @notes 获取微信公众号配置
     * @return array
     * @date 2022/9/6 19:49
     */
    public static function getOaConfig()
    {
        $config = [
            'app_id' => ConfigService::get('oa_setting', 'app_id'),
            'secret' => ConfigService::get('oa_setting', 'app_secret'),
            'mch_id' => ConfigService::get('oa_setting', 'mch_id'),
            'key' => ConfigService::get('oa_setting', 'key'),
            'token' => ConfigService::get('oa_setting', 'token'),
            'response_type' => 'array',
            'log' => [
                'level' => 'debug',
                'file' => '../runtime/log/wechat.log'
            ],
        ];
        return $config;
    }


    /**
     * @notes 获取微信开放平台配置
     * @return array
     * @date 2022/10/20 15:51
     */
    public static function getOpConfig()
    {
        $config = [
            'app_id' => ConfigService::get('open_platform', 'app_id'),
            'secret' => ConfigService::get('open_platform', 'app_secret'),
            'response_type' => 'array',
            'log' => [
                'level' => 'debug',
                'file' => '../runtime/log/wechat.log'
            ],
        ];
        return $config;
    }

}