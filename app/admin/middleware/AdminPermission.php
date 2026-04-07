<?php
declare (strict_types = 1);

namespace app\admin\middleware;

use think\facade\Session;

class AdminPermission
{
    use \app\common\traits\Base;

    /**
     * 处理请求
     *
     * @param \think\Request $request
     * @param \Closure       $next
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        //超级管理员不需要验证
        if (Session::get('admin.id') == 1) return $next($request);
        //验证权限
        $url = strtolower($request->controller(true).'/'.$request->action(true));
		if(substr($url,0,6) == 'h5.erp'){
			$url = 'erp.'.substr($url,6);
		}
        $auth = array_column(Session::get('admin.menu'), 'auth');
        if (!in_array($url, $auth)) {
			return $request->isAjax()? $this->json('权限不足',999):$this->error('权限不足','');
		}
        return $next($request);
    }
}
