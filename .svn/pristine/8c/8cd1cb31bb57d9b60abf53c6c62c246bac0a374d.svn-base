<?php
declare (strict_types = 1);

namespace app\admin\controller;
use app\common\util\Form;
use app\admin\logic\BaseLogic;


class Base extends \app\BaseController{
	
	protected $middleware = ['\app\admin\middleware\AdminCheck','\app\admin\middleware\AdminPermission'];
	protected function initialize(){
		$this->assign(['form'=>new Form(),'_admin'=>session('admin')]);
		BaseLogic::setAdmin(session('admin'));
	}

    // 获取系统参数
    protected function getSystem(){
        return [ 
            'os' => PHP_OS,
            'space' => round((disk_free_space('.')/(1024*1024)),2).'M',
            'addr' =>$_SERVER['HTTP_HOST'],
            'run' => $this->request->server('SERVER_SOFTWARE'),
            'php' => PHP_VERSION,
            'php_run' => php_sapi_name(),
            'mysql' => function_exists('mysql_get_server_info')?mysql_get_server_info():\think\facade\Db::query('SELECT VERSION() as mysql_version')[0]['mysql_version'],
            'think' => $this->app->version(),
            'upload' => ini_get('upload_max_filesize'),
            'max' => ini_get('max_execution_time').'秒',
            'ver' => 'V5.0.1',
        ];
    }

}
