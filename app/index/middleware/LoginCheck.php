<?php
declare (strict_types = 1);
namespace app\index\middleware;
use app\common\cache\UserTokenCache;
use app\index\service\UserTokenService;
use think\facade\Config;

class LoginCheck
{
	 use \app\common\traits\Base;
    /**
     * 处理请求
     */
    public function handle($request, \Closure $next)
    {
		$isAjax				= $request->isAjax();
		$token 				= $request->header('token');
		if(empty($token)) {
			$token			= cookie(Config::get('project.user_token.cookie_key'));
		}		
		if(empty($token)) {
			return $isAjax?$this->json('请先登录',999): redirect((string)url('login/index'));
		}
		$userInfo			= (new UserTokenCache())->getUserInfo($token);
		if(empty($userInfo)) {
			return $isAjax?$this->json('登录超时，请重新登录',999): redirect((string)url('login/index'));
		}
		//获取临近过期自动续期时长
		$beExpireDuration 	= Config::get('project.user_token.be_expire_duration');
		//token续期
		if (time() > ($userInfo['expire_time'] - $beExpireDuration)) {
			$result 		= UserTokenService::overtimeToken($token);
			//续期失败（数据表被删除导致）
			if (empty($result)) {
				return $isAjax?$this->json('登录过期',999): redirect((string)url('login/index'));
			}
		}
		
		//给request赋值，用于控制器
		$request->userInfo 	= $userInfo;
		$request->userId 	= $userInfo['user_id'] ?? 0;

        return $next($request);
    }
	

}
