<?php
declare (strict_types = 1);

namespace app\admin\middleware;

use app\admin\logic\AdminAdminLogic;

class AdminCheck
{
    /**
     * 处理请求
     */
    public function handle($request, \Closure $next)
    {
        if(AdminAdminLogic::isLogin() == false){
            return redirect((string)url('login/index'));
        }
        (new \app\common\model\AdminAdminLog)->record();
        return $next($request);
    }
}
