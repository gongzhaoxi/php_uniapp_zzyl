<?php
declare (strict_types = 1);
namespace app\admin\controller;
use think\facade\Session;
use app\common\util\Upload as Up;
use app\admin\logic\{AdminPhotoLogic,AdminAdminLogic,ErpNoticeLogic};
use app\common\model\{ErpOrderProduceProcess,ErpOrderProduce,ErpOrder};

class Index extends Base
{
    protected $middleware = ['\app\admin\middleware\AdminCheck'];
    
    // 首页
    public function index(){
        return $this->fetch('',[
            'nickname'  => get_field('admin_admin',Session::get('admin.id'),'nickname')
        ]);
    }

    // 清除缓存
    public function cache(){
        Session::clear();
		return $this->getJson(rm());
	}

    // 菜单
    public function menu(){
        return json(get_tree(Session::get('admin.menu')));
    }

    // 欢迎页
    public function home(){
		$page = $this->request->param('page/d',1);
        return $this->fetch('',['page'=>$page,'notice'=>ErpNoticeLogic::getNoRead($page)]);
    }

    // 修改密码
    public function pass(){
        if ($this->request->isAjax()){
			return $this->getJson(AdminAdminLogic::goPass($this->request->only(['password'])));
        }
        return $this->fetch();
    }

    // 通用上传
    public function upload(){
		$config 		= [];
		$path			= $this->request->post('path','image');
		if($this->request->param('excel') == 1){
			$config 	= ['upload_size'=>30*1024,'upload_ext'=>'xlsx,xls','file-type'=>1];
			$path		= 'excel';
		}
        return $this->getJson(Up::putFile($this->request->file(),$path,$config));
    }

    // 图库选择
    public function optPhoto($type='radio',$file_type='image'){
        if ($this->request->isAjax()) {
            return $this->getJson(AdminPhotoLogic::getList($this->request->param(),$this->request->param('limit')));
        }
		$this->assign(['type'=>$type,'file_type'=>$file_type]);
        return $this->fetch('',AdminPhotoLogic::getPath());
    }

}
