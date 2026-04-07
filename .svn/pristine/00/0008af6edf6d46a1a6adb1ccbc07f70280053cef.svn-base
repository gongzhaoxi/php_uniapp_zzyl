<?php
declare (strict_types = 1);

namespace app\supplier\middleware;

use app\supplier\logic\ErpSupplierLogic;

class SupplierCheck
{
    /**
     * 处理请求
     */
    public function handle($request, \Closure $next)
    {
        if(ErpSupplierLogic::isLogin() == false){
            return redirect((string)url('login/index'));
        }
        return $next($request);
    }
}
