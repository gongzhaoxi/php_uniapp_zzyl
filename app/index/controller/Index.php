<?php
declare (strict_types = 1);

namespace app\index\controller;
use EasyWeChat\Factory;
use app\common\util\Upload as Up;
use app\index\logic\{ErpUserLogic};

class Index extends Base
{
	    // 首页
    public function index(){
        return $this->fetch('',[
            //'nickname'  => get_field('admin_admin',Session::get('admin.id'),'nickname')
        ]);
    }

    public function web()
    {
		$web = config('web');
        return  $this->getJson(['code'=>200,'data'=>['sys_name'=>$web['sys_name'],'logo'=>get_browse_url($web['sys_logo']),'title'=>$web['title']]]);
    }
	
	public function weixinSign(){
		
		$options 	= ['debug' => false,'app_id'=>'wx8dcae19c9b23cbda','secret'=> '47972ec2fc69ba0435c862524a2e59ef','response_type'=>'array'];
		$app 		= Factory::officialAccount($options);
		$url		= urldecode($this->request->param('url',''));
		$app->jssdk->setUrl($url);
		$result 	= $app->jssdk->buildConfig(['updateAppMessageShareData', 'updateTimelineShareData',
				'editAddress', 'chooseImage',
				'onMenuShareAppMessage', 'onMenuShareTimeline',
				'chooseImage','scanQRCode',
				'previewImage', 'uploadImage',
				'downloadImage', 'chooseWXPay',
				'getLocation', 'openLocation'], false, false, false);
		return $this->getJson(['data'=>$result]);
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
	
	
	//二维码
	public function qrcode() {
		$str					= urldecode($this->request->param('str'));	
		$qrcode 				= new \app\common\util\Qrcode();
		$errorCorrectionLevel 	= "L";
		$matrixPointSize 		= "15";
		$qrcode->png($str, false, $errorCorrectionLevel, $matrixPointSize ,'1');
		exit ();
	}
	
	
	public function toDoCount(){
		return $this->getJson(['data'=>ErpUserLogic::toDoCount($this->request->userInfo)]);
	}
	
	public function toDoList(){
        if ($this->request->isAjax()) {
			return $this->getJson(ErpUserLogic::toDoList($this->request->userInfo,$this->request->param(),$this->request->param('limit')));
        }
        return $this->fetch('',[]);
    }
	
	// 修改密码
    public function pass(){
        if ($this->request->isAjax()){
			return $this->getJson(ErpUserLogic::goPass($this->request->userInfo,$this->request->only(['sn'])));
        }
        return $this->fetch();
    }
	
}
