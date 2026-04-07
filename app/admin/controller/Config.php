<?php
declare (strict_types = 1);
namespace app\admin\controller;

class Config extends Base
{

    // 系统配置
    public function index(){
        if($this->request->isPost()){
           set_web($this->request->post('','','strip_tags'));
           return  $this->success('保存成功','config/index');
        }
        return $this->fetch('',[
            'data' => config('web')
        ]);
    }
}
