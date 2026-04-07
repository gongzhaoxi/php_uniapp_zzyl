<?php
declare (strict_types = 1);
namespace app\common\model;
use app\common\model\BaseModel;

class AdminPermission extends BaseModel
{


    // 子权限
    public function child()
    {
        return $this->hasMany('AdminPermission','pid','id');
    }
}
